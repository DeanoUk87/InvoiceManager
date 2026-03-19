import { NextResponse } from "next/server";
import { db } from "@/db";
import { sales, invoices, settings } from "@/db/schema";
import { auth } from "@/lib/auth";
import { eq, and } from "drizzle-orm";

export const maxDuration = 60;

export async function POST() {
  try {
    const session = await auth();
    if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

    const ungrouped = await db.select({
      invoiceNumber: sales.invoiceNumber,
      customerAccount: sales.customerAccount,
      invoiceDate: sales.invoiceDate,
      numb2: sales.numb2,
    }).from(sales).where(and(eq(sales.msCreated, 0), eq(sales.invoiceReady, 0)));

    if (ungrouped.length === 0) {
      return NextResponse.json({ success: false, message: "No new sales data found. Upload a CSV first." });
    }

    // Deduplicate by invoice key
    const seen = new Set<string>();
    const unique = ungrouped.filter(s => {
      const key = `${s.invoiceNumber}|${s.customerAccount}|${s.invoiceDate}`;
      if (seen.has(key)) return false;
      seen.add(key);
      return true;
    });

    const [sett] = await db.select().from(settings).limit(1);
    const dueDays = sett?.invoiceDueDate ?? 30;

    // Fetch all existing invoices in ONE query - avoid N+1
    const existingRows = await db.select({
      customerAccount: invoices.customerAccount,
      invoiceNumber: invoices.invoiceNumber,
      invoiceDate: invoices.invoiceDate,
    }).from(invoices);

    const existingSet = new Set(
      existingRows.map(i => `${i.invoiceNumber}|${i.customerAccount}|${i.invoiceDate}`)
    );

    // Build new invoices to insert
    const toInsert = unique
      .filter(s => !existingSet.has(`${s.invoiceNumber}|${s.customerAccount}|${s.invoiceDate}`))
      .map(s => {
        let dueDate: string | null = null;
        if (s.invoiceDate) {
          const d = parseSafeDate(s.invoiceDate);
          if (d) {
            d.setDate(d.getDate() + (s.numb2 ?? dueDays));
            dueDate = d.toISOString().split("T")[0];
          }
        }
        return {
          customerAccount: s.customerAccount,
          invoiceNumber: s.invoiceNumber,
          invoiceDate: s.invoiceDate,
          dueDate,
          dateCreated: new Date().toISOString().split("T")[0],
          printer: 0,
          emailStatus: 0,
        };
      });

    // Insert all new invoices in parallel batches of 20 concurrent requests
    const CONCURRENCY = 20;
    for (let i = 0; i < toInsert.length; i += CONCURRENCY) {
      await Promise.all(
        toInsert.slice(i, i + CONCURRENCY).map(inv =>
          db.insert(invoices).values(inv)
        )
      );
    }

    // Mark sales as invoice_ready in one update
    await db.update(sales)
      .set({ invoiceReady: 1 })
      .where(and(eq(sales.msCreated, 0), eq(sales.invoiceReady, 0)));

    return NextResponse.json({
      success: true,
      generated: toInsert.length,
      message: `Generated ${toInsert.length} invoice(s) from ${unique.length} unique invoice numbers.`,
    });

  } catch (e) {
    console.error("Generate invoices error:", e);
    return NextResponse.json({ error: `Failed: ${String(e)}` }, { status: 500 });
  }
}

// Safely parse a date string in YYYY-MM-DD, DD/MM/YYYY, or MM/DD/YYYY format
function parseSafeDate(dateStr: string): Date | null {
  if (!dateStr) return null;
  const clean = dateStr.trim();
  // YYYY-MM-DD
  if (/^\d{4}-\d{2}-\d{2}$/.test(clean)) {
    const d = new Date(clean);
    return isNaN(d.getTime()) ? null : d;
  }
  // DD/MM/YYYY
  const dmY = clean.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
  if (dmY) {
    const d = new Date(`${dmY[3]}-${dmY[2].padStart(2, "0")}-${dmY[1].padStart(2, "0")}`);
    return isNaN(d.getTime()) ? null : d;
  }
  // Try native parse as last resort
  const d = new Date(clean);
  return isNaN(d.getTime()) ? null : d;
}

import { NextResponse } from "next/server";
import { db, batchDb } from "@/db";
import { sales, invoices, settings } from "@/db/schema";
import { auth } from "@/lib/auth";
import { eq, and } from "drizzle-orm";

export async function POST() {
  try {
    const session = await auth();
    if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

    // Get all unprocessed sales grouped by unique invoice
    const ungrouped = await db.select({
      invoiceNumber: sales.invoiceNumber,
      customerAccount: sales.customerAccount,
      invoiceDate: sales.invoiceDate,
      numb2: sales.numb2,
      id: sales.id,
    }).from(sales).where(and(eq(sales.msCreated, 0), eq(sales.invoiceReady, 0)));

    if (ungrouped.length === 0) {
      return NextResponse.json({ success: false, message: "No new sales data found. Upload a CSV first." });
    }

    // Deduplicate by invoice number + account + date
    const seen = new Set<string>();
    const unique = ungrouped.filter(s => {
      const key = `${s.invoiceNumber}|${s.customerAccount}|${s.invoiceDate}`;
      if (seen.has(key)) return false;
      seen.add(key);
      return true;
    });

    const [sett] = await db.select().from(settings).limit(1);
    const dueDays = sett?.invoiceDueDate ?? 30;

    // Get all existing invoices for these accounts in one query to avoid N+1
    const existingInvoices = await db.select({
      customerAccount: invoices.customerAccount,
      invoiceNumber: invoices.invoiceNumber,
      invoiceDate: invoices.invoiceDate,
    }).from(invoices);

    const existingSet = new Set(
      existingInvoices.map(i => `${i.invoiceNumber}|${i.customerAccount}|${i.invoiceDate}`)
    );

    // Build list of new invoices to insert
    const toInsert = unique
      .filter(sale => !existingSet.has(`${sale.invoiceNumber}|${sale.customerAccount}|${sale.invoiceDate}`))
      .map(sale => {
        let dueDate: string | null = null;
        if (sale.invoiceDate) {
          const d = new Date(sale.invoiceDate);
          d.setDate(d.getDate() + (sale.numb2 ?? dueDays));
          dueDate = d.toISOString().split("T")[0];
        }
        return {
          customerAccount: sale.customerAccount,
          invoiceNumber: sale.invoiceNumber,
          invoiceDate: sale.invoiceDate,
          dueDate,
          dateCreated: new Date().toISOString().split("T")[0],
          printer: 0,
          emailStatus: 0,
        };
      });

    // Insert all new invoices using batch (parallel) - one HTTP call per 200
    const BATCH_SIZE = 200;
    for (let i = 0; i < toInsert.length; i += BATCH_SIZE) {
      const chunk = toInsert.slice(i, i + BATCH_SIZE);
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      await (batchDb as any).batch(
        chunk.map(inv => batchDb.insert(invoices).values(inv))
      );
    }

    // Mark all those sales as invoice_ready in one update
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
    return NextResponse.json({ error: `Failed to generate invoices: ${String(e)}` }, { status: 500 });
  }
}

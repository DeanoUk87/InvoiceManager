import { NextResponse } from "next/server";
import { db } from "@/db";
import { sales, invoices, settings } from "@/db/schema";
import { auth } from "@/lib/auth";
import { eq, and } from "drizzle-orm";

export async function POST() {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  const ungrouped = await db.select({
    invoiceNumber: sales.invoiceNumber,
    customerAccount: sales.customerAccount,
    invoiceDate: sales.invoiceDate,
    numb2: sales.numb2,
    id: sales.id,
  }).from(sales).where(and(eq(sales.msCreated, 0), eq(sales.invoiceReady, 0)));

  if (ungrouped.length === 0) {
    return NextResponse.json({ success: false, message: "No new sales data found." });
  }

  const seen = new Set<string>();
  const unique = ungrouped.filter(s => {
    const key = `${s.invoiceNumber}|${s.customerAccount}|${s.invoiceDate}`;
    if (seen.has(key)) return false;
    seen.add(key);
    return true;
  });

  const [sett] = await db.select().from(settings).limit(1);
  const dueDays = sett?.invoiceDueDate ?? 30;

  let generated = 0;
  for (const sale of unique) {
    let dueDate: string | null = null;
    if (sale.invoiceDate) {
      const d = new Date(sale.invoiceDate);
      d.setDate(d.getDate() + (sale.numb2 ?? dueDays));
      dueDate = d.toISOString().split('T')[0];
    }

    const conds = [eq(invoices.customerAccount, sale.customerAccount), eq(invoices.invoiceNumber, sale.invoiceNumber)];
    if (sale.invoiceDate) conds.push(eq(invoices.invoiceDate, sale.invoiceDate));
    const [existing] = await db.select().from(invoices).where(and(...conds)).limit(1);

    if (!existing) {
      await db.insert(invoices).values({
        customerAccount: sale.customerAccount,
        invoiceNumber: sale.invoiceNumber,
        invoiceDate: sale.invoiceDate,
        dueDate,
        dateCreated: new Date().toISOString().split('T')[0],
        printer: 0,
        emailStatus: 0,
      });
      generated++;
    }
  }

  await db.update(sales).set({ invoiceReady: 1 }).where(and(eq(sales.msCreated, 0), eq(sales.invoiceReady, 0)));

  return NextResponse.json({ success: true, generated, message: `Generated ${generated} invoice(s) from ${unique.length} unique invoice numbers.` });
}

import { NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";
import { auth } from "@/lib/auth";

export async function POST() {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  // Find all sales that haven't had invoices generated yet
  const ungrouped = await prisma.sale.findMany({
    where: { msCreated: 0, invoiceReady: 0 },
    select: { invoiceNumber: true, customerAccount: true, invoiceDate: true, numb2: true, id: true },
    orderBy: { id: "asc" },
  });

  if (ungrouped.length === 0) {
    return NextResponse.json({ success: false, message: "No new sales data found to generate invoices." });
  }

  // Group by invoice number (unique invoices)
  const seen = new Set<string>();
  const unique = ungrouped.filter((s) => {
    const key = `${s.invoiceNumber}|${s.customerAccount}|${s.invoiceDate}`;
    if (seen.has(key)) return false;
    seen.add(key);
    return true;
  });

  const settings = await prisma.settings.findFirst();
  const dueDays = settings?.invoiceDueDate ?? 30;

  let generated = 0;
  for (const sale of unique) {
    // Calculate due date
    let dueDate: string | null = null;
    if (sale.invoiceDate) {
      const d = new Date(sale.invoiceDate);
      d.setDate(d.getDate() + (sale.numb2 ?? dueDays));
      dueDate = d.toISOString().split("T")[0];
    }

    // Upsert invoice
    const existing = await prisma.invoice.findFirst({
      where: {
        customerAccount: sale.customerAccount,
        invoiceNumber: sale.invoiceNumber,
        invoiceDate: sale.invoiceDate ?? undefined,
      },
    });

    if (!existing) {
      await prisma.invoice.create({
        data: {
          customerAccount: sale.customerAccount,
          invoiceNumber: sale.invoiceNumber,
          invoiceDate: sale.invoiceDate ?? undefined,
          dueDate,
          dateCreated: new Date().toISOString().split("T")[0],
          printer: 0,
          emailStatus: 0,
        },
      });
      generated++;
    }
  }

  // Mark all those sales as invoice_ready
  await prisma.sale.updateMany({
    where: { msCreated: 0, invoiceReady: 0 },
    data: { invoiceReady: 1 },
  });

  return NextResponse.json({
    success: true,
    generated,
    message: `Generated ${generated} invoice(s) from ${unique.length} unique invoice numbers.`,
  });
}

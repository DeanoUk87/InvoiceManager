import { NextResponse } from "next/server";
import { db } from "@/db";
import { invoices, sales } from "@/db/schema";
import { auth } from "@/lib/auth";
import { eq, and } from "drizzle-orm";

export async function GET(_req: Request, { params }: { params: Promise<{ id: string }> }) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
  const { id } = await params;
  const [invoice] = await db.select().from(invoices).where(eq(invoices.id, parseInt(id)));
  if (!invoice) return NextResponse.json({ error: "Not found" }, { status: 404 });

  const conditions = [eq(sales.customerAccount, invoice.customerAccount), eq(sales.invoiceNumber, invoice.invoiceNumber)];
  if (invoice.invoiceDate) conditions.push(eq(sales.invoiceDate, invoice.invoiceDate));
  const saleRows = await db.select().from(sales).where(and(...conditions));

  const headers = ["JOB DATE","INVOICE NUMBER","JOB NUMBER","SENDERS REF","POSTCODE","DESTINATION","INVOICE DATE","TOWN/CITY","SERVICE TYPE","ITEMS","WEIGHT","CHARGE","INVOICE TOTAL"];
  const rows = saleRows.map(s => [s.jobDate??'',s.invoiceNumber,s.jobNumber??'',s.senderReference??'',s.postcode2??'',s.destination??'',s.invoiceDate??'',s.town2??'',s.serviceType??'',s.items2??'',s.volumeWeight??'',s.subTotal??'',s.invoiceTotal??'']);
  const csv = [headers, ...rows].map(r => r.map(v => `"${v}"`).join(",")).join("\n");

  return new Response(csv, {
    headers: {
      "Content-Type": "text/csv",
      "Content-Disposition": `attachment; filename="Invoice_${invoice.invoiceNumber}.csv"`,
    },
  });
}

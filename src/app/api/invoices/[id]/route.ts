import { NextResponse } from "next/server";
import { db } from "@/db";
import { invoices, customers, sales, settings } from "@/db/schema";
import { auth } from "@/lib/auth";
import { eq, and } from "drizzle-orm";

export async function GET(_req: Request, { params }: { params: Promise<{ id: string }> }) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
  const { id } = await params;

  const [invoice] = await db.select().from(invoices).where(eq(invoices.id, parseInt(id)));
  if (!invoice) return NextResponse.json({ error: "Not found" }, { status: 404 });

  const conditions = [
    eq(sales.customerAccount, invoice.customerAccount),
    eq(sales.invoiceNumber, invoice.invoiceNumber),
  ];
  if (invoice.invoiceDate) conditions.push(eq(sales.invoiceDate, invoice.invoiceDate));

  const [customer, saleRows, [sett]] = await Promise.all([
    db.select().from(customers).where(eq(customers.customerAccount, invoice.customerAccount)).then(r => r[0] ?? null),
    db.select().from(sales).where(and(...conditions)).orderBy(sales.id),
    db.select().from(settings).limit(1),
  ]);

  return NextResponse.json({ invoice, customer, sales: saleRows, settings: sett ?? null });
}

export async function PUT(req: Request, { params }: { params: Promise<{ id: string }> }) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
  const { id } = await params;
  const data = await req.json();
  const [invoice] = await db.update(invoices).set(data).where(eq(invoices.id, parseInt(id))).returning();
  return NextResponse.json(invoice);
}

export async function DELETE(_req: Request, { params }: { params: Promise<{ id: string }> }) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
  const { id } = await params;
  await db.delete(invoices).where(eq(invoices.id, parseInt(id)));
  return NextResponse.json({ success: true });
}

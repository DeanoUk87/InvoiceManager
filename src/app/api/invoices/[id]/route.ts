import { NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";
import { auth } from "@/lib/auth";

export async function GET(_req: Request, { params }: { params: Promise<{ id: string }> }) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
  const { id } = await params;

  const invoice = await prisma.invoice.findUnique({ where: { id: parseInt(id) } });
  if (!invoice) return NextResponse.json({ error: "Not found" }, { status: 404 });

  const [customer, sales, settings] = await Promise.all([
    prisma.customer.findFirst({ where: { customerAccount: invoice.customerAccount } }),
    prisma.sale.findMany({
      where: {
        customerAccount: invoice.customerAccount,
        invoiceNumber: invoice.invoiceNumber,
        invoiceDate: invoice.invoiceDate ?? undefined,
      },
      orderBy: { id: "asc" },
    }),
    prisma.settings.findFirst(),
  ]);

  return NextResponse.json({ invoice, customer, sales, settings });
}

export async function PUT(req: Request, { params }: { params: Promise<{ id: string }> }) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
  const { id } = await params;
  const data = await req.json();
  const invoice = await prisma.invoice.update({ where: { id: parseInt(id) }, data });
  return NextResponse.json(invoice);
}

export async function DELETE(_req: Request, { params }: { params: Promise<{ id: string }> }) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
  const { id } = await params;
  await prisma.invoice.delete({ where: { id: parseInt(id) } });
  return NextResponse.json({ success: true });
}

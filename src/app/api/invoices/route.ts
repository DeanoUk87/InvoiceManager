import { NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";
import { auth } from "@/lib/auth";

export async function GET(req: Request) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  const { searchParams } = new URL(req.url);
  const dateFrom = searchParams.get("dateFrom");
  const dateTo = searchParams.get("dateTo");
  const account = searchParams.get("account");
  const invoiceNo = searchParams.get("invoiceNo");
  const status = searchParams.get("status"); // "printed" | "unprinted"

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const where: any = {};

  if (status === "printed") where.printer = 2;
  if (status === "unprinted") where.printer = { in: [0, 1] };

  if (account) where.customerAccount = { contains: account };
  if (invoiceNo) where.invoiceNumber = { contains: invoiceNo };
  if (dateFrom && dateTo) {
    where.invoiceDate = { gte: dateFrom, lte: dateTo };
  }

  const invoices = await prisma.invoice.findMany({
    where,
    orderBy: { invoiceDate: "desc" },
    take: 1000,
  });

  return NextResponse.json(invoices);
}

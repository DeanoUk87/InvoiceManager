import { NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";
import { auth } from "@/lib/auth";

export async function GET(req: Request) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  const { searchParams } = new URL(req.url);
  const dateFrom = searchParams.get("dateFrom");
  const dateTo = searchParams.get("dateTo");
  const jobNumber = searchParams.get("jobNumber");
  const account = searchParams.get("account");

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const where: any = {};
  if (jobNumber) where.jobNumber = { contains: jobNumber };
  if (account) where.customerAccount = { contains: account };
  if (dateFrom && dateTo) {
    where.invoiceDate = { gte: dateFrom, lte: dateTo };
  }

  const sales = await prisma.sale.findMany({
    where,
    orderBy: { id: "desc" },
    take: 500,
  });

  return NextResponse.json(sales);
}

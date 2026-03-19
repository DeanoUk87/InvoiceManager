import { NextResponse } from "next/server";
import { db } from "@/db";
import { invoices } from "@/db/schema";
import { auth } from "@/lib/auth";
import { eq, and, inArray, like, gte, lte, desc } from "drizzle-orm";

export async function GET(req: Request) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  const { searchParams } = new URL(req.url);
  const dateFrom = searchParams.get("dateFrom");
  const dateTo = searchParams.get("dateTo");
  const account = searchParams.get("account");
  const invoiceNo = searchParams.get("invoiceNo");
  const status = searchParams.get("status");

  const conditions = [];
  if (status === "printed") conditions.push(eq(invoices.printer, 2));
  if (status === "unprinted") conditions.push(inArray(invoices.printer, [0, 1]));
  if (account) conditions.push(like(invoices.customerAccount, `%${account}%`));
  if (invoiceNo) conditions.push(like(invoices.invoiceNumber, `%${invoiceNo}%`));
  if (dateFrom) conditions.push(gte(invoices.invoiceDate, dateFrom));
  if (dateTo) conditions.push(lte(invoices.invoiceDate, dateTo));

  const result = await db.select().from(invoices)
    .where(conditions.length > 0 ? and(...conditions) : undefined)
    .orderBy(desc(invoices.invoiceDate))
    .limit(1000);

  return NextResponse.json(result);
}

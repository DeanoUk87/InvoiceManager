import { NextResponse } from "next/server";
import { db } from "@/db";
import { sales } from "@/db/schema";
import { auth } from "@/lib/auth";
import { like, and, gte, lte, desc } from "drizzle-orm";

export async function GET(req: Request) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
  const { searchParams } = new URL(req.url);
  const dateFrom = searchParams.get("dateFrom");
  const dateTo = searchParams.get("dateTo");
  const jobNumber = searchParams.get("jobNumber");
  const account = searchParams.get("account");

  const conditions = [];
  if (jobNumber) conditions.push(like(sales.jobNumber, `%${jobNumber}%`));
  if (account) conditions.push(like(sales.customerAccount, `%${account}%`));
  if (dateFrom) conditions.push(gte(sales.invoiceDate, dateFrom));
  if (dateTo) conditions.push(lte(sales.invoiceDate, dateTo));

  const result = await db.select().from(sales)
    .where(conditions.length > 0 ? and(...conditions) : undefined)
    .orderBy(desc(sales.id))
    .limit(500);
  return NextResponse.json(result);
}

import { NextResponse } from "next/server";
import { db } from "@/db";
import { invoices } from "@/db/schema";
import { auth } from "@/lib/auth";
import { and, gte, lte, asc } from "drizzle-orm";

export async function GET(req: Request) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
  const { searchParams } = new URL(req.url);
  const dateFrom = searchParams.get("dateFrom");
  const dateTo = searchParams.get("dateTo");

  const conditions = [];
  if (dateFrom) conditions.push(gte(invoices.invoiceDate, dateFrom));
  if (dateTo) conditions.push(lte(invoices.invoiceDate, dateTo));

  const result = await db.select().from(invoices)
    .where(conditions.length > 0 ? and(...conditions) : undefined)
    .orderBy(asc(invoices.invoiceDate));

  const headers = ["Invoice Number","Invoice Date","Due Date","Customer Account","Status","Date Created"];
  const rows = result.map(inv => [inv.invoiceNumber, inv.invoiceDate??'', inv.dueDate??'', inv.customerAccount, inv.printer===2?'Printed':'Unprinted', inv.dateCreated??'']);
  const csv = [headers, ...rows].map(r => r.map(v => `"${v}"`).join(",")).join("\n");

  return new Response(csv, {
    headers: {
      "Content-Type": "text/csv",
      "Content-Disposition": `attachment; filename="invoices_export.csv"`,
    },
  });
}

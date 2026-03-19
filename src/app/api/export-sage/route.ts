import { NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";
import { auth } from "@/lib/auth";

export async function GET(req: Request) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  const { searchParams } = new URL(req.url);
  const dateFrom = searchParams.get("dateFrom");
  const dateTo = searchParams.get("dateTo");

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const where: any = {};
  if (dateFrom && dateTo) {
    where.invoiceDate = { gte: dateFrom, lte: dateTo };
  }

  const invoices = await prisma.invoice.findMany({ where, orderBy: { invoiceDate: "asc" } });

  const headers = [
    "Invoice Number", "Invoice Date", "Due Date", "Customer Account",
    "Status", "Date Created"
  ];

  const rows = invoices.map((inv) => [
    inv.invoiceNumber,
    inv.invoiceDate ?? "",
    inv.dueDate ?? "",
    inv.customerAccount,
    inv.printer === 2 ? "Printed" : "Unprinted",
    inv.dateCreated ?? "",
  ]);

  const csv = [
    headers.join(","),
    ...rows.map((r) => r.map((v) => `"${v}"`).join(",")),
  ].join("\n");

  return new Response(csv, {
    headers: {
      "Content-Type": "text/csv",
      "Content-Disposition": `attachment; filename="invoices_export_${dateFrom ?? "all"}.csv"`,
    },
  });
}

import { NextResponse } from "next/server";
import { db } from "@/db";
import { sales, invoices, uploadedCsv, salesArchive, invoicesArchive } from "@/db/schema";
import { auth } from "@/lib/auth";

export async function POST(req: Request) {
  const session = await auth();
  if (!session || session.user.role !== "admin") {
    return NextResponse.json({ error: "Forbidden - Admin only" }, { status: 403 });
  }

  const { action } = await req.json();

  if (action === "clear-invoices") {
    await db.delete(invoices);
    await db.delete(sales);
    await db.delete(uploadedCsv);
    return NextResponse.json({ success: true, message: "All invoices, sales data and uploaded CSV records cleared." });
  }

  if (action === "clear-archives") {
    await db.delete(salesArchive);
    await db.delete(invoicesArchive);
    return NextResponse.json({ success: true, message: "Archive data cleared." });
  }

  return NextResponse.json({ error: "Unknown action" }, { status: 400 });
}

import { NextResponse } from "next/server";
import { db } from "@/db";
import { uploadedCsv, sales, invoices } from "@/db/schema";
import { auth } from "@/lib/auth";
import { eq } from "drizzle-orm";

export async function DELETE(_req: Request, { params }: { params: Promise<{ id: string }> }) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  const { id } = await params;
  const [upload] = await db.select().from(uploadedCsv).where(eq(uploadedCsv.id, parseInt(id)));
  if (!upload) return NextResponse.json({ error: "Not found" }, { status: 404 });

  const uploadTs = upload.uploadTs;

  // Delete all sales rows with this uploadTs
  await db.delete(sales).where(eq(sales.uploadTs, uploadTs));

  // Delete the upload record itself
  await db.delete(uploadedCsv).where(eq(uploadedCsv.id, parseInt(id)));

  return NextResponse.json({ success: true, message: `Deleted upload "${upload.filename}" and its sales data.` });
}

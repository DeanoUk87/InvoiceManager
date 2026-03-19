import { NextResponse } from "next/server";
import { db } from "@/db";
import { sales } from "@/db/schema";
import { auth } from "@/lib/auth";
import { eq } from "drizzle-orm";

export async function DELETE(_req: Request, { params }: { params: Promise<{ id: string }> }) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
  const { id } = await params;
  await db.delete(sales).where(eq(sales.id, parseInt(id)));
  return NextResponse.json({ success: true });
}

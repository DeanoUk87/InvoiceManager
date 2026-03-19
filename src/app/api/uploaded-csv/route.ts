import { NextResponse } from "next/server";
import { db } from "@/db";
import { uploadedCsv } from "@/db/schema";
import { auth } from "@/lib/auth";
import { desc } from "drizzle-orm";

export async function GET() {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
  const uploads = await db.select().from(uploadedCsv).orderBy(desc(uploadedCsv.createdAt));
  return NextResponse.json(uploads);
}

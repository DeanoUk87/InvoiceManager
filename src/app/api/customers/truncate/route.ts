import { NextResponse } from "next/server";
import { db } from "@/db";
import { customers } from "@/db/schema";
import { auth } from "@/lib/auth";

export async function POST() {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
  await db.delete(customers);
  return NextResponse.json({ success: true });
}

import { NextResponse } from "next/server";
import { db } from "@/db";
import { settings } from "@/db/schema";
import { auth } from "@/lib/auth";
import { eq } from "drizzle-orm";

export async function GET() {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
  const [s] = await db.select().from(settings).limit(1);
  return NextResponse.json(s ?? null);
}

export async function PUT(req: Request) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
  const data = await req.json();
  const [existing] = await db.select().from(settings).limit(1);
  if (existing) {
    const [updated] = await db.update(settings).set(data).where(eq(settings.id, existing.id)).returning();
    return NextResponse.json(updated);
  } else {
    const [created] = await db.insert(settings).values(data).returning();
    return NextResponse.json(created);
  }
}

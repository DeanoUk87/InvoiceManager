import { NextResponse } from "next/server";
import { db } from "@/db";
import { users } from "@/db/schema";
import { auth } from "@/lib/auth";
import { eq } from "drizzle-orm";
import bcrypt from "bcryptjs";

export async function PUT(req: Request, { params }: { params: Promise<{ id: string }> }) {
  const session = await auth();
  if (!session || session.user.role !== "admin") {
    return NextResponse.json({ error: "Forbidden" }, { status: 403 });
  }
  const { id } = await params;
  const { name, email, role, username, password } = await req.json();

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const updateData: any = { name: name || null, email, role, username: username || null };
  if (password && password.trim()) {
    updateData.password = await bcrypt.hash(password, 10);
  }

  const [user] = await db.update(users).set(updateData).where(eq(users.id, id))
    .returning({ id: users.id, email: users.email, role: users.role, name: users.name });
  return NextResponse.json(user);
}

export async function DELETE(_req: Request, { params }: { params: Promise<{ id: string }> }) {
  const session = await auth();
  if (!session || session.user.role !== "admin") {
    return NextResponse.json({ error: "Forbidden" }, { status: 403 });
  }
  const { id } = await params;
  // Prevent deleting self
  if (id === session.user.id) {
    return NextResponse.json({ error: "You cannot delete your own account." }, { status: 400 });
  }
  await db.delete(users).where(eq(users.id, id));
  return NextResponse.json({ success: true });
}

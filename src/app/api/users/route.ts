import { NextResponse } from "next/server";
import { db } from "@/db";
import { users } from "@/db/schema";
import { auth } from "@/lib/auth";
import { asc } from "drizzle-orm";
import bcrypt from "bcryptjs";
import { v4 as uuid } from "uuid";

export async function GET() {
  const session = await auth();
  if (!session || session.user.role !== "admin") {
    return NextResponse.json({ error: "Forbidden" }, { status: 403 });
  }
  const result = await db.select({
    id: users.id,
    name: users.name,
    email: users.email,
    role: users.role,
    username: users.username,
    createdAt: users.createdAt,
  }).from(users).orderBy(asc(users.createdAt));
  return NextResponse.json(result);
}

export async function POST(req: Request) {
  const session = await auth();
  if (!session || session.user.role !== "admin") {
    return NextResponse.json({ error: "Forbidden" }, { status: 403 });
  }
  const { name, email, password, role, username } = await req.json();
  if (!email || !password || !role) {
    return NextResponse.json({ error: "Email, password and role are required." }, { status: 400 });
  }
  const hashed = await bcrypt.hash(password, 10);
  const [user] = await db.insert(users).values({
    id: uuid(),
    name: name || null,
    email,
    password: hashed,
    role,
    username: username || null,
  }).returning({ id: users.id, email: users.email, role: users.role, name: users.name });
  return NextResponse.json(user);
}

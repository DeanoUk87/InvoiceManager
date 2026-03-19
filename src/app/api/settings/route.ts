import { NextResponse } from "next/server";
import { db } from "@/db";
import { settings, users, sales, invoices, uploadedCsv } from "@/db/schema";
import { auth } from "@/lib/auth";
import { eq, asc } from "drizzle-orm";
import bcrypt from "bcryptjs";

// eslint-disable-next-line @typescript-eslint/no-explicit-any
function adminOnly(session: any) {
  return !session || session.user?.role !== "admin";
}

// GET: settings object, or ?action=users to list users
export async function GET(req: Request) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized", v: "v2" }, { status: 401 });
  const { searchParams } = new URL(req.url);
  const action = searchParams.get("action");

  if (action === "users") {
    if (adminOnly(session)) return NextResponse.json({ error: "Forbidden" }, { status: 403 });
    const result = await db.select({
      id: users.id, name: users.name, email: users.email,
      role: users.role, username: users.username,
    }).from(users).orderBy(asc(users.createdAt));
    return NextResponse.json(result);
  }

  const [s] = await db.select().from(settings).limit(1);
  return NextResponse.json(s ?? null);
}

// PUT: update settings OR create/update user
export async function PUT(req: Request) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized", v: "v2" }, { status: 401 });

  const data = await req.json();
  const action = data.action;

  if (action === "create-user") {
    if (adminOnly(session)) return NextResponse.json({ error: "Forbidden" }, { status: 403 });
    const { name, email, password, role, username } = data;
    if (!email || !password || !role) return NextResponse.json({ error: "Email, password and role required." }, { status: 400 });
    const hashed = await bcrypt.hash(password, 10);
    const id = `user-${Date.now()}-${Math.random().toString(36).slice(2,8)}`;
    const [user] = await db.insert(users).values({
      id, name: name || null, email, password: hashed, role, username: username || null,
    }).returning({ id: users.id, email: users.email, role: users.role, name: users.name });
    return NextResponse.json(user);
  }

  if (action === "update-user") {
    if (adminOnly(session)) return NextResponse.json({ error: "Forbidden" }, { status: 403 });
    const { userId, name, email, role, username, password } = data;
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const updateData: any = { name: name || null, email, role, username: username || null };
    if (password && password.trim()) updateData.password = await bcrypt.hash(password, 10);
    const [user] = await db.update(users).set(updateData).where(eq(users.id, userId))
      .returning({ id: users.id, email: users.email, role: users.role, name: users.name });
    return NextResponse.json(user);
  }

  // Default: update settings
  if (adminOnly(session)) return NextResponse.json({ error: "Forbidden" }, { status: 403 });
  const [existing] = await db.select().from(settings).limit(1);
  if (existing) {
    const [updated] = await db.update(settings).set(data).where(eq(settings.id, existing.id)).returning();
    return NextResponse.json(updated);
  } else {
    const [created] = await db.insert(settings).values(data).returning();
    return NextResponse.json(created);
  }
}

// DELETE: delete user OR clear invoice data
export async function DELETE(req: Request) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized", v: "v2" }, { status: 401 });
  if (adminOnly(session)) return NextResponse.json({ error: "Forbidden" }, { status: 403 });

  const data = await req.json();
  const action = data.action;

  if (action === "delete-user") {
    if (data.userId === session.user.id) return NextResponse.json({ error: "Cannot delete your own account." }, { status: 400 });
    await db.delete(users).where(eq(users.id, data.userId));
    return NextResponse.json({ success: true });
  }

  if (action === "delete-upload") {
    const [upload] = await db.select().from(uploadedCsv).where(eq(uploadedCsv.id, parseInt(data.uploadId)));
    if (!upload) return NextResponse.json({ error: "Not found" }, { status: 404 });
    await db.delete(sales).where(eq(sales.uploadTs, upload.uploadTs));
    await db.delete(uploadedCsv).where(eq(uploadedCsv.id, parseInt(data.uploadId)));
    return NextResponse.json({ success: true, message: `Deleted "${upload.filename}" and its sales data.` });
  }

  if (action === "clear-invoices") {
    await db.delete(invoices);
    await db.delete(sales);
    await db.delete(uploadedCsv);
    return NextResponse.json({ success: true, message: "All invoices, sales data and uploaded CSV records cleared." });
  }

  return NextResponse.json({ error: "Unknown action" }, { status: 400 });
}

// VERSION: v2-$(date +%s)

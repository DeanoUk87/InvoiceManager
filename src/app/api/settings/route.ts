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

function safeJson(data: unknown) {
  return NextResponse.json(data);
}

// GET: settings or ?action=users list
export async function GET(req: Request) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  const { searchParams } = new URL(req.url);
  const action = searchParams.get("action");

  if (action === "users") {
    if (adminOnly(session)) return NextResponse.json({ error: "Forbidden" }, { status: 403 });
    const result = await db.select({
      id: users.id, name: users.name, email: users.email,
      role: users.role, username: users.username,
    }).from(users).orderBy(asc(users.createdAt));
    return safeJson(result);
  }

  if (action === "uploads") {
    const { uploadedCsv: uCsv } = await import("@/db/schema");
    const { desc } = await import("drizzle-orm");
    const uploads = await db.select().from(uCsv).orderBy(desc(uCsv.createdAt));
    return safeJson(uploads);
  }

  const [s] = await db.select().from(settings).limit(1);
  return safeJson(s ?? null);
}

// POST: create user (new user = POST)
export async function POST(req: Request) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
  if (adminOnly(session)) return NextResponse.json({ error: "Forbidden" }, { status: 403 });

  try {
    const data = await req.json();
    const { action, name, email, password, role, username } = data;

    if (action === "create-user") {
      if (!email || !password || !role) {
        return NextResponse.json({ error: "Email, password and role are required." }, { status: 400 });
      }
      const [existing] = await db.select({ id: users.id }).from(users).where(eq(users.email, email));
      if (existing) {
        return NextResponse.json({ error: "A user with this email already exists." }, { status: 409 });
      }
      const hashed = await bcrypt.hash(password, 8);
      const id = `user-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;
      await db.insert(users).values({
        id, name: name || null, email, password: hashed, role,
        username: username || null,
      });
      return safeJson({ success: true, id, email, role });
    }

    return NextResponse.json({ error: "Unknown action" }, { status: 400 });
  } catch (e) {
    console.error("POST /api/settings error:", e);
    return NextResponse.json({ error: String(e) }, { status: 500 });
  }
}

// PUT: update settings or update user
export async function PUT(req: Request) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  try {
    const data = await req.json();
    const action = data.action;

    if (action === "update-user") {
      if (adminOnly(session)) return NextResponse.json({ error: "Forbidden" }, { status: 403 });
      const { userId, name, email, role, username, password } = data;
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      const updateData: any = { name: name || null, email, role, username: username || null };
      if (password && password.trim()) {
        updateData.password = await bcrypt.hash(password, 8);
      }
      await db.update(users).set(updateData).where(eq(users.id, userId));
      return safeJson({ success: true, id: userId, email, role });
    }

    // Default: update settings (no action = settings save)
    if (adminOnly(session)) return NextResponse.json({ error: "Forbidden" }, { status: 403 });

    // Strip out action field before saving to settings
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const { action: _action, ...settingsData } = data;

    const [existing] = await db.select({ id: settings.id }).from(settings).limit(1);
    if (existing) {
      await db.update(settings).set(settingsData).where(eq(settings.id, existing.id));
    } else {
      await db.insert(settings).values(settingsData);
    }
    // Fetch and return the saved settings
    const [saved] = await db.select().from(settings).limit(1);
    return safeJson(saved ?? { success: true });
  } catch (e) {
    console.error("PUT /api/settings error:", e);
    return NextResponse.json({ error: String(e) }, { status: 500 });
  }
}

// DELETE: delete user, delete upload, or clear all invoice data
export async function DELETE(req: Request) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
  if (adminOnly(session)) return NextResponse.json({ error: "Forbidden" }, { status: 403 });

  try {
    const data = await req.json();
    const action = data.action;

    if (action === "delete-user") {
      if (data.userId === session.user.id) {
        return NextResponse.json({ error: "Cannot delete your own account." }, { status: 400 });
      }
      await db.delete(users).where(eq(users.id, data.userId));
      return safeJson({ success: true });
    }

    if (action === "delete-upload") {
      const [upload] = await db.select().from(uploadedCsv).where(eq(uploadedCsv.id, parseInt(data.uploadId)));
      if (!upload) return NextResponse.json({ error: "Not found" }, { status: 404 });
      await db.delete(sales).where(eq(sales.uploadTs, upload.uploadTs));
      await db.delete(uploadedCsv).where(eq(uploadedCsv.id, parseInt(data.uploadId)));
      return safeJson({ success: true, message: `Deleted "${upload.filename}" and its sales data.` });
    }

    if (action === "clear-invoices") {
      await db.delete(invoices);
      await db.delete(sales);
      await db.delete(uploadedCsv);
      return safeJson({ success: true, message: "All invoices, sales data and uploaded CSV records cleared." });
    }

    return NextResponse.json({ error: "Unknown action" }, { status: 400 });
  } catch (e) {
    console.error("DELETE /api/settings error:", e);
    return NextResponse.json({ error: String(e) }, { status: 500 });
  }
}

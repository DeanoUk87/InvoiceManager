import { NextResponse } from "next/server";
import { db } from "@/db";
import { users, customers } from "@/db/schema";
import { auth } from "@/lib/auth";
import { eq } from "drizzle-orm";
import bcrypt from "bcryptjs";

// GET: check if customer has a portal login
export async function GET(req: Request) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  const { searchParams } = new URL(req.url);
  const customerAccount = searchParams.get("account");
  if (!customerAccount) return NextResponse.json({ error: "account required" }, { status: 400 });

  const [user] = await db.select({
    id: users.id, email: users.email, name: users.name, username: users.username,
  }).from(users).where(eq(users.username, customerAccount));

  return NextResponse.json({ hasLogin: !!user, user: user ?? null });
}

// POST: create or update customer portal login
export async function POST(req: Request) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  const { customerAccount, email, password, action } = await req.json();
  if (!customerAccount) return NextResponse.json({ error: "customerAccount required" }, { status: 400 });

  // Get customer details
  const [customer] = await db.select().from(customers).where(eq(customers.customerAccount, customerAccount));
  if (!customer) return NextResponse.json({ error: "Customer not found" }, { status: 404 });

  if (action === "disable") {
    // Remove portal user and disable login access
    await db.delete(users).where(eq(users.username, customerAccount));
    await db.update(customers).set({ loginAccess: false }).where(eq(customers.customerAccount, customerAccount));
    return NextResponse.json({ success: true, message: "Login access disabled." });
  }

  if (!email || !password) return NextResponse.json({ error: "Email and password required." }, { status: 400 });

  const hashed = await bcrypt.hash(password, 10);
  const [existing] = await db.select({ id: users.id }).from(users).where(eq(users.username, customerAccount));

  if (existing) {
    // Update existing portal user
    await db.update(users).set({ email, password: hashed, name: customer.customerAccount })
      .where(eq(users.id, existing.id));
  } else {
    // Create new portal user
    const id = `cust-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;
    await db.insert(users).values({
      id,
      email,
      password: hashed,
      name: customerAccount,
      role: "customer",
      username: customerAccount,
    });
  }

  // Enable login access on customer record
  await db.update(customers).set({ loginAccess: true }).where(eq(customers.customerAccount, customerAccount));

  return NextResponse.json({ success: true, message: `Portal login ${existing ? "updated" : "created"} for ${customerAccount}.` });
}

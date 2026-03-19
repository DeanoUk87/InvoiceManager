import { NextResponse } from "next/server";
import { db } from "@/db";
import { users, settings } from "@/db/schema";
import bcrypt from "bcryptjs";
import { eq } from "drizzle-orm";

export async function GET() {
  // Create admin user if not exists
  const [existing] = await db.select().from(users).where(eq(users.email, "admin@invoicemanager.com"));
  if (!existing) {
    const hashed = await bcrypt.hash("admin123", 10);
    await db.insert(users).values({
      id: "admin-001",
      email: "admin@invoicemanager.com",
      name: "Admin",
      password: hashed,
      role: "admin",
      username: "admin",
    });
  }

  // Create default settings if not exists
  const [existingSettings] = await db.select().from(settings).limit(1);
  if (!existingSettings) {
    await db.insert(settings).values({
      companyName: "Your Company Ltd",
      companyAddress1: "123 Business Street",
      city: "Your City",
      postcode: "AB1 2CD",
      country: "United Kingdom",
      phone: "01234 567890",
      cemail: "invoices@yourcompany.com",
      vatNumber: "000000000",
      invoiceDueDate: 30,
      messageTitle: "Invoice #{invoice_number}",
      defaultMessage: "Please find your invoice attached.",
      defaultMessage2: "Dear Customer,\n\nPlease find attached invoice #{invoice_number}.\n\nKind regards",
      sendLimit: 50,
      fuelSurchargePercent: 3.5,
      resourcingSurchargePercent: 0,
      vatPercent: 20,
    });
  }

  return NextResponse.json({ success: true, message: "Seed complete. Login: admin@invoicemanager.com / admin123" });
}

import { PrismaClient } from "@prisma/client";
import bcrypt from "bcryptjs";

const prisma = new PrismaClient();

async function main() {
  // Create admin user
  const hashedPassword = await bcrypt.hash("admin123", 10);
  await prisma.user.upsert({
    where: { email: "admin@invoicemanager.com" },
    update: {},
    create: {
      email: "admin@invoicemanager.com",
      name: "Admin",
      password: hashedPassword,
      role: "admin",
      username: "admin",
    },
  });

  // Create default settings
  const existing = await prisma.settings.findFirst();
  if (!existing) {
    await prisma.settings.create({
      data: {
        companyName: "Your Company Ltd",
        companyAddress1: "123 Business Street",
        companyAddress2: "",
        city: "Your City",
        postcode: "AB1 2CD",
        country: "United Kingdom",
        phone: "01234 567890",
        cemail: "invoices@yourcompany.com",
        vatNumber: "000000000",
        invoiceDueDate: 30,
        messageTitle: "Invoice #{invoice_number}",
        defaultMessage: "Please find your invoice attached.",
        defaultMessage2:
          "Dear Customer,\n\nPlease find attached your invoice #{invoice_number}.\n\nKind regards",
        sendLimit: 50,
        fuelSurchargePercent: 3.5,
        resourcingSurchargePercent: 0,
        vatPercent: 20,
      },
    });
  }

  console.log("Seed completed. Admin login: admin@invoicemanager.com / admin123");
}

main()
  .catch(console.error)
  .finally(() => prisma.$disconnect());

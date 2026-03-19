import { NextResponse } from "next/server";
import { db } from "@/db";
import { invoices, customers, sales, settings } from "@/db/schema";
import { auth } from "@/lib/auth";
import { eq, and, inArray } from "drizzle-orm";
import nodemailer from "nodemailer";

export async function POST() {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  const [sett] = await db.select().from(settings).limit(1);
  if (!sett?.cemail) {
    return NextResponse.json({ error: "Email settings not configured." }, { status: 400 });
  }

  const pendingInvoices = await db.select().from(invoices)
    .where(and(eq(invoices.emailStatus, 0), inArray(invoices.printer, [0, 1])))
    .limit(sett.sendLimit ?? 50);

  if (pendingInvoices.length === 0) {
    return NextResponse.json({ success: false, message: "No pending invoices to send." });
  }

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const transporter = (nodemailer as any).createTransport({
    host: process.env.SMTP_HOST ?? "smtp.gmail.com",
    port: parseInt(process.env.SMTP_PORT ?? "587"),
    secure: false,
    auth: { user: process.env.SMTP_USER ?? sett.cemail ?? "", pass: process.env.SMTP_PASS ?? "" },
  });

  const results: { invoiceNumber: string; status: string; email?: string }[] = [];

  for (const invoice of pendingInvoices) {
    const [customer] = await db.select().from(customers).where(eq(customers.customerAccount, invoice.customerAccount));

    if (!customer?.customerEmail) {
      results.push({ invoiceNumber: invoice.invoiceNumber, status: "no_email" });
      continue;
    }

    const conds = [eq(sales.customerAccount, invoice.customerAccount), eq(sales.invoiceNumber, invoice.invoiceNumber)];
    if (invoice.invoiceDate) conds.push(eq(sales.invoiceDate, invoice.invoiceDate));
    const saleRows = await db.select().from(sales).where(and(...conds));

    const subTotal = saleRows.reduce((s, r) => s + (r.subTotal ?? 0), 0);
    const fuelSurcharge = subTotal * ((sett.fuelSurchargePercent ?? 3.5) / 100);
    const netTotal = subTotal + fuelSurcharge;
    const vatAmount = netTotal * ((sett.vatPercent ?? 20) / 100);
    const total = netTotal + vatAmount;

    const lineItems = saleRows.map(s => `<tr><td>${s.jobDate??''}</td><td>${s.jobNumber??''}</td><td>${s.postcode2??''}</td><td>${s.destination??''}</td><td>${s.serviceType??''}</td><td>${s.items2??''}</td><td>${s.volumeWeight??''}</td><td>£${(s.subTotal??0).toFixed(2)}</td></tr>`).join('');
    const subject = (sett.messageTitle ?? 'Invoice {invoice_number}').replace('{invoice_number}', invoice.invoiceNumber);
    const body = (sett.defaultMessage2 ?? 'Please find your invoice {invoice_number}.').replace('{invoice_number}', invoice.invoiceNumber);
    const html = `<div style="font-family:Arial,sans-serif"><h2>${sett.companyName}</h2><p>${body.replace(/\n/g,'<br>')}</p><table border="1" style="border-collapse:collapse;width:100%"><thead><tr><th>Job Date</th><th>Job No.</th><th>Postcode</th><th>Destination</th><th>Service</th><th>Items</th><th>Weight</th><th>Charge</th></tr></thead><tbody>${lineItems}</tbody></table><p><strong>Total: £${total.toFixed(2)}</strong></p></div>`;

    try {
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      const mailOptions: any = { from: `"${sett.companyName}" <${sett.cemail}>`, to: customer.customerEmail, subject, html };
      if (customer.customerEmailBcc) mailOptions.bcc = customer.customerEmailBcc;
      if (process.env.SMTP_PASS) await transporter.sendMail(mailOptions);

      await db.update(invoices).set({ printer: 2, emailStatus: 1 }).where(eq(invoices.id, invoice.id));
      await db.update(sales).set({ msCreated: 1 }).where(and(eq(sales.customerAccount, invoice.customerAccount), eq(sales.invoiceNumber, invoice.invoiceNumber)));
      results.push({ invoiceNumber: invoice.invoiceNumber, status: "sent", email: customer.customerEmail });
    } catch {
      results.push({ invoiceNumber: invoice.invoiceNumber, status: "error" });
    }
  }

  const sent = results.filter(r => r.status === "sent").length;
  const noEmail = results.filter(r => r.status === "no_email").length;
  return NextResponse.json({ success: true, sent, noEmail, total: results.length, results, message: `Processed ${results.length} invoices: ${sent} sent, ${noEmail} moved to unprinted.` });
}

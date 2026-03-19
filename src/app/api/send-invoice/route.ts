import { NextResponse } from "next/server";
import { db } from "@/db";
import { invoices, sales, settings } from "@/db/schema";
import { auth } from "@/lib/auth";
import { eq, and } from "drizzle-orm";
import nodemailer from "nodemailer";

export async function POST(req: Request) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  const { invoiceId, email, bcc } = await req.json();
  if (!invoiceId || !email) return NextResponse.json({ error: "Invoice ID and email required" }, { status: 400 });

  const [invoice] = await db.select().from(invoices).where(eq(invoices.id, parseInt(invoiceId)));
  if (!invoice) return NextResponse.json({ error: "Invoice not found" }, { status: 404 });

  const [sett] = await db.select().from(settings).limit(1);
  const conds = [eq(sales.customerAccount, invoice.customerAccount), eq(sales.invoiceNumber, invoice.invoiceNumber)];
  if (invoice.invoiceDate) conds.push(eq(sales.invoiceDate, invoice.invoiceDate));
  const saleRows = await db.select().from(sales).where(and(...conds));

  // Use values directly from CSV - already calculated by source system
  const subTotal = saleRows.reduce((s, r) => s + (r.subTotal ?? 0), 0);
  const fuelSurchargeAmount = saleRows.reduce((s, r) => s + (r.percentageFuelSurcharge ?? 0), 0);
  const resourcingSurchargeAmount = saleRows.reduce((s, r) => s + (r.percentageResourcingSurcharge ?? 0), 0);
  const vatAmount = saleRows.reduce((s, r) => s + (r.vatAmount ?? 0), 0);
  const netTotal = subTotal + fuelSurchargeAmount + resourcingSurchargeAmount;
  const total = saleRows.reduce((s, r) => s + (r.invoiceTotal ?? 0), 0);
  const fuelPct = sett?.fuelSurchargePercent ?? 3.5;
  const resourcingPct = sett?.resourcingSurchargePercent ?? 0;
  const vatPct = (saleRows[0]?.vatPercent) ?? (sett?.vatPercent) ?? 20;

  const lineItems = saleRows.map(s => `<tr><td>${s.jobDate??''}</td><td>${s.jobNumber??''}</td><td>${s.senderReference??''}</td><td>${s.postcode2??''}</td><td>${s.destination??''}</td><td>${s.serviceType??''}</td><td>${s.items2??''}</td><td>${s.volumeWeight??''}</td><td>£${(s.subTotal??0).toFixed(2)}</td></tr>`).join('');
  const subject = (sett?.messageTitle ?? 'Invoice {invoice_number}').replace('{invoice_number}', invoice.invoiceNumber);
  const body = (sett?.defaultMessage2 ?? 'Please find your invoice {invoice_number}.').replace('{invoice_number}', invoice.invoiceNumber);
  const html = `<div style="font-family:Arial,sans-serif"><h2>${sett?.companyName??''}</h2><p>${body.replace(/\n/g,'<br>')}</p><table border="1" style="border-collapse:collapse;width:100%"><thead><tr><th>Job Date</th><th>Job No.</th><th>Senders Ref</th><th>Postcode</th><th>Destination</th><th>Service</th><th>Items</th><th>Weight</th><th>Charge</th></tr></thead><tbody>${lineItems}</tbody></table><br><strong>Sub Total: £${subTotal.toFixed(2)}</strong><br><strong>Fuel Surcharge ${fuelPct}%: £${fuelSurchargeAmount.toFixed(2)}</strong><br><strong>Net Total: £${netTotal.toFixed(2)}</strong><br><strong>VAT ${vatPct}%: £${vatAmount.toFixed(2)}</strong><br><strong style="color:#2563eb;font-size:16px">TOTAL: £${total.toFixed(2)}</strong></div>`;

  if (process.env.SMTP_PASS) {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const transporter = (nodemailer as any).createTransport({
      host: process.env.SMTP_HOST ?? "smtp.gmail.com",
      port: parseInt(process.env.SMTP_PORT ?? "587"),
      secure: false,
      auth: { user: process.env.SMTP_USER ?? sett?.cemail ?? "", pass: process.env.SMTP_PASS },
    });
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const mailOptions: any = { from: `"${sett?.companyName}" <${sett?.cemail}>`, to: email, subject, html };
    if (bcc) mailOptions.bcc = bcc;
    await transporter.sendMail(mailOptions);
  }

  await db.update(invoices).set({ printer: 2, emailStatus: 1 }).where(eq(invoices.id, invoice.id));
  await db.update(sales).set({ msCreated: 1 }).where(and(eq(sales.customerAccount, invoice.customerAccount), eq(sales.invoiceNumber, invoice.invoiceNumber)));

  return NextResponse.json({ success: true, message: `Invoice sent to ${email}` });
}

import { NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";
import { auth } from "@/lib/auth";
import nodemailer from "nodemailer";

export async function POST(req: Request) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  const { invoiceId, email, bcc } = await req.json();
  if (!invoiceId || !email) {
    return NextResponse.json({ error: "Invoice ID and email are required" }, { status: 400 });
  }

  const invoice = await prisma.invoice.findUnique({ where: { id: parseInt(invoiceId) } });
  if (!invoice) return NextResponse.json({ error: "Invoice not found" }, { status: 404 });

  const [settings, sales] = await Promise.all([
    prisma.settings.findFirst(),
    prisma.sale.findMany({
      where: {
        customerAccount: invoice.customerAccount,
        invoiceNumber: invoice.invoiceNumber,
        invoiceDate: invoice.invoiceDate ?? undefined,
      },
    }),
  ]);

  const subTotal = sales.reduce((s, r) => s + (r.subTotal ?? 0), 0);
  const fuelSurcharge = subTotal * ((settings?.fuelSurchargePercent ?? 3.5) / 100);
  const resourcingSurcharge = subTotal * ((settings?.resourcingSurchargePercent ?? 0) / 100);
  const netTotal = subTotal + fuelSurcharge + resourcingSurcharge;
  const vatAmount = netTotal * ((settings?.vatPercent ?? 20) / 100);
  const total = netTotal + vatAmount;

  const lineItems = sales.map((s) =>
    `<tr>
      <td style="padding:6px 8px;border-bottom:1px solid #eee">${s.jobDate ?? ""}</td>
      <td style="padding:6px 8px;border-bottom:1px solid #eee">${s.jobNumber ?? ""}</td>
      <td style="padding:6px 8px;border-bottom:1px solid #eee">${s.senderReference ?? ""}</td>
      <td style="padding:6px 8px;border-bottom:1px solid #eee">${s.postcode2 ?? ""}</td>
      <td style="padding:6px 8px;border-bottom:1px solid #eee">${s.destination ?? ""}</td>
      <td style="padding:6px 8px;border-bottom:1px solid #eee">${s.serviceType ?? ""}</td>
      <td style="padding:6px 8px;border-bottom:1px solid #eee;text-align:right">${s.items2 ?? ""}</td>
      <td style="padding:6px 8px;border-bottom:1px solid #eee;text-align:right">${s.volumeWeight ?? ""}</td>
      <td style="padding:6px 8px;border-bottom:1px solid #eee;text-align:right">£${(s.subTotal ?? 0).toFixed(2)}</td>
    </tr>`
  ).join("");

  const subject = (settings?.messageTitle ?? "Invoice #{invoice_number}").replace("{invoice_number}", invoice.invoiceNumber);
  const body = (settings?.defaultMessage2 ?? "Please find attached your invoice {invoice_number}.").replace("{invoice_number}", invoice.invoiceNumber);

  const html = `
    <div style="font-family:Arial,sans-serif;max-width:800px;margin:0 auto">
      <div style="background:#2563eb;padding:20px;text-align:center">
        <h2 style="color:white;margin:0">${settings?.companyName ?? "Invoice"}</h2>
      </div>
      <div style="padding:20px">
        <p>${body.replace(/\n/g, "<br>")}</p>
        <table style="width:100%;border-collapse:collapse;margin-top:20px">
          <thead>
            <tr style="background:#f3f4f6">
              <th style="padding:8px;text-align:left">Job Date</th>
              <th style="padding:8px;text-align:left">Job No.</th>
              <th style="padding:8px;text-align:left">Ref</th>
              <th style="padding:8px;text-align:left">Postcode</th>
              <th style="padding:8px;text-align:left">Destination</th>
              <th style="padding:8px;text-align:left">Service</th>
              <th style="padding:8px;text-align:right">Items</th>
              <th style="padding:8px;text-align:right">Weight</th>
              <th style="padding:8px;text-align:right">Charge</th>
            </tr>
          </thead>
          <tbody>${lineItems}</tbody>
        </table>
        <table style="width:300px;margin-left:auto;margin-top:16px">
          <tr><td>Sub Total:</td><td style="text-align:right">£${subTotal.toFixed(2)}</td></tr>
          <tr><td>Fuel Surcharge ${settings?.fuelSurchargePercent ?? 3.5}%:</td><td style="text-align:right">£${fuelSurcharge.toFixed(2)}</td></tr>
          <tr><td>Resourcing Surcharge ${settings?.resourcingSurchargePercent ?? 0}%:</td><td style="text-align:right">£${resourcingSurcharge.toFixed(2)}</td></tr>
          <tr><td>Net Total:</td><td style="text-align:right">£${netTotal.toFixed(2)}</td></tr>
          <tr><td>VAT ${settings?.vatPercent ?? 20}%:</td><td style="text-align:right">£${vatAmount.toFixed(2)}</td></tr>
          <tr style="font-weight:bold;color:#2563eb">
            <td>TOTAL:</td><td style="text-align:right">£${total.toFixed(2)}</td>
          </tr>
        </table>
      </div>
    </div>
  `;

  if (process.env.SMTP_PASS) {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const transporter = (nodemailer as any).createTransport({
      host: process.env.SMTP_HOST ?? "smtp.gmail.com",
      port: parseInt(process.env.SMTP_PORT ?? "587"),
      secure: false,
      auth: {
        user: process.env.SMTP_USER ?? settings?.cemail ?? "",
        pass: process.env.SMTP_PASS,
      },
    });

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const mailOptions: any = {
      from: `"${settings?.companyName}" <${settings?.cemail}>`,
      to: email,
      subject,
      html,
    };
    if (bcc) mailOptions.bcc = bcc;

    await transporter.sendMail(mailOptions);
  }

  // Mark as printed / sent
  await prisma.invoice.update({
    where: { id: invoice.id },
    data: { printer: 2, emailStatus: 1 },
  });

  await prisma.sale.updateMany({
    where: {
      customerAccount: invoice.customerAccount,
      invoiceNumber: invoice.invoiceNumber,
    },
    data: { msCreated: 1 },
  });

  return NextResponse.json({ success: true, message: `Invoice sent to ${email}` });
}

import { NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";
import { auth } from "@/lib/auth";
import nodemailer from "nodemailer";

export async function POST() {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  const settings = await prisma.settings.findFirst();
  if (!settings?.cemail) {
    return NextResponse.json({ error: "Email settings not configured. Please set up your company email in Invoice Settings." }, { status: 400 });
  }

  // Get all unsent invoices where customer has an email
  const pendingInvoices = await prisma.invoice.findMany({
    where: { emailStatus: 0, printer: { in: [0, 1] } },
    take: settings.sendLimit ?? 50,
  });

  if (pendingInvoices.length === 0) {
    return NextResponse.json({ success: false, message: "No pending invoices to send." });
  }

  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  const transporter = (nodemailer as any).createTransport({
    host: process.env.SMTP_HOST ?? "smtp.gmail.com",
    port: parseInt(process.env.SMTP_PORT ?? "587"),
    secure: false,
    auth: {
      user: process.env.SMTP_USER ?? settings.cemail ?? "",
      pass: process.env.SMTP_PASS ?? "",
    },
  });

  const results: { invoiceNumber: string; status: string; email?: string }[] = [];

  for (const invoice of pendingInvoices) {
    const customer = await prisma.customer.findFirst({
      where: { customerAccount: invoice.customerAccount },
    });

    if (!customer?.customerEmail) {
      results.push({ invoiceNumber: invoice.invoiceNumber, status: "no_email" });
      continue;
    }

    const sales = await prisma.sale.findMany({
      where: {
        customerAccount: invoice.customerAccount,
        invoiceNumber: invoice.invoiceNumber,
        invoiceDate: invoice.invoiceDate ?? undefined,
      },
    });

    const subTotal = sales.reduce((s, r) => s + (r.subTotal ?? 0), 0);
    const fuelSurcharge = subTotal * ((settings.fuelSurchargePercent ?? 3.5) / 100);
    const netTotal = subTotal + fuelSurcharge;
    const vatAmount = netTotal * ((settings.vatPercent ?? 20) / 100);
    const total = netTotal + vatAmount;

    const lineItems = sales
      .map(
        (s) =>
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
      )
      .join("");

    const subject = (settings.messageTitle ?? "Invoice {invoice_number}").replace(
      "{invoice_number}",
      invoice.invoiceNumber
    );
    const body = (settings.defaultMessage2 ?? "Please find attached your invoice {invoice_number}.").replace(
      "{invoice_number}",
      invoice.invoiceNumber
    );

    const html = `
      <div style="font-family:Arial,sans-serif;max-width:800px;margin:0 auto">
        <div style="background:#2563eb;padding:20px;text-align:center">
          <h2 style="color:white;margin:0">${settings.companyName ?? "Invoice"}</h2>
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
            <tr><td>Fuel Surcharge ${settings.fuelSurchargePercent}%:</td><td style="text-align:right">£${fuelSurcharge.toFixed(2)}</td></tr>
            <tr><td>Net Total:</td><td style="text-align:right">£${netTotal.toFixed(2)}</td></tr>
            <tr><td>VAT ${settings.vatPercent}%:</td><td style="text-align:right">£${vatAmount.toFixed(2)}</td></tr>
            <tr style="font-weight:bold;font-size:1.1em;color:#2563eb">
              <td>TOTAL:</td><td style="text-align:right">£${total.toFixed(2)}</td>
            </tr>
          </table>
        </div>
        <div style="background:#f9fafb;padding:16px;text-align:center;font-size:12px;color:#6b7280">
          ${settings.companyName} | ${settings.phone ?? ""} | ${settings.cemail}
        </div>
      </div>
    `;

    try {
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      const mailOptions: any = {
        from: `"${settings.companyName}" <${settings.cemail}>`,
        to: customer.customerEmail,
        subject,
        html,
      };

      if (customer.customerEmailBcc) {
        mailOptions.bcc = customer.customerEmailBcc;
      }

      if (process.env.SMTP_PASS) {
        await transporter.sendMail(mailOptions);
      }

      // Mark invoice as sent
      await prisma.invoice.update({
        where: { id: invoice.id },
        data: { printer: 2, emailStatus: 1 },
      });

      // Mark sales as processed
      await prisma.sale.updateMany({
        where: {
          customerAccount: invoice.customerAccount,
          invoiceNumber: invoice.invoiceNumber,
          invoiceDate: invoice.invoiceDate ?? undefined,
        },
        data: { msCreated: 1 },
      });

      results.push({ invoiceNumber: invoice.invoiceNumber, status: "sent", email: customer.customerEmail });
    } catch {
      results.push({ invoiceNumber: invoice.invoiceNumber, status: "error" });
    }
  }

  const sent = results.filter((r) => r.status === "sent").length;
  const noEmail = results.filter((r) => r.status === "no_email").length;

  return NextResponse.json({
    success: true,
    sent,
    noEmail,
    total: results.length,
    results,
    message: `Processed ${results.length} invoices: ${sent} sent, ${noEmail} moved to unprinted (no email).`,
  });
}

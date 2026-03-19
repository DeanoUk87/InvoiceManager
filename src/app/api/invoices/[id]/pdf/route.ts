import { NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";
import { auth } from "@/lib/auth";

export async function GET(_req: Request, { params }: { params: Promise<{ id: string }> }) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  const { id } = await params;
  const invoice = await prisma.invoice.findUnique({ where: { id: parseInt(id) } });
  if (!invoice) return NextResponse.json({ error: "Not found" }, { status: 404 });

  const [sales, settings] = await Promise.all([
    prisma.sale.findMany({
      where: {
        customerAccount: invoice.customerAccount,
        invoiceNumber: invoice.invoiceNumber,
        invoiceDate: invoice.invoiceDate ?? undefined,
      },
    }),
    prisma.settings.findFirst(),
  ]);

  const subTotal = sales.reduce((s, r) => s + (r.subTotal ?? 0), 0);
  const fuelSurcharge = subTotal * ((settings?.fuelSurchargePercent ?? 3.5) / 100);
  const resourcingSurcharge = subTotal * ((settings?.resourcingSurchargePercent ?? 0) / 100);
  const netTotal = subTotal + fuelSurcharge + resourcingSurcharge;
  const vatAmount = netTotal * ((settings?.vatPercent ?? 20) / 100);
  const total = netTotal + vatAmount;

  const lineItems = sales.map((s) =>
    `<tr>
      <td>${s.jobDate ?? ""}</td>
      <td>${s.jobNumber ?? ""}</td>
      <td>${s.senderReference ?? ""}</td>
      <td>${s.postcode2 ?? ""}</td>
      <td>${s.destination ?? ""}</td>
      <td>${s.serviceType ?? ""}</td>
      <td style="text-align:right">${s.items2 ?? ""}</td>
      <td style="text-align:right">${s.volumeWeight ?? ""}</td>
      <td style="text-align:right">£${(s.subTotal ?? 0).toFixed(2)}</td>
    </tr>`
  ).join("");

  const html = `<!DOCTYPE html><html><head><meta charset="utf-8">
<style>
  body{font-family:Arial,sans-serif;font-size:11px;color:#333;margin:20px}
  .header{display:flex;justify-content:space-between;margin-bottom:20px}
  .logo{background:#2563eb;color:white;padding:10px 20px;border-radius:8px;font-size:18px;font-weight:bold}
  .company-info{text-align:right}
  table{width:100%;border-collapse:collapse;margin:16px 0}
  th{background:#f3f4f6;padding:6px 8px;text-align:left;border:1px solid #e5e7eb}
  td{padding:5px 8px;border:1px solid #e5e7eb}
  .totals{width:320px;margin-left:auto}
  .totals td{padding:4px 8px}
  .total-row{font-weight:bold;color:#2563eb;font-size:13px}
  h2{margin:0}
</style>
</head><body>
<div class="header">
  <div class="logo">${settings?.companyName ?? "Invoice"}</div>
  <div class="company-info">
    <strong>${settings?.companyName}</strong><br>
    ${settings?.companyAddress1 ?? ""}<br>
    ${settings?.city ?? ""} ${settings?.postcode ?? ""}<br>
    TEL: ${settings?.phone ?? ""}<br>
    VAT: ${settings?.vatNumber ?? ""}
  </div>
</div>
<hr>
<div style="display:flex;justify-content:space-between;margin:16px 0">
  <div><strong>${invoice.customerAccount}</strong></div>
  <div style="text-align:right">
    <strong>ACCOUNT:</strong> ${invoice.customerAccount}<br>
    <strong>INVOICE NO:</strong> ${invoice.invoiceNumber}<br>
    <strong>INVOICE DATE:</strong> ${invoice.invoiceDate ?? ""}<br>
  </div>
</div>
<table>
  <thead><tr>
    <th>JOB DATE</th><th>JOB NUMBER</th><th>SENDERS REF</th>
    <th>POSTCODE</th><th>DESTINATION</th><th>SERVICE TYPE</th>
    <th>ITEMS</th><th>WEIGHT</th><th>CHARGE</th>
  </tr></thead>
  <tbody>${lineItems}</tbody>
</table>
<table class="totals">
  <tr><td>SUB TOTAL:</td><td style="text-align:right">£${subTotal.toFixed(2)}</td></tr>
  <tr><td>FUEL SURCHARGE ${settings?.fuelSurchargePercent ?? 3.5}%:</td><td style="text-align:right">£${fuelSurcharge.toFixed(2)}</td></tr>
  <tr><td>RESOURCING SURCHARGE ${settings?.resourcingSurchargePercent ?? 0}%:</td><td style="text-align:right">£${resourcingSurcharge.toFixed(2)}</td></tr>
  <tr><td>NET TOTAL:</td><td style="text-align:right">£${netTotal.toFixed(2)}</td></tr>
  <tr><td>VAT ${settings?.vatPercent ?? 20}%:</td><td style="text-align:right">£${vatAmount.toFixed(2)}</td></tr>
  <tr class="total-row"><td>TOTAL:</td><td style="text-align:right">£${total.toFixed(2)}</td></tr>
</table>
</body></html>`;

  // Return as HTML for browser printing (client-side PDF generation via print dialog)
  return new Response(html, {
    headers: {
      "Content-Type": "text/html",
      "X-Invoice-Number": invoice.invoiceNumber,
    },
  });
}

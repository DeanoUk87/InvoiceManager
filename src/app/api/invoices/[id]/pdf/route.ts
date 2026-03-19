import { NextResponse } from "next/server";
import { db } from "@/db";
import { invoices, sales, settings } from "@/db/schema";
import { auth } from "@/lib/auth";
import { eq, and } from "drizzle-orm";

export async function GET(_req: Request, { params }: { params: Promise<{ id: string }> }) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
  const { id } = await params;
  const [invoice] = await db.select().from(invoices).where(eq(invoices.id, parseInt(id)));
  if (!invoice) return NextResponse.json({ error: "Not found" }, { status: 404 });

  const conditions = [eq(sales.customerAccount, invoice.customerAccount), eq(sales.invoiceNumber, invoice.invoiceNumber)];
  if (invoice.invoiceDate) conditions.push(eq(sales.invoiceDate, invoice.invoiceDate));
  const [saleRows, [sett]] = await Promise.all([
    db.select().from(sales).where(and(...conditions)),
    db.select().from(settings).limit(1),
  ]);

  const subTotal = saleRows.reduce((s, r) => s + (r.subTotal ?? 0), 0);
  const fuelSurcharge = subTotal * ((sett?.fuelSurchargePercent ?? 3.5) / 100);
  const resourcingSurcharge = subTotal * ((sett?.resourcingSurchargePercent ?? 0) / 100);
  const netTotal = subTotal + fuelSurcharge + resourcingSurcharge;
  const vatAmount = netTotal * ((sett?.vatPercent ?? 20) / 100);
  const total = netTotal + vatAmount;

  const lineItems = saleRows.map(s => `<tr><td>${s.jobDate??''}</td><td>${s.jobNumber??''}</td><td>${s.senderReference??''}</td><td>${s.postcode2??''}</td><td>${s.destination??''}</td><td>${s.serviceType??''}</td><td align="right">${s.items2??''}</td><td align="right">${s.volumeWeight??''}</td><td align="right">£${(s.subTotal??0).toFixed(2)}</td></tr>`).join('');

  const html = `<!DOCTYPE html><html><head><meta charset="utf-8"><title>Invoice ${invoice.invoiceNumber}</title>
<style>body{font-family:Arial,sans-serif;font-size:11px;margin:20px}table{width:100%;border-collapse:collapse;margin:12px 0}th{background:#f3f4f6;padding:6px 8px;text-align:left;border:1px solid #e5e7eb}td{padding:5px 8px;border:1px solid #e5e7eb}.totals{width:300px;margin-left:auto}.hdr{display:flex;justify-content:space-between}.logo{background:#2563eb;color:#fff;padding:10px 16px;border-radius:6px;font-size:16px;font-weight:bold}.company{text-align:right}.grand{font-weight:bold;color:#2563eb}</style></head>
<body>
<div class="hdr"><div class="logo">${sett?.companyName??'Invoice'}</div><div class="company"><strong>${sett?.companyName??''}</strong><br>${sett?.companyAddress1??''}<br>${sett?.city??''} ${sett?.postcode??''}<br>TEL: ${sett?.phone??''}<br>VAT: ${sett?.vatNumber??''}</div></div>
<hr>
<div class="hdr"><div><strong>${invoice.customerAccount}</strong></div><div style="text-align:right"><strong>ACCOUNT:</strong> ${invoice.customerAccount}<br><strong>INVOICE NO:</strong> ${invoice.invoiceNumber}<br><strong>DATE:</strong> ${invoice.invoiceDate??''}</div></div>
<table><thead><tr><th>JOB DATE</th><th>JOB NO.</th><th>SENDERS REF</th><th>POSTCODE</th><th>DESTINATION</th><th>SERVICE</th><th>ITEMS</th><th>WEIGHT</th><th>CHARGE</th></tr></thead><tbody>${lineItems}</tbody></table>
<table class="totals"><tr><td>SUB TOTAL:</td><td align="right">£${subTotal.toFixed(2)}</td></tr><tr><td>FUEL ${sett?.fuelSurchargePercent??3.5}%:</td><td align="right">£${fuelSurcharge.toFixed(2)}</td></tr><tr><td>RESOURCING ${sett?.resourcingSurchargePercent??0}%:</td><td align="right">£${resourcingSurcharge.toFixed(2)}</td></tr><tr><td>NET TOTAL:</td><td align="right">£${netTotal.toFixed(2)}</td></tr><tr><td>VAT ${sett?.vatPercent??20}%:</td><td align="right">£${vatAmount.toFixed(2)}</td></tr><tr class="grand"><td>TOTAL:</td><td align="right">£${total.toFixed(2)}</td></tr></table>
<script>window.onload=()=>window.print();</script>
</body></html>`;

  return new Response(html, { headers: { "Content-Type": "text/html" } });
}

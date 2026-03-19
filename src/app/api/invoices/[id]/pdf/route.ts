import { NextResponse } from "next/server";
import { db } from "@/db";
import { invoices, sales, settings, customers } from "@/db/schema";
import { auth } from "@/lib/auth";
import { eq, and } from "drizzle-orm";

function fmt(n: number) { return n.toFixed(2); }
function esc(s: string | null | undefined) {
  return (s ?? "").replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;");
}
/** Normalise YYYYMMDD / YYYY-MM-DD / DD/MM/YYYY → YYYY-MM-DD */
function toISO(raw: string | null | undefined): string | null {
  if (!raw) return null;
  const s = raw.trim();
  if (/^\d{4}-\d{2}-\d{2}$/.test(s)) return s;
  if (/^\d{8}$/.test(s)) return `${s.slice(0,4)}-${s.slice(4,6)}-${s.slice(6,8)}`;
  const dm = s.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
  if (dm) return `${dm[3]}-${dm[2].padStart(2,"0")}-${dm[1].padStart(2,"0")}`;
  return null;
}
/** Format any date as DD-MM-YYYY */
function fmtDate(d: string | null | undefined): string {
  const iso = toISO(d);
  if (!iso) return "";
  const m = iso.match(/^(\d{4})-(\d{2})-(\d{2})/);
  return m ? `${m[3]}-${m[2]}-${m[1]}` : "";
}
/** Calculate due date as DD-MM-YYYY */
function calcDue(invoiceDate: string | null | undefined, days: number | null | undefined): string {
  if (!invoiceDate || days == null) return "";
  const iso = toISO(invoiceDate);
  if (!iso) return "";
  const m = iso.match(/^(\d{4})-(\d{2})-(\d{2})/);
  if (!m) return "";
  const base = new Date(Date.UTC(parseInt(m[1]), parseInt(m[2]) - 1, parseInt(m[3])));
  if (isNaN(base.getTime())) return "";
  base.setUTCDate(base.getUTCDate() + Math.round(days));
  const d = base.getUTCDate().toString().padStart(2, "0");
  const mo = (base.getUTCMonth() + 1).toString().padStart(2, "0");
  return `${d}-${mo}-${base.getUTCFullYear()}`;
}

export async function GET(_req: Request, { params }: { params: Promise<{ id: string }> }) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
  const { id } = await params;

  const [invoice] = await db.select().from(invoices).where(eq(invoices.id, parseInt(id)));
  if (!invoice) return NextResponse.json({ error: "Not found" }, { status: 404 });

  const conditions = [
    eq(sales.customerAccount, invoice.customerAccount),
    eq(sales.invoiceNumber, invoice.invoiceNumber),
  ];
  if (invoice.invoiceDate) conditions.push(eq(sales.invoiceDate, invoice.invoiceDate));

  const [saleRows, [sett], [customer]] = await Promise.all([
    db.select().from(sales).where(and(...conditions)).orderBy(sales.id),
    db.select().from(settings).limit(1),
    db.select().from(customers).where(eq(customers.customerAccount, invoice.customerAccount)),
  ]);

  // Totals from CSV source values
  const subTotal = saleRows.reduce((s, r) => s + (r.subTotal ?? 0), 0);
  const fuelPct = saleRows[0]?.percentageFuelSurcharge ?? sett?.fuelSurchargePercent ?? 3.5;
  const resourcingPct = sett?.resourcingSurchargePercent ?? 0;
  const fuelSurchargeAmount = subTotal * (fuelPct / 100);
  const resourcingSurchargeAmount = subTotal * (resourcingPct / 100);
  const netTotal = subTotal + fuelSurchargeAmount + resourcingSurchargeAmount;
  const vatPct = saleRows[0]?.vatPercent ?? sett?.vatPercent ?? 20;
  const vatAmount = saleRows[0]?.vatAmount ?? 0; // invoice-level figure, same on every line
  const total = saleRows[0]?.invoiceTotal ?? (netTotal + vatAmount);

  // Customer address from first sales row (comes from CSV cols 3-8)
  const first = saleRows[0];
  const custName = esc(first?.customerName);
  // Only include addr1 if it's different from customerName (sometimes CSV repeats it)
  const addr1Raw = first?.address1 && first.address1 !== first.customerName ? esc(first.address1) : "";
  const addr2 = esc(first?.address2);
  const town = esc(first?.town);
  const country = esc(first?.country);
  const postcode = esc(first?.postcode);

  // Build address lines (skip empty)
  const addrLines = [addr1Raw, addr2, town, country, postcode]
    .filter(Boolean)
    .map(l => `<div>${l}</div>`)
    .join("");

  // Company details
  const companyName = esc(sett?.companyName ?? "Invoice Manager");
  const compAddr1 = esc(sett?.companyAddress1 ?? "");
  const compAddr2 = esc(sett?.companyAddress2 ?? "");
  const compCity = esc(sett?.city ?? "");
  const compPostcode = esc(sett?.postcode ?? "");
  const compCountry = esc(sett?.country ?? "");
  const compPhone = esc(sett?.phone ?? "");
  const compEmail = esc(sett?.cemail ?? "");
  const vatNo = esc(sett?.vatNumber ?? "");

  // Use numb2 (days) from first sale row to calculate due date
  const numb2 = saleRows[0]?.numb2 ?? null;
  const dueDateStr = invoice.dueDate
    ? fmtDate(invoice.dueDate)
    : calcDue(invoice.invoiceDate, numb2);

  const lineItems = saleRows.map((s, i) => `
    <tr class="${i % 2 === 0 ? "row-even" : "row-odd"}">
      <td>${fmtDate(s.jobDate)}</td>
      <td>${esc(s.jobNumber)}</td>
      <td>${esc(s.senderReference)}</td>
      <td>${esc(s.postcode2)}</td>
      <td>${esc(s.destination)}</td>
      <td>${esc(s.serviceType)}</td>
      <td class="num">${s.items2 ?? ""}</td>
      <td class="num">${s.volumeWeight ?? ""}</td>
      <td class="num">£${fmt(s.subTotal ?? 0)}</td>
    </tr>`).join("");

  const html = `<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Invoice ${invoice.invoiceNumber}</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #1a1a2e; background: #fff; }
  .page { max-width: 800px; margin: 0 auto; padding: 32px 36px; }

  /* Header */
  .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 28px; }
  .logo-block { display: flex; align-items: center; gap: 12px; }
  .logo-icon { width: 48px; height: 48px; background: #2563eb; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; }
  .logo-icon svg { width: 28px; height: 28px; fill: white; }
  .logo-name { font-size: 20px; font-weight: 700; color: #2563eb; line-height: 1.1; }
  .logo-sub { font-size: 10px; color: #64748b; }
  .company-info { text-align: right; line-height: 1.6; color: #374151; }
  .company-info .company-name { font-size: 13px; font-weight: 700; color: #1e293b; }

  /* Title bar */
  .invoice-title-bar { background: #2563eb; color: white; padding: 10px 20px;
    border-radius: 8px; margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; }
  .invoice-title-bar .inv-label { font-size: 18px; font-weight: 700; letter-spacing: 1px; }
  .invoice-title-bar .inv-num { font-size: 14px; opacity: 0.9; }

  /* Bill to / Invoice details */
  .details-row { display: flex; justify-content: space-between; margin-bottom: 24px; gap: 16px; }
  .bill-to { flex: 1; }
  .inv-details { min-width: 220px; }
  .section-label { font-size: 9px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 1px; color: #64748b; margin-bottom: 6px; }
  .bill-to-name { font-size: 12px; font-weight: 700; color: #1e293b; margin-bottom: 2px; }
  .bill-to-addr { color: #374151; line-height: 1.7; }
  .inv-details table { width: 100%; }
  .inv-details td { padding: 3px 0; color: #374151; }
  .inv-details td:first-child { font-weight: 600; color: #1e293b; width: 120px; }
  .inv-details .due-date { color: #dc2626; font-weight: 700; }

  /* Line items table */
  .items-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 10.5px; }
  .items-table thead tr { background: #1e40af; color: white; }
  .items-table thead th { padding: 8px 10px; text-align: left; font-weight: 600;
    font-size: 9.5px; letter-spacing: 0.5px; text-transform: uppercase; }
  .items-table thead th.num { text-align: right; }
  .items-table tbody .row-even { background: #ffffff; }
  .items-table tbody .row-odd { background: #f8fafc; }
  .items-table tbody tr:hover { background: #eff6ff; }
  .items-table tbody td { padding: 6px 10px; border-bottom: 1px solid #e5e7eb; color: #374151; }
  .items-table tbody td.num { text-align: right; font-variant-numeric: tabular-nums; }
  .items-table tfoot td { padding: 6px 10px; }

  /* Totals */
  .totals-wrap { display: flex; justify-content: flex-end; margin-bottom: 28px; }
  .totals-table { width: 280px; border-collapse: collapse; }
  .totals-table tr td { padding: 5px 10px; font-size: 11px; }
  .totals-table tr td:last-child { text-align: right; font-variant-numeric: tabular-nums; }
  .totals-table .subtotal-row td { border-top: 1px solid #e5e7eb; color: #374151; }
  .totals-table .surcharge-row td { color: #64748b; font-size: 10.5px; }
  .totals-table .net-row td { border-top: 1px solid #cbd5e1; font-weight: 600; color: #1e293b; }
  .totals-table .vat-row td { color: #64748b; font-size: 10.5px; }
  .totals-table .grand-row td { border-top: 2px solid #2563eb; background: #eff6ff;
    font-size: 13px; font-weight: 700; color: #2563eb; padding: 8px 10px; border-radius: 0 0 6px 6px; }

  /* Messages */
  .msg-customer { font-size: 11px; color: #374151; line-height: 1.7; border-top: 1px solid #e5e7eb; padding-top: 14px; margin-top: 8px; }
  .msg-default { font-size: 10.5px; color: #6b7280; line-height: 1.7; border-top: 1px solid #e5e7eb; padding-top: 10px; margin-top: 8px; }

  .num { text-align: right; }

  @media print {
    body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
    .page { padding: 16px; }
    @page { margin: 0.5cm; size: A4; }
  }
</style>
</head>
<body>
<div class="page">

  <!-- Header -->
  <div class="header">
    <div class="logo-block">
      <div class="logo-icon">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/>
        </svg>
      </div>
      <div>
        <div class="logo-name">${companyName}</div>
        <div class="logo-sub">Invoice Management System</div>
      </div>
    </div>
    <div class="company-info">
      ${compAddr1 ? `<div>${compAddr1}</div>` : ""}
      ${compAddr2 ? `<div>${compAddr2}</div>` : ""}
      ${compCity || compPostcode ? `<div>${compCity}${compCity && compPostcode ? " " : ""}${compPostcode}</div>` : ""}
      ${compCountry ? `<div>${compCountry}</div>` : ""}
      ${compPhone ? `<div>Tel: ${compPhone}</div>` : ""}
      ${compEmail ? `<div>${compEmail}</div>` : ""}
      ${vatNo ? `<div>VAT Reg: ${vatNo}</div>` : ""}
    </div>
  </div>

  <!-- Invoice title bar -->
  <div class="invoice-title-bar">
    <div class="inv-label">INVOICE</div>
    <div class="inv-num"># ${esc(invoice.invoiceNumber)}</div>
  </div>

  <!-- Bill to + Invoice details -->
  <div class="details-row">
    <div class="bill-to">
      <div class="section-label">Bill To</div>
      ${custName ? `<div class="bill-to-name">${custName}</div>` : `<div class="bill-to-name">${esc(invoice.customerAccount)}</div>`}
      <div class="bill-to-addr">${addrLines}</div>
    </div>
    <div class="inv-details">
      <div class="section-label">Invoice Details</div>
      <table>
        <tr><td>Account:</td><td>${esc(invoice.customerAccount)}</td></tr>
        <tr><td>Invoice No:</td><td><strong>${esc(invoice.invoiceNumber)}</strong></td></tr>
        <tr><td>Invoice Date:</td><td>${fmtDate(invoice.invoiceDate)}</td></tr>
        <tr><td>Due Date:</td><td class="due-date">${dueDateStr}</td></tr>
        ${(customer?.poNumber || invoice.poNumber) ? `<tr><td>PO Number:</td><td>${esc(customer?.poNumber || invoice.poNumber)}</td></tr>` : ""}
      </table>
    </div>
  </div>

  <!-- Line items -->
  <table class="items-table">
    <thead>
      <tr>
        <th>Job Date</th>
        <th>Job No.</th>
        <th>Senders Ref</th>
        <th>Postcode</th>
        <th>Destination</th>
        <th>Service</th>
        <th class="num">Items</th>
        <th class="num">Weight</th>
        <th class="num">Charge</th>
      </tr>
    </thead>
    <tbody>${lineItems}</tbody>
  </table>

  <!-- Totals -->
  <div class="totals-wrap">
    <table class="totals-table">
      <tr class="subtotal-row"><td>Sub Total:</td><td>£${fmt(subTotal)}</td></tr>
      <tr class="surcharge-row"><td>Fuel Surcharge (${fuelPct}%):</td><td>£${fmt(fuelSurchargeAmount)}</td></tr>
      ${resourcingPct > 0 ? `<tr class="surcharge-row"><td>Resourcing Surcharge (${resourcingPct}%):</td><td>£${fmt(resourcingSurchargeAmount)}</td></tr>` : ""}
      <tr class="net-row"><td>Net Total:</td><td>£${fmt(netTotal)}</td></tr>
      <tr class="vat-row"><td>VAT (${vatPct}%):</td><td>£${fmt(vatAmount)}</td></tr>
      <tr class="grand-row"><td>TOTAL DUE:</td><td>£${fmt(total)}</td></tr>
    </table>
  </div>

  <!-- Messages section -->
  ${customer?.customerMessage ? `
  <div style="border-top:1px solid #e5e7eb;margin-top:16px;padding-top:14px;font-size:11px;color:#374151;line-height:1.7">
    ${customer.customerMessage}
  </div>` : ""}
  ${sett?.invoiceDefaultMessage ? `
  <div style="border-top:1px solid #e5e7eb;margin-top:10px;padding-top:10px;font-size:10.5px;color:#6b7280;line-height:1.7">
    ${sett.invoiceDefaultMessage}
  </div>` : ""}

</div>
<script>window.onload = () => window.print();</script>
</body>
</html>`;

  return new Response(html, { headers: { "Content-Type": "text/html" } });
}

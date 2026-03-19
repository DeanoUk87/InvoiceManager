import { auth } from "@/lib/auth";
import { db } from "@/db";
import { invoices, sales, settings, customers } from "@/db/schema";
import { eq, and } from "drizzle-orm";
import { formatDate, calcDueDate, formatCurrency } from "@/lib/utils";
import Link from "next/link";
import { redirect, notFound } from "next/navigation";
import { ArrowLeft, Download, FileText } from "lucide-react";

export const dynamic = "force-dynamic";

export default async function CustomerInvoiceDetailPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  const session = await auth();
  if (!session || session.user.role !== "customer") redirect("/login");

  const customerAccount = session.user.name ?? "";

  const [invoice] = await db.select().from(invoices).where(eq(invoices.id, parseInt(id)));
  if (!invoice) notFound();
  // Security: ensure this invoice belongs to this customer
  if (invoice.customerAccount !== customerAccount) redirect("/my-invoices");

  const conditions = [
    eq(sales.customerAccount, invoice.customerAccount),
    eq(sales.invoiceNumber, invoice.invoiceNumber),
  ];
  if (invoice.invoiceDate) conditions.push(eq(sales.invoiceDate, invoice.invoiceDate));

  const [saleRows, [sett], [customer]] = await Promise.all([
    db.select().from(sales).where(and(...conditions)).orderBy(sales.id),
    db.select().from(settings).limit(1),
    db.select().from(customers).where(eq(customers.customerAccount, customerAccount)),
  ]);

  const subTotal = saleRows.reduce((s, r) => s + (r.subTotal ?? 0), 0);
  const fuelPct = saleRows[0]?.percentageFuelSurcharge ?? sett?.fuelSurchargePercent ?? 3.5;
  const resourcingPct = sett?.resourcingSurchargePercent ?? 0;
  const fuelAmount = subTotal * (fuelPct / 100);
  const resourcingAmount = subTotal * (resourcingPct / 100);
  const netTotal = subTotal + fuelAmount + resourcingAmount;
  const vatPct = saleRows[0]?.vatPercent ?? 20;
  const vatAmount = saleRows[0]?.vatAmount ?? 0;
  const total = saleRows[0]?.invoiceTotal ?? (netTotal + vatAmount);
  const dueDate = invoice.dueDate ? formatDate(invoice.dueDate) : calcDueDate(invoice.invoiceDate, saleRows[0]?.numb2);

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between flex-wrap gap-3">
        <Link href="/my-invoices"
          className="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-blue-600 transition-colors">
          <ArrowLeft size={14} /> Back to My Invoices
        </Link>
        <div className="flex gap-2">
          <a href={`/api/invoices/${id}/pdf`} target="_blank"
            className="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
            <FileText size={14} /> Download PDF
          </a>
          <a href={`/api/invoices/${id}/excel`}
            className="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition-colors">
            <Download size={14} /> Download Excel
          </a>
        </div>
      </div>

      <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-8">
        {/* Company header */}
        <div className="flex justify-between items-start mb-6">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
              <FileText size={20} className="text-white" />
            </div>
            <div>
              <p className="font-bold text-blue-600">{sett?.companyName ?? "Invoice Manager"}</p>
              <p className="text-xs text-gray-400">Invoice Management System</p>
            </div>
          </div>
          <div className="text-right text-xs text-gray-600 leading-relaxed">
            {sett?.companyAddress1 && <p>{sett.companyAddress1}</p>}
            {sett?.city && <p>{sett.city}{sett.postcode ? ` ${sett.postcode}` : ""}</p>}
            {sett?.phone && <p>Tel: {sett.phone}</p>}
            {sett?.vatNumber && <p>VAT: {sett.vatNumber}</p>}
          </div>
        </div>

        {/* Blue title bar */}
        <div className="bg-blue-600 text-white rounded-lg px-5 py-3 flex justify-between items-center mb-6">
          <span className="text-lg font-bold tracking-wide">INVOICE</span>
          <span className="text-sm opacity-90">#{invoice.invoiceNumber}</span>
        </div>

        {/* Bill To + Details */}
        <div className="flex justify-between items-start mb-6 gap-6">
          <div className="flex-1">
            <p className="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2">Bill To</p>
            {saleRows[0]?.customerName && <p className="font-bold text-sm text-gray-900">{saleRows[0].customerName}</p>}
            <div className="text-xs text-gray-600 leading-relaxed mt-1">
              {saleRows[0]?.address1 && saleRows[0].address1 !== saleRows[0].customerName && <p>{saleRows[0].address1}</p>}
              {saleRows[0]?.address2 && <p>{saleRows[0].address2}</p>}
              {saleRows[0]?.town && <p>{saleRows[0].town}</p>}
              {saleRows[0]?.country && <p>{saleRows[0].country}</p>}
              {saleRows[0]?.postcode && <p>{saleRows[0].postcode}</p>}
            </div>
          </div>
          <div className="min-w-[200px]">
            <p className="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2">Invoice Details</p>
            <table className="text-xs w-full">
              <tbody>
                <tr><td className="font-semibold text-gray-700 py-0.5 pr-3">Account:</td><td>{invoice.customerAccount}</td></tr>
                <tr><td className="font-semibold text-gray-700 py-0.5 pr-3">Invoice No:</td><td className="font-bold">{invoice.invoiceNumber}</td></tr>
                <tr><td className="font-semibold text-gray-700 py-0.5 pr-3">Date:</td><td>{formatDate(invoice.invoiceDate)}</td></tr>
                <tr><td className="font-semibold text-gray-700 py-0.5 pr-3">Due Date:</td><td className="text-red-600 font-semibold">{dueDate}</td></tr>
                {(customer?.poNumber || invoice.poNumber) && (
                  <tr><td className="font-semibold text-gray-700 py-0.5 pr-3">PO Number:</td><td>{customer?.poNumber || invoice.poNumber}</td></tr>
                )}
              </tbody>
            </table>
          </div>
        </div>

        {/* Line items */}
        <div className="overflow-x-auto rounded-lg border border-gray-200 mb-6">
          <table className="w-full text-xs">
            <thead>
              <tr className="bg-blue-700 text-white">
                {["Job Date","Job No.","Senders Ref","Postcode","Destination","Service","Items","Weight","Charge"].map(h => (
                  <th key={h} className={`px-3 py-2.5 text-left font-semibold uppercase tracking-wide text-[10px] ${["Items","Weight","Charge"].includes(h) ? "text-right" : ""}`}>{h}</th>
                ))}
              </tr>
            </thead>
            <tbody>
              {saleRows.map((s, i) => (
                <tr key={s.id} className={`border-b border-gray-100 ${i % 2 === 0 ? "bg-white" : "bg-gray-50"}`}>
                  <td className="px-3 py-2">{formatDate(s.jobDate)}</td>
                  <td className="px-3 py-2 font-medium">{s.jobNumber ?? ""}</td>
                  <td className="px-3 py-2">{s.senderReference ?? ""}</td>
                  <td className="px-3 py-2">{s.postcode2 ?? ""}</td>
                  <td className="px-3 py-2">{s.destination ?? ""}</td>
                  <td className="px-3 py-2">{s.serviceType ?? ""}</td>
                  <td className="px-3 py-2 text-right">{s.items2 ?? ""}</td>
                  <td className="px-3 py-2 text-right">{s.volumeWeight ?? ""}</td>
                  <td className="px-3 py-2 text-right font-medium">£{(s.subTotal ?? 0).toFixed(2)}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        {/* Totals */}
        <div className="flex justify-end mb-6">
          <div className="w-64 text-sm space-y-0">
            <div className="flex justify-between py-2 border-b border-gray-100"><span className="text-gray-600">Sub Total:</span><span>£{subTotal.toFixed(2)}</span></div>
            <div className="flex justify-between py-2 border-b border-gray-100"><span className="text-gray-500 text-xs">Fuel Surcharge ({fuelPct}%):</span><span>£{fuelAmount.toFixed(2)}</span></div>
            {resourcingPct > 0 && <div className="flex justify-between py-2 border-b border-gray-100"><span className="text-gray-500 text-xs">Resourcing ({resourcingPct}%):</span><span>£{resourcingAmount.toFixed(2)}</span></div>}
            <div className="flex justify-between py-2 border-b border-gray-200 font-semibold"><span>Net Total:</span><span>£{netTotal.toFixed(2)}</span></div>
            <div className="flex justify-between py-2 border-b border-gray-100"><span className="text-gray-500 text-xs">VAT ({vatPct}%):</span><span>£{vatAmount.toFixed(2)}</span></div>
            <div className="flex justify-between py-3 px-3 bg-blue-600 text-white rounded-lg mt-1">
              <span className="font-bold">TOTAL DUE:</span>
              <span className="font-bold">{formatCurrency(total)}</span>
            </div>
          </div>
        </div>

        {/* Messages */}
        {(customer?.customerMessage || sett?.invoiceDefaultMessage) && (
          <div className="border-t border-gray-100 pt-5 space-y-3">
            {customer?.customerMessage && (
              <div className="text-sm text-gray-700 leading-relaxed"
                dangerouslySetInnerHTML={{ __html: customer.customerMessage }} />
            )}
            {sett?.invoiceDefaultMessage && (
              <div className="text-sm text-gray-600 leading-relaxed border-t border-gray-100 pt-3"
                dangerouslySetInnerHTML={{ __html: sett.invoiceDefaultMessage }} />
            )}
          </div>
        )}
      </div>
    </div>
  );
}

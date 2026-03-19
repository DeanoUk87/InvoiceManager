"use client";
import { use, useEffect, useState } from "react";
import { formatDate, formatCurrency, calcDueDate } from "@/lib/utils";
import { Button } from "@/components/ui/button";
import { ArrowLeft, FileText, Mail, Printer, Download } from "lucide-react";
import Link from "next/link";
import { useRouter } from "next/navigation";

interface Sale {
  id: number;
  jobDate: string | null;
  jobNumber: string | null;
  senderReference: string | null;
  postcode2: string | null;
  destination: string | null;
  serviceType: string | null;
  items2: number | null;
  volumeWeight: number | null;
  subTotal: number | null;
  invoiceTotal: number | null;
  percentageFuelSurcharge: number | null;
  percentageResourcingSurcharge: number | null;
  vatAmount: number | null;
  vatPercent: number | null;
  customerName: string | null;
  address1: string | null;
  address2: string | null;
  town: string | null;
  country: string | null;
  postcode: string | null;
  numb2: number | null;
}

interface InvoiceData {
  invoice: {
    id: number;
    customerAccount: string;
    invoiceNumber: string;
    invoiceDate: string | null;
    dueDate: string | null;
    poNumber: string | null;
    printer: number;
  };
  customer: {
    customerAccount: string;
    customerEmail: string | null;
    customerEmailBcc: string | null;
    poNumber: string | null;
    customerMessage: string | null;
  } | null;
  sales: Sale[];
  settings: {
    companyName: string | null;
    logo: string | null;
    companyAddress1: string | null;
    companyAddress2: string | null;
    city: string | null;
    postcode: string | null;
    country: string | null;
    phone: string | null;
    cemail: string | null;
    vatNumber: string | null;
    fuelSurchargePercent: number;
    resourcingSurchargePercent: number;
    vatPercent: number;
    invoiceDefaultMessage: string | null;
  } | null;
}

export default function InvoicePage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = use(params);
  const router = useRouter();
  const [data, setData] = useState<InvoiceData | null>(null);
  const [loading, setLoading] = useState(true);
  const [emailAddr, setEmailAddr] = useState("");
  const [showEmailForm, setShowEmailForm] = useState(false);
  const [sending, setSending] = useState(false);
  const [message, setMessage] = useState("");

  useEffect(() => {
    fetch(`/api/invoices/${id}`)
      .then((r) => r.json())
      .then((d) => {
        setData(d);
        setEmailAddr(d.customer?.customerEmail ?? "");
        setLoading(false);
      });
  }, [id]);

  if (loading) return <div className="flex items-center justify-center h-64 text-gray-400">Loading...</div>;
  if (!data) return <div className="text-red-500">Invoice not found</div>;

  const { invoice, customer, sales, settings } = data;

  // percentageFuelSurcharge (col34) = the % rate (e.g. 8 = 8%), NOT a £ amount
  // vatAmount (col38) = per-line VAT £, sum these
  // invoiceTotal (col14) = grand total, same value repeated on every line - use first row
  const subTotal = sales.reduce((s, r) => s + (r.subTotal ?? 0), 0);
  const fuelSurchargePct = sales[0]?.percentageFuelSurcharge ?? settings?.fuelSurchargePercent ?? 3.5;
  const resourcingSurchargePct = settings?.resourcingSurchargePercent ?? 0;
  const fuelSurchargeAmount = subTotal * (fuelSurchargePct / 100);
  const resourcingSurchargeAmount = subTotal * (resourcingSurchargePct / 100);
  const netTotal = subTotal + fuelSurchargeAmount + resourcingSurchargeAmount;
  const vatPct = sales[0]?.vatPercent ?? settings?.vatPercent ?? 20;
  const vatAmount = sales.reduce((s, r) => s + (r.vatAmount ?? 0), 0);
  const total = sales[0]?.invoiceTotal ?? (netTotal + vatAmount);

  const handleMarkPrinted = async () => {
    await fetch(`/api/invoices/${id}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ printer: 2 }),
    });
    router.refresh();
    setData((d) => d ? { ...d, invoice: { ...d.invoice, printer: 2 } } : d);
  };

  const handleEmail = async () => {
    if (!emailAddr) return;
    setSending(true);
    const res = await fetch("/api/send-invoice", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        invoiceId: id,
        email: emailAddr,
        bcc: customer?.customerEmailBcc,
      }),
    });
    const result = await res.json();
    setSending(false);
    if (result.success) {
      setMessage("Invoice sent successfully!");
      setShowEmailForm(false);
      handleMarkPrinted();
    } else {
      setMessage(result.error ?? "Failed to send");
    }
  };

  const handleExportPDF = () => {
    window.open(`/api/invoices/${id}/pdf`, "_blank");
  };

  const handleExportExcel = () => {
    window.location.href = `/api/invoices/${id}/excel`;
  };

  return (
    <div className="space-y-4">
      {/* Action Bar */}
      <div className="flex items-center justify-between flex-wrap gap-3">
        <h1 className="text-xl font-bold text-gray-900">Invoice</h1>
        <div className="flex items-center gap-2 flex-wrap">
          <Link href="/invoices">
            <Button variant="outline" size="sm">
              <ArrowLeft size={14} /> Go Back
            </Button>
          </Link>
          <Button variant="primary" size="sm" onClick={handleExportPDF}>
            <FileText size={14} /> PDF
          </Button>
          <Button variant="success" size="sm" onClick={handleExportExcel}>
            <Download size={14} /> Excel
          </Button>
          <Button variant="warning" size="sm" onClick={handleMarkPrinted}>
            <Printer size={14} /> Print
          </Button>
          <Button variant="primary" size="sm" onClick={() => setShowEmailForm(!showEmailForm)}>
            <Mail size={14} /> Email
          </Button>
        </div>
      </div>

      {message && (
        <div className="p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
          {message}
        </div>
      )}

      {/* Email form for unprinted */}
      {showEmailForm && (
        <div className="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-end gap-3">
          <div className="flex-1">
            <label className="block text-sm font-medium text-gray-700 mb-1">Send to Email</label>
            <input
              type="email"
              value={emailAddr}
              onChange={(e) => setEmailAddr(e.target.value)}
              className="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="customer@email.com"
            />
          </div>
          <Button onClick={handleEmail} loading={sending} size="sm">
            <Mail size={14} /> Send Invoice
          </Button>
          <Button variant="outline" size="sm" onClick={() => setShowEmailForm(false)}>
            Cancel
          </Button>
        </div>
      )}

      {/* Invoice Document */}
      <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-8" id="invoice-content">

        {/* Company Header */}
        <div className="flex justify-between items-start mb-6">
          <div className="flex items-center gap-3">
            <div className="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center shrink-0">
              <FileText size={24} className="text-white" />
            </div>
            <div>
              <p className="text-lg font-bold text-blue-600 leading-tight">{settings?.companyName ?? "Invoice Manager"}</p>
              <p className="text-xs text-gray-400">Invoice Management System</p>
            </div>
          </div>
          <div className="text-right text-xs leading-relaxed text-gray-600">
            {settings?.companyAddress1 && <p>{settings.companyAddress1}</p>}
            {settings?.companyAddress2 && <p>{settings.companyAddress2}</p>}
            {(settings?.city || settings?.postcode) && (
              <p>{[settings.city, settings.postcode].filter(Boolean).join(" ")}</p>
            )}
            {settings?.country && <p>{settings.country}</p>}
            {settings?.phone && <p><span className="font-semibold text-gray-700">Tel:</span> {settings.phone}</p>}
            {settings?.cemail && <p className="text-blue-600">{settings.cemail}</p>}
            {settings?.vatNumber && <p><span className="font-semibold text-gray-700">VAT Reg:</span> {settings.vatNumber}</p>}
          </div>
        </div>

        {/* Blue invoice title bar */}
        <div className="bg-blue-600 text-white rounded-lg px-5 py-3 flex justify-between items-center mb-6">
          <span className="text-lg font-bold tracking-wide">INVOICE</span>
          <span className="text-sm opacity-90">#{invoice.invoiceNumber}</span>
        </div>

        {/* Bill To + Invoice Details */}
        <div className="flex justify-between items-start mb-6 gap-6">
          <div className="flex-1">
            <p className="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2">Bill To</p>
            {sales[0]?.customerName && (
              <p className="font-bold text-gray-900 text-sm">{sales[0].customerName}</p>
            )}
            <div className="text-xs text-gray-600 leading-relaxed mt-1">
              {/* Only show address1 if it differs from customerName */}
              {sales[0]?.address1 && sales[0].address1 !== sales[0].customerName && <p>{sales[0].address1}</p>}
              {sales[0]?.address2 && <p>{sales[0].address2}</p>}
              {sales[0]?.town && <p>{sales[0].town}</p>}
              {sales[0]?.country && <p>{sales[0].country}</p>}
              {sales[0]?.postcode && <p>{sales[0].postcode}</p>}
            </div>
            {customer?.customerEmail && (
              <p className="text-xs text-blue-600 mt-1">{customer.customerEmail}</p>
            )}
          </div>
          <div className="min-w-[220px]">
            <p className="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-2">Invoice Details</p>
            <table className="text-xs w-full">
              <tbody>
                <tr><td className="font-semibold text-gray-700 py-0.5 pr-3">Account:</td><td className="text-gray-600">{invoice.customerAccount}</td></tr>
                <tr><td className="font-semibold text-gray-700 py-0.5 pr-3">Invoice No:</td><td className="font-bold text-gray-900">{invoice.invoiceNumber}</td></tr>
                <tr><td className="font-semibold text-gray-700 py-0.5 pr-3">Invoice Date:</td><td className="text-gray-600">{formatDate(invoice.invoiceDate)}</td></tr>
                <tr><td className="font-semibold text-gray-700 py-0.5 pr-3">Due Date:</td><td className="text-red-600 font-semibold">{invoice.dueDate ? formatDate(invoice.dueDate) : calcDueDate(invoice.invoiceDate, sales[0]?.numb2)}</td></tr>
                {(customer?.poNumber || invoice.poNumber) && <tr><td className="font-semibold text-gray-700 py-0.5 pr-3">PO Number:</td><td className="text-gray-600">{customer?.poNumber || invoice.poNumber}</td></tr>}
              </tbody>
            </table>
          </div>
        </div>

        {/* Line Items */}
        <div className="overflow-x-auto rounded-lg border border-gray-200 mb-6">
          <table className="w-full text-xs">
            <thead>
              <tr className="bg-blue-700 text-white">
                <th className="px-3 py-2.5 text-left font-semibold uppercase tracking-wide text-[10px]">Job Date</th>
                <th className="px-3 py-2.5 text-left font-semibold uppercase tracking-wide text-[10px]">Job No.</th>
                <th className="px-3 py-2.5 text-left font-semibold uppercase tracking-wide text-[10px]">Senders Ref</th>
                <th className="px-3 py-2.5 text-left font-semibold uppercase tracking-wide text-[10px]">Postcode</th>
                <th className="px-3 py-2.5 text-left font-semibold uppercase tracking-wide text-[10px]">Destination</th>
                <th className="px-3 py-2.5 text-left font-semibold uppercase tracking-wide text-[10px]">Service</th>
                <th className="px-3 py-2.5 text-right font-semibold uppercase tracking-wide text-[10px]">Items</th>
                <th className="px-3 py-2.5 text-right font-semibold uppercase tracking-wide text-[10px]">Weight</th>
                <th className="px-3 py-2.5 text-right font-semibold uppercase tracking-wide text-[10px]">Charge</th>
              </tr>
            </thead>
            <tbody>
              {sales.length === 0 ? (
                <tr><td colSpan={9} className="px-3 py-6 text-center text-gray-400">No line items</td></tr>
              ) : (
                sales.map((s, i) => (
                  <tr key={s.id} className={`border-b border-gray-100 ${i % 2 === 0 ? "bg-white" : "bg-gray-50"} hover:bg-blue-50`}>
                    <td className="px-3 py-2 text-gray-600">{formatDate(s.jobDate)}</td>
                    <td className="px-3 py-2 text-gray-700 font-medium">{s.jobNumber ?? ""}</td>
                    <td className="px-3 py-2 text-gray-600">{s.senderReference ?? ""}</td>
                    <td className="px-3 py-2 text-gray-600">{s.postcode2 ?? ""}</td>
                    <td className="px-3 py-2 text-gray-700">{s.destination ?? ""}</td>
                    <td className="px-3 py-2 text-gray-600">{s.serviceType ?? ""}</td>
                    <td className="px-3 py-2 text-right text-gray-600">{s.items2 ?? ""}</td>
                    <td className="px-3 py-2 text-right text-gray-600">{s.volumeWeight ?? ""}</td>
                    <td className="px-3 py-2 text-right font-medium text-gray-900">£{(s.subTotal ?? 0).toFixed(2)}</td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>

        {/* Totals */}
        <div className="flex justify-end mb-6">
          <div className="w-72 text-sm">
            <div className="flex justify-between py-2 border-b border-gray-100">
              <span className="text-gray-600">Sub Total:</span>
              <span className="font-medium">£{subTotal.toFixed(2)}</span>
            </div>
            <div className="flex justify-between py-2 border-b border-gray-100">
              <span className="text-gray-500 text-xs">Fuel Surcharge ({fuelSurchargePct}%):</span>
              <span className="text-gray-600">£{fuelSurchargeAmount.toFixed(2)}</span>
            </div>
            {resourcingSurchargePct > 0 && (
              <div className="flex justify-between py-2 border-b border-gray-100">
                <span className="text-gray-500 text-xs">Resourcing Surcharge ({resourcingSurchargePct}%):</span>
                <span className="text-gray-600">£{resourcingSurchargeAmount.toFixed(2)}</span>
              </div>
            )}
            <div className="flex justify-between py-2 border-b border-gray-200">
              <span className="font-semibold text-gray-800">Net Total:</span>
              <span className="font-semibold">£{netTotal.toFixed(2)}</span>
            </div>
            <div className="flex justify-between py-2 border-b border-gray-100">
              <span className="text-gray-500 text-xs">VAT ({vatPct}%):</span>
              <span className="text-gray-600">£{vatAmount.toFixed(2)}</span>
            </div>
            <div className="flex justify-between py-3 px-3 bg-blue-600 text-white rounded-lg mt-1">
              <span className="font-bold text-sm">TOTAL DUE:</span>
              <span className="font-bold text-base">{formatCurrency(total)}</span>
            </div>
          </div>
        </div>

        {/* Messages below totals */}
        {(customer?.customerMessage || settings?.invoiceDefaultMessage) && (
          <div className="border-t border-gray-100 pt-5 mt-2 space-y-3">
            {customer?.customerMessage && (
              <div className="text-sm text-gray-700 leading-relaxed"
                dangerouslySetInnerHTML={{ __html: customer.customerMessage }} />
            )}
            {settings?.invoiceDefaultMessage && (
              <div className="text-sm text-gray-600 leading-relaxed border-t border-gray-100 pt-3"
                dangerouslySetInnerHTML={{ __html: settings.invoiceDefaultMessage }} />
            )}
          </div>
        )}

        {/* Status only */}
        <div className="flex items-center pt-4 border-t border-gray-100 mt-4">
          <span className={`inline-flex px-3 py-1 rounded-full text-xs font-medium ${
            invoice.printer === 2 ? "bg-green-100 text-green-700" : "bg-orange-100 text-orange-700"
          }`}>
            {invoice.printer === 2 ? "Printed / Sent" : "Unprinted"}
          </span>
        </div>
      </div>
    </div>
  );
}

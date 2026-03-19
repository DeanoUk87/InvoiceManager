"use client";
import { use, useEffect, useState } from "react";
import { formatDate, formatCurrency } from "@/lib/utils";
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
  } | null;
  sales: Sale[];
  settings: {
    companyName: string | null;
    logo: string | null;
    companyAddress1: string | null;
    companyAddress2: string | null;
    city: string | null;
    postcode: string | null;
    phone: string | null;
    vatNumber: string | null;
    fuelSurchargePercent: number;
    resourcingSurchargePercent: number;
    vatPercent: number;
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

  // Use values directly from CSV data - these are already calculated by the source system
  const subTotal = sales.reduce((s, r) => s + (r.subTotal ?? 0), 0);
  const fuelSurchargeAmount = sales.reduce((s, r) => s + (r.percentageFuelSurcharge ?? 0), 0);
  const resourcingSurchargeAmount = sales.reduce((s, r) => s + (r.percentageResourcingSurcharge ?? 0), 0);
  const vatAmount = sales.reduce((s, r) => s + (r.vatAmount ?? 0), 0);
  const netTotal = subTotal + fuelSurchargeAmount + resourcingSurchargeAmount;
  const total = sales.reduce((s, r) => s + (r.invoiceTotal ?? 0), 0);
  // Fuel/resourcing surcharge % for display label only (from first row or settings)
  const fuelSurchargePct = sales[0]?.percentageFuelSurcharge != null && subTotal > 0
    ? ((fuelSurchargeAmount / subTotal) * 100).toFixed(1)
    : (settings?.fuelSurchargePercent ?? 3.5);
  const resourcingSurchargePct = sales[0]?.percentageResourcingSurcharge != null && subTotal > 0
    ? ((resourcingSurchargeAmount / subTotal) * 100).toFixed(1)
    : (settings?.resourcingSurchargePercent ?? 0);
  const vatPct = sales[0]?.vatPercent ?? settings?.vatPercent ?? 20;

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
        {/* Header */}
        <div className="flex justify-between items-start mb-8">
          <div className="w-24 h-16 bg-blue-600 rounded-lg flex items-center justify-center">
            <FileText size={32} className="text-white" />
          </div>
          <div className="text-right text-sm">
            <p className="font-bold text-gray-900 text-base">{settings?.companyName}</p>
            {settings?.companyAddress1 && <p className="text-gray-600">{settings.companyAddress1}</p>}
            {settings?.companyAddress2 && <p className="text-gray-600">{settings.companyAddress2}</p>}
            {settings?.city && <p className="text-gray-600">{settings.city}</p>}
            {settings?.postcode && <p className="text-gray-600">{settings.postcode}</p>}
            {settings?.phone && <p className="text-gray-600"><span className="font-semibold">TEL:</span> {settings.phone}</p>}
            {settings?.vatNumber && <p className="text-gray-600"><span className="font-semibold">VAT NUMBER:</span> {settings.vatNumber}</p>}
          </div>
        </div>

        <hr className="border-gray-200 mb-6" />

        {/* Customer + Invoice Details */}
        <div className="flex justify-between items-start mb-6">
          <div className="text-sm">
            {sales[0] && (
              <>
                <p className="font-bold text-gray-900">{sales[0].destination ?? invoice.customerAccount}</p>
                {sales[0].destination && <p className="text-gray-700">{sales[0].destination}</p>}
              </>
            )}
            <p className="text-gray-700 font-medium">{invoice.customerAccount}</p>
          </div>
          <div className="text-sm text-right space-y-1">
            <p className="text-gray-700">
              <span className="font-bold">ACCOUNT:</span> {invoice.customerAccount}
            </p>
            <p className="text-gray-700">
              <span className="font-bold">INVOICE NO:</span> {invoice.invoiceNumber}
            </p>
            <p className="text-gray-700">
              <span className="font-bold">INVOICE DATE:</span> {formatDate(invoice.invoiceDate)}
            </p>
            <p className="text-gray-700">
              <span className="font-bold">PO NUMBER:</span> {invoice.poNumber ?? ""}
            </p>
          </div>
        </div>

        {/* Line Items */}
        <div className="overflow-x-auto rounded-lg border border-gray-200 mb-6">
          <table className="w-full text-xs">
            <thead>
              <tr className="bg-gray-100 border-b border-gray-200">
                <th className="px-3 py-2.5 text-left font-bold text-gray-700">JOB DATE</th>
                <th className="px-3 py-2.5 text-left font-bold text-gray-700">JOB NUMBER</th>
                <th className="px-3 py-2.5 text-left font-bold text-gray-700">SENDERS REF</th>
                <th className="px-3 py-2.5 text-left font-bold text-gray-700">POSTCODE</th>
                <th className="px-3 py-2.5 text-left font-bold text-gray-700">DESTINATION</th>
                <th className="px-3 py-2.5 text-left font-bold text-gray-700">SERVICE TYPE</th>
                <th className="px-3 py-2.5 text-right font-bold text-gray-700">ITEMS</th>
                <th className="px-3 py-2.5 text-right font-bold text-gray-700">WEIGHT</th>
                <th className="px-3 py-2.5 text-right font-bold text-gray-700">CHARGE</th>
              </tr>
            </thead>
            <tbody>
              {sales.length === 0 ? (
                <tr>
                  <td colSpan={9} className="px-3 py-6 text-center text-gray-400">No line items</td>
                </tr>
              ) : (
                sales.map((s) => (
                  <tr key={s.id} className="border-b border-gray-100 hover:bg-gray-50">
                    <td className="px-3 py-2">{formatDate(s.jobDate)}</td>
                    <td className="px-3 py-2">{s.jobNumber ?? ""}</td>
                    <td className="px-3 py-2">{s.senderReference ?? ""}</td>
                    <td className="px-3 py-2">{s.postcode2 ?? ""}</td>
                    <td className="px-3 py-2">{s.destination ?? ""}</td>
                    <td className="px-3 py-2">{s.serviceType ?? ""}</td>
                    <td className="px-3 py-2 text-right">{s.items2 ?? ""}</td>
                    <td className="px-3 py-2 text-right">{s.volumeWeight ?? ""}</td>
                    <td className="px-3 py-2 text-right">{s.subTotal?.toFixed(2) ?? ""}</td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>

        {/* Totals */}
        <div className="flex justify-end">
          <div className="w-72 text-sm space-y-1.5">
            <div className="flex justify-between py-1.5 border-b border-gray-100">
              <span className="text-gray-600">SUB TOTAL:</span>
              <span className="font-medium">{subTotal.toFixed(2)}</span>
            </div>
            <div className="flex justify-between py-1.5 border-b border-gray-100">
              <span className="text-gray-600">FUEL SURCHARGE {fuelSurchargePct}%:</span>
              <span className="font-medium">{fuelSurchargeAmount.toFixed(2)}</span>
            </div>
            <div className="flex justify-between py-1.5 border-b border-gray-100">
              <span className="text-gray-600">PERCENTAGE RESOURCING SURCHARGE {resourcingSurchargePct}%:</span>
              <span className="font-medium">{resourcingSurchargeAmount.toFixed(2)}</span>
            </div>
            <div className="flex justify-between py-1.5 border-b border-gray-100">
              <span className="text-gray-600 font-medium">NET TOTAL:</span>
              <span className="font-medium">{netTotal.toFixed(2)}</span>
            </div>
            <div className="flex justify-between py-1.5 border-b border-gray-100">
              <span className="text-gray-600">VAT ({vatPct}%):</span>
              <span className="font-medium">{vatAmount.toFixed(2)}</span>
            </div>
            <div className="flex justify-between py-2 bg-gray-50 rounded px-2">
              <span className="font-bold text-gray-900">TOTAL:</span>
              <span className="font-bold text-blue-600 text-base">{formatCurrency(total)}</span>
            </div>
          </div>
        </div>

        {/* Status */}
        <div className="mt-6 flex items-center gap-2">
          <span
            className={`inline-flex px-3 py-1 rounded-full text-xs font-medium ${
              invoice.printer === 2
                ? "bg-green-100 text-green-700"
                : "bg-orange-100 text-orange-700"
            }`}
          >
            {invoice.printer === 2 ? "Printed / Sent" : "Unprinted"}
          </span>
          {customer?.customerEmail && (
            <span className="text-xs text-gray-500">Email: {customer.customerEmail}</span>
          )}
        </div>
      </div>
    </div>
  );
}

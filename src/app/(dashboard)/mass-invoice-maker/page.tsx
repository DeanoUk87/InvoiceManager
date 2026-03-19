"use client";
import { useState } from "react";
import { Button } from "@/components/ui/button";
import { Zap, Send, CheckCircle, AlertCircle, Mail, FileX } from "lucide-react";

interface BulkResult {
  success: boolean;
  message?: string;
  error?: string;
  generated?: number;
  sent?: number;
  noEmail?: number;
  total?: number;
  results?: { invoiceNumber: string; status: string; email?: string }[];
}

export default function MassInvoiceMakerPage() {
  const [generating, setGenerating] = useState(false);
  const [sending, setSending] = useState(false);
  const [generateResult, setGenerateResult] = useState<BulkResult | null>(null);
  const [sendResult, setSendResult] = useState<BulkResult | null>(null);

  const safeFetch = async (url: string): Promise<BulkResult> => {
    try {
      const res = await fetch(url, { method: "POST" });
      const ct = res.headers.get("content-type") ?? "";
      if (!ct.includes("application/json")) {
        const txt = await res.text();
        return { success: false, message: `Server error (${res.status}): ${txt.substring(0, 300)}` };
      }
      const data = await res.json();
      // Normalise: if API returned { error: "..." } treat as failure with message
      if (!res.ok && data.error && !data.message) {
        return { success: false, message: data.error, ...data };
      }
      return data;
    } catch (e) {
      return { success: false, message: `Request failed: ${String(e)}` };
    }
  };

  const handleGenerate = async () => {
    setGenerating(true);
    setGenerateResult(null);
    const data = await safeFetch("/api/generate-invoices");
    setGenerateResult(data);
    setGenerating(false);
  };

  const handleBulkSend = async () => {
    setSending(true);
    setSendResult(null);
    const data = await safeFetch("/api/bulk-send");
    setSendResult(data);
    setSending(false);
  };

  return (
    <div className="space-y-6">
      <h1 className="text-xl font-bold text-gray-900">Mass Invoice Maker</h1>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        {/* Generate Invoices */}
        <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
          <div className="flex items-center gap-3 mb-4">
            <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
              <Zap size={20} className="text-blue-600" />
            </div>
            <div>
              <h2 className="font-semibold text-gray-900">Generate Invoices</h2>
              <p className="text-xs text-gray-500">Create invoices from uploaded CSV data</p>
            </div>
          </div>
          <p className="text-sm text-gray-600 mb-5">
            This will scan all uploaded CSV data and create invoice records for each unique invoice
            number / customer account combination. New data will be grouped and matched.
          </p>
          <Button onClick={handleGenerate} loading={generating} className="w-full">
            <Zap size={16} />
            Generate Invoices
          </Button>
          {generateResult && (
            <div
              className={`mt-4 p-3 rounded-lg border ${
                generateResult.success
                  ? "bg-green-50 border-green-200"
                  : "bg-orange-50 border-orange-200"
              }`}
            >
              <div className="flex items-start gap-2">
                {generateResult.success ? (
                  <CheckCircle size={16} className="text-green-600 mt-0.5 shrink-0" />
                ) : (
                  <AlertCircle size={16} className="text-orange-600 mt-0.5 shrink-0" />
                )}
                <p className={`text-sm ${generateResult.success ? "text-green-700" : "text-orange-700"}`}>
                  {generateResult.message || generateResult.error || "An error occurred. Please try again."}
                </p>
              </div>
            </div>
          )}
        </div>

        {/* Bulk Send */}
        <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
          <div className="flex items-center gap-3 mb-4">
            <div className="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
              <Send size={20} className="text-green-600" />
            </div>
            <div>
              <h2 className="font-semibold text-gray-900">Bulk Send Invoices</h2>
              <p className="text-xs text-gray-500">Email invoices to all customers with an email address</p>
            </div>
          </div>
          <p className="text-sm text-gray-600 mb-5">
            This will send emails to all customers who have an email address and have unsent invoices.
            Invoices for customers without an email will be moved to <strong>Unprinted</strong> for
            manual handling.
          </p>
          <Button variant="success" onClick={handleBulkSend} loading={sending} className="w-full">
            <Send size={16} />
            Generate &amp; Bulk Send
          </Button>
          {sendResult && (
            <div className="mt-4 space-y-3">
              <div
                className={`p-3 rounded-lg border ${
                  sendResult.success
                    ? "bg-green-50 border-green-200"
                    : "bg-red-50 border-red-200"
                }`}
              >
                <p className={`text-sm ${sendResult.success ? "text-green-700" : "text-red-700"}`}>
                  {sendResult.message || sendResult.error || "An error occurred. Please try again."}
                </p>
              </div>
              {sendResult.results && sendResult.results.length > 0 && (
                <div className="max-h-60 overflow-y-auto space-y-1">
                  {sendResult.results.map((r, i) => (
                    <div key={i} className="flex items-center gap-2 text-xs p-2 rounded bg-gray-50">
                      {r.status === "sent" ? (
                        <Mail size={12} className="text-green-600" />
                      ) : r.status === "no_email" ? (
                        <FileX size={12} className="text-orange-600" />
                      ) : (
                        <AlertCircle size={12} className="text-red-500" />
                      )}
                      <span className="font-medium">#{r.invoiceNumber}</span>
                      <span className={
                        r.status === "sent" ? "text-green-600" :
                        r.status === "no_email" ? "text-orange-600" : "text-red-500"
                      }>
                        {r.status === "sent" ? `Sent to ${r.email}` :
                         r.status === "no_email" ? "No email - moved to Unprinted" : "Error sending"}
                      </span>
                    </div>
                  ))}
                </div>
              )}
            </div>
          )}
        </div>
      </div>

      {/* Info */}
      <div className="bg-blue-50 border border-blue-100 rounded-xl p-5">
        <h3 className="text-sm font-semibold text-blue-900 mb-2">How it works</h3>
        <ol className="text-sm text-blue-800 space-y-1.5 list-decimal list-inside">
          <li>Upload your CSV file via the <strong>Upload CSV</strong> section</li>
          <li>Click <strong>Generate Invoices</strong> to create invoice records from the uploaded data</li>
          <li>Click <strong>Generate &amp; Bulk Send</strong> to email all invoices to customers</li>
          <li>Customers without an email address will appear in <strong>Unprinted Invoices</strong></li>
          <li>Sent invoices are automatically marked as <strong>Printed</strong></li>
        </ol>
      </div>
    </div>
  );
}

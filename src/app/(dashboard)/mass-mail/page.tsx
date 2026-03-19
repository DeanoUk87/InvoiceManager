"use client";
import { useState } from "react";
import { Button } from "@/components/ui/button";
import { Send, CheckCircle, AlertCircle } from "lucide-react";

interface SendResult {
  success: boolean;
  message: string;
  sent?: number;
  noEmail?: number;
  total?: number;
}

export default function MassMailPage() {
  const [sending, setSending] = useState(false);
  const [result, setResult] = useState<SendResult | null>(null);

  const handleSend = async () => {
    setSending(true);
    setResult(null);
    const res = await fetch("/api/bulk-send", { method: "POST" });
    const data = await res.json();
    setResult(data);
    setSending(false);
  };

  return (
    <div className="space-y-4">
      <h1 className="text-xl font-bold text-gray-900">Mass Mail</h1>
      <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6 max-w-xl">
        <div className="flex items-center gap-3 mb-4">
          <div className="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
            <Send size={20} className="text-blue-600" />
          </div>
          <div>
            <h2 className="font-semibold text-gray-900">Send Mass Invoices</h2>
            <p className="text-sm text-gray-500">Email all pending invoices to customers</p>
          </div>
        </div>
        <p className="text-sm text-gray-600 mb-6">
          This will send all pending (unsent) invoices to customers via email. Invoices for customers
          without an email address will be moved to the Unprinted queue.
        </p>
        <Button onClick={handleSend} loading={sending} className="w-full">
          <Send size={16} />
          Send Mass Mail to Customers
        </Button>
        {result && (
          <div
            className={`mt-4 p-4 rounded-lg border ${
              result.success ? "bg-green-50 border-green-200" : "bg-orange-50 border-orange-200"
            }`}
          >
            <div className="flex items-start gap-2">
              {result.success ? (
                <CheckCircle size={16} className="text-green-600 mt-0.5" />
              ) : (
                <AlertCircle size={16} className="text-orange-600 mt-0.5" />
              )}
              <div>
                <p className={`text-sm font-medium ${result.success ? "text-green-800" : "text-orange-800"}`}>
                  {result.message}
                </p>
                {result.total !== undefined && (
                  <div className="mt-2 grid grid-cols-3 gap-3">
                    <div className="text-center p-2 bg-white rounded border">
                      <p className="text-lg font-bold text-gray-900">{result.total}</p>
                      <p className="text-xs text-gray-500">Total</p>
                    </div>
                    <div className="text-center p-2 bg-white rounded border">
                      <p className="text-lg font-bold text-green-600">{result.sent}</p>
                      <p className="text-xs text-gray-500">Sent</p>
                    </div>
                    <div className="text-center p-2 bg-white rounded border">
                      <p className="text-lg font-bold text-orange-500">{result.noEmail}</p>
                      <p className="text-xs text-gray-500">No Email</p>
                    </div>
                  </div>
                )}
              </div>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}

"use client";
import { useState, useEffect, useCallback } from "react";
import { DataTable } from "@/components/DataTable";
import { Button } from "@/components/ui/button";
import { formatDate } from "@/lib/utils";
import { Eye, Mail } from "lucide-react";
import Link from "next/link";

interface Invoice {
  id: number;
  customerAccount: string;
  invoiceNumber: string;
  invoiceDate: string | null;
  dueDate: string | null;
  printer: number;
}

export default function UnprintedPage() {
  const [invoices, setInvoices] = useState<Invoice[]>([]);
  const [loading, setLoading] = useState(true);
  const [emailModal, setEmailModal] = useState<{ id: number; invoiceNumber: string } | null>(null);
  const [emailAddr, setEmailAddr] = useState("");
  const [sending, setSending] = useState(false);

  const load = useCallback(async () => {
    setLoading(true);
    const res = await fetch("/api/invoices?status=unprinted");
    setInvoices(await res.json());
    setLoading(false);
  }, []);

  useEffect(() => { load(); }, [load]);

  const handleSendEmail = async () => {
    if (!emailModal || !emailAddr) return;
    setSending(true);
    const res = await fetch("/api/send-invoice", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ invoiceId: emailModal.id, email: emailAddr }),
    });
    const data = await res.json();
    setSending(false);
    if (data.success) {
      setEmailModal(null);
      setEmailAddr("");
      load();
    }
  };

  const columns = [
    {
      key: "invoiceDate",
      label: "Invoice Date",
      render: (row: Record<string, unknown>) => formatDate(row.invoiceDate as string),
    },
    {
      key: "customerAccount",
      label: "Customer Account",
      render: (row: Record<string, unknown>) => (
        <span className="font-medium">{String(row.customerAccount)}</span>
      ),
    },
    { key: "invoiceNumber", label: "Invoice Number" },
    {
      key: "dueDate",
      label: "Due Date",
      render: (row: Record<string, unknown>) => formatDate(row.dueDate as string),
    },
    {
      key: "preview",
      label: "Preview",
      sortable: false,
      render: (row: Record<string, unknown>) => (
        <Link href={`/invoices/${row.id}`}>
          <button className="px-3 py-1 border border-gray-300 rounded text-xs hover:bg-gray-100">
            Invoice
          </button>
        </Link>
      ),
    },
    {
      key: "status",
      label: "Status",
      sortable: false,
      render: () => (
        <span className="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
          Unprinted
        </span>
      ),
    },
    {
      key: "actions",
      label: "Actions",
      sortable: false,
      render: (row: Record<string, unknown>) => (
        <div className="flex items-center gap-1.5">
          <Link href={`/invoices/${row.id}`}>
            <Button variant="primary" size="sm" className="px-2 py-1">
              <Eye size={13} />
            </Button>
          </Link>
          <Button
            variant="success"
            size="sm"
            className="px-2 py-1"
            onClick={() => {
              setEmailModal({ id: row.id as number, invoiceNumber: row.invoiceNumber as string });
              setEmailAddr("");
            }}
          >
            <Mail size={13} />
          </Button>
        </div>
      ),
    },
  ];

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-xl font-bold text-gray-900">Unprinted Invoices</h1>
          <p className="text-sm text-gray-500 mt-0.5">
            Invoices where the customer had no email address — handle manually below
          </p>
        </div>
      </div>

      <div className="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
        <p className="text-sm text-yellow-800">
          <strong>What is Unprinted?</strong> These invoices were generated but the customer had no
          email on record. You can view the invoice, download it, or manually enter an email to send
          it. Once sent or downloaded, it will be marked as <strong>Printed</strong>.
        </p>
      </div>

      <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        {loading ? (
          <div className="text-center py-10 text-gray-400">Loading...</div>
        ) : (
          <DataTable
            columns={columns}
            data={invoices as unknown as Record<string, unknown>[]}
            searchKeys={["customerAccount", "invoiceNumber"]}
          />
        )}
      </div>

      {/* Email Modal */}
      {emailModal && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
          <div className="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md mx-4">
            <h3 className="text-base font-semibold text-gray-900 mb-1">
              Send Invoice #{emailModal.invoiceNumber}
            </h3>
            <p className="text-sm text-gray-500 mb-4">
              Enter the customer email address to send this invoice.
            </p>
            <input
              type="email"
              value={emailAddr}
              onChange={(e) => setEmailAddr(e.target.value)}
              placeholder="customer@email.com"
              className="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
            <div className="flex gap-3">
              <Button onClick={handleSendEmail} loading={sending} className="flex-1">
                <Mail size={14} /> Send Invoice
              </Button>
              <Button
                variant="outline"
                onClick={() => setEmailModal(null)}
                className="flex-1"
              >
                Cancel
              </Button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

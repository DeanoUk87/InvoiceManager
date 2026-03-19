"use client";
import { useState, useEffect, useCallback } from "react";
import { DataTable } from "@/components/DataTable";
import { formatDate } from "@/lib/utils";
import Link from "next/link";

interface Invoice {
  id: number;
  customerAccount: string;
  invoiceNumber: string;
  invoiceDate: string | null;
  dueDate: string | null;
  printer: number;
}

export default function PrintedPage() {
  const [invoices, setInvoices] = useState<Invoice[]>([]);
  const [loading, setLoading] = useState(true);

  const load = useCallback(async () => {
    setLoading(true);
    const res = await fetch("/api/invoices?status=printed");
    setInvoices(await res.json());
    setLoading(false);
  }, []);

  useEffect(() => { load(); }, [load]);

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
        <span className="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
          Printed
        </span>
      ),
    },
  ];

  return (
    <div className="space-y-4">
      <h1 className="text-xl font-bold text-gray-900">Printed Invoices</h1>
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
    </div>
  );
}

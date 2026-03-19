"use client";
import { useState, useCallback } from "react";
import { DataTable } from "@/components/DataTable";
import { Button } from "@/components/ui/button";
import { formatDate } from "@/lib/utils";
import { Pencil, Trash2, Send } from "lucide-react";
import Link from "next/link";

interface Invoice {
  id: number;
  customerAccount: string;
  invoiceNumber: string;
  invoiceDate: string | null;
  dueDate: string | null;
  printer: number;
}

export default function InvoicesPage() {
  const [invoices, setInvoices] = useState<Invoice[]>([]);
  const [loading, setLoading] = useState(false);
  const [loaded, setLoaded] = useState(false);
  const today = new Date().toISOString().split("T")[0];
  const [dateFrom, setDateFrom] = useState(today);
  const [dateTo, setDateTo] = useState(today);
  const [account, setAccount] = useState("");
  const [invoiceNo, setInvoiceNo] = useState("");

  const load = useCallback(async () => {
    setLoading(true);
    const p = new URLSearchParams();
    if (dateFrom) p.set("dateFrom", dateFrom);
    if (dateTo) p.set("dateTo", dateTo);
    if (account) p.set("account", account);
    if (invoiceNo) p.set("invoiceNo", invoiceNo);
    const res = await fetch(`/api/invoices?${p}`);
    setInvoices(await res.json());
    setLoading(false);
    setLoaded(true);
  }, [dateFrom, dateTo, account, invoiceNo]);

  const handleDelete = async (id: number) => {
    if (!confirm("Delete this invoice?")) return;
    await fetch(`/api/invoices/${id}`, { method: "DELETE" });
    load();
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
      key: "printer",
      label: "Status",
      render: (row: Record<string, unknown>) => (
        <span
          className={`inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium ${
            row.printer === 2
              ? "bg-green-100 text-green-700"
              : "bg-orange-100 text-orange-700"
          }`}
        >
          {row.printer === 2 ? "Printed" : "Unprinted"}
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
            <Button variant="success" size="sm" className="px-2 py-1">
              <Pencil size={13} />
            </Button>
          </Link>
          <Button
            variant="danger"
            size="sm"
            className="px-2 py-1"
            onClick={() => handleDelete(row.id as number)}
          >
            <Trash2 size={13} />
          </Button>
        </div>
      ),
    },
  ];

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <h1 className="text-xl font-bold text-gray-900">
          Invoices{" "}
          <span className="text-sm font-normal text-gray-500">
            Filter By (Date or Account or Invoice No.)
          </span>
        </h1>
        <Link href="/mass-mail">
          <Button size="sm">
            <Send size={14} />
            Send Mass Mail to Customers
          </Button>
        </Link>
      </div>

      {/* Filters */}
      <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
        <div className="flex flex-wrap items-end gap-3">
          <div>
            <label className="block text-xs text-gray-500 mb-1">Date From</label>
            <input
              type="date"
              value={dateFrom}
              onChange={(e) => setDateFrom(e.target.value)}
              className="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label className="block text-xs text-gray-500 mb-1">Date To</label>
            <input
              type="date"
              value={dateTo}
              onChange={(e) => setDateTo(e.target.value)}
              className="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label className="block text-xs text-gray-500 mb-1">Customer Account</label>
            <input
              type="text"
              value={account}
              onChange={(e) => setAccount(e.target.value)}
              placeholder="Account..."
              className="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label className="block text-xs text-gray-500 mb-1">Invoice Number</label>
            <input
              type="text"
              value={invoiceNo}
              onChange={(e) => setInvoiceNo(e.target.value)}
              placeholder="Invoice No..."
              className="border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <Button onClick={load} loading={loading}>View</Button>
        </div>
      </div>

      <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        {!loaded ? (
          <div className="text-center py-10 text-gray-400">Use filters above and click View to load invoices</div>
        ) : loading ? (
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

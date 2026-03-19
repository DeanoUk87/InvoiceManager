"use client";
import { useState, useCallback } from "react";
import { DataTable } from "@/components/DataTable";
import { Button } from "@/components/ui/button";
import { formatDate } from "@/lib/utils";
import { Eye, Pencil, Trash2 } from "lucide-react";
import Link from "next/link";

interface Sale {
  id: number;
  invoiceDate: string | null;
  customerAccount: string;
  jobNumber: string | null;
  postcode2: string | null;
  serviceType: string | null;
  items2: number | null;
  volumeWeight: number | null;
  subTotal: number | null;
  invoiceNumber: string;
}

export default function JobSearchPage() {
  const [sales, setSales] = useState<Sale[]>([]);
  const [loading, setLoading] = useState(false);
  const [loaded, setLoaded] = useState(false);
  const today = new Date().toISOString().split("T")[0];
  const [dateFrom, setDateFrom] = useState(today);
  const [dateTo, setDateTo] = useState(today);

  const load = useCallback(async () => {
    setLoading(true);
    const p = new URLSearchParams();
    if (dateFrom) p.set("dateFrom", dateFrom);
    if (dateTo) p.set("dateTo", dateTo);
    const res = await fetch(`/api/sales?${p}`);
    setSales(await res.json());
    setLoading(false);
    setLoaded(true);
  }, [dateFrom, dateTo]);

  const handleDelete = async (id: number) => {
    if (!confirm("Delete this job?")) return;
    await fetch(`/api/sales/${id}`, { method: "DELETE" });
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
      label: "Account",
      render: (row: Record<string, unknown>) => (
        <span className="font-medium">{String(row.customerAccount)}</span>
      ),
    },
    { key: "jobNumber", label: "Job Number" },
    { key: "postcode2", label: "Postcode" },
    { key: "serviceType", label: "Service Type" },
    {
      key: "items2",
      label: "Items",
      render: (row: Record<string, unknown>) => String(row.items2 ?? "-"),
    },
    {
      key: "volumeWeight",
      label: "Weight",
      render: (row: Record<string, unknown>) => String(row.volumeWeight ?? "-"),
    },
    {
      key: "subTotal",
      label: "Charge",
      render: (row: Record<string, unknown>) =>
        row.subTotal !== null ? `£${Number(row.subTotal).toFixed(2)}` : "-",
    },
    {
      key: "actions",
      label: "Actions",
      sortable: false,
      render: (row: Record<string, unknown>) => (
        <div className="flex items-center gap-1.5">
          <Link href={`/invoices?invoiceNo=${row.invoiceNumber}`}>
            <Button variant="primary" size="sm" className="px-2 py-1">
              <Eye size={13} />
            </Button>
          </Link>
          <Button variant="success" size="sm" className="px-2 py-1">
            <Pencil size={13} />
          </Button>
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
      <h1 className="text-xl font-bold text-gray-900">Job Search</h1>

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
          <Button onClick={load} loading={loading}>Search</Button>
        </div>
      </div>

      <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        {!loaded ? (
          <div className="text-center py-10 text-gray-400">Select a date range and click Search</div>
        ) : loading ? (
          <div className="text-center py-10 text-gray-400">Loading...</div>
        ) : (
          <DataTable
            columns={columns}
            data={sales as unknown as Record<string, unknown>[]}
            searchKeys={["jobNumber", "customerAccount", "postcode2"]}
          />
        )}
      </div>
    </div>
  );
}

"use client";
import { useState, useEffect, useCallback } from "react";
import { DataTable } from "@/components/DataTable";
import { Button } from "@/components/ui/button";
import { Eye, Pencil, Trash2, Plus, UserX } from "lucide-react";
import Link from "next/link";

interface Customer {
  id: number;
  customerAccount: string;
  customerEmail: string | null;
  customerEmailBcc: string | null;
  customerPhone: string | null;
  loginAccess: boolean;
}

export default function CustomersPage() {
  const [customers, setCustomers] = useState<Customer[]>([]);
  const [loading, setLoading] = useState(true);

  const load = useCallback(async () => {
    setLoading(true);
    const res = await fetch("/api/customers");
    const data = await res.json();
    setCustomers(data);
    setLoading(false);
  }, []);

  useEffect(() => { load(); }, [load]);

  const handleDelete = async (id: number) => {
    if (!confirm("Delete this customer?")) return;
    await fetch(`/api/customers/${id}`, { method: "DELETE" });
    load();
  };

  const columns = [
    {
      key: "customerAccount",
      label: "Customer Account",
      render: (row: Record<string, unknown>) => (
        <span className="font-medium text-gray-900">{String(row.customerAccount)}</span>
      ),
    },
    {
      key: "customerEmail",
      label: "Customer Email",
      render: (row: Record<string, unknown>) => (
        <span className="text-gray-600">{String(row.customerEmail ?? "")}</span>
      ),
    },
    {
      key: "customerEmailBcc",
      label: "BCC Email",
      render: (row: Record<string, unknown>) => (
        <span className="text-gray-600">{String(row.customerEmailBcc ?? "")}</span>
      ),
    },
    {
      key: "customerPhone",
      label: "Customer Phone",
      render: (row: Record<string, unknown>) => (
        <span className="text-gray-600">{String(row.customerPhone ?? "")}</span>
      ),
    },
    {
      key: "loginAccess",
      label: "Login Access",
      sortable: false,
      render: (row: Record<string, unknown>) => (
        <span
          className={`inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium ${
            row.loginAccess ? "bg-green-100 text-green-700" : "bg-red-100 text-red-700"
          }`}
        >
          {row.loginAccess ? "Access" : "No Access"}
        </span>
      ),
    },
    {
      key: "actions",
      label: "Actions",
      sortable: false,
      render: (row: Record<string, unknown>) => (
        <div className="flex items-center gap-1.5">
          <Link href={`/customers/${row.id}`}>
            <Button variant="primary" size="sm" className="px-2 py-1">
              <Eye size={13} />
            </Button>
          </Link>
          <Link href={`/customers/${row.id}/edit`}>
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
        <h1 className="text-xl font-bold text-gray-900">Customers</h1>
        <div className="flex gap-2">
          <Link href="/customers/new">
            <Button size="sm">
              <Plus size={14} />
              Add Customer
            </Button>
          </Link>
          <Button
            variant="danger"
            size="sm"
            onClick={async () => {
              if (!confirm("This will DELETE all customers. Are you sure?")) return;
              await fetch("/api/customers/truncate", { method: "POST" });
              load();
            }}
          >
            <UserX size={14} />
            Truncate
          </Button>
        </div>
      </div>

      <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
        {loading ? (
          <div className="text-center py-10 text-gray-400">Loading...</div>
        ) : (
          <DataTable
            columns={columns}
            data={customers as unknown as Record<string, unknown>[]}
            searchKeys={["customerAccount", "customerEmail"]}
          />
        )}
      </div>
    </div>
  );
}

"use client";
import { useState, useEffect, useCallback } from "react";
import { formatDate } from "@/lib/utils";
import { Button } from "@/components/ui/button";
import { Trash2, RefreshCw } from "lucide-react";

interface Upload {
  id: number; filename: string; uploadTs: string;
  rowCount: number | null; status: string | null; createdAt: string | null;
}

export default function UploadedCsvPage() {
  const [uploads, setUploads] = useState<Upload[]>([]);
  const [loading, setLoading] = useState(true);
  const [deleting, setDeleting] = useState<number | null>(null);

  const load = useCallback(async () => {
    setLoading(true);
    // Use the sales API to get uploaded CSVs via a dedicated endpoint
    const res = await fetch("/api/sales?action=uploads");
    if (res.ok) setUploads(await res.json());
    else {
      // Fallback: fetch from settings endpoint
      const res2 = await fetch("/api/settings?action=uploads");
      if (res2.ok) setUploads(await res2.json());
    }
    setLoading(false);
  }, []);

  useEffect(() => { load(); }, [load]);

  const handleDelete = async (id: number, filename: string) => {
    if (!confirm(`Delete "${filename}" and all its imported sales data?\n\nThis cannot be undone.`)) return;
    setDeleting(id);
    const res = await fetch("/api/settings", {
      method: "DELETE",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ action: "delete-upload", uploadId: id }),
    });
    const data = await res.json();
    setDeleting(null);
    if (data.success) load();
    else alert(data.error ?? "Delete failed");
  };

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <h1 className="text-xl font-bold text-gray-900">Uploaded CSV Files</h1>
        <Button variant="outline" size="sm" onClick={load}><RefreshCw size={14} /> Refresh</Button>
      </div>
      <div className="bg-blue-50 border border-blue-100 rounded-xl p-4 text-sm text-blue-800">
        <strong>Note:</strong> Deleting a CSV upload removes the record and all its imported sales rows, allowing you to re-upload the same file.
      </div>
      <div className="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table className="w-full text-sm">
          <thead>
            <tr className="bg-gray-50 border-b border-gray-100">
              <th className="px-4 py-3 text-left font-semibold text-blue-600">#</th>
              <th className="px-4 py-3 text-left font-semibold text-blue-600">Filename</th>
              <th className="px-4 py-3 text-left font-semibold text-blue-600">Rows</th>
              <th className="px-4 py-3 text-left font-semibold text-blue-600">Status</th>
              <th className="px-4 py-3 text-left font-semibold text-blue-600">Uploaded</th>
              <th className="px-4 py-3 text-left font-semibold text-blue-600">Actions</th>
            </tr>
          </thead>
          <tbody>
            {loading ? (
              <tr><td colSpan={6} className="px-4 py-10 text-center text-gray-400">Loading...</td></tr>
            ) : uploads.length === 0 ? (
              <tr><td colSpan={6} className="px-4 py-10 text-center text-gray-400">No CSV files uploaded yet</td></tr>
            ) : uploads.map((u, i) => (
              <tr key={u.id} className="border-b border-gray-50 hover:bg-gray-50">
                <td className="px-4 py-3 text-gray-500">{i + 1}</td>
                <td className="px-4 py-3 font-medium text-gray-900">{u.filename}</td>
                <td className="px-4 py-3 text-gray-600">{(u.rowCount ?? 0).toLocaleString()}</td>
                <td className="px-4 py-3">
                  <span className="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{u.status ?? "uploaded"}</span>
                </td>
                <td className="px-4 py-3 text-gray-500">{u.createdAt ? formatDate(u.createdAt) : "-"}</td>
                <td className="px-4 py-3">
                  <Button variant="danger" size="sm" loading={deleting === u.id}
                    onClick={() => handleDelete(u.id, u.filename)} className="px-2 py-1">
                    <Trash2 size={13} /> Delete
                  </Button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

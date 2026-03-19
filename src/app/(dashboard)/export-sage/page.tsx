"use client";
import { useState } from "react";
import { Button } from "@/components/ui/button";
import { Download } from "lucide-react";

export default function ExportSagePage() {
  const today = new Date().toISOString().split("T")[0];
  const [dateFrom, setDateFrom] = useState(today);
  const [dateTo, setDateTo] = useState(today);

  const handleExport = () => {
    const url = `/api/export-sage?dateFrom=${dateFrom}&dateTo=${dateTo}`;
    window.location.href = url;
  };

  return (
    <div className="space-y-4">
      <h1 className="text-xl font-bold text-gray-900">Export CSV / SAGE</h1>
      <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6 max-w-xl">
        <p className="text-sm text-gray-600 mb-5">
          Export invoice data as a CSV file compatible with SAGE accounting software.
          Select a date range to filter the export.
        </p>
        <div className="flex flex-wrap items-end gap-3 mb-5">
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
        </div>
        <Button onClick={handleExport}>
          <Download size={16} />
          Export CSV / SAGE
        </Button>
      </div>
    </div>
  );
}

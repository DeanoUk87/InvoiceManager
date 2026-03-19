"use client";
import { useState, useMemo } from "react";
import { Search, ChevronUp, ChevronDown } from "lucide-react";
import { cn } from "@/lib/utils";

interface Column<T> {
  key: string;
  label: string;
  render?: (row: T) => React.ReactNode;
  sortable?: boolean;
  className?: string;
}

interface DataTableProps<T extends Record<string, unknown>> {
  columns: Column<T>[];
  data: T[];
  searchKeys?: (keyof T)[];
  pageSize?: number;
}

export function DataTable<T extends Record<string, unknown>>({
  columns,
  data,
  searchKeys = [],
  pageSize = 25,
}: DataTableProps<T>) {
  const [search, setSearch] = useState("");
  const [sortKey, setSortKey] = useState<string | null>(null);
  const [sortDir, setSortDir] = useState<"asc" | "desc">("asc");
  const [page, setPage] = useState(1);
  const [perPage, setPerPage] = useState(pageSize);

  const filtered = useMemo(() => {
    if (!search) return data;
    const q = search.toLowerCase();
    return data.filter((row) =>
      searchKeys.some((k) => String(row[k] ?? "").toLowerCase().includes(q))
    );
  }, [data, search, searchKeys]);

  const sorted = useMemo(() => {
    if (!sortKey) return filtered;
    return [...filtered].sort((a, b) => {
      const av = String(a[sortKey] ?? "");
      const bv = String(b[sortKey] ?? "");
      return sortDir === "asc" ? av.localeCompare(bv) : bv.localeCompare(av);
    });
  }, [filtered, sortKey, sortDir]);

  const totalPages = Math.max(1, Math.ceil(sorted.length / perPage));
  const paged = sorted.slice((page - 1) * perPage, page * perPage);

  const handleSort = (key: string) => {
    if (sortKey === key) {
      setSortDir(sortDir === "asc" ? "desc" : "asc");
    } else {
      setSortKey(key);
      setSortDir("asc");
    }
  };

  return (
    <div>
      <div className="flex items-center justify-between mb-4 gap-4">
        <div className="flex items-center gap-2 text-sm text-gray-600">
          Show
          <select
            value={perPage}
            onChange={(e) => { setPerPage(Number(e.target.value)); setPage(1); }}
            className="border border-gray-300 rounded px-2 py-1 text-sm"
          >
            {[10, 25, 50, 100].map((n) => <option key={n} value={n}>{n}</option>)}
          </select>
          entries
        </div>
        <div className="relative">
          <Search size={14} className="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
          <input
            value={search}
            onChange={(e) => { setSearch(e.target.value); setPage(1); }}
            placeholder="Search..."
            className="pl-8 pr-3 py-1.5 border border-gray-300 rounded-md text-sm w-56 focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
      </div>

      <div className="overflow-x-auto rounded-lg border border-gray-200">
        <table className="w-full text-sm">
          <thead>
            <tr className="bg-gray-50 border-b border-gray-200">
              {columns.map((col) => (
                <th
                  key={col.key}
                  onClick={() => col.sortable !== false && handleSort(col.key)}
                  className={cn(
                    "px-4 py-3 text-left font-semibold text-blue-600 whitespace-nowrap",
                    col.sortable !== false && "cursor-pointer hover:bg-gray-100 select-none",
                    col.className
                  )}
                >
                  <div className="flex items-center gap-1">
                    {col.label}
                    {col.sortable !== false && (
                      <span className="text-gray-400">
                        {sortKey === col.key ? (
                          sortDir === "asc" ? <ChevronUp size={12} /> : <ChevronDown size={12} />
                        ) : (
                          <span className="opacity-30">
                            <ChevronUp size={12} />
                          </span>
                        )}
                      </span>
                    )}
                  </div>
                </th>
              ))}
            </tr>
          </thead>
          <tbody>
            {paged.length === 0 ? (
              <tr>
                <td colSpan={columns.length} className="px-4 py-10 text-center text-gray-400">
                  No records found
                </td>
              </tr>
            ) : (
              paged.map((row, i) => (
                <tr key={i} className="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                  {columns.map((col) => (
                    <td key={col.key} className={cn("px-4 py-3 text-gray-700", col.className)}>
                      {col.render ? col.render(row) : String(row[col.key] ?? "-")}
                    </td>
                  ))}
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>

      <div className="flex items-center justify-between mt-4 text-sm text-gray-600">
        <span>
          Showing {sorted.length === 0 ? 0 : (page - 1) * perPage + 1} to{" "}
          {Math.min(page * perPage, sorted.length)} of {sorted.length} entries
        </span>
        <div className="flex items-center gap-1">
          <button
            onClick={() => setPage(Math.max(1, page - 1))}
            disabled={page === 1}
            className="px-3 py-1.5 border border-gray-300 rounded hover:bg-gray-100 disabled:opacity-40"
          >
            Previous
          </button>
          {Array.from({ length: Math.min(totalPages, 7) }, (_, i) => {
            const p = i + 1;
            return (
              <button
                key={p}
                onClick={() => setPage(p)}
                className={cn(
                  "px-3 py-1.5 border rounded",
                  p === page
                    ? "bg-blue-600 text-white border-blue-600"
                    : "border-gray-300 hover:bg-gray-100"
                )}
              >
                {p}
              </button>
            );
          })}
          <button
            onClick={() => setPage(Math.min(totalPages, page + 1))}
            disabled={page === totalPages}
            className="px-3 py-1.5 border border-gray-300 rounded hover:bg-gray-100 disabled:opacity-40"
          >
            Next
          </button>
        </div>
      </div>
    </div>
  );
}

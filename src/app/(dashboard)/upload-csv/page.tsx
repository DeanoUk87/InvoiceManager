"use client";
import { useState, useRef } from "react";
import { Button } from "@/components/ui/button";
import { Upload, CheckCircle, XCircle, FileText } from "lucide-react";

export default function UploadCsvPage() {
  const [file, setFile] = useState<File | null>(null);
  const [uploading, setUploading] = useState(false);
  const [result, setResult] = useState<{ success?: boolean; message?: string; rowCount?: number } | null>(null);
  const fileRef = useRef<HTMLInputElement>(null);

  const handleUpload = async () => {
    if (!file) return;
    setUploading(true);
    setResult(null);
    const form = new FormData();
    form.append("file", file);
    const res = await fetch("/api/upload", { method: "POST", body: form });
    const data = await res.json();
    setUploading(false);
    if (data.success) {
      setResult({ success: true, message: `Successfully imported ${data.rowCount} rows.`, rowCount: data.rowCount });
      setFile(null);
      if (fileRef.current) fileRef.current.value = "";
    } else {
      setResult({ success: false, message: data.error ?? "Upload failed" });
    }
  };

  return (
    <div className="space-y-4">
      <h1 className="text-xl font-bold text-gray-900">Upload CSV</h1>
      <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-8 max-w-xl">
        <div className="space-y-5">
          <div>
            <p className="text-sm text-gray-600 mb-4">
              Upload the CSV file exported from your system. The file will be parsed and imported into
              the sales database, ready for invoice generation.
            </p>
            <div
              className="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:border-blue-400 transition-colors cursor-pointer"
              onClick={() => fileRef.current?.click()}
            >
              <Upload size={36} className="mx-auto text-gray-400 mb-3" />
              <p className="text-sm font-medium text-gray-700">
                {file ? file.name : "Click to select CSV file"}
              </p>
              <p className="text-xs text-gray-400 mt-1">
                {file ? `${(file.size / 1024).toFixed(1)} KB` : "Supports .csv files"}
              </p>
              <input
                ref={fileRef}
                type="file"
                accept=".csv"
                className="hidden"
                onChange={(e) => setFile(e.target.files?.[0] ?? null)}
              />
            </div>
          </div>

          {file && (
            <div className="flex items-center gap-3 p-3 bg-blue-50 rounded-lg border border-blue-100">
              <FileText size={20} className="text-blue-600 shrink-0" />
              <div className="flex-1 min-w-0">
                <p className="text-sm font-medium text-gray-900 truncate">{file.name}</p>
                <p className="text-xs text-gray-500">{(file.size / 1024).toFixed(1)} KB</p>
              </div>
            </div>
          )}

          {result && (
            <div
              className={`flex items-start gap-3 p-4 rounded-lg border ${
                result.success
                  ? "bg-green-50 border-green-200"
                  : "bg-red-50 border-red-200"
              }`}
            >
              {result.success ? (
                <CheckCircle size={18} className="text-green-600 shrink-0 mt-0.5" />
              ) : (
                <XCircle size={18} className="text-red-600 shrink-0 mt-0.5" />
              )}
              <p className={`text-sm ${result.success ? "text-green-700" : "text-red-700"}`}>
                {result.message}
              </p>
            </div>
          )}

          <Button
            onClick={handleUpload}
            disabled={!file}
            loading={uploading}
            className="w-full"
          >
            <Upload size={16} />
            {uploading ? "Uploading..." : "Upload CSV"}
          </Button>
        </div>
      </div>
    </div>
  );
}

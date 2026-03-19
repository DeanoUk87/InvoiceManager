import { db } from "@/db";
import { uploadedCsv } from "@/db/schema";
import { formatDate } from "@/lib/utils";
import { desc } from "drizzle-orm";

export const dynamic = "force-dynamic";

export default async function UploadedCsvPage() {
  const uploads = await db.select().from(uploadedCsv).orderBy(desc(uploadedCsv.createdAt));
  return (
    <div className="space-y-4">
      <h1 className="text-xl font-bold text-gray-900">Uploaded CSV Files</h1>
      <div className="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table className="w-full text-sm">
          <thead>
            <tr className="bg-gray-50 border-b border-gray-100">
              <th className="px-4 py-3 text-left font-semibold text-blue-600">#</th>
              <th className="px-4 py-3 text-left font-semibold text-blue-600">Filename</th>
              <th className="px-4 py-3 text-left font-semibold text-blue-600">Rows</th>
              <th className="px-4 py-3 text-left font-semibold text-blue-600">Status</th>
              <th className="px-4 py-3 text-left font-semibold text-blue-600">Uploaded</th>
            </tr>
          </thead>
          <tbody>
            {uploads.length === 0 ? (
              <tr><td colSpan={5} className="px-4 py-10 text-center text-gray-400">No CSV files uploaded yet</td></tr>
            ) : uploads.map((u, i) => (
              <tr key={u.id} className="border-b border-gray-50 hover:bg-gray-50">
                <td className="px-4 py-3 text-gray-500">{i + 1}</td>
                <td className="px-4 py-3 font-medium text-gray-900">{u.filename}</td>
                <td className="px-4 py-3 text-gray-600">{(u.rowCount??0).toLocaleString()}</td>
                <td className="px-4 py-3"><span className="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">{u.status}</span></td>
                <td className="px-4 py-3 text-gray-500">{u.createdAt ? formatDate(u.createdAt.toISOString()) : "-"}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

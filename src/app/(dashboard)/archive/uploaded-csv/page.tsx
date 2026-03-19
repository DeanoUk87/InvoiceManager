import { db } from "@/db";
import { uploadedCsv } from "@/db/schema";
import { formatDate } from "@/lib/utils";
import { eq, desc } from "drizzle-orm";

export const dynamic = "force-dynamic";

export default async function UploadedCsvArchivePage() {
  const uploads = await db.select().from(uploadedCsv).where(eq(uploadedCsv.status, "archived")).orderBy(desc(uploadedCsv.createdAt));
  return (
    <div className="space-y-4">
      <h1 className="text-xl font-bold text-gray-900">Uploaded CSV (Archived)</h1>
      <div className="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table className="w-full text-sm">
          <thead>
            <tr className="bg-gray-50 border-b border-gray-100">
              <th className="px-4 py-3 text-left font-semibold text-blue-600">#</th>
              <th className="px-4 py-3 text-left font-semibold text-blue-600">Filename</th>
              <th className="px-4 py-3 text-left font-semibold text-blue-600">Rows</th>
              <th className="px-4 py-3 text-left font-semibold text-blue-600">Uploaded</th>
            </tr>
          </thead>
          <tbody>
            {uploads.length === 0 ? (
              <tr><td colSpan={4} className="px-4 py-10 text-center text-gray-400">No archived CSV files</td></tr>
            ) : uploads.map((u, i) => (
              <tr key={u.id} className="border-b border-gray-50 hover:bg-gray-50">
                <td className="px-4 py-3 text-gray-500">{i + 1}</td>
                <td className="px-4 py-3 font-medium">{u.filename}</td>
                <td className="px-4 py-3">{u.rowCount}</td>
                <td className="px-4 py-3 text-gray-500">{u.createdAt ? formatDate(u.createdAt.toISOString()) : "-"}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

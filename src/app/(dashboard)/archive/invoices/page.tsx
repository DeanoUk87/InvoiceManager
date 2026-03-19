import { prisma } from "@/lib/prisma";
import { formatDate } from "@/lib/utils";

export const dynamic = "force-dynamic";

export default async function InvoicesArchivePage() {
  const invoices = await prisma.invoiceArchive.findMany({
    orderBy: { archivedAt: "desc" },
    take: 500,
  });

  return (
    <div className="space-y-4">
      <h1 className="text-xl font-bold text-gray-900">Invoices (Archived)</h1>
      <div className="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table className="w-full text-sm">
          <thead>
            <tr className="bg-gray-50 border-b border-gray-100">
              <th className="px-4 py-3 text-left font-semibold text-blue-600">Customer Account</th>
              <th className="px-4 py-3 text-left font-semibold text-blue-600">Invoice Number</th>
              <th className="px-4 py-3 text-left font-semibold text-blue-600">Invoice Date</th>
              <th className="px-4 py-3 text-left font-semibold text-blue-600">Due Date</th>
              <th className="px-4 py-3 text-left font-semibold text-blue-600">Status</th>
              <th className="px-4 py-3 text-left font-semibold text-blue-600">Archived</th>
            </tr>
          </thead>
          <tbody>
            {invoices.length === 0 ? (
              <tr><td colSpan={6} className="px-4 py-10 text-center text-gray-400">No archived invoices</td></tr>
            ) : (
              invoices.map((inv) => (
                <tr key={inv.id} className="border-b border-gray-50 hover:bg-gray-50">
                  <td className="px-4 py-3 font-medium">{inv.customerAccount}</td>
                  <td className="px-4 py-3">{inv.invoiceNumber}</td>
                  <td className="px-4 py-3">{formatDate(inv.invoiceDate)}</td>
                  <td className="px-4 py-3">{formatDate(inv.dueDate)}</td>
                  <td className="px-4 py-3">
                    <span className="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                      {inv.printer === 2 ? "Printed" : "Unprinted"}
                    </span>
                  </td>
                  <td className="px-4 py-3 text-gray-500">{formatDate(inv.archivedAt.toISOString())}</td>
                </tr>
              ))
            )}
          </tbody>
        </table>
      </div>
    </div>
  );
}

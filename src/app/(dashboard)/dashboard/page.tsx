import { db } from "@/db";
import { customers, invoices } from "@/db/schema";
import { formatDate } from "@/lib/utils";
import Link from "next/link";
import { Users, FileText, AlertCircle, CheckCircle } from "lucide-react";
import { eq, inArray, desc } from "drizzle-orm";
import { sql } from "drizzle-orm";

export const dynamic = "force-dynamic";

export default async function DashboardPage() {
  const [customerCount, invoiceCount, unprintedCount, printedCount, latestInvoices] = await Promise.all([
    db.select({ count: sql<number>`count(*)` }).from(customers).then(r => r[0].count),
    db.select({ count: sql<number>`count(*)` }).from(invoices).then(r => r[0].count),
    db.select({ count: sql<number>`count(*)` }).from(invoices).where(inArray(invoices.printer, [0, 1])).then(r => r[0].count),
    db.select({ count: sql<number>`count(*)` }).from(invoices).where(eq(invoices.printer, 2)).then(r => r[0].count),
    db.select().from(invoices).orderBy(desc(invoices.id)).limit(10),
  ]);

  const stats = [
    { label: "Total Customers", value: Number(customerCount).toLocaleString(), icon: Users, color: "text-purple-600", bg: "bg-purple-50", href: "/customers" },
    { label: "Total Invoices", value: Number(invoiceCount).toLocaleString(), icon: FileText, color: "text-yellow-600", bg: "bg-yellow-50", href: "/invoices" },
    { label: "Printed Invoices", value: Number(printedCount).toLocaleString(), icon: CheckCircle, color: "text-green-600", bg: "bg-green-50", href: "/printed" },
    { label: "Unprinted Invoices", value: Number(unprintedCount).toLocaleString(), icon: AlertCircle, color: "text-red-600", bg: "bg-red-50", href: "/unprinted" },
  ];

  return (
    <div className="space-y-6">
      <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {stats.map(stat => (
          <Link key={stat.label} href={stat.href}>
            <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:shadow-md transition-shadow cursor-pointer">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-sm text-gray-500">{stat.label}</p>
                  <p className="text-3xl font-bold text-gray-900 mt-1">{stat.value}</p>
                </div>
                <div className={`w-12 h-12 rounded-xl ${stat.bg} flex items-center justify-center`}>
                  <stat.icon className={stat.color} size={22} />
                </div>
              </div>
            </div>
          </Link>
        ))}
      </div>
      <div className="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div className="px-6 py-4 border-b border-gray-100">
          <h2 className="text-base font-semibold text-gray-900">Latest Invoices</h2>
        </div>
        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="bg-gray-50 border-b border-gray-100">
                <th className="px-4 py-3 text-left font-semibold text-blue-600">Customer Account</th>
                <th className="px-4 py-3 text-left font-semibold text-blue-600">Invoice Number</th>
                <th className="px-4 py-3 text-left font-semibold text-blue-600">Invoice Date</th>
                <th className="px-4 py-3 text-left font-semibold text-blue-600">Due Date</th>
                <th className="px-4 py-3 text-left font-semibold text-blue-600">Status</th>
                <th className="px-4 py-3 text-left font-semibold text-blue-600">Preview</th>
              </tr>
            </thead>
            <tbody>
              {latestInvoices.length === 0 ? (
                <tr><td colSpan={6} className="px-4 py-8 text-center text-gray-400">No invoices yet</td></tr>
              ) : latestInvoices.map(inv => (
                <tr key={inv.id} className="border-b border-gray-50 hover:bg-gray-50">
                  <td className="px-4 py-3 font-medium text-gray-900">{inv.customerAccount}</td>
                  <td className="px-4 py-3 text-gray-700">{inv.invoiceNumber}</td>
                  <td className="px-4 py-3 text-gray-600">{formatDate(inv.invoiceDate)}</td>
                  <td className="px-4 py-3 text-gray-600">{formatDate(inv.dueDate)}</td>
                  <td className="px-4 py-3">
                    <span className={`inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium ${inv.printer === 2 ? "bg-green-100 text-green-700" : "bg-orange-100 text-orange-700"}`}>
                      {inv.printer === 2 ? "Printed" : "Unprinted"}
                    </span>
                  </td>
                  <td className="px-4 py-3">
                    <Link href={`/invoices/${inv.id}`} className="inline-flex px-3 py-1 border border-gray-300 rounded text-xs hover:bg-gray-100 transition-colors">Invoice</Link>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
}

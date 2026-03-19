import { auth } from "@/lib/auth";
import { db } from "@/db";
import { invoices, sales } from "@/db/schema";
import { eq, desc } from "drizzle-orm";
import { formatDate, calcDueDate } from "@/lib/utils";
import Link from "next/link";
import { redirect } from "next/navigation";
import { FileText, Download } from "lucide-react";

export const dynamic = "force-dynamic";

export default async function CustomerInvoicesPage() {
  const session = await auth();
  if (!session || session.user.role !== "customer") redirect("/login");

  // customer's account = their username
  const customerAccount = session.user.name ?? "";

  const customerInvoices = await db.select().from(invoices)
    .where(eq(invoices.customerAccount, customerAccount))
    .orderBy(desc(invoices.invoiceDate))
    .limit(200);

  // Get numb2 for due dates
  const numb2Map: Record<number, number | null> = {};
  await Promise.all(
    customerInvoices.filter(i => !i.dueDate).map(async (inv) => {
      const [s] = await db.select({ numb2: sales.numb2 }).from(sales)
        .where(eq(sales.invoiceNumber, inv.invoiceNumber)).limit(1);
      numb2Map[inv.id] = s?.numb2 ?? null;
    })
  );

  return (
    <div className="space-y-5">
      <div>
        <h1 className="text-xl font-bold text-gray-900">My Invoices</h1>
        <p className="text-sm text-gray-500 mt-1">
          Account: <span className="font-semibold text-blue-600">{customerAccount}</span>
          {" · "}{customerInvoices.length} invoice{customerInvoices.length !== 1 ? "s" : ""}
        </p>
      </div>

      <div className="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <table className="w-full text-sm">
          <thead>
            <tr className="bg-blue-600 text-white">
              <th className="px-4 py-3 text-left font-semibold text-xs uppercase tracking-wide">Invoice No.</th>
              <th className="px-4 py-3 text-left font-semibold text-xs uppercase tracking-wide">Invoice Date</th>
              <th className="px-4 py-3 text-left font-semibold text-xs uppercase tracking-wide">Due Date</th>
              <th className="px-4 py-3 text-left font-semibold text-xs uppercase tracking-wide">Status</th>
              <th className="px-4 py-3 text-left font-semibold text-xs uppercase tracking-wide">Download</th>
            </tr>
          </thead>
          <tbody>
            {customerInvoices.length === 0 ? (
              <tr>
                <td colSpan={5} className="px-4 py-12 text-center text-gray-400">
                  <FileText size={32} className="mx-auto mb-2 text-gray-300" />
                  No invoices found for your account
                </td>
              </tr>
            ) : customerInvoices.map((inv, i) => (
              <tr key={inv.id} className={`border-b border-gray-50 hover:bg-blue-50 transition-colors ${i % 2 === 0 ? "bg-white" : "bg-gray-50/50"}`}>
                <td className="px-4 py-3 font-semibold text-blue-600">#{inv.invoiceNumber}</td>
                <td className="px-4 py-3 text-gray-700">{formatDate(inv.invoiceDate)}</td>
                <td className="px-4 py-3 text-gray-600">
                  {inv.dueDate ? formatDate(inv.dueDate) : calcDueDate(inv.invoiceDate, numb2Map[inv.id])}
                </td>
                <td className="px-4 py-3">
                  <span className={`inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium ${
                    inv.printer === 2 ? "bg-green-100 text-green-700" : "bg-orange-100 text-orange-700"
                  }`}>
                    {inv.printer === 2 ? "Sent" : "Pending"}
                  </span>
                </td>
                <td className="px-4 py-3">
                  <div className="flex items-center gap-2">
                    <Link href={`/my-invoices/${inv.id}`}
                      className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors">
                      <FileText size={12} /> View
                    </Link>
                    <a href={`/api/invoices/${inv.id}/excel`}
                      className="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-medium rounded-lg transition-colors">
                      <Download size={12} /> Excel
                    </a>
                    <a href={`/api/invoices/${inv.id}/pdf`} target="_blank"
                      className="inline-flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-medium rounded-lg transition-colors">
                      <Download size={12} /> PDF
                    </a>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

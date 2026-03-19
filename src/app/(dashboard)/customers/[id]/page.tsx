import { prisma } from "@/lib/prisma";
import { notFound } from "next/navigation";
import Link from "next/link";
import { ArrowLeft, Pencil } from "lucide-react";

export default async function CustomerViewPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = await params;
  const customer = await prisma.customer.findUnique({ where: { id: parseInt(id) } });
  if (!customer) notFound();

  const invoices = await prisma.invoice.findMany({
    where: { customerAccount: customer.customerAccount },
    orderBy: { invoiceDate: "desc" },
    take: 20,
  });

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <h1 className="text-xl font-bold text-gray-900">Customer: {customer.customerAccount}</h1>
        <div className="flex gap-2">
          <Link href={`/customers/${id}/edit`}>
            <button className="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition-colors">
              <Pencil size={14} /> Edit
            </button>
          </Link>
          <Link href="/customers">
            <button className="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-md transition-colors">
              <ArrowLeft size={14} /> Go Back
            </button>
          </Link>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
          <h2 className="text-base font-semibold text-gray-900 mb-4">Customer Details</h2>
          <dl className="space-y-3">
            {[
              ["Account", customer.customerAccount],
              ["Email", customer.customerEmail ?? "-"],
              ["BCC Email", customer.customerEmailBcc ?? "-"],
              ["Phone", customer.customerPhone ?? "-"],
              ["Terms of Payment", customer.termsOfPayment ?? "-"],
              ["Login Access", customer.loginAccess ? "Enabled" : "Disabled"],
            ].map(([label, value]) => (
              <div key={label} className="flex gap-4">
                <dt className="w-36 text-sm text-gray-500 shrink-0">{label}</dt>
                <dd className="text-sm font-medium text-gray-900">{value}</dd>
              </div>
            ))}
          </dl>
          {customer.customerMessage && (
            <div className="mt-4 pt-4 border-t border-gray-100">
              <p className="text-xs text-gray-500 mb-1">Customer Message</p>
              <p className="text-sm text-gray-700">{customer.customerMessage}</p>
            </div>
          )}
        </div>

        <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
          <h2 className="text-base font-semibold text-gray-900 mb-4">
            Recent Invoices ({invoices.length})
          </h2>
          {invoices.length === 0 ? (
            <p className="text-sm text-gray-400">No invoices found</p>
          ) : (
            <div className="space-y-2">
              {invoices.map((inv) => (
                <Link key={inv.id} href={`/invoices/${inv.id}`}>
                  <div className="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 border border-gray-100 cursor-pointer">
                    <div>
                      <p className="text-sm font-medium text-gray-900">#{inv.invoiceNumber}</p>
                      <p className="text-xs text-gray-500">{inv.invoiceDate}</p>
                    </div>
                    <span
                      className={`text-xs px-2 py-0.5 rounded-full font-medium ${
                        inv.printer === 2 ? "bg-green-100 text-green-700" : "bg-orange-100 text-orange-700"
                      }`}
                    >
                      {inv.printer === 2 ? "Printed" : "Unprinted"}
                    </span>
                  </div>
                </Link>
              ))}
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

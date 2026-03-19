"use client";
import { useState, useEffect, use } from "react";
import { useRouter } from "next/navigation";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { ArrowLeft } from "lucide-react";
import Link from "next/link";

export default function EditCustomerPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = use(params);
  const router = useRouter();
  const [saving, setSaving] = useState(false);
  const [form, setForm] = useState({
    customerAccount: "",
    customerEmail: "",
    customerEmailBcc: "",
    customerPhone: "",
    termsOfPayment: "",
    customerMessage: "",
  });

  useEffect(() => {
    fetch(`/api/customers/${id}`)
      .then((r) => r.json())
      .then((d) => setForm({
        customerAccount: d.customerAccount ?? "",
        customerEmail: d.customerEmail ?? "",
        customerEmailBcc: d.customerEmailBcc ?? "",
        customerPhone: d.customerPhone ?? "",
        termsOfPayment: d.termsOfPayment ?? "",
        customerMessage: d.customerMessage ?? "",
      }));
  }, [id]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);
    await fetch(`/api/customers/${id}`, {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(form),
    });
    setSaving(false);
    router.push("/customers");
  };

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <h1 className="text-xl font-bold text-gray-900">Customers - Update</h1>
        <Link href="/customers">
          <Button variant="outline" size="sm">
            <ArrowLeft size={14} /> Go Back
          </Button>
        </Link>
      </div>
      <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6 max-w-2xl">
        <form onSubmit={handleSubmit} className="space-y-4">
          <Input
            label="Customer Account"
            value={form.customerAccount}
            onChange={(e) => setForm({ ...form, customerAccount: e.target.value })}
            disabled
          />
          <Input
            label="Customer Email"
            type="email"
            value={form.customerEmail}
            onChange={(e) => setForm({ ...form, customerEmail: e.target.value })}
          />
          <Input
            label="BCC Email"
            type="email"
            value={form.customerEmailBcc}
            onChange={(e) => setForm({ ...form, customerEmailBcc: e.target.value })}
          />
          <Input
            label="Customer Phone"
            value={form.customerPhone}
            onChange={(e) => setForm({ ...form, customerPhone: e.target.value })}
          />
          <Input
            label="Terms of Payment"
            value={form.termsOfPayment}
            onChange={(e) => setForm({ ...form, termsOfPayment: e.target.value })}
          />
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">
              Customer Specific Message
            </label>
            <div className="border border-gray-300 rounded-md overflow-hidden">
              <div className="bg-gray-50 border-b border-gray-200 px-3 py-1.5 flex items-center gap-2 text-gray-500 text-xs flex-wrap">
                <button type="button" className="hover:text-gray-900 font-bold px-1">B</button>
                <button type="button" className="hover:text-gray-900 italic px-1">I</button>
                <button type="button" className="hover:text-gray-900 px-1">H1</button>
                <button type="button" className="hover:text-gray-900 px-1">H2</button>
                <button type="button" className="hover:text-gray-900 line-through px-1">S</button>
              </div>
              <textarea
                value={form.customerMessage}
                onChange={(e) => setForm({ ...form, customerMessage: e.target.value })}
                rows={5}
                className="w-full px-3 py-2 text-sm focus:outline-none focus:ring-0 resize-none"
              />
            </div>
          </div>
          <div className="flex gap-3 pt-2">
            <Button type="submit" loading={saving}>Update Customer</Button>
            <Link href="/customers">
              <Button type="button" variant="outline">Cancel</Button>
            </Link>
          </div>
        </form>
      </div>
    </div>
  );
}

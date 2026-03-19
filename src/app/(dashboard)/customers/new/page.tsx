"use client";
import { useState } from "react";
import { useRouter } from "next/navigation";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { ArrowLeft } from "lucide-react";
import Link from "next/link";

export default function NewCustomerPage() {
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

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);
    await fetch("/api/customers", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(form),
    });
    setSaving(false);
    router.push("/customers");
  };

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <h1 className="text-xl font-bold text-gray-900">Add Customer</h1>
        <Link href="/customers">
          <Button variant="outline" size="sm">
            <ArrowLeft size={14} /> Go Back
          </Button>
        </Link>
      </div>
      <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6 max-w-2xl">
        <form onSubmit={handleSubmit} className="space-y-4">
          <Input
            label="Customer Account *"
            required
            value={form.customerAccount}
            onChange={(e) => setForm({ ...form, customerAccount: e.target.value })}
            placeholder="e.g. CUST001"
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
            placeholder="e.g. 30 days from document date"
          />
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Customer Specific Message</label>
            <textarea
              value={form.customerMessage}
              onChange={(e) => setForm({ ...form, customerMessage: e.target.value })}
              rows={4}
              className="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div className="flex gap-3 pt-2">
            <Button type="submit" loading={saving}>Save Customer</Button>
            <Link href="/customers">
              <Button type="button" variant="outline">Cancel</Button>
            </Link>
          </div>
        </form>
      </div>
    </div>
  );
}

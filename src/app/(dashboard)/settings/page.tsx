"use client";
import { useState, useEffect } from "react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { CheckCircle } from "lucide-react";

interface Settings {
  companyName: string;
  companyAddress1: string;
  companyAddress2: string;
  city: string;
  postcode: string;
  country: string;
  phone: string;
  cemail: string;
  website: string;
  vatNumber: string;
  invoiceDueDate: number;
  messageTitle: string;
  defaultMessage: string;
  defaultMessage2: string;
  sendLimit: number;
  fuelSurchargePercent: number;
  resourcingSurchargePercent: number;
  vatPercent: number;
}

export default function SettingsPage() {
  const [form, setForm] = useState<Settings>({
    companyName: "", companyAddress1: "", companyAddress2: "", city: "", postcode: "",
    country: "", phone: "", cemail: "", website: "", vatNumber: "",
    invoiceDueDate: 30, messageTitle: "", defaultMessage: "", defaultMessage2: "",
    sendLimit: 50, fuelSurchargePercent: 3.5, resourcingSurchargePercent: 0, vatPercent: 20,
  });
  const [saving, setSaving] = useState(false);
  const [saved, setSaved] = useState(false);

  useEffect(() => {
    fetch("/api/settings").then((r) => r.json()).then((d) => {
      if (d) setForm((prev) => ({ ...prev, ...d }));
    });
  }, []);

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault();
    setSaving(true);
    await fetch("/api/settings", {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(form),
    });
    setSaving(false);
    setSaved(true);
    setTimeout(() => setSaved(false), 3000);
  };

  const field = (key: keyof Settings, label: string, type = "text") => (
    <Input
      label={label}
      type={type}
      value={String(form[key] ?? "")}
      onChange={(e) =>
        setForm({ ...form, [key]: type === "number" ? parseFloat(e.target.value) || 0 : e.target.value })
      }
    />
  );

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <h1 className="text-xl font-bold text-gray-900">Invoice Settings</h1>
        {saved && (
          <div className="flex items-center gap-2 text-sm text-green-600">
            <CheckCircle size={16} /> Saved successfully
          </div>
        )}
      </div>

      <form onSubmit={handleSave} className="space-y-5">
        {/* Company Details */}
        <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
          <h2 className="text-base font-semibold text-gray-900 mb-4">Company Details</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
            {field("companyName", "Company Name")}
            {field("cemail", "Company Email", "email")}
            {field("companyAddress1", "Address Line 1")}
            {field("companyAddress2", "Address Line 2")}
            {field("city", "City")}
            {field("postcode", "Postcode")}
            {field("country", "Country")}
            {field("phone", "Phone")}
            {field("website", "Website")}
            {field("vatNumber", "VAT Number")}
          </div>
        </div>

        {/* Invoice Settings */}
        <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
          <h2 className="text-base font-semibold text-gray-900 mb-2">Invoice Configuration</h2>
          <p className="text-sm text-gray-500 mb-4">
            Fuel surcharge %, VAT %, and due days are taken directly from the uploaded CSV file each time.
          </p>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-sm">
            {field("sendLimit", "Bulk Send Limit (per batch)", "number")}
          </div>
        </div>

        {/* Email Templates */}
        <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
          <h2 className="text-base font-semibold text-gray-900 mb-4">Email Templates</h2>
          <div className="space-y-4">
            {field("messageTitle", "Email Subject (use {invoice_number} for invoice no.)")}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Default Message (short)</label>
              <textarea
                value={form.defaultMessage}
                onChange={(e) => setForm({ ...form, defaultMessage: e.target.value })}
                rows={3}
                className="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">
                Email Body (use {"{invoice_number}"} placeholder)
              </label>
              <textarea
                value={form.defaultMessage2}
                onChange={(e) => setForm({ ...form, defaultMessage2: e.target.value })}
                rows={6}
                className="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>
          </div>
        </div>

        {/* SMTP */}
        <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
          <h2 className="text-base font-semibold text-gray-900 mb-2">SMTP Configuration</h2>
          <p className="text-sm text-gray-500 mb-4">
            Configure SMTP via environment variables: <code className="bg-gray-100 px-1 rounded text-xs">SMTP_HOST</code>,{" "}
            <code className="bg-gray-100 px-1 rounded text-xs">SMTP_PORT</code>,{" "}
            <code className="bg-gray-100 px-1 rounded text-xs">SMTP_USER</code>,{" "}
            <code className="bg-gray-100 px-1 rounded text-xs">SMTP_PASS</code> in your .env file.
          </p>
        </div>

        <Button type="submit" loading={saving} size="lg">
          Save Settings
        </Button>
      </form>
    </div>
  );
}

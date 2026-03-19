"use client";
import { useState, useEffect, use } from "react";
import { useRouter } from "next/navigation";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { ArrowLeft, Key, Eye, EyeOff, CheckCircle, XCircle, RefreshCw, UserCheck, UserX } from "lucide-react";
import Link from "next/link";

function generatePassword(len = 12) {
  const chars = "ABCDEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$";
  return Array.from({ length: len }, () => chars[Math.floor(Math.random() * chars.length)]).join("");
}

export default function CustomerLoginPage({ params }: { params: Promise<{ id: string }> }) {
  const { id } = use(params);
  const router = useRouter();
  const [customer, setCustomer] = useState<{ customerAccount: string; customerEmail: string | null; loginAccess: boolean } | null>(null);
  const [existing, setExisting] = useState<{ email: string } | null>(null);
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [showPw, setShowPw] = useState(false);
  const [saving, setSaving] = useState(false);
  const [disabling, setDisabling] = useState(false);
  const [result, setResult] = useState<{ success: boolean; message: string } | null>(null);

  useEffect(() => {
    fetch(`/api/customers/${id}`)
      .then(r => r.json())
      .then(d => {
        setCustomer(d);
        setEmail(d.customerEmail ?? "");
        // Check if portal login exists
        return fetch(`/api/customer-login?account=${encodeURIComponent(d.customerAccount)}`);
      })
      .then(r => r.json())
      .then(d => {
        if (d.hasLogin) {
          setExisting(d.user);
          setEmail(d.user.email);
        }
      });
  }, [id]);

  const handleSave = async () => {
    if (!customer) return;
    setSaving(true); setResult(null);
    const res = await fetch("/api/customer-login", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ customerAccount: customer.customerAccount, email, password }),
    });
    const data = await res.json();
    setSaving(false);
    setResult({ success: data.success, message: data.message ?? data.error });
    if (data.success) setExisting({ email });
  };

  const handleDisable = async () => {
    if (!customer || !confirm("Disable portal login for this customer?")) return;
    setDisabling(true); setResult(null);
    const res = await fetch("/api/customer-login", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ customerAccount: customer.customerAccount, action: "disable" }),
    });
    const data = await res.json();
    setDisabling(false);
    setResult({ success: data.success, message: data.message ?? data.error });
    if (data.success) { setExisting(null); setPassword(""); }
  };

  if (!customer) return <div className="flex items-center justify-center h-64 text-gray-400">Loading...</div>;

  return (
    <div className="space-y-4 max-w-lg">
      <div className="flex items-center justify-between">
        <h1 className="text-xl font-bold text-gray-900">Customer Portal Login</h1>
        <Link href="/customers">
          <Button variant="outline" size="sm"><ArrowLeft size={14} /> Back to Customers</Button>
        </Link>
      </div>

      {/* Status banner */}
      <div className={`flex items-center gap-3 p-4 rounded-xl border ${existing ? "bg-green-50 border-green-200" : "bg-gray-50 border-gray-200"}`}>
        {existing
          ? <UserCheck size={20} className="text-green-600 shrink-0" />
          : <UserX size={20} className="text-gray-400 shrink-0" />
        }
        <div>
          <p className="text-sm font-semibold text-gray-900">
            Account: <span className="text-blue-600">{customer.customerAccount}</span>
          </p>
          <p className="text-xs text-gray-500 mt-0.5">
            {existing ? `Portal login active — ${existing.email}` : "No portal login — customer cannot log in"}
          </p>
        </div>
      </div>

      <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
        <h2 className="text-sm font-semibold text-gray-700">
          {existing ? "Update Portal Login" : "Create Portal Login"}
        </h2>
        <p className="text-xs text-gray-500">
          The customer will use this email and password to log in. They will only be able to view and
          download their own invoices.
        </p>

        <Input
          label="Login Email *"
          type="email"
          value={email}
          onChange={e => setEmail(e.target.value)}
          placeholder="customer@email.com"
        />

        <div>
          <label className="block text-sm font-medium text-gray-700 mb-1">
            Password * {existing && <span className="text-gray-400 font-normal">(set new password)</span>}
          </label>
          <div className="flex gap-2">
            <div className="relative flex-1">
              <input
                type={showPw ? "text" : "password"}
                value={password}
                onChange={e => setPassword(e.target.value)}
                placeholder="Enter or generate password"
                className="w-full border border-gray-300 rounded-md px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
              <button type="button" onClick={() => setShowPw(!showPw)}
                className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                {showPw ? <EyeOff size={14} /> : <Eye size={14} />}
              </button>
            </div>
            <Button variant="outline" size="sm" type="button"
              onClick={() => { const pw = generatePassword(); setPassword(pw); setShowPw(true); }}>
              <RefreshCw size={13} /> Generate
            </Button>
          </div>
          {password && showPw && (
            <p className="mt-1.5 text-xs text-gray-500 bg-gray-50 px-3 py-1.5 rounded border font-mono break-all">
              {password}
            </p>
          )}
        </div>

        {result && (
          <div className={`flex items-start gap-2 p-3 rounded-lg border text-sm ${result.success ? "bg-green-50 border-green-200 text-green-700" : "bg-red-50 border-red-200 text-red-700"}`}>
            {result.success ? <CheckCircle size={15} className="mt-0.5 shrink-0" /> : <XCircle size={15} className="mt-0.5 shrink-0" />}
            {result.message}
          </div>
        )}

        <div className="flex gap-3 pt-1">
          <Button onClick={handleSave} loading={saving} disabled={!email || !password}>
            <Key size={14} /> {existing ? "Update Login" : "Create Login"}
          </Button>
          {existing && (
            <Button variant="danger" onClick={handleDisable} loading={disabling}>
              <UserX size={14} /> Disable Login
            </Button>
          )}
          <Button variant="outline" onClick={() => router.push("/customers")}>Cancel</Button>
        </div>
      </div>

      <div className="bg-blue-50 border border-blue-100 rounded-xl p-4 text-xs text-blue-700 space-y-1">
        <p className="font-semibold">Customer Portal Access</p>
        <p>Once created, the customer can log in at the same URL and will only see their own invoices.</p>
        <p>They can view and download invoices but cannot access any admin features.</p>
      </div>
    </div>
  );
}

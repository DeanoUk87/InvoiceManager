"use client";
import { useState, useEffect, useCallback } from "react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { useSession } from "next-auth/react";
import { useRouter } from "next/navigation";
import {
  Users, Plus, Pencil, Trash2, CheckCircle, AlertTriangle,
  ShieldAlert, RefreshCw, X, Eye, EyeOff
} from "lucide-react";

interface User {
  id: string;
  name: string | null;
  email: string;
  role: string;
  username: string | null;
  createdAt: string | null;
}

const ROLES = [
  { value: "admin", label: "Admin", desc: "Full access including Admin area and Settings" },
  { value: "admin2", label: "Manager", desc: "Can process uploads, generate invoices, view all data" },
  { value: "user", label: "User", desc: "Can upload CSVs, generate and view invoices" },
];

const ROLE_COLOURS: Record<string, string> = {
  admin: "bg-red-100 text-red-700",
  admin2: "bg-purple-100 text-purple-700",
  user: "bg-blue-100 text-blue-700",
};

export default function AdminPage() {
  const { data: session, status } = useSession();
  const router = useRouter();
  const [users, setUsers] = useState<User[]>([]);
  const [loading, setLoading] = useState(true);
  const [showModal, setShowModal] = useState(false);
  const [editUser, setEditUser] = useState<User | null>(null);
  const [showPw, setShowPw] = useState(false);
  const [saving, setSaving] = useState(false);
  const [clearing, setClearing] = useState(false);
  const [clearResult, setClearResult] = useState<{ success: boolean; message: string } | null>(null);
  const [form, setForm] = useState({ name: "", email: "", password: "", role: "user", username: "" });
  const [formError, setFormError] = useState("");

  // Redirect non-admins
  useEffect(() => {
    if (status === "authenticated" && session?.user?.role !== "admin") {
      router.push("/dashboard");
    }
  }, [session, status, router]);

  const loadUsers = useCallback(async () => {
    setLoading(true);
    const res = await fetch("/api/users");
    if (res.ok) setUsers(await res.json());
    setLoading(false);
  }, []);

  useEffect(() => { loadUsers(); }, [loadUsers]);

  const openCreate = () => {
    setEditUser(null);
    setForm({ name: "", email: "", password: "", role: "user", username: "" });
    setFormError("");
    setShowPw(false);
    setShowModal(true);
  };

  const openEdit = (u: User) => {
    setEditUser(u);
    setForm({ name: u.name ?? "", email: u.email, password: "", role: u.role, username: u.username ?? "" });
    setFormError("");
    setShowPw(false);
    setShowModal(true);
  };

  const handleSave = async () => {
    setFormError("");
    if (!form.email) { setFormError("Email is required."); return; }
    if (!editUser && !form.password) { setFormError("Password is required for new users."); return; }
    setSaving(true);
    const url = editUser ? `/api/users/${editUser.id}` : "/api/users";
    const method = editUser ? "PUT" : "POST";
    const res = await fetch(url, {
      method,
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(form),
    });
    const data = await res.json();
    setSaving(false);
    if (!res.ok) { setFormError(data.error ?? "Save failed"); return; }
    setShowModal(false);
    loadUsers();
  };

  const handleDelete = async (u: User) => {
    if (!confirm(`Delete user "${u.email}"? This cannot be undone.`)) return;
    const res = await fetch(`/api/users/${u.id}`, { method: "DELETE" });
    const data = await res.json();
    if (data.error) { alert(data.error); return; }
    loadUsers();
  };

  const handleClearData = async () => {
    if (!confirm(
      "⚠️ WARNING: This will permanently delete ALL invoices, sales data and uploaded CSV records.\n\n" +
      "Type DELETE in the next prompt to confirm."
    )) return;
    const confirm2 = window.prompt('Type DELETE to confirm clearing all invoice data:');
    if (confirm2 !== "DELETE") { alert("Cancelled - you must type DELETE exactly."); return; }
    setClearing(true);
    setClearResult(null);
    const res = await fetch("/api/admin", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ action: "clear-invoices" }),
    });
    const data = await res.json();
    setClearing(false);
    setClearResult({ success: data.success, message: data.message ?? data.error });
  };

  if (status === "loading") return null;
  if (session?.user?.role !== "admin") return null;

  return (
    <div className="space-y-6 max-w-4xl">
      <div>
        <h1 className="text-xl font-bold text-gray-900 flex items-center gap-2">
          <ShieldAlert size={22} className="text-red-600" /> Admin Area
        </h1>
        <p className="text-sm text-gray-500 mt-1">Restricted to Admin users only.</p>
      </div>

      {/* User Management */}
      <div className="bg-white rounded-xl border border-gray-100 shadow-sm">
        <div className="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
          <div className="flex items-center gap-2">
            <Users size={18} className="text-blue-600" />
            <h2 className="text-base font-semibold text-gray-900">User Management</h2>
          </div>
          <div className="flex gap-2">
            <Button variant="outline" size="sm" onClick={loadUsers}>
              <RefreshCw size={13} />
            </Button>
            <Button size="sm" onClick={openCreate}>
              <Plus size={14} /> Add User
            </Button>
          </div>
        </div>

        {/* Role legend */}
        <div className="px-6 py-3 bg-gray-50 border-b border-gray-100 flex flex-wrap gap-4">
          {ROLES.map(r => (
            <div key={r.value} className="flex items-center gap-2 text-xs">
              <span className={`px-2 py-0.5 rounded-full font-medium ${ROLE_COLOURS[r.value]}`}>{r.label}</span>
              <span className="text-gray-500">{r.desc}</span>
            </div>
          ))}
        </div>

        <div className="overflow-x-auto">
          <table className="w-full text-sm">
            <thead>
              <tr className="bg-gray-50 border-b border-gray-100">
                <th className="px-4 py-3 text-left font-semibold text-blue-600">Name</th>
                <th className="px-4 py-3 text-left font-semibold text-blue-600">Email</th>
                <th className="px-4 py-3 text-left font-semibold text-blue-600">Username</th>
                <th className="px-4 py-3 text-left font-semibold text-blue-600">Role</th>
                <th className="px-4 py-3 text-left font-semibold text-blue-600">Actions</th>
              </tr>
            </thead>
            <tbody>
              {loading ? (
                <tr><td colSpan={5} className="px-4 py-8 text-center text-gray-400">Loading...</td></tr>
              ) : users.map(u => (
                <tr key={u.id} className="border-b border-gray-50 hover:bg-gray-50">
                  <td className="px-4 py-3 font-medium text-gray-900">{u.name ?? "-"}</td>
                  <td className="px-4 py-3 text-gray-600">{u.email}</td>
                  <td className="px-4 py-3 text-gray-500">{u.username ?? "-"}</td>
                  <td className="px-4 py-3">
                    <span className={`inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium ${ROLE_COLOURS[u.role] ?? "bg-gray-100 text-gray-700"}`}>
                      {ROLES.find(r => r.value === u.role)?.label ?? u.role}
                    </span>
                  </td>
                  <td className="px-4 py-3">
                    <div className="flex items-center gap-1.5">
                      <Button variant="success" size="sm" className="px-2 py-1" onClick={() => openEdit(u)}>
                        <Pencil size={13} />
                      </Button>
                      {u.id !== session?.user?.id && (
                        <Button variant="danger" size="sm" className="px-2 py-1" onClick={() => handleDelete(u)}>
                          <Trash2 size={13} />
                        </Button>
                      )}
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* Danger Zone - Clear Data */}
      <div className="bg-white rounded-xl border border-red-200 shadow-sm">
        <div className="px-6 py-4 border-b border-red-100 bg-red-50 rounded-t-xl">
          <h2 className="text-base font-semibold text-red-700 flex items-center gap-2">
            <AlertTriangle size={18} /> Danger Zone
          </h2>
          <p className="text-sm text-red-600 mt-1">
            These actions are irreversible. Use with extreme caution.
          </p>
        </div>
        <div className="p-6 space-y-4">
          <div className="flex items-start justify-between p-4 border border-red-100 rounded-lg gap-4">
            <div>
              <p className="font-semibold text-gray-900">Clear All Invoice Data</p>
              <p className="text-sm text-gray-500 mt-0.5">
                Permanently deletes all invoices, sales/job data, and uploaded CSV records.
                Customers and settings are preserved. Use this to start a fresh upload cycle.
              </p>
            </div>
            <Button
              variant="danger"
              loading={clearing}
              onClick={handleClearData}
              className="shrink-0"
            >
              <Trash2 size={14} /> Clear All Data
            </Button>
          </div>

          {clearResult && (
            <div className={`flex items-start gap-2 p-3 rounded-lg border ${
              clearResult.success ? "bg-green-50 border-green-200" : "bg-red-50 border-red-200"
            }`}>
              {clearResult.success
                ? <CheckCircle size={16} className="text-green-600 mt-0.5 shrink-0" />
                : <AlertTriangle size={16} className="text-red-500 mt-0.5 shrink-0" />
              }
              <p className={`text-sm ${clearResult.success ? "text-green-700" : "text-red-700"}`}>
                {clearResult.message}
              </p>
            </div>
          )}
        </div>
      </div>

      {/* Create/Edit User Modal */}
      {showModal && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div className="flex items-center justify-between px-6 py-4 border-b border-gray-100">
              <h3 className="font-semibold text-gray-900">
                {editUser ? "Edit User" : "Create New User"}
              </h3>
              <button onClick={() => setShowModal(false)} className="p-1 rounded hover:bg-gray-100 text-gray-400">
                <X size={18} />
              </button>
            </div>
            <div className="px-6 py-5 space-y-4">
              {formError && (
                <div className="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                  {formError}
                </div>
              )}
              <Input
                label="Full Name"
                value={form.name}
                onChange={e => setForm({ ...form, name: e.target.value })}
                placeholder="John Smith"
              />
              <Input
                label="Email Address *"
                type="email"
                value={form.email}
                onChange={e => setForm({ ...form, email: e.target.value })}
                placeholder="user@example.com"
              />
              <Input
                label="Username"
                value={form.username}
                onChange={e => setForm({ ...form, username: e.target.value })}
                placeholder="jsmith"
              />
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  Password {editUser && <span className="text-gray-400 font-normal">(leave blank to keep current)</span>}
                  {!editUser && <span className="text-red-500"> *</span>}
                </label>
                <div className="relative">
                  <input
                    type={showPw ? "text" : "password"}
                    value={form.password}
                    onChange={e => setForm({ ...form, password: e.target.value })}
                    placeholder={editUser ? "Enter new password to change" : "Minimum 6 characters"}
                    className="w-full border border-gray-300 rounded-md px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                  <button type="button" onClick={() => setShowPw(!showPw)}
                    className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    {showPw ? <EyeOff size={15} /> : <Eye size={15} />}
                  </button>
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                <div className="space-y-2">
                  {ROLES.map(r => (
                    <label key={r.value} className={`flex items-start gap-3 p-3 rounded-lg border cursor-pointer transition-colors ${
                      form.role === r.value ? "border-blue-500 bg-blue-50" : "border-gray-200 hover:bg-gray-50"
                    }`}>
                      <input
                        type="radio"
                        name="role"
                        value={r.value}
                        checked={form.role === r.value}
                        onChange={() => setForm({ ...form, role: r.value })}
                        className="mt-0.5"
                      />
                      <div>
                        <span className={`inline-flex px-2 py-0.5 rounded-full text-xs font-medium ${ROLE_COLOURS[r.value]}`}>
                          {r.label}
                        </span>
                        <p className="text-xs text-gray-500 mt-0.5">{r.desc}</p>
                      </div>
                    </label>
                  ))}
                </div>
              </div>
            </div>
            <div className="px-6 py-4 border-t border-gray-100 flex gap-3">
              <Button onClick={handleSave} loading={saving} className="flex-1">
                {editUser ? "Save Changes" : "Create User"}
              </Button>
              <Button variant="outline" onClick={() => setShowModal(false)} className="flex-1">
                Cancel
              </Button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

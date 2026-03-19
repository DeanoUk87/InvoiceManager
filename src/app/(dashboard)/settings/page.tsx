"use client";
import { useState, useEffect, useCallback, useRef } from "react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import {
  CheckCircle, ShieldAlert, Users, Plus, Pencil,
  Trash2, AlertTriangle, RefreshCw, X, Eye, EyeOff, Settings,
  Bold, Italic, Underline, List
} from "lucide-react";
import { useSession } from "next-auth/react";
import { useRouter } from "next/navigation";

interface SettingsForm {
  companyName: string; companyAddress1: string; companyAddress2: string;
  city: string; postcode: string; country: string; phone: string;
  cemail: string; website: string; vatNumber: string;
  invoiceDueDate: number; messageTitle: string; defaultMessage: string;
  defaultMessage2: string; invoiceDefaultMessage: string;
  sendLimit: number; fuelSurchargePercent: number;
  resourcingSurchargePercent: number; vatPercent: number;
}

interface User {
  id: string; name: string | null; email: string;
  role: string; username: string | null;
}

const ROLES = [
  { value: "admin", label: "Admin", desc: "Full access including Settings & Admin" },
  { value: "admin2", label: "Manager", desc: "Upload, generate invoices, view all data" },
  { value: "user", label: "User", desc: "Upload CSVs and view invoices" },
];
const ROLE_COLOURS: Record<string, string> = {
  admin: "bg-red-100 text-red-700",
  admin2: "bg-purple-100 text-purple-700",
  user: "bg-blue-100 text-blue-700",
};

// Rich text editor for invoice default message
function InvoiceDefaultMessageEditor({ value, onChange }: { value: string; onChange: (v: string) => void }) {
  const editorRef = useRef<HTMLDivElement>(null);

  // Sync external value into editor only on mount
  useEffect(() => {
    if (editorRef.current && editorRef.current.innerHTML !== value) {
      editorRef.current.innerHTML = value;
    }
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const exec = (cmd: string, val?: string) => {
    document.execCommand(cmd, false, val);
    editorRef.current?.focus();
  };

  const toolbarBtn = (title: string, icon: React.ReactNode, cmd: string, val?: string) => (
    <button type="button" title={title} onMouseDown={e => { e.preventDefault(); exec(cmd, val); }}
      className="p-1.5 rounded hover:bg-gray-200 text-gray-600 transition-colors">
      {icon}
    </button>
  );

  return (
    <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
      <h2 className="text-base font-semibold text-gray-900 mb-1">Invoice Default Message</h2>
      <p className="text-sm text-gray-500 mb-4">
        This message appears at the bottom of every invoice. If a customer has a specific message set,
        it will appear above this one.
      </p>
      <div className="border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-transparent">
        {/* Toolbar */}
        <div className="flex items-center gap-1 px-3 py-2 bg-gray-50 border-b border-gray-200">
          {toolbarBtn("Bold", <Bold size={14} />, "bold")}
          {toolbarBtn("Italic", <Italic size={14} />, "italic")}
          {toolbarBtn("Underline", <Underline size={14} />, "underline")}
          <div className="w-px h-5 bg-gray-300 mx-1" />
          {toolbarBtn("Bullet list", <List size={14} />, "insertUnorderedList")}
          {toolbarBtn("H2", <span className="text-xs font-bold">H2</span>, "formatBlock", "h2")}
          {toolbarBtn("H3", <span className="text-xs font-bold">H3</span>, "formatBlock", "h3")}
          <div className="w-px h-5 bg-gray-300 mx-1" />
          {toolbarBtn("Align left", <span className="text-xs">≡</span>, "justifyLeft")}
          {toolbarBtn("Align center", <span className="text-xs">☰</span>, "justifyCenter")}
          <div className="flex-1" />
          <button type="button" onClick={() => { if (editorRef.current) { editorRef.current.innerHTML = ""; onChange(""); } }}
            className="text-xs text-gray-400 hover:text-red-500 px-2">Clear</button>
        </div>
        {/* Editable area */}
        <div
          ref={editorRef}
          contentEditable
          suppressContentEditableWarning
          onInput={() => onChange(editorRef.current?.innerHTML ?? "")}
          className="min-h-[120px] px-4 py-3 text-sm text-gray-700 focus:outline-none leading-relaxed"
          style={{ wordBreak: "break-word" }}
        />
      </div>
      <p className="text-xs text-gray-400 mt-2">
        Supports bold, italic, underline, headings and bullet lists. HTML is stored and rendered on invoices.
      </p>
    </div>
  );
}

export default function SettingsPage() {
  const { data: session, status } = useSession();
  const router = useRouter();
  const isAdmin = session?.user?.role === "admin";
  const [tab, setTab] = useState<"settings" | "users" | "danger">("settings");

  useEffect(() => {
    if (status === "authenticated" && !isAdmin) router.push("/dashboard");
  }, [session, status, isAdmin, router]);

  // --- Settings state ---
  const [form, setForm] = useState<SettingsForm>({
    companyName: "", companyAddress1: "", companyAddress2: "", city: "", postcode: "",
    country: "", phone: "", cemail: "", website: "", vatNumber: "",
    invoiceDueDate: 30, messageTitle: "", defaultMessage: "", defaultMessage2: "",
    invoiceDefaultMessage: "",
    sendLimit: 50, fuelSurchargePercent: 3.5, resourcingSurchargePercent: 0, vatPercent: 20,
  });
  const [saving, setSaving] = useState(false);
  const [saved, setSaved] = useState(false);

  useEffect(() => {
    if (isAdmin) fetch("/api/settings").then(r => r.json()).then(d => { if (d) setForm(p => ({ ...p, ...d })); });
  }, [isAdmin]);

  const handleSave = async (e: React.FormEvent) => {
    e.preventDefault(); setSaving(true);
    await fetch("/api/settings", { method: "PUT", headers: { "Content-Type": "application/json" }, body: JSON.stringify(form) });
    setSaving(false); setSaved(true); setTimeout(() => setSaved(false), 3000);
  };

  const field = (key: keyof SettingsForm, label: string, type = "text") => (
    <Input label={label} type={type} value={String(form[key] ?? "")}
      onChange={e => setForm({ ...form, [key]: type === "number" ? parseFloat(e.target.value) || 0 : e.target.value })} />
  );

  // --- Users state ---
  const [users, setUsers] = useState<User[]>([]);
  const [usersLoading, setUsersLoading] = useState(false);
  const [showModal, setShowModal] = useState(false);
  const [editUser, setEditUser] = useState<User | null>(null);
  const [showPw, setShowPw] = useState(false);
  const [userSaving, setUserSaving] = useState(false);
  const [userForm, setUserForm] = useState({ name: "", email: "", password: "", role: "user", username: "" });
  const [userError, setUserError] = useState("");

  const loadUsers = useCallback(async () => {
    setUsersLoading(true);
    const res = await fetch("/api/settings?action=users");
    if (res.ok) setUsers(await res.json());
    setUsersLoading(false);
  }, []);

  useEffect(() => { if (tab === "users" && isAdmin) loadUsers(); }, [tab, isAdmin, loadUsers]);

  const openCreate = () => { setEditUser(null); setUserForm({ name: "", email: "", password: "", role: "user", username: "" }); setUserError(""); setShowPw(false); setShowModal(true); };
  const openEdit = (u: User) => { setEditUser(u); setUserForm({ name: u.name ?? "", email: u.email, password: "", role: u.role, username: u.username ?? "" }); setUserError(""); setShowPw(false); setShowModal(true); };

  const handleSaveUser = async () => {
    setUserError("");
    if (!userForm.email) { setUserError("Email is required."); return; }
    if (!editUser && !userForm.password) { setUserError("Password is required."); return; }
    setUserSaving(true);
    const res = await fetch("/api/settings", {
      method: editUser ? "PUT" : "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ action: editUser ? "update-user" : "create-user", userId: editUser?.id, ...userForm }),
    });
    const data = await res.json();
    setUserSaving(false);
    if (!res.ok) { setUserError(data.error ?? "Save failed"); return; }
    setShowModal(false); loadUsers();
  };

  const handleDeleteUser = async (u: User) => {
    if (!confirm(`Delete user "${u.email}"?`)) return;
    const res = await fetch("/api/settings", {
      method: "DELETE",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ action: "delete-user", userId: u.id }),
    });
    const data = await res.json();
    if (data.error) { alert(data.error); return; }
    loadUsers();
  };

  // --- Danger zone ---
  const [clearing, setClearing] = useState(false);
  const [clearMsg, setClearMsg] = useState<{ success: boolean; message: string } | null>(null);

  const handleClearData = async () => {
    if (!confirm("⚠️ WARNING: This will permanently delete ALL invoices, sales data and uploaded CSV records.\n\nType DELETE in the next prompt to confirm.")) return;
    const c = window.prompt("Type DELETE to confirm:");
    if (c !== "DELETE") { alert("Cancelled - you must type DELETE exactly."); return; }
    setClearing(true); setClearMsg(null);
    const res = await fetch("/api/settings", {
      method: "DELETE",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ action: "clear-invoices" }),
    });
    const data = await res.json();
    setClearing(false); setClearMsg({ success: data.success, message: data.message ?? data.error });
  };

  if (status === "loading" || !isAdmin) return null;

  const tabs = [
    { key: "settings", label: "Invoice Settings", icon: Settings },
    { key: "users", label: "User Management", icon: Users },
    { key: "danger", label: "Danger Zone", icon: AlertTriangle },
  ] as const;

  return (
    <div className="space-y-5 max-w-4xl">
      <div className="flex items-center justify-between">
        <h1 className="text-xl font-bold text-gray-900 flex items-center gap-2">
          <ShieldAlert size={20} className="text-red-600" /> Admin & Settings
        </h1>
        {saved && tab === "settings" && (
          <div className="flex items-center gap-2 text-sm text-green-600"><CheckCircle size={16} /> Saved</div>
        )}
      </div>

      {/* Tabs */}
      <div className="flex border-b border-gray-200">
        {tabs.map(t => (
          <button key={t.key} onClick={() => setTab(t.key)}
            className={`flex items-center gap-2 px-5 py-3 text-sm font-medium border-b-2 transition-colors ${
              tab === t.key ? "border-blue-600 text-blue-600" : "border-transparent text-gray-500 hover:text-gray-700"
            } ${t.key === "danger" ? (tab === t.key ? "!text-red-600 !border-red-600" : "!text-red-400 hover:!text-red-600") : ""}`}>
            <t.icon size={15} /> {t.label}
          </button>
        ))}
      </div>

      {/* Settings Tab */}
      {tab === "settings" && (
        <form onSubmit={handleSave} className="space-y-5">
          <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 className="text-base font-semibold text-gray-900 mb-4">Company Details</h2>
            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
              {field("companyName", "Company Name")} {field("cemail", "Company Email", "email")}
              {field("companyAddress1", "Address Line 1")} {field("companyAddress2", "Address Line 2")}
              {field("city", "City")} {field("postcode", "Postcode")}
              {field("country", "Country")} {field("phone", "Phone")}
              {field("website", "Website")} {field("vatNumber", "VAT Number")}
            </div>
          </div>
          <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 className="text-base font-semibold text-gray-900 mb-2">Invoice Configuration</h2>
            <p className="text-sm text-gray-500 mb-4">Fuel surcharge %, VAT %, and due days are taken from the CSV file.</p>
            <div className="max-w-xs">{field("sendLimit", "Bulk Send Limit (per batch)", "number")}</div>
          </div>
          <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 className="text-base font-semibold text-gray-900 mb-4">Email Templates</h2>
            <div className="space-y-4">
              {field("messageTitle", "Email Subject (use {invoice_number})")}
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">Email Body</label>
                <textarea value={form.defaultMessage2} rows={6}
                  onChange={e => setForm({ ...form, defaultMessage2: e.target.value })}
                  className="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
              </div>
            </div>
          </div>
          <InvoiceDefaultMessageEditor
            value={form.invoiceDefaultMessage}
            onChange={(v: string) => setForm({ ...form, invoiceDefaultMessage: v })}
          />
          <div className="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 className="text-base font-semibold text-gray-900 mb-2">SMTP Configuration</h2>
            <p className="text-sm text-gray-500">Set <code className="bg-gray-100 px-1 rounded text-xs">SMTP_HOST</code>, <code className="bg-gray-100 px-1 rounded text-xs">SMTP_PORT</code>, <code className="bg-gray-100 px-1 rounded text-xs">SMTP_USER</code>, <code className="bg-gray-100 px-1 rounded text-xs">SMTP_PASS</code> in your environment.</p>
          </div>
          <Button type="submit" loading={saving} size="lg">Save Settings</Button>
        </form>
      )}

      {/* Users Tab */}
      {tab === "users" && (
        <div className="space-y-4">
          <div className="bg-white rounded-xl border border-gray-100 shadow-sm">
            <div className="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
              <h2 className="font-semibold text-gray-900">User Management</h2>
              <div className="flex gap-2">
                <Button variant="outline" size="sm" onClick={loadUsers}><RefreshCw size={13} /></Button>
                <Button size="sm" onClick={openCreate}><Plus size={14} /> Add User</Button>
              </div>
            </div>
            <div className="px-6 py-3 bg-gray-50 border-b border-gray-100 flex flex-wrap gap-4">
              {ROLES.map(r => (
                <div key={r.value} className="flex items-center gap-2 text-xs">
                  <span className={`px-2 py-0.5 rounded-full font-medium ${ROLE_COLOURS[r.value]}`}>{r.label}</span>
                  <span className="text-gray-500">{r.desc}</span>
                </div>
              ))}
            </div>
            <table className="w-full text-sm">
              <thead><tr className="bg-gray-50 border-b border-gray-100">
                <th className="px-4 py-3 text-left font-semibold text-blue-600">Name</th>
                <th className="px-4 py-3 text-left font-semibold text-blue-600">Email</th>
                <th className="px-4 py-3 text-left font-semibold text-blue-600">Username</th>
                <th className="px-4 py-3 text-left font-semibold text-blue-600">Role</th>
                <th className="px-4 py-3 text-left font-semibold text-blue-600">Actions</th>
              </tr></thead>
              <tbody>
                {usersLoading ? <tr><td colSpan={5} className="px-4 py-8 text-center text-gray-400">Loading...</td></tr>
                  : users.map(u => (
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
                      <div className="flex gap-1.5">
                        <Button variant="success" size="sm" className="px-2 py-1" onClick={() => openEdit(u)}><Pencil size={13} /></Button>
                        {u.id !== session?.user?.id && (
                          <Button variant="danger" size="sm" className="px-2 py-1" onClick={() => handleDeleteUser(u)}><Trash2 size={13} /></Button>
                        )}
                      </div>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {/* Danger Zone Tab */}
      {tab === "danger" && (
        <div className="bg-white rounded-xl border border-red-200 shadow-sm">
          <div className="px-6 py-4 border-b border-red-100 bg-red-50 rounded-t-xl">
            <h2 className="font-semibold text-red-700 flex items-center gap-2"><AlertTriangle size={18} /> Danger Zone</h2>
            <p className="text-sm text-red-600 mt-1">These actions are irreversible.</p>
          </div>
          <div className="p-6">
            <div className="flex items-start justify-between p-4 border border-red-100 rounded-lg gap-4">
              <div>
                <p className="font-semibold text-gray-900">Clear All Invoice Data</p>
                <p className="text-sm text-gray-500 mt-0.5">Permanently deletes all invoices, sales/job data, and uploaded CSV records. Customers and settings are preserved.</p>
              </div>
              <Button variant="danger" loading={clearing} onClick={handleClearData} className="shrink-0">
                <Trash2 size={14} /> Clear All Data
              </Button>
            </div>
            {clearMsg && (
              <div className={`mt-4 flex items-start gap-2 p-3 rounded-lg border ${clearMsg.success ? "bg-green-50 border-green-200" : "bg-red-50 border-red-200"}`}>
                {clearMsg.success ? <CheckCircle size={16} className="text-green-600 mt-0.5" /> : <AlertTriangle size={16} className="text-red-500 mt-0.5" />}
                <p className={`text-sm ${clearMsg.success ? "text-green-700" : "text-red-700"}`}>{clearMsg.message}</p>
              </div>
            )}
          </div>
        </div>
      )}

      {/* User Modal */}
      {showModal && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
          <div className="bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div className="flex items-center justify-between px-6 py-4 border-b border-gray-100">
              <h3 className="font-semibold text-gray-900">{editUser ? "Edit User" : "Create New User"}</h3>
              <button onClick={() => setShowModal(false)} className="p-1 rounded hover:bg-gray-100 text-gray-400"><X size={18} /></button>
            </div>
            <div className="px-6 py-5 space-y-4">
              {userError && <div className="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">{userError}</div>}
              <Input label="Full Name" value={userForm.name} onChange={e => setUserForm({ ...userForm, name: e.target.value })} placeholder="John Smith" />
              <Input label="Email Address *" type="email" value={userForm.email} onChange={e => setUserForm({ ...userForm, email: e.target.value })} />
              <Input label="Username" value={userForm.username} onChange={e => setUserForm({ ...userForm, username: e.target.value })} />
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-1">
                  Password {editUser && <span className="text-gray-400 font-normal">(blank = keep current)</span>}{!editUser && <span className="text-red-500"> *</span>}
                </label>
                <div className="relative">
                  <input type={showPw ? "text" : "password"} value={userForm.password}
                    onChange={e => setUserForm({ ...userForm, password: e.target.value })}
                    className="w-full border border-gray-300 rounded-md px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />
                  <button type="button" onClick={() => setShowPw(!showPw)} className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                    {showPw ? <EyeOff size={15} /> : <Eye size={15} />}
                  </button>
                </div>
              </div>
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                <div className="space-y-2">
                  {ROLES.map(r => (
                    <label key={r.value} className={`flex items-start gap-3 p-3 rounded-lg border cursor-pointer ${userForm.role === r.value ? "border-blue-500 bg-blue-50" : "border-gray-200 hover:bg-gray-50"}`}>
                      <input type="radio" name="role" value={r.value} checked={userForm.role === r.value} onChange={() => setUserForm({ ...userForm, role: r.value })} className="mt-0.5" />
                      <div>
                        <span className={`inline-flex px-2 py-0.5 rounded-full text-xs font-medium ${ROLE_COLOURS[r.value]}`}>{r.label}</span>
                        <p className="text-xs text-gray-500 mt-0.5">{r.desc}</p>
                      </div>
                    </label>
                  ))}
                </div>
              </div>
            </div>
            <div className="px-6 py-4 border-t border-gray-100 flex gap-3">
              <Button onClick={handleSaveUser} loading={userSaving} className="flex-1">{editUser ? "Save Changes" : "Create User"}</Button>
              <Button variant="outline" onClick={() => setShowModal(false)} className="flex-1">Cancel</Button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

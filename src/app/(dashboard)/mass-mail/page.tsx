"use client";
import { useState, useRef } from "react";
import { Button } from "@/components/ui/button";
import {
  Send, CheckCircle, AlertCircle, Paperclip, X,
  Mail, Users, Bold, Italic, List
} from "lucide-react";

interface SendResult {
  success: boolean;
  message?: string;
  error?: string;
  sent?: number;
  failed?: number;
  total?: number;
}

export default function MassMailPage() {
  const [subject, setSubject] = useState("");
  const [body, setBody] = useState("");
  const [attachments, setAttachments] = useState<File[]>([]);
  const [sending, setSending] = useState(false);
  const [result, setResult] = useState<SendResult | null>(null);
  const fileRef = useRef<HTMLInputElement>(null);
  const textRef = useRef<HTMLTextAreaElement>(null);

  const addAttachment = (e: React.ChangeEvent<HTMLInputElement>) => {
    const files = Array.from(e.target.files ?? []);
    setAttachments(prev => {
      const combined = [...prev, ...files].slice(0, 2); // max 2
      return combined;
    });
    e.target.value = "";
  };

  const removeAttachment = (i: number) => {
    setAttachments(prev => prev.filter((_, idx) => idx !== i));
  };

  // Simple text formatting helpers
  const insertFormat = (prefix: string, suffix = prefix) => {
    const ta = textRef.current;
    if (!ta) return;
    const start = ta.selectionStart;
    const end = ta.selectionEnd;
    const selected = body.substring(start, end);
    const before = body.substring(0, start);
    const after = body.substring(end);
    const newBody = `${before}${prefix}${selected || "text"}${suffix}${after}`;
    setBody(newBody);
    setTimeout(() => {
      ta.focus();
      ta.setSelectionRange(start + prefix.length, start + prefix.length + (selected || "text").length);
    }, 0);
  };

  const handleSend = async () => {
    if (!subject.trim()) { alert("Please enter an email subject."); return; }
    if (!body.trim()) { alert("Please enter a message body."); return; }
    if (!confirm(`Send this campaign to all customers with an email address?`)) return;

    setSending(true);
    setResult(null);
    try {
      const form = new FormData();
      form.append("subject", subject);
      form.append("body", body);
      if (attachments[0]) form.append("attachment1", attachments[0]);
      if (attachments[1]) form.append("attachment2", attachments[1]);

      const res = await fetch("/api/mass-mail", { method: "POST", body: form });
      const ct = res.headers.get("content-type") ?? "";
      if (!ct.includes("application/json")) {
        const txt = await res.text();
        setResult({ success: false, message: `Server error (${res.status}): ${txt.substring(0, 300)}` });
      } else {
        const data = await res.json();
        setResult({ ...data, message: data.message ?? data.error });
      }
    } catch (e) {
      setResult({ success: false, message: `Request failed: ${String(e)}` });
    } finally {
      setSending(false);
    }
  };

  const charCount = body.length;

  return (
    <div className="space-y-5 max-w-3xl">
      <div>
        <h1 className="text-xl font-bold text-gray-900">Mass Mail</h1>
        <p className="text-sm text-gray-500 mt-1">
          Create and send an email campaign to all customers with an email address on record.
        </p>
      </div>

      <div className="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        {/* Header */}
        <div className="bg-blue-600 px-6 py-4 flex items-center gap-3">
          <div className="w-9 h-9 bg-white/20 rounded-lg flex items-center justify-center">
            <Mail size={18} className="text-white" />
          </div>
          <div>
            <h2 className="font-semibold text-white">New Email Campaign</h2>
            <p className="text-blue-200 text-xs">Sends to all customers with a registered email</p>
          </div>
        </div>

        <div className="p-6 space-y-5">
          {/* Recipients info */}
          <div className="flex items-center gap-2 p-3 bg-blue-50 border border-blue-100 rounded-lg text-sm text-blue-800">
            <Users size={15} className="shrink-0" />
            <span>This campaign will be sent to <strong>all customers</strong> who have an email address on file.</span>
          </div>

          {/* Subject */}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1.5">
              Email Subject <span className="text-red-500">*</span>
            </label>
            <input
              type="text"
              value={subject}
              onChange={e => setSubject(e.target.value)}
              placeholder="e.g. Important update from Your Company"
              className="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>

          {/* Body with toolbar */}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1.5">
              Message Body <span className="text-red-500">*</span>
            </label>
            <div className="border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-transparent">
              {/* Formatting toolbar */}
              <div className="flex items-center gap-1 px-3 py-2 bg-gray-50 border-b border-gray-200">
                <button
                  type="button"
                  onClick={() => insertFormat("<strong>", "</strong>")}
                  className="p-1.5 rounded hover:bg-gray-200 text-gray-600 transition-colors"
                  title="Bold"
                >
                  <Bold size={14} />
                </button>
                <button
                  type="button"
                  onClick={() => insertFormat("<em>", "</em>")}
                  className="p-1.5 rounded hover:bg-gray-200 text-gray-600 transition-colors"
                  title="Italic"
                >
                  <Italic size={14} />
                </button>
                <div className="w-px h-5 bg-gray-300 mx-1" />
                <button
                  type="button"
                  onClick={() => {
                    const ta = textRef.current;
                    if (!ta) return;
                    const pos = ta.selectionStart;
                    const newBody = body.substring(0, pos) + "\n• " + body.substring(pos);
                    setBody(newBody);
                    setTimeout(() => { ta.focus(); ta.setSelectionRange(pos + 3, pos + 3); }, 0);
                  }}
                  className="p-1.5 rounded hover:bg-gray-200 text-gray-600 transition-colors"
                  title="Bullet point"
                >
                  <List size={14} />
                </button>
                <div className="flex-1" />
                <span className="text-xs text-gray-400">{charCount} chars</span>
              </div>
              <textarea
                ref={textRef}
                value={body}
                onChange={e => setBody(e.target.value)}
                rows={10}
                placeholder="Write your message here...

You can use plain text or add simple HTML like <strong>bold</strong> or <em>italic</em>.

Each blank line creates a new paragraph in the email."
                className="w-full px-4 py-3 text-sm focus:outline-none resize-none leading-relaxed text-gray-700"
              />
            </div>
            <p className="text-xs text-gray-400 mt-1">
              Blank lines between paragraphs will be preserved in the email.
            </p>
          </div>

          {/* Attachments */}
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1.5">
              Attachments <span className="text-gray-400 font-normal">(max 2 files)</span>
            </label>
            <div className="space-y-2">
              {attachments.map((f, i) => (
                <div key={i} className="flex items-center gap-3 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                  <Paperclip size={15} className="text-blue-500 shrink-0" />
                  <div className="flex-1 min-w-0">
                    <p className="text-sm font-medium text-gray-800 truncate">{f.name}</p>
                    <p className="text-xs text-gray-400">{(f.size / 1024).toFixed(1)} KB</p>
                  </div>
                  <button
                    type="button"
                    onClick={() => removeAttachment(i)}
                    className="p-1 rounded hover:bg-red-100 text-gray-400 hover:text-red-500 transition-colors"
                  >
                    <X size={14} />
                  </button>
                </div>
              ))}
              {attachments.length < 2 && (
                <button
                  type="button"
                  onClick={() => fileRef.current?.click()}
                  className="w-full flex items-center justify-center gap-2 p-3 border-2 border-dashed border-gray-200 rounded-lg text-sm text-gray-500 hover:border-blue-400 hover:text-blue-600 transition-colors"
                >
                  <Paperclip size={15} />
                  Click to attach a file
                </button>
              )}
              <input ref={fileRef} type="file" className="hidden" onChange={addAttachment} />
            </div>
          </div>

          {/* Email preview hint */}
          <div className="bg-gray-50 border border-gray-100 rounded-lg p-4 text-xs text-gray-500 space-y-1">
            <p className="font-semibold text-gray-700 text-sm mb-2">Email preview</p>
            <p><span className="font-medium text-gray-600">Subject:</span> {subject || <span className="italic">No subject entered</span>}</p>
            <p><span className="font-medium text-gray-600">Attachments:</span> {attachments.length === 0 ? "None" : attachments.map(f => f.name).join(", ")}</p>
            <p className="text-gray-400 italic mt-1">The email will be sent in a branded template with your company header and footer.</p>
          </div>

          {/* Result */}
          {result && (
            <div className={`flex items-start gap-3 p-4 rounded-lg border ${
              result.success ? "bg-green-50 border-green-200" : "bg-red-50 border-red-200"
            }`}>
              {result.success
                ? <CheckCircle size={18} className="text-green-600 shrink-0 mt-0.5" />
                : <AlertCircle size={18} className="text-red-500 shrink-0 mt-0.5" />
              }
              <div className="flex-1">
                <p className={`text-sm font-medium ${result.success ? "text-green-800" : "text-red-700"}`}>
                  {result.message ?? result.error}
                </p>
                {result.success && result.total !== undefined && (
                  <div className="mt-3 flex gap-4">
                    <div className="text-center px-4 py-2 bg-white rounded-lg border border-green-100">
                      <p className="text-xl font-bold text-gray-900">{result.sent}</p>
                      <p className="text-xs text-gray-500">Sent</p>
                    </div>
                    {(result.failed ?? 0) > 0 && (
                      <div className="text-center px-4 py-2 bg-white rounded-lg border border-red-100">
                        <p className="text-xl font-bold text-red-500">{result.failed}</p>
                        <p className="text-xs text-gray-500">Failed</p>
                      </div>
                    )}
                    <div className="text-center px-4 py-2 bg-white rounded-lg border">
                      <p className="text-xl font-bold text-gray-900">{result.total}</p>
                      <p className="text-xs text-gray-500">Total</p>
                    </div>
                  </div>
                )}
              </div>
            </div>
          )}

          {/* Send button */}
          <Button
            onClick={handleSend}
            loading={sending}
            className="w-full py-3"
            disabled={!subject.trim() || !body.trim()}
          >
            <Send size={16} />
            {sending ? "Sending campaign..." : "Send Campaign to All Customers"}
          </Button>
        </div>
      </div>
    </div>
  );
}

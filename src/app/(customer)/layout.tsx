import { auth } from "@/lib/auth";
import { redirect } from "next/navigation";
import { FileText, LogOut } from "lucide-react";
import { SignOutButton } from "@/components/SignOutButton";

export default async function CustomerLayout({ children }: { children: React.ReactNode }) {
  const session = await auth();
  if (!session) redirect("/login");
  // Only allow customer role here
  if (session.user.role !== "customer") redirect("/dashboard");

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Header */}
      <header className="bg-blue-600 text-white px-6 py-4 flex items-center justify-between shadow-md">
        <div className="flex items-center gap-3">
          <div className="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
            <FileText size={16} className="text-white" />
          </div>
          <div>
            <p className="font-bold text-sm leading-tight">Invoice Manager</p>
            <p className="text-blue-200 text-xs">Customer Portal</p>
          </div>
        </div>
        <div className="flex items-center gap-3">
          <span className="text-sm text-blue-200">
            Account: <span className="text-white font-semibold">{session.user.name}</span>
          </span>
          <SignOutButton />
        </div>
      </header>
      <main className="max-w-5xl mx-auto p-6">{children}</main>
    </div>
  );
}

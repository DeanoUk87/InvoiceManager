"use client";
import Link from "next/link";
import { usePathname } from "next/navigation";
import { cn } from "@/lib/utils";
import {
  LayoutDashboard, Users, FileText, Search, Printer, FileX,
  Upload, FolderOpen, Zap, Settings, Mail, Download, Archive,
  ChevronDown, ChevronRight, User, ShieldAlert,
} from "lucide-react";
import { useState } from "react";
import { useSession } from "next-auth/react";

const navItems = [
  { href: "/dashboard", label: "Dashboard", icon: LayoutDashboard },
  {
    label: "Account",
    icon: User,
    adminOnly: false,
    children: [
      { href: "/settings", label: "Invoice Settings", icon: Settings, adminOnly: true },
    ],
  },
  { href: "/upload-csv", label: "Upload CSV", icon: Upload },
  { href: "/uploaded-csv", label: "Uploaded CSV", icon: FolderOpen },
  { href: "/mass-invoice-maker", label: "Mass Invoice Maker", icon: Zap },
  { href: "/customers", label: "Customers", icon: Users },
  { href: "/invoices", label: "Invoices", icon: FileText },
  { href: "/job-search", label: "Job Search", icon: Search },
  { href: "/printed", label: "Printed Invoices", icon: Printer },
  { href: "/unprinted", label: "Unprinted Invoices", icon: FileX },
  { href: "/export-sage", label: "Export CSV/SAGE", icon: Download },
  { href: "/mass-mail", label: "Mass Mail", icon: Mail },
];

const archiveItems = [
  { href: "/archive/uploaded-csv", label: "Uploaded CSV (Archived)", icon: Archive },
  { href: "/archive/invoices", label: "Invoices (Archived)", icon: Archive },
];

type NavItem = {
  href?: string;
  label: string;
  icon: React.ComponentType<{ size?: number; className?: string }>;
  adminOnly?: boolean;
  children?: { href: string; label: string; icon: React.ComponentType<{ size?: number; className?: string }>; adminOnly?: boolean }[];
};

interface NavItemProps {
  item: NavItem;
  pathname: string;
  role: string;
}

function NavItemComp({ item, pathname, role }: NavItemProps) {
  const [open, setOpen] = useState(false);
  const isAdmin = role === "admin";

  if ("children" in item && item.children) {
    const visibleChildren = item.children.filter(c => !c.adminOnly || isAdmin);
    if (visibleChildren.length === 0) return null;
    const isActive = visibleChildren.some((c) => pathname.startsWith(c.href));
    return (
      <div>
        <button
          onClick={() => setOpen(!open)}
          className={cn(
            "w-full flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors text-left",
            isActive ? "bg-blue-50 text-blue-700" : "text-gray-600 hover:bg-gray-100 hover:text-gray-900"
          )}
        >
          <item.icon size={18} className="shrink-0" />
          <span className="flex-1">{item.label}</span>
          {open || isActive ? <ChevronDown size={14} /> : <ChevronRight size={14} />}
        </button>
        {(open || isActive) && (
          <div className="ml-4 mt-1 space-y-1">
            {visibleChildren.map((child) => (
              <Link
                key={child.href}
                href={child.href}
                className={cn(
                  "flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors",
                  pathname === child.href
                    ? "bg-blue-600 text-white font-medium"
                    : "text-gray-600 hover:bg-gray-100 hover:text-gray-900"
                )}
              >
                <child.icon size={16} className="shrink-0" />
                {child.label}
              </Link>
            ))}
          </div>
        )}
      </div>
    );
  }

  if (!("href" in item) || !item.href) return null;
  if (item.adminOnly && !isAdmin) return null;

  const isActive = pathname === item.href || pathname.startsWith(item.href + "/");
  return (
    <Link
      href={item.href}
      className={cn(
        "flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors",
        isActive
          ? "bg-blue-600 text-white shadow-sm"
          : "text-gray-600 hover:bg-gray-100 hover:text-gray-900"
      )}
    >
      <item.icon size={18} className="shrink-0" />
      {item.label}
    </Link>
  );
}

export function Sidebar() {
  const pathname = usePathname();
  const { data: session } = useSession();
  const role = session?.user?.role ?? "user";
  const isAdmin = role === "admin";

  return (
    <aside className="w-56 min-h-screen bg-white border-r border-gray-200 flex flex-col">
      {/* Logo */}
      <div className="px-4 py-5 border-b border-gray-100">
        <div className="flex items-center gap-2">
          <div className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
            <FileText size={16} className="text-white" />
          </div>
          <div>
            <div className="text-sm font-bold text-gray-900 leading-tight">Invoice</div>
            <div className="text-xs text-gray-500">Manager</div>
          </div>
        </div>
      </div>

      {/* Nav */}
      <nav className="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
        <p className="px-3 mb-2 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">
          System Menu
        </p>
        {navItems.map((item, i) => (
          <NavItemComp key={i} item={item as NavItem} pathname={pathname} role={role} />
        ))}

        {/* Admin section - only for admins */}
        {isAdmin && (
          <>
            <p className="px-3 mt-4 mb-2 text-[10px] font-semibold text-red-400 uppercase tracking-wider">
              Admin
            </p>
            <Link
              href="/admin"
              className={cn(
                "flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors",
                pathname === "/admin"
                  ? "bg-red-600 text-white shadow-sm"
                  : "text-red-600 hover:bg-red-50 hover:text-red-700"
              )}
            >
              <ShieldAlert size={18} className="shrink-0" />
              Admin Area
            </Link>
          </>
        )}

        <p className="px-3 mt-4 mb-2 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">
          New Data Archives
        </p>
        {archiveItems.map((item) => (
          <Link
            key={item.href}
            href={item.href}
            className={cn(
              "flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-colors",
              pathname === item.href
                ? "bg-blue-600 text-white shadow-sm"
                : "text-gray-600 hover:bg-gray-100 hover:text-gray-900"
            )}
          >
            <item.icon size={18} className="shrink-0" />
            {item.label}
          </Link>
        ))}
      </nav>
    </aside>
  );
}

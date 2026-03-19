"use client";
import { signOut, useSession } from "next-auth/react";
import { LogOut, User, Bell } from "lucide-react";
import { useState } from "react";

export function Topbar() {
  const { data: session } = useSession();
  const [open, setOpen] = useState(false);

  return (
    <header className="h-14 bg-blue-600 text-white flex items-center px-6 justify-between shadow-md sticky top-0 z-10">
      <div className="flex items-center gap-3">
        <h1 className="font-semibold text-sm">
          Welcome: <span className="font-bold">{session?.user?.name ?? "User"}</span>
        </h1>
      </div>
      <div className="flex items-center gap-3">
        <button className="relative p-1.5 rounded-full hover:bg-blue-700 transition-colors">
          <Bell size={18} />
        </button>
        <div className="relative">
          <button
            onClick={() => setOpen(!open)}
            className="flex items-center gap-2 p-1.5 rounded-full hover:bg-blue-700 transition-colors"
          >
            <div className="w-7 h-7 bg-white/20 rounded-full flex items-center justify-center">
              <User size={14} />
            </div>
          </button>
          {open && (
            <div className="absolute right-0 top-10 bg-white rounded-xl shadow-xl border border-gray-100 w-48 py-2 z-50">
              <div className="px-4 py-2 border-b border-gray-100">
                <p className="text-sm font-medium text-gray-900">{session?.user?.name}</p>
                <p className="text-xs text-gray-500">{session?.user?.email}</p>
                <span className="inline-block mt-1 px-2 py-0.5 bg-blue-100 text-blue-700 text-xs rounded-full font-medium">
                  {session?.user?.role}
                </span>
              </div>
              <button
                onClick={() => signOut({ callbackUrl: "/login" })}
                className="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors"
              >
                <LogOut size={14} />
                Sign out
              </button>
            </div>
          )}
        </div>
      </div>
    </header>
  );
}

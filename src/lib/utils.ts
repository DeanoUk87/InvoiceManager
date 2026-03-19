import { clsx, type ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

/**
 * Normalise any date string to YYYY-MM-DD for internal storage/comparison.
 * Handles: YYYYMMDD, YYYY-MM-DD, DD/MM/YYYY
 */
export function normaliseDateToISO(raw: string | null | undefined): string | null {
  if (!raw) return null;
  const s = raw.trim();
  // Already YYYY-MM-DD
  if (/^\d{4}-\d{2}-\d{2}$/.test(s)) return s;
  // YYYYMMDD (no separators) e.g. 20260313
  if (/^\d{8}$/.test(s)) {
    return `${s.slice(0, 4)}-${s.slice(4, 6)}-${s.slice(6, 8)}`;
  }
  // DD/MM/YYYY
  const dmY = s.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
  if (dmY) return `${dmY[3]}-${dmY[2].padStart(2, "0")}-${dmY[1].padStart(2, "0")}`;
  return s;
}

/**
 * Format any date string as DD-MM-YYYY for display.
 * Handles: YYYY-MM-DD, YYYYMMDD, DD/MM/YYYY
 */
export function formatDate(dateStr: string | null | undefined): string {
  if (!dateStr) return "-";
  const iso = normaliseDateToISO(dateStr);
  if (!iso) return "-";
  const m = iso.match(/^(\d{4})-(\d{2})-(\d{2})/);
  if (m) return `${m[3]}-${m[2]}-${m[1]}`;
  return dateStr;
}

/**
 * Calculate due date (DD-MM-YYYY) from invoice date + numb2 days.
 * Works with any of the supported date formats.
 */
export function calcDueDate(
  invoiceDate: string | null | undefined,
  days: number | null | undefined
): string {
  if (!invoiceDate || days == null) return "-";
  try {
    const iso = normaliseDateToISO(invoiceDate);
    if (!iso) return "-";
    const m = iso.match(/^(\d{4})-(\d{2})-(\d{2})/);
    if (!m) return "-";
    const base = new Date(Date.UTC(parseInt(m[1]), parseInt(m[2]) - 1, parseInt(m[3])));
    if (isNaN(base.getTime())) return "-";
    base.setUTCDate(base.getUTCDate() + Math.round(days));
    const d = base.getUTCDate().toString().padStart(2, "0");
    const mo = (base.getUTCMonth() + 1).toString().padStart(2, "0");
    return `${d}-${mo}-${base.getUTCFullYear()}`;
  } catch {
    return "-";
  }
}

export function formatCurrency(amount: number | null | undefined): string {
  if (amount === null || amount === undefined) return "£0.00";
  return new Intl.NumberFormat("en-GB", {
    style: "currency",
    currency: "GBP",
  }).format(amount);
}

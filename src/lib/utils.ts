import { clsx, type ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

export function formatDate(dateStr: string | null | undefined): string {
  if (!dateStr) return "-";
  try {
    const clean = dateStr.trim();
    // YYYY-MM-DD — parse parts directly to avoid timezone shifting
    const isoMatch = clean.match(/^(\d{4})-(\d{2})-(\d{2})/);
    if (isoMatch) {
      return `${isoMatch[3]}/${isoMatch[2]}/${isoMatch[1]}`;
    }
    // Already DD/MM/YYYY
    if (/^\d{2}\/\d{2}\/\d{4}$/.test(clean)) return clean;
    // Fallback
    const d = new Date(clean);
    if (isNaN(d.getTime())) return clean;
    return d.toLocaleDateString("en-GB", { day: "2-digit", month: "2-digit", year: "numeric" });
  } catch {
    return dateStr;
  }
}

/** Calculate due date from an invoice date string + number of days */
export function calcDueDate(invoiceDate: string | null | undefined, days: number | null | undefined): string {
  if (!invoiceDate || days == null) return "-";
  try {
    const isoMatch = invoiceDate.trim().match(/^(\d{4})-(\d{2})-(\d{2})/);
    if (!isoMatch) return "-";
    // Use UTC to avoid DST shifts
    const base = new Date(Date.UTC(parseInt(isoMatch[1]), parseInt(isoMatch[2]) - 1, parseInt(isoMatch[3])));
    base.setUTCDate(base.getUTCDate() + Math.round(days));
    const d = base.getUTCDate().toString().padStart(2, "0");
    const m = (base.getUTCMonth() + 1).toString().padStart(2, "0");
    const y = base.getUTCFullYear();
    return `${d}/${m}/${y}`;
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

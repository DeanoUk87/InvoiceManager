import { NextResponse } from "next/server";
import { db } from "@/db";
import { uploadedCsv, sales } from "@/db/schema";
import { auth } from "@/lib/auth";
import { eq } from "drizzle-orm";
// eslint-disable-next-line @typescript-eslint/no-require-imports
const { createExecuteQuery } = require("@kilocode/app-builder-db");

// Column names matching the sales table exactly
const SALES_COLS = [
  "invoice_number","invoice_date","customer_account","customer_name",
  "address1","address2","town","country","postcode","spacer1",
  "customer_account2","numb1","items","weight","invoice_total","numb2",
  "spacer2","job_number","job_date","sending_depot","delivery_depot",
  "destination","town2","postcode2","service_type","items2",
  "volume_weight","numb3","increased_liability_cover","sub_total",
  "spacer3","numb4","sender_reference","numb5",
  "percentage_fuel_surcharge","percentage_resourcing_surcharge",
  "spacer4","senders_postcode","vat_amount","vat_percent",
  "upload_code","ms_created","invoice_ready","upload_ts",
];

type RowValue = string | number | null;

export async function POST(req: Request) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  const formData = await req.formData();
  const file = formData.get("file") as File;
  if (!file) return NextResponse.json({ error: "No file uploaded" }, { status: 400 });

  const filename = file.name.replace(/\.[^.]+$/, "");

  // Duplicate check
  const existing = await db.select({ id: sales.id }).from(sales).where(eq(sales.uploadTs, filename)).limit(1);
  if (existing.length > 0) {
    return NextResponse.json({ error: "Duplicate: this CSV file has already been uploaded." }, { status: 409 });
  }

  const text = await file.text();
  const lines = text.split(/\r?\n/).filter(l => l.trim());
  if (lines.length === 0) return NextResponse.json({ error: "Empty file" }, { status: 400 });

  // Parse all rows first
  const rows: RowValue[][] = [];
  for (const line of lines) {
    const cols = parseCSVLine(line);
    if (cols.length < 10) continue;
    const customerAccount = cols[2]?.trim() ?? "";
    if (!customerAccount) continue;

    rows.push([
      cols[0]?.trim() || null,
      parseDate(cols[1]),
      customerAccount,
      cols[3]?.trim() || null,
      cols[4]?.trim() || null,
      cols[5]?.trim() || null,
      cols[6]?.trim() || null,
      cols[7]?.trim() || null,
      cols[8]?.trim() || null,
      cols[9]?.trim() || null,
      cols[10]?.trim() || null,
      toFloat(cols[11]),
      toFloat(cols[12]),
      toFloat(cols[13]),
      toFloat(cols[14]),
      toFloat(cols[15]),
      cols[16]?.trim() || null,
      cols[17]?.trim() || null,
      parseDate(cols[18]),
      cols[19]?.trim() || null,
      cols[20]?.trim() || null,
      cols[21]?.trim() || null,
      cols[22]?.trim() || null,
      cols[23]?.trim() || null,
      cols[24]?.trim() || null,
      toFloat(cols[25]),
      toFloat(cols[26]),
      toFloat(cols[27]),
      toFloat(cols[28]),
      toFloat(cols[29]),
      cols[30]?.trim() || null,
      toFloat(cols[31]),
      cols[32]?.trim() || null,
      toFloat(cols[33]),
      toFloat(cols[34]),
      toFloat(cols[35]),
      cols[36]?.trim() || null,
      cols[37]?.trim() || null,
      toFloat(cols[38]),
      toFloat(cols[39]),
      Math.random().toString(36).substring(2, 10),
      0,
      0,
      filename,
    ]);
  }

  if (rows.length === 0) return NextResponse.json({ error: "No valid data rows found" }, { status: 400 });

  // Use raw executeQuery for bulk insert (one HTTP call per chunk = much faster)
  const executeQuery = createExecuteQuery();
  const colList = SALES_COLS.join(", ");
  const CHUNK = 50; // 50 rows × 44 cols = 2200 params per call

  for (let i = 0; i < rows.length; i += CHUNK) {
    const chunk = rows.slice(i, i + CHUNK);
    const placeholders = chunk.map(() => `(${SALES_COLS.map(() => "?").join(", ")})`).join(", ");
    const params = chunk.flat();
    await executeQuery(
      `INSERT INTO sales (${colList}) VALUES ${placeholders}`,
      params,
      "run"
    );
  }

  await db.insert(uploadedCsv).values({
    filename: file.name,
    uploadTs: filename,
    rowCount: rows.length,
    status: "uploaded",
  });

  return NextResponse.json({ success: true, rowCount: rows.length });
}

function parseCSVLine(line: string): string[] {
  const result: string[] = [];
  let current = "";
  let inQuotes = false;
  for (let i = 0; i < line.length; i++) {
    const ch = line[i];
    if (ch === '"') {
      if (inQuotes && line[i + 1] === '"') { current += '"'; i++; }
      else { inQuotes = !inQuotes; }
    } else if (ch === "," && !inQuotes) {
      result.push(current.trim());
      current = "";
    } else {
      current += ch;
    }
  }
  result.push(current.trim());
  return result;
}

function toFloat(val: string | undefined): number | null {
  if (!val || val.trim() === "") return null;
  const n = parseFloat(val.replace(/[^0-9.-]/g, ""));
  return isNaN(n) ? null : n;
}

function parseDate(val: string | undefined): string | null {
  if (!val || val.trim() === "") return null;
  const clean = val.trim();
  if (/^\d{4}-\d{2}-\d{2}$/.test(clean)) return clean;
  const dmY = clean.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
  if (dmY) return `${dmY[3]}-${dmY[2].padStart(2, "0")}-${dmY[1].padStart(2, "0")}`;
  return clean;
}

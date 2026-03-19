import { NextResponse } from "next/server";
import { db } from "@/db";
import { uploadedCsv, sales } from "@/db/schema";
import { auth } from "@/lib/auth";
import { eq } from "drizzle-orm";

type RowValue = string | number | null;

function rowToInsert(row: RowValue[], filename: string) {
  return {
    invoiceNumber: String(row[0] ?? ""),
    invoiceDate: row[1] as string | null,
    customerAccount: String(row[2] ?? ""),
    customerName: row[3] as string | null,
    address1: row[4] as string | null,
    address2: row[5] as string | null,
    town: row[6] as string | null,
    country: row[7] as string | null,
    postcode: row[8] as string | null,
    spacer1: row[9] as string | null,
    customerAccount2: row[10] as string | null,
    numb1: row[11] as number | null,
    items: row[12] as number | null,
    weight: row[13] as number | null,
    invoiceTotal: row[14] as number | null,
    numb2: row[15] as number | null,
    spacer2: row[16] as string | null,
    jobNumber: row[17] as string | null,
    jobDate: row[18] as string | null,
    sendingDepot: row[19] as string | null,
    deliveryDepot: row[20] as string | null,
    destination: row[21] as string | null,
    town2: row[22] as string | null,
    postcode2: row[23] as string | null,
    serviceType: row[24] as string | null,
    items2: row[25] as number | null,
    volumeWeight: row[26] as number | null,
    numb3: row[27] as number | null,
    increasedLiabilityCover: row[28] as number | null,
    subTotal: row[29] as number | null,
    spacer3: row[30] as string | null,
    numb4: row[31] as number | null,
    senderReference: row[32] as string | null,
    numb5: row[33] as number | null,
    percentageFuelSurcharge: row[34] as number | null,
    percentageResourcingSurcharge: row[35] as number | null,
    spacer4: row[36] as string | null,
    sendersPostcode: row[37] as string | null,
    vatAmount: row[38] as number | null,
    vatPercent: row[39] as number | null,
    uploadCode: Math.random().toString(36).substring(2, 10),
    msCreated: 0,
    invoiceReady: 0,
    uploadTs: filename,
  };
}

export async function POST(req: Request) {
  try {
    const session = await auth();
    if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

    let file: File | null = null;
    try {
      const formData = await req.formData();
      file = formData.get("file") as File;
    } catch (e) {
      return NextResponse.json({ error: `Failed to read upload: ${String(e)}` }, { status: 400 });
    }
    if (!file) return NextResponse.json({ error: "No file uploaded" }, { status: 400 });

    const filename = file.name.replace(/\.[^.]+$/, "");

    try {
      const existing = await db.select({ id: sales.id }).from(sales).where(eq(sales.uploadTs, filename)).limit(1);
      if (existing.length > 0) {
        return NextResponse.json({ error: "Duplicate: this CSV file has already been uploaded." }, { status: 409 });
      }
    } catch (e) {
      return NextResponse.json({ error: `Database connection failed: ${String(e)}` }, { status: 500 });
    }

    const text = await file.text();
    const lines = text.split(/\r?\n/).filter(l => l.trim());
    if (lines.length === 0) return NextResponse.json({ error: "Empty file" }, { status: 400 });

    // Parse rows - no header row in this CSV format
    const rows: RowValue[][] = [];
    for (const line of lines) {
      const cols = parseCSVLine(line);
      if (cols.length < 3) continue;
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
      ]);
    }

    if (rows.length === 0) return NextResponse.json({ error: "No valid data rows found" }, { status: 400 });

    // Use db.batch() with parallel batchCallback - all queries fire simultaneously
    // Chunk into groups of 200 to avoid hitting any request size limits
    const BATCH_SIZE = 200;
    for (let i = 0; i < rows.length; i += BATCH_SIZE) {
      const chunk = rows.slice(i, i + BATCH_SIZE);
      try {
        // eslint-disable-next-line @typescript-eslint/no-explicit-any
        await Promise.all(
          chunk.map(row => db.insert(sales).values(rowToInsert(row, filename)))
        );
      } catch (e) {
        return NextResponse.json({ error: `Insert failed at row ${i + 1}: ${String(e)}` }, { status: 500 });
      }
    }

    await db.insert(uploadedCsv).values({
      filename: file.name,
      uploadTs: filename,
      rowCount: rows.length,
      status: "uploaded",
    });

    return NextResponse.json({ success: true, rowCount: rows.length });

  } catch (e) {
    console.error("Upload error:", e);
    return NextResponse.json({ error: `Unexpected error: ${String(e)}` }, { status: 500 });
  }
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
  // Already YYYY-MM-DD
  if (/^\d{4}-\d{2}-\d{2}$/.test(clean)) return clean;
  // YYYYMMDD (no separators) e.g. 20260313
  if (/^\d{8}$/.test(clean)) {
    return `${clean.slice(0, 4)}-${clean.slice(4, 6)}-${clean.slice(6, 8)}`;
  }
  // DD/MM/YYYY
  const dmY = clean.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
  if (dmY) return `${dmY[3]}-${dmY[2].padStart(2, "0")}-${dmY[1].padStart(2, "0")}`;
  return clean;
}

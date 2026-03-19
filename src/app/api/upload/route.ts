import { NextResponse } from "next/server";
import { db } from "@/db";
import { sales, uploadedCsv } from "@/db/schema";
import { auth } from "@/lib/auth";
import { eq } from "drizzle-orm";

export async function POST(req: Request) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  const formData = await req.formData();
  const file = formData.get("file") as File;
  if (!file) return NextResponse.json({ error: "No file uploaded" }, { status: 400 });

  const filename = file.name.replace(/\.[^.]+$/, "");
  const existing = await db.select().from(sales).where(eq(sales.uploadTs, filename)).limit(1);
  if (existing.length > 0) {
    return NextResponse.json({ error: "Duplicate: this CSV file has already been uploaded." }, { status: 409 });
  }

  const text = await file.text();
  const lines = text.split(/\r?\n/).filter(l => l.trim());
  if (lines.length === 0) return NextResponse.json({ error: "Empty file" }, { status: 400 });

  const rows = [];
  for (const line of lines) {
    const cols = parseCSVLine(line);
    if (cols.length < 10) continue;
    const customerAccount = cols[2]?.trim() ?? '';
    if (!customerAccount) continue;
    rows.push({
      invoiceNumber: cols[0]?.trim() ?? '',
      invoiceDate: parseDate(cols[1]),
      customerAccount,
      customerName: cols[3]?.trim() ?? null,
      address1: cols[4]?.trim() ?? null,
      address2: cols[5]?.trim() ?? null,
      town: cols[6]?.trim() ?? null,
      country: cols[7]?.trim() ?? null,
      postcode: cols[8]?.trim() ?? null,
      spacer1: cols[9]?.trim() ?? null,
      customerAccount2: cols[10]?.trim() ?? null,
      numb1: toFloat(cols[11]),
      items: toFloat(cols[12]),
      weight: toFloat(cols[13]),
      invoiceTotal: toFloat(cols[14]),
      numb2: toFloat(cols[15]),
      spacer2: cols[16]?.trim() ?? null,
      jobNumber: cols[17]?.trim() ?? null,
      jobDate: parseDate(cols[18]),
      sendingDepot: cols[19]?.trim() ?? null,
      deliveryDepot: cols[20]?.trim() ?? null,
      destination: cols[21]?.trim() ?? null,
      town2: cols[22]?.trim() ?? null,
      postcode2: cols[23]?.trim() ?? null,
      serviceType: cols[24]?.trim() ?? null,
      items2: toFloat(cols[25]),
      volumeWeight: toFloat(cols[26]),
      numb3: toFloat(cols[27]),
      increasedLiabilityCover: toFloat(cols[28]),
      subTotal: toFloat(cols[29]),
      spacer3: cols[30]?.trim() ?? null,
      numb4: toFloat(cols[31]),
      senderReference: cols[32]?.trim() ?? null,
      numb5: toFloat(cols[33]),
      percentageFuelSurcharge: toFloat(cols[34]),
      percentageResourcingSurcharge: toFloat(cols[35]),
      spacer4: cols[36]?.trim() ?? null,
      sendersPostcode: cols[37]?.trim() ?? null,
      vatAmount: toFloat(cols[38]),
      vatPercent: toFloat(cols[39]),
      uploadCode: Math.random().toString(36).substring(2, 10),
      msCreated: 0,
      invoiceReady: 0,
      uploadTs: filename,
    });
  }

  if (rows.length === 0) return NextResponse.json({ error: "No valid data rows found" }, { status: 400 });

  const chunkSize = 500;
  for (let i = 0; i < rows.length; i += chunkSize) {
    await db.insert(sales).values(rows.slice(i, i + chunkSize));
  }

  await db.insert(uploadedCsv).values({ filename: file.name, uploadTs: filename, rowCount: rows.length, status: "uploaded" });

  return NextResponse.json({ success: true, rowCount: rows.length });
}

function parseCSVLine(line: string): string[] {
  const result: string[] = [];
  let current = "";
  let inQuotes = false;
  for (let i = 0; i < line.length; i++) {
    const ch = line[i];
    if (ch === '"') { if (inQuotes && line[i+1] === '"') { current += '"'; i++; } else { inQuotes = !inQuotes; } }
    else if (ch === ',' && !inQuotes) { result.push(current.trim()); current = ""; }
    else { current += ch; }
  }
  result.push(current.trim());
  return result;
}

function toFloat(val: string | undefined): number | null {
  if (!val || val.trim() === '') return null;
  const n = parseFloat(val.replace(/[^0-9.-]/g, ''));
  return isNaN(n) ? null : n;
}

function parseDate(val: string | undefined): string | null {
  if (!val || val.trim() === '') return null;
  const clean = val.trim();
  if (/^\d{4}-\d{2}-\d{2}$/.test(clean)) return clean;
  const dmY = clean.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
  if (dmY) return `${dmY[3]}-${dmY[2].padStart(2,'0')}-${dmY[1].padStart(2,'0')}`;
  return clean;
}

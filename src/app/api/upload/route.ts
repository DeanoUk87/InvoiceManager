import { NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";
import { auth } from "@/lib/auth";

export async function POST(req: Request) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

  const formData = await req.formData();
  const file = formData.get("file") as File;
  if (!file) return NextResponse.json({ error: "No file uploaded" }, { status: 400 });

  const filename = file.name.replace(/\.[^.]+$/, ""); // strip extension

  // Check for duplicate
  const existing = await prisma.sale.findFirst({ where: { uploadTs: filename } });
  if (existing) {
    return NextResponse.json(
      { error: "Duplicate: this CSV file has already been uploaded." },
      { status: 409 }
    );
  }

  const text = await file.text();
  const lines = text.split(/\r?\n/).filter((l) => l.trim());
  if (lines.length === 0) return NextResponse.json({ error: "Empty file" }, { status: 400 });

  // Parse CSV (no header row based on the original import)
  const rows: Record<string, string | number | null>[] = [];
  for (const line of lines) {
    const cols = parseCSVLine(line);
    if (cols.length < 10) continue;

    const row = {
      invoiceNumber: cols[0] ?? null,
      invoiceDate: parseDate(cols[1]),
      customerAccount: cols[2] ?? null,
      customerName: cols[3] ?? null,
      address1: cols[4] ?? null,
      address2: cols[5] ?? null,
      town: cols[6] ?? null,
      country: cols[7] ?? null,
      postcode: cols[8] ?? null,
      spacer1: cols[9] ?? null,
      customerAccount2: cols[10] ?? null,
      numb1: toFloat(cols[11]),
      items: toFloat(cols[12]),
      weight: toFloat(cols[13]),
      invoiceTotal: toFloat(cols[14]),
      numb2: toFloat(cols[15]),
      spacer2: cols[16] ?? null,
      jobNumber: cols[17] ?? null,
      jobDate: parseDate(cols[18]),
      sendingDepot: cols[19] ?? null,
      deliveryDepot: cols[20] ?? null,
      destination: cols[21] ?? null,
      town2: cols[22] ?? null,
      postcode2: cols[23] ?? null,
      serviceType: cols[24] ?? null,
      items2: toFloat(cols[25]),
      volumeWeight: toFloat(cols[26]),
      numb3: toFloat(cols[27]),
      increasedLiabilityCover: toFloat(cols[28]),
      subTotal: toFloat(cols[29]),
      spacer3: cols[30] ?? null,
      numb4: toFloat(cols[31]),
      senderReference: cols[32] ?? null,
      numb5: toFloat(cols[33]),
      percentageFuelSurcharge: toFloat(cols[34]),
      percentageResourcingSurcharge: toFloat(cols[35]),
      spacer4: cols[36] ?? null,
      sendersPostcode: cols[37] ?? null,
      vatAmount: toFloat(cols[38]),
      vatPercent: toFloat(cols[39]),
      uploadCode: Math.random().toString(36).substring(2, 10),
      msCreated: 0,
      invoiceReady: 0,
      uploadTs: filename,
    };

    // Skip rows with no customer account
    if (!row.customerAccount) continue;
    rows.push(row as Record<string, string | number | null>);
  }

  if (rows.length === 0) return NextResponse.json({ error: "No valid data rows found" }, { status: 400 });

  // Batch insert in chunks of 500
  const chunkSize = 500;
  for (let i = 0; i < rows.length; i += chunkSize) {
    const chunk = rows.slice(i, i + chunkSize);
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    await prisma.sale.createMany({ data: chunk as any });
  }

  // Log upload
  await prisma.uploadedCsv.create({
    data: {
      filename: file.name,
      uploadTs: filename,
      rowCount: rows.length,
      status: "uploaded",
    },
  });

  // Remove rows with empty customer account
  await prisma.sale.deleteMany({ where: { customerAccount: "" } });

  return NextResponse.json({ success: true, rowCount: rows.length });
}

function parseCSVLine(line: string): string[] {
  const result: string[] = [];
  let current = "";
  let inQuotes = false;
  for (let i = 0; i < line.length; i++) {
    const ch = line[i];
    if (ch === '"') {
      if (inQuotes && line[i + 1] === '"') {
        current += '"';
        i++;
      } else {
        inQuotes = !inQuotes;
      }
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
  // Try various formats
  const clean = val.trim();
  // Already YYYY-MM-DD
  if (/^\d{4}-\d{2}-\d{2}$/.test(clean)) return clean;
  // DD/MM/YYYY
  const dmY = clean.match(/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/);
  if (dmY) return `${dmY[3]}-${dmY[2].padStart(2, "0")}-${dmY[1].padStart(2, "0")}`;
  // MM/DD/YYYY
  const mdY = clean.match(/^(\d{1,2})\/(\d{1,2})\/(\d{2,4})$/);
  if (mdY) return `${mdY[3].padStart(4, "20")}-${mdY[1].padStart(2, "0")}-${mdY[2].padStart(2, "0")}`;
  return clean;
}

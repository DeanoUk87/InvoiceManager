import { NextResponse } from "next/server";
import { db } from "@/db";
import { customers, settings } from "@/db/schema";
import { auth } from "@/lib/auth";
import { ne } from "drizzle-orm";
import nodemailer from "nodemailer";

export async function POST(req: Request) {
  try {
    const session = await auth();
    if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });

    const formData = await req.formData();
    const subject = formData.get("subject") as string;
    const body = formData.get("body") as string;
    const attachment1 = formData.get("attachment1") as File | null;
    const attachment2 = formData.get("attachment2") as File | null;

    if (!subject || !body) {
      return NextResponse.json({ error: "Subject and message body are required." }, { status: 400 });
    }

    const [sett] = await db.select().from(settings).limit(1);
    if (!sett?.cemail) {
      return NextResponse.json({ error: "Company email not configured. Set it in Invoice Settings." }, { status: 400 });
    }

    // Get all customers with an email
    const allCustomers = await db.select({
      customerAccount: customers.customerAccount,
      customerEmail: customers.customerEmail,
    }).from(customers).where(ne(customers.customerEmail, ""));

    const recipients = allCustomers.filter(c => c.customerEmail && c.customerEmail.trim());
    if (recipients.length === 0) {
      return NextResponse.json({ error: "No customers with email addresses found." }, { status: 400 });
    }

    // Build attachments
    const attachments: { filename: string; content: Buffer; contentType: string }[] = [];
    if (attachment1 && attachment1.size > 0) {
      const buf = Buffer.from(await attachment1.arrayBuffer());
      attachments.push({ filename: attachment1.name, content: buf, contentType: attachment1.type });
    }
    if (attachment2 && attachment2.size > 0) {
      const buf = Buffer.from(await attachment2.arrayBuffer());
      attachments.push({ filename: attachment2.name, content: buf, contentType: attachment2.type });
    }

    // Convert plain text body to HTML paragraphs
    const htmlBody = body
      .split(/\n\n+/)
      .map(para => `<p style="margin:0 0 14px 0;line-height:1.7">${para.replace(/\n/g, "<br>")}</p>`)
      .join("");

    const html = `<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif">
  <table width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:32px 16px">
    <tr><td align="center">
      <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08)">
        <!-- Header -->
        <tr>
          <td style="background:#2563eb;padding:28px 36px;text-align:center">
            <div style="display:inline-block;background:rgba(255,255,255,0.15);border-radius:10px;padding:10px 16px;margin-bottom:10px">
              <span style="color:white;font-size:22px;font-weight:700;letter-spacing:0.5px">${sett.companyName ?? "Invoice Manager"}</span>
            </div>
            <p style="color:rgba(255,255,255,0.85);margin:0;font-size:13px">${subject}</p>
          </td>
        </tr>
        <!-- Body -->
        <tr>
          <td style="padding:36px 36px 24px">
            <div style="font-size:14px;color:#374151">
              ${htmlBody}
            </div>
          </td>
        </tr>
        <!-- Divider -->
        <tr><td style="padding:0 36px"><hr style="border:none;border-top:1px solid #e5e7eb;margin:0"></td></tr>
        <!-- Footer -->
        <tr>
          <td style="padding:20px 36px;background:#f8fafc;text-align:center">
            <p style="margin:0 0 4px;font-size:12px;font-weight:600;color:#1e293b">${sett.companyName ?? ""}</p>
            ${sett.companyAddress1 ? `<p style="margin:0;font-size:11px;color:#64748b">${sett.companyAddress1}${sett.city ? `, ${sett.city}` : ""}${sett.postcode ? ` ${sett.postcode}` : ""}</p>` : ""}
            ${sett.phone ? `<p style="margin:4px 0 0;font-size:11px;color:#64748b">Tel: ${sett.phone}</p>` : ""}
            ${sett.cemail ? `<p style="margin:4px 0 0;font-size:11px;color:#2563eb">${sett.cemail}</p>` : ""}
            <p style="margin:12px 0 0;font-size:10px;color:#94a3b8">This email was sent to you as a customer of ${sett.companyName ?? "our company"}.</p>
          </td>
        </tr>
      </table>
    </td></tr>
  </table>
</body>
</html>`;

    if (!process.env.SMTP_PASS) {
      // Simulate success in dev/demo mode
      return NextResponse.json({
        success: true,
        sent: recipients.length,
        message: `[Demo mode - SMTP not configured] Would have sent to ${recipients.length} customers.`,
      });
    }

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const transporter = (nodemailer as any).createTransport({
      host: process.env.SMTP_HOST ?? "smtp.gmail.com",
      port: parseInt(process.env.SMTP_PORT ?? "587"),
      secure: false,
      auth: { user: process.env.SMTP_USER ?? sett.cemail, pass: process.env.SMTP_PASS },
    });

    let sent = 0;
    let failed = 0;

    // Send in batches of 10 concurrent
    const CONCURRENCY = 10;
    for (let i = 0; i < recipients.length; i += CONCURRENCY) {
      const batch = recipients.slice(i, i + CONCURRENCY);
      await Promise.all(batch.map(async (c) => {
        try {
          // eslint-disable-next-line @typescript-eslint/no-explicit-any
          const mail: any = {
            from: `"${sett.companyName}" <${sett.cemail}>`,
            to: c.customerEmail!,
            subject,
            html,
            attachments,
          };
          await transporter.sendMail(mail);
          sent++;
        } catch {
          failed++;
        }
      }));
    }

    return NextResponse.json({
      success: true,
      sent,
      failed,
      total: recipients.length,
      message: `Campaign sent to ${sent} customers.${failed > 0 ? ` ${failed} failed.` : ""}`,
    });

  } catch (e) {
    console.error("Mass mail error:", e);
    return NextResponse.json({ error: `Failed: ${String(e)}` }, { status: 500 });
  }
}

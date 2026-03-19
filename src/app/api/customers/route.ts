import { NextResponse } from "next/server";
import { prisma } from "@/lib/prisma";
import { auth } from "@/lib/auth";

export async function GET() {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
  const customers = await prisma.customer.findMany({ orderBy: { customerAccount: "asc" } });
  return NextResponse.json(customers);
}

export async function POST(req: Request) {
  const session = await auth();
  if (!session) return NextResponse.json({ error: "Unauthorized" }, { status: 401 });
  const data = await req.json();
  const customer = await prisma.customer.create({ data });
  return NextResponse.json(customer);
}

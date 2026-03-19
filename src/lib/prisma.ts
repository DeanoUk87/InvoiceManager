import { PrismaClient } from "@prisma/client";
import path from "path";

let prismaInstance: PrismaClient | null = null;

function createPrismaClient(): PrismaClient {
  // eslint-disable-next-line @typescript-eslint/no-require-imports
  const { PrismaBetterSqlite3 } = require("@prisma/adapter-better-sqlite3");
  const dbPath = path.resolve(process.cwd(), "prisma/dev.db");
  const adapter = new PrismaBetterSqlite3({ url: `file:${dbPath}` });
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  return new PrismaClient({ adapter } as any);
}

export function getPrisma(): PrismaClient {
  if (!prismaInstance) {
    prismaInstance = createPrismaClient();
  }
  return prismaInstance;
}

export const prisma: PrismaClient = new Proxy({} as PrismaClient, {
  get(_target, prop) {
    const instance = getPrisma();
    const val = (instance as unknown as Record<string | symbol, unknown>)[prop];
    if (typeof val === "function") return val.bind(instance);
    return val;
  },
});

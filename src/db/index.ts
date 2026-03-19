import { createDatabase } from "@kilocode/app-builder-db";
import { drizzle } from "drizzle-orm/sqlite-proxy";
import * as schema from "./schema";

type Schema = typeof schema;
type DrizzleDb = ReturnType<typeof drizzle<Schema>>;

let _db: DrizzleDb | null = null;

export function getDb(): DrizzleDb {
  if (!_db) {
    _db = createDatabase(schema) as unknown as DrizzleDb;
  }
  return _db;
}

// Lazy proxy - DB connection only made on first request, not at build time
export const db: DrizzleDb = new Proxy({} as DrizzleDb, {
  get(_target, prop) {
    const instance = getDb();
    const val = (instance as unknown as Record<string | symbol, unknown>)[prop];
    if (typeof val === "function") return val.bind(instance);
    return val;
  },
});

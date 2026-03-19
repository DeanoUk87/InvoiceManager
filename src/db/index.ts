import { createDatabase } from "@kilocode/app-builder-db";
import * as schema from "./schema";
import type { LibSQLDatabase } from "drizzle-orm/libsql";

type Schema = typeof schema;

let _db: LibSQLDatabase<Schema> | null = null;

export function getDb(): LibSQLDatabase<Schema> {
  if (!_db) {
    _db = createDatabase(schema) as LibSQLDatabase<Schema>;
  }
  return _db;
}

// Lazy proxy so `db.select()` etc. work as if db is already initialised,
// but the actual connection is only made on first use at request time.
export const db: LibSQLDatabase<Schema> = new Proxy({} as LibSQLDatabase<Schema>, {
  get(_target, prop) {
    const instance = getDb();
    const val = (instance as unknown as Record<string | symbol, unknown>)[prop];
    if (typeof val === "function") return val.bind(instance);
    return val;
  },
});

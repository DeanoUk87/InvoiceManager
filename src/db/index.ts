import { drizzle } from "drizzle-orm/sqlite-proxy";
import * as schema from "./schema";

type Schema = typeof schema;
type DrizzleDb = ReturnType<typeof drizzle<Schema>>;

let _db: DrizzleDb | null = null;

export function getDb(): DrizzleDb {
  if (_db) return _db;
  // createDatabase() calls createExecuteQuery() which reads DB_URL/DB_TOKEN
  // This MUST only run at request time, never at module load / build time
  // eslint-disable-next-line @typescript-eslint/no-require-imports
  const { createDatabase } = require("@kilocode/app-builder-db");
  _db = createDatabase(schema) as unknown as DrizzleDb;
  return _db;
}

// Lazy proxy - getDb() is only called when a property is first accessed (request time)
export const db: DrizzleDb = new Proxy({} as DrizzleDb, {
  get(_target, prop) {
    const instance = getDb();
    const val = (instance as unknown as Record<string | symbol, unknown>)[prop];
    if (typeof val === "function") return val.bind(instance);
    return val;
  },
});

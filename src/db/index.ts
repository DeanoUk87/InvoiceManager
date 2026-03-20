import { drizzle as drizzleProxy } from "drizzle-orm/sqlite-proxy";
import * as schema from "./schema";

type Schema = typeof schema;
type DrizzleDb = ReturnType<typeof drizzleProxy<Schema>>;

let _db: DrizzleDb | null = null;

export function getDb(): DrizzleDb {
  if (_db) return _db;

  const dbUrl = process.env.DATABASE_URL;

  // Self-hosted: DATABASE_URL=file:./data/company.db
  // Uses @libsql/client - pure JavaScript, no native compilation needed
  if (dbUrl && dbUrl.startsWith("file:")) {
    const { createClient } = require("@libsql/client"); // eslint-disable-line @typescript-eslint/no-require-imports
    const { drizzle } = require("drizzle-orm/libsql");  // eslint-disable-line @typescript-eslint/no-require-imports
    const client = createClient({ url: dbUrl.replace(/^file:\/\//, "file:") });
    _db = drizzle(client, { schema }) as unknown as DrizzleDb;
    return _db;
  }

  // Kilo sandbox: uses @kilocode/app-builder-db remote proxy (DB_URL + DB_TOKEN)
  const { createDatabase } = require("@kilocode/app-builder-db"); // eslint-disable-line @typescript-eslint/no-require-imports
  _db = createDatabase(schema) as unknown as DrizzleDb;
  return _db;
}

// Lazy proxy - only connects at request time, never at build time
export const db: DrizzleDb = new Proxy({} as DrizzleDb, {
  get(_target, prop) {
    const instance = getDb();
    const val = (instance as unknown as Record<string | symbol, unknown>)[prop];
    if (typeof val === "function") return val.bind(instance);
    return val;
  },
});

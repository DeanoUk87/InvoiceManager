import { drizzle } from "drizzle-orm/sqlite-proxy";
import * as schema from "./schema";

type Schema = typeof schema;
type DrizzleDb = ReturnType<typeof drizzle<Schema>>;

let _db: DrizzleDb | null = null;

export function getDb(): DrizzleDb {
  if (_db) return _db;

  const dbUrl = process.env.DATABASE_URL;

  // Self-hosted: DATABASE_URL=file:./data/company.db
  if (dbUrl && dbUrl.startsWith("file:")) {
    const path = require("path"); // eslint-disable-line @typescript-eslint/no-require-imports
    const fs = require("fs");     // eslint-disable-line @typescript-eslint/no-require-imports
    const Database = require("better-sqlite3"); // eslint-disable-line @typescript-eslint/no-require-imports
    const dbPath = path.resolve(process.cwd(), dbUrl.replace(/^file:/, ""));
    const dir = path.dirname(dbPath);
    if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });
    const sqlite = new Database(dbPath);
    sqlite.pragma("journal_mode = WAL");
    const { drizzle: drizzleBs } = require("drizzle-orm/better-sqlite3"); // eslint-disable-line @typescript-eslint/no-require-imports
    _db = drizzleBs(sqlite, { schema }) as unknown as DrizzleDb;
    return _db;
  }

  // Kilo sandbox: DB_URL + DB_TOKEN remote proxy
  const { createDatabase } = require("@kilocode/app-builder-db"); // eslint-disable-line @typescript-eslint/no-require-imports
  _db = createDatabase(schema) as unknown as DrizzleDb;
  return _db;
}

export const db: DrizzleDb = new Proxy({} as DrizzleDb, {
  get(_target, prop) {
    const instance = getDb();
    const val = (instance as unknown as Record<string | symbol, unknown>)[prop];
    if (typeof val === "function") return val.bind(instance);
    return val;
  },
});

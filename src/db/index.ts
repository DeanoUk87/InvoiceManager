import { createDatabase, createExecuteQuery } from "@kilocode/app-builder-db";
import { drizzle } from "drizzle-orm/sqlite-proxy";
import * as schema from "./schema";

type Schema = typeof schema;
// Use the drizzle sqlite-proxy type directly
type DrizzleDb = ReturnType<typeof drizzle<Schema>>;

let _db: DrizzleDb | null = null;

export function getDb(): DrizzleDb {
  if (!_db) {
    _db = createDatabase(schema) as unknown as DrizzleDb;
  }
  return _db;
}

// Lazy proxy - connection only made on first request, not at build time
export const db: DrizzleDb = new Proxy({} as DrizzleDb, {
  get(_target, prop) {
    const instance = getDb();
    const val = (instance as unknown as Record<string | symbol, unknown>)[prop];
    if (typeof val === "function") return val.bind(instance);
    return val;
  },
});

/**
 * A db instance that supports db.batch() by running all queries in parallel
 * via Promise.all - sends all INSERT/UPDATE queries concurrently to the proxy.
 * Use this for bulk operations like CSV upload.
 */
let _batchDb: DrizzleDb | null = null;

export function getBatchDb(): DrizzleDb {
  if (!_batchDb) {
    const executeQuery = createExecuteQuery();

    // Single-query callback (same as normal db)
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const queryCallback = async (sql: string, params: unknown[], method: any) => {
      const result = await executeQuery(sql, params, method);
      return { rows: result.rows as unknown[][] };
    };

    // Batch callback: runs all queries in parallel (Promise.all)
    // Each query still goes to the proxy individually but all fire simultaneously
    const batchCallback = async (
      // eslint-disable-next-line @typescript-eslint/no-explicit-any
      queries: { sql: string; params: unknown[]; method: any }[]
    ) => {
      const results = await Promise.all(
        queries.map(q => executeQuery(q.sql, q.params, q.method))
      );
      return results.map(r => ({ rows: r.rows as unknown[][] }));
    };

    _batchDb = drizzle(queryCallback, batchCallback, { schema }) as unknown as DrizzleDb;
  }
  return _batchDb;
}

export const batchDb: DrizzleDb = new Proxy({} as DrizzleDb, {
  get(_target, prop) {
    const instance = getBatchDb();
    const val = (instance as unknown as Record<string | symbol, unknown>)[prop];
    if (typeof val === "function") return val.bind(instance);
    return val;
  },
});

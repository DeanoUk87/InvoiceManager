#!/usr/bin/env node
/**
 * Database setup script for self-hosted deployments.
 * Run once: node scripts/setup-db.js
 * Uses @libsql/client - no native compilation needed.
 */

const path = require("path");
const fs = require("fs");

const dbUrl = process.env.DATABASE_URL || "file:./data/invoice.db";
if (!dbUrl.startsWith("file:")) {
  console.log("Not a local SQLite database - skipping local setup.");
  process.exit(0);
}

// Ensure data directory exists
const dbPath = path.resolve(process.cwd(), dbUrl.replace(/^file:/, ""));
const dir = path.dirname(dbPath);
if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });

console.log(`Setting up database at: ${dbPath}`);

// Use @libsql/client (pure JS - works on any server, no compilation needed)
const { createClient } = require("@libsql/client");
const client = createClient({ url: dbUrl });

async function run() {
  // Read and apply the SQL setup file
  const sqlFile = path.join(__dirname, "../setup-database.sql");
  if (!fs.existsSync(sqlFile)) {
    console.error("setup-database.sql not found!");
    process.exit(1);
  }

  const sql = fs.readFileSync(sqlFile, "utf8");
  // Split on semicolons, skip comments and empty statements
  const statements = sql
    .split(";")
    .map(s => s.replace(/--[^\n]*/g, "").trim())
    .filter(s => s.length > 0);

  for (const stmt of statements) {
    try {
      await client.execute(stmt);
    } catch (e) {
      const msg = e.message || "";
      if (!msg.includes("already exists") && !msg.includes("UNIQUE constraint")) {
        console.warn("Warning:", msg.substring(0, 120));
      }
    }
  }

  console.log("Database schema ready.");

  // Verify admin user was created
  const result = await client.execute("SELECT email, role FROM users WHERE role='admin' LIMIT 1");
  if (result.rows.length > 0) {
    console.log("\nAdmin user ready:");
    console.log("  Email:    admin@invoicemanager.com");
    console.log("  Password: Admin123!");
    console.log("  ⚠️  Change this after first login via Admin & Settings > User Management\n");
  }

  await client.close();
  console.log("✅ Setup complete. Run: npm run build && npm start");
}

run().catch(e => { console.error("Setup failed:", e.message); process.exit(1); });

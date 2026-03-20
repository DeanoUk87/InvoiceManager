#!/usr/bin/env node
/**
 * Database setup script for self-hosted deployments.
 * Run once after deployment: node scripts/setup-db.js
 * Or add to your start command: node scripts/setup-db.js && bun start
 */
const path = require("path");
const fs = require("fs");
const Database = require("better-sqlite3");
const bcrypt = require("bcryptjs");

const dbUrl = process.env.DATABASE_URL || "file:./data/invoice.db";
if (!dbUrl.startsWith("file:")) {
  console.log("Not a local SQLite database, skipping setup.");
  process.exit(0);
}

const dbPath = path.resolve(process.cwd(), dbUrl.replace(/^file:/, ""));
const dir = path.dirname(dbPath);
if (!fs.existsSync(dir)) fs.mkdirSync(dir, { recursive: true });

console.log(`Setting up database at: ${dbPath}`);
const db = new Database(dbPath);
db.pragma("journal_mode = WAL");

// Read and apply the migration SQL
const migrationPath = path.join(__dirname, "../src/db/migrations/0000_romantic_butterfly.sql");
const migration1Path = path.join(__dirname, "../src/db/migrations/0001_fantastic_wither.sql");

function applyMigration(sqlPath) {
  if (!fs.existsSync(sqlPath)) return;
  const sql = fs.readFileSync(sqlPath, "utf8");
  const statements = sql.split(";").map(s => s.trim()).filter(s => s.length > 0);
  for (const stmt of statements) {
    try { db.exec(stmt + ";"); } catch (e) {
      // Ignore "already exists" errors on re-run
      if (!e.message.includes("already exists") && !e.message.includes("duplicate")) {
        console.warn("Migration warning:", e.message.substring(0, 100));
      }
    }
  }
}

applyMigration(migrationPath);
applyMigration(migration1Path);

// Handle additional migrations
const migrationsDir = path.join(__dirname, "../src/db/migrations");
const migrationFiles = fs.readdirSync(migrationsDir)
  .filter(f => f.endsWith(".sql"))
  .sort();
for (const file of migrationFiles) {
  applyMigration(path.join(migrationsDir, file));
}

console.log("Database schema ready.");

// Seed admin user if none exists
const adminEmail = process.env.ADMIN_EMAIL || "admin@invoicemanager.com";
const adminPassword = process.env.ADMIN_PASSWORD || "ChangeMe123!";
const companyName = process.env.COMPANY_NAME || "Your Company Ltd";

const existingAdmin = db.prepare("SELECT id FROM users WHERE role = 'admin' LIMIT 1").get();
if (!existingAdmin) {
  const hashed = bcrypt.hashSync(adminPassword, 10);
  const id = `admin-${Date.now()}`;
  db.prepare("INSERT INTO users (id, name, email, password, role, username) VALUES (?, ?, ?, ?, ?, ?)")
    .run(id, "Admin", adminEmail, hashed, "admin", "admin");
  console.log(`\nAdmin user created:`);
  console.log(`  Email: ${adminEmail}`);
  console.log(`  Password: ${adminPassword}`);
  console.log(`  ⚠️  Change this password after first login!\n`);
} else {
  console.log("Admin user already exists.");
}

// Seed default settings if none exist
const existingSettings = db.prepare("SELECT id FROM settings LIMIT 1").get();
if (!existingSettings) {
  db.prepare(`INSERT INTO settings (company_name, cemail, send_limit, fuel_surcharge_percent, resourcing_surcharge_percent, vat_percent)
    VALUES (?, ?, 50, 3.5, 0, 20)`)
    .run(companyName, adminEmail);
  console.log(`Settings created for: ${companyName}`);
}

db.close();
console.log("\n✅ Setup complete. You can now start the application.");

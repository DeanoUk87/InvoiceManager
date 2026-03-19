# Current Context

## Current State
Full Invoice Manager application built and deployed. Database layer migrated from Prisma/better-sqlite3 to Drizzle ORM with @kilocode/app-builder-db (sandbox-compatible remote SQLite proxy).

## Recently Completed
- [x] Full authentication (NextAuth v5, JWT, role-based)
- [x] Drizzle ORM schema (8 tables: users, customers, sales, invoices, settings, uploaded_csv, sales_archive, invoices_archive)
- [x] Drizzle migrations generated in src/db/migrations/
- [x] Lazy db proxy to avoid build-time DB_URL/DB_TOKEN errors
- [x] All 37 routes built (API + pages)
- [x] CSV upload with batch processing (500 rows/chunk)
- [x] Invoice generation from CSV data
- [x] Bulk email send with nodemailer
- [x] PDF/Excel export per invoice
- [x] Seed endpoint at /api/seed

## Session History
- 2026-03-19: Complete application built from scratch based on Laravel/PHP original

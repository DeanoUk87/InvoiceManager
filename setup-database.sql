-- =============================================================================
-- INVOICE MANAGER - DATABASE SETUP SQL
-- =============================================================================
-- Run this file once to create all tables and the default admin user.
--
-- INSTRUCTIONS (SQLite):
--   sqlite3 /path/to/your-company.db < setup-database.sql
--
-- DEFAULT LOGIN AFTER RUNNING THIS SCRIPT:
--   Email:    admin@invoicemanager.com
--   Password: Admin123!
--
-- IMPORTANT: Log in immediately and change the email and password via:
--   Admin & Settings > User Management > Edit (pencil icon on admin user)
-- =============================================================================


-- -----------------------------------------------------------------------------
-- USERS TABLE
-- Stores admin, manager, user and customer portal logins.
-- Roles: admin | admin2 (manager) | user | customer
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id`         TEXT    PRIMARY KEY NOT NULL,
    `name`       TEXT,
    `email`      TEXT    NOT NULL,
    `password`   TEXT    NOT NULL,
    `role`       TEXT    NOT NULL DEFAULT 'user',
    `username`   TEXT,
    `created_at` INTEGER,
    `updated_at` INTEGER
);

CREATE UNIQUE INDEX IF NOT EXISTS `users_email_unique`    ON `users` (`email`);
CREATE UNIQUE INDEX IF NOT EXISTS `users_username_unique` ON `users` (`username`);


-- -----------------------------------------------------------------------------
-- CUSTOMERS TABLE
-- One row per customer account.
-- customer_account links to the sales/invoices data from the CSV.
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `customers` (
    `id`               INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    `customer_account` TEXT    NOT NULL,
    `customer_email`   TEXT,
    `customer_email_bcc` TEXT,
    `customer_phone`   TEXT,
    `terms_of_payment` TEXT,
    `message_type`     TEXT,
    `po_number`        TEXT,
    `customer_message` TEXT,
    `login_access`     INTEGER DEFAULT 0,
    `created_at`       INTEGER,
    `updated_at`       INTEGER
);

CREATE UNIQUE INDEX IF NOT EXISTS `customers_customer_account_unique` ON `customers` (`customer_account`);


-- -----------------------------------------------------------------------------
-- SALES TABLE
-- One row per job line from the uploaded CSV.
-- Multiple rows share the same invoice_number for one invoice.
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sales` (
    `id`                              INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    `invoice_number`                  TEXT    NOT NULL,
    `invoice_date`                    TEXT,
    `customer_account`                TEXT    NOT NULL,
    `customer_name`                   TEXT,
    `address1`                        TEXT,
    `address2`                        TEXT,
    `town`                            TEXT,
    `country`                         TEXT,
    `postcode`                        TEXT,
    `spacer1`                         TEXT,
    `customer_account2`               TEXT,
    `numb1`                           REAL,
    `items`                           REAL,
    `weight`                          REAL,
    `invoice_total`                   REAL,
    `numb2`                           REAL,
    `spacer2`                         TEXT,
    `job_number`                      TEXT,
    `job_date`                        TEXT,
    `sending_depot`                   TEXT,
    `delivery_depot`                  TEXT,
    `destination`                     TEXT,
    `town2`                           TEXT,
    `postcode2`                       TEXT,
    `service_type`                    TEXT,
    `items2`                          REAL,
    `volume_weight`                   REAL,
    `numb3`                           REAL,
    `increased_liability_cover`       REAL,
    `sub_total`                       REAL,
    `spacer3`                         TEXT,
    `numb4`                           REAL,
    `sender_reference`                TEXT,
    `numb5`                           REAL,
    `percentage_fuel_surcharge`       REAL,
    `spacer4`                         TEXT,
    `senders_postcode`                TEXT,
    `vat_amount`                      REAL,
    `vat_percent`                     REAL,
    `percentage_resourcing_surcharge` REAL,
    `upload_code`                     TEXT,
    `ms_created`                      INTEGER DEFAULT 0,
    `invoice_ready`                   INTEGER DEFAULT 0,
    `upload_ts`                       TEXT,
    `created_at`                      INTEGER
);


-- -----------------------------------------------------------------------------
-- INVOICES TABLE
-- One row per unique invoice (header record).
-- printer: 0=unprinted, 1=pending, 2=printed/sent
-- email_status: 0=not sent, 1=sent
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `invoices` (
    `id`               INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    `sales_id`         INTEGER,
    `customer_account` TEXT    NOT NULL,
    `invoice_number`   TEXT    NOT NULL,
    `invoice_date`     TEXT,
    `due_date`         TEXT,
    `date_created`     TEXT,
    `terms`            TEXT,
    `printer`          INTEGER DEFAULT 0,
    `po_number`        TEXT,
    `num`              INTEGER,
    `email_status`     INTEGER DEFAULT 0,
    `batch_no`         TEXT,
    `created_at`       INTEGER
);


-- -----------------------------------------------------------------------------
-- SETTINGS TABLE
-- One row per company deployment - stores company details, email templates etc.
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `settings` (
    `id`                        INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    `company_name`              TEXT,
    `logo`                      TEXT,
    `company_address1`          TEXT,
    `company_address2`          TEXT,
    `state`                     TEXT,
    `city`                      TEXT,
    `postcode`                  TEXT,
    `country`                   TEXT,
    `phone`                     TEXT,
    `fax`                       TEXT,
    `cemail`                    TEXT,
    `website`                   TEXT,
    `primary_contact`           TEXT,
    `base_currency`             TEXT,
    `vat_number`                TEXT,
    `invoice_due_date`          INTEGER,
    `invoice_due_payment_by`    TEXT,
    `message_title`             TEXT,
    `default_message`           TEXT,
    `default_message2`          TEXT,
    `invoice_default_message`   TEXT,
    `send_limit`                INTEGER DEFAULT 50,
    `fuel_surcharge_percent`    REAL    DEFAULT 3.5,
    `resourcing_surcharge_percent` REAL DEFAULT 0,
    `vat_percent`               REAL    DEFAULT 20
);


-- -----------------------------------------------------------------------------
-- UPLOADED CSV TABLE
-- Tracks which CSV files have been uploaded to prevent duplicates.
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `uploaded_csv` (
    `id`         INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    `filename`   TEXT    NOT NULL,
    `upload_ts`  TEXT    NOT NULL,
    `row_count`  INTEGER DEFAULT 0,
    `status`     TEXT    DEFAULT 'uploaded',
    `created_at` INTEGER
);


-- -----------------------------------------------------------------------------
-- ARCHIVE TABLES
-- Used when archiving old invoice data.
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `sales_archive` (
    `id`                              INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    `invoice_number`                  TEXT    NOT NULL,
    `invoice_date`                    TEXT,
    `customer_account`                TEXT    NOT NULL,
    `customer_name`                   TEXT,
    `address1`                        TEXT,
    `address2`                        TEXT,
    `town`                            TEXT,
    `country`                         TEXT,
    `postcode`                        TEXT,
    `job_number`                      TEXT,
    `job_date`                        TEXT,
    `destination`                     TEXT,
    `town2`                           TEXT,
    `postcode2`                       TEXT,
    `service_type`                    TEXT,
    `items2`                          REAL,
    `volume_weight`                   REAL,
    `sub_total`                       REAL,
    `sender_reference`                TEXT,
    `percentage_fuel_surcharge`       REAL,
    `percentage_resourcing_surcharge` REAL,
    `vat_amount`                      REAL,
    `vat_percent`                     REAL,
    `invoice_total`                   REAL,
    `archived_at`                     INTEGER
);

CREATE TABLE IF NOT EXISTS `invoices_archive` (
    `id`               INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    `customer_account` TEXT    NOT NULL,
    `invoice_number`   TEXT    NOT NULL,
    `invoice_date`     TEXT,
    `due_date`         TEXT,
    `printer`          INTEGER DEFAULT 2,
    `email_status`     INTEGER DEFAULT 1,
    `archived_at`      INTEGER
);


-- =============================================================================
-- DEFAULT ADMIN USER
-- =============================================================================
-- Password: Admin123!  (bcrypt hashed, cost factor 10)
-- CHANGE THIS after first login via Admin & Settings > User Management
-- =============================================================================
INSERT OR IGNORE INTO `users` (
    `id`, `name`, `email`, `password`, `role`, `username`, `created_at`, `updated_at`
) VALUES (
    'admin-default-001',
    'Admin',
    'admin@invoicemanager.com',
    '$2b$10$5WczaaVDdQTTjTwp2k6uZuliDWeawbpcVBwl.ZXsV5fRGCem3SW0u',
    'admin',
    'admin',
    strftime('%s', 'now') * 1000,
    strftime('%s', 'now') * 1000
);


-- =============================================================================
-- DEFAULT SETTINGS ROW
-- =============================================================================
-- Edit company details via Admin & Settings > Invoice Settings after login.
-- =============================================================================
INSERT OR IGNORE INTO `settings` (
    `company_name`,
    `cemail`,
    `send_limit`,
    `fuel_surcharge_percent`,
    `resourcing_surcharge_percent`,
    `vat_percent`,
    `message_title`,
    `default_message2`
) VALUES (
    'Your Company Name',
    'invoices@yourcompany.com',
    50,
    3.5,
    0,
    20,
    'Invoice #{invoice_number}',
    'Dear Customer,

Please find attached your invoice #{invoice_number}.

Kind regards,
Your Company Name'
);

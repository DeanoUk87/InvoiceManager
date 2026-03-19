-- CreateTable
CREATE TABLE "users" (
    "id" TEXT NOT NULL PRIMARY KEY,
    "name" TEXT,
    "email" TEXT NOT NULL,
    "password" TEXT NOT NULL,
    "role" TEXT NOT NULL DEFAULT 'user',
    "username" TEXT,
    "createdAt" DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    "updatedAt" DATETIME NOT NULL
);

-- CreateTable
CREATE TABLE "customers" (
    "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    "customer_account" TEXT NOT NULL,
    "customer_email" TEXT,
    "customer_email_bcc" TEXT,
    "customer_phone" TEXT,
    "terms_of_payment" TEXT,
    "message_type" TEXT,
    "po_number" TEXT,
    "customer_message" TEXT,
    "login_access" BOOLEAN NOT NULL DEFAULT false,
    "createdAt" DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    "updatedAt" DATETIME NOT NULL
);

-- CreateTable
CREATE TABLE "sales" (
    "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    "invoice_number" TEXT NOT NULL,
    "invoice_date" TEXT,
    "customer_account" TEXT NOT NULL,
    "customer_name" TEXT,
    "address1" TEXT,
    "address2" TEXT,
    "town" TEXT,
    "country" TEXT,
    "postcode" TEXT,
    "spacer1" TEXT,
    "customer_account2" TEXT,
    "numb1" REAL,
    "items" REAL,
    "weight" REAL,
    "invoice_total" REAL,
    "numb2" REAL,
    "spacer2" TEXT,
    "job_number" TEXT,
    "job_date" TEXT,
    "sending_depot" TEXT,
    "delivery_depot" TEXT,
    "destination" TEXT,
    "town2" TEXT,
    "postcode2" TEXT,
    "service_type" TEXT,
    "items2" REAL,
    "volume_weight" REAL,
    "numb3" REAL,
    "increased_liability_cover" REAL,
    "sub_total" REAL,
    "spacer3" TEXT,
    "numb4" REAL,
    "sender_reference" TEXT,
    "numb5" REAL,
    "percentage_fuel_surcharge" REAL,
    "percentage_resourcing_surcharge" REAL,
    "spacer4" TEXT,
    "senders_postcode" TEXT,
    "vat_amount" REAL,
    "vat_percent" REAL,
    "upload_code" TEXT,
    "ms_created" INTEGER NOT NULL DEFAULT 0,
    "invoice_ready" INTEGER NOT NULL DEFAULT 0,
    "upload_ts" TEXT,
    "createdAt" DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- CreateTable
CREATE TABLE "invoices" (
    "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    "sales_id" INTEGER,
    "customer_account" TEXT NOT NULL,
    "invoice_number" TEXT NOT NULL,
    "invoice_date" TEXT,
    "due_date" TEXT,
    "date_created" TEXT,
    "terms" TEXT,
    "printer" INTEGER NOT NULL DEFAULT 0,
    "po_number" TEXT,
    "num" INTEGER,
    "email_status" INTEGER NOT NULL DEFAULT 0,
    "batch_no" TEXT,
    "createdAt" DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- CreateTable
CREATE TABLE "settings" (
    "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    "company_name" TEXT,
    "logo" TEXT,
    "company_address1" TEXT,
    "company_address2" TEXT,
    "state" TEXT,
    "city" TEXT,
    "postcode" TEXT,
    "country" TEXT,
    "phone" TEXT,
    "fax" TEXT,
    "cemail" TEXT,
    "website" TEXT,
    "primary_contact" TEXT,
    "base_currency" TEXT,
    "vat_number" TEXT,
    "invoice_due_date" INTEGER,
    "invoice_due_payment_by" TEXT,
    "message_title" TEXT,
    "default_message" TEXT,
    "default_message2" TEXT,
    "send_limit" INTEGER NOT NULL DEFAULT 50,
    "fuel_surcharge_percent" REAL NOT NULL DEFAULT 3.5,
    "resourcing_surcharge_percent" REAL NOT NULL DEFAULT 0,
    "vat_percent" REAL NOT NULL DEFAULT 20
);

-- CreateTable
CREATE TABLE "uploaded_csv" (
    "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    "filename" TEXT NOT NULL,
    "upload_ts" TEXT NOT NULL,
    "row_count" INTEGER NOT NULL DEFAULT 0,
    "status" TEXT NOT NULL DEFAULT 'uploaded',
    "createdAt" DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- CreateTable
CREATE TABLE "sales_archive" (
    "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    "invoice_number" TEXT NOT NULL,
    "invoice_date" TEXT,
    "customer_account" TEXT NOT NULL,
    "customer_name" TEXT,
    "address1" TEXT,
    "address2" TEXT,
    "town" TEXT,
    "country" TEXT,
    "postcode" TEXT,
    "job_number" TEXT,
    "job_date" TEXT,
    "destination" TEXT,
    "town2" TEXT,
    "postcode2" TEXT,
    "service_type" TEXT,
    "items2" REAL,
    "volume_weight" REAL,
    "sub_total" REAL,
    "sender_reference" TEXT,
    "percentage_fuel_surcharge" REAL,
    "percentage_resourcing_surcharge" REAL,
    "vat_amount" REAL,
    "vat_percent" REAL,
    "invoice_total" REAL,
    "archivedAt" DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- CreateTable
CREATE TABLE "invoices_archive" (
    "id" INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    "customer_account" TEXT NOT NULL,
    "invoice_number" TEXT NOT NULL,
    "invoice_date" TEXT,
    "due_date" TEXT,
    "printer" INTEGER NOT NULL DEFAULT 2,
    "email_status" INTEGER NOT NULL DEFAULT 1,
    "archivedAt" DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- CreateIndex
CREATE UNIQUE INDEX "users_email_key" ON "users"("email");

-- CreateIndex
CREATE UNIQUE INDEX "users_username_key" ON "users"("username");

-- CreateIndex
CREATE UNIQUE INDEX "customers_customer_account_key" ON "customers"("customer_account");

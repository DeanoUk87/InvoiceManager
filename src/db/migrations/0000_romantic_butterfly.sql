CREATE TABLE `customers` (
	`id` integer PRIMARY KEY AUTOINCREMENT NOT NULL,
	`customer_account` text NOT NULL,
	`customer_email` text,
	`customer_email_bcc` text,
	`customer_phone` text,
	`terms_of_payment` text,
	`message_type` text,
	`po_number` text,
	`customer_message` text,
	`login_access` integer DEFAULT false,
	`created_at` integer,
	`updated_at` integer
);
--> statement-breakpoint
CREATE UNIQUE INDEX `customers_customer_account_unique` ON `customers` (`customer_account`);--> statement-breakpoint
CREATE TABLE `invoices` (
	`id` integer PRIMARY KEY AUTOINCREMENT NOT NULL,
	`sales_id` integer,
	`customer_account` text NOT NULL,
	`invoice_number` text NOT NULL,
	`invoice_date` text,
	`due_date` text,
	`date_created` text,
	`terms` text,
	`printer` integer DEFAULT 0,
	`po_number` text,
	`num` integer,
	`email_status` integer DEFAULT 0,
	`batch_no` text,
	`created_at` integer
);
--> statement-breakpoint
CREATE TABLE `invoices_archive` (
	`id` integer PRIMARY KEY AUTOINCREMENT NOT NULL,
	`customer_account` text NOT NULL,
	`invoice_number` text NOT NULL,
	`invoice_date` text,
	`due_date` text,
	`printer` integer DEFAULT 2,
	`email_status` integer DEFAULT 1,
	`archived_at` integer
);
--> statement-breakpoint
CREATE TABLE `sales` (
	`id` integer PRIMARY KEY AUTOINCREMENT NOT NULL,
	`invoice_number` text NOT NULL,
	`invoice_date` text,
	`customer_account` text NOT NULL,
	`customer_name` text,
	`address1` text,
	`address2` text,
	`town` text,
	`country` text,
	`postcode` text,
	`spacer1` text,
	`customer_account2` text,
	`numb1` real,
	`items` real,
	`weight` real,
	`invoice_total` real,
	`numb2` real,
	`spacer2` text,
	`job_number` text,
	`job_date` text,
	`sending_depot` text,
	`delivery_depot` text,
	`destination` text,
	`town2` text,
	`postcode2` text,
	`service_type` text,
	`items2` real,
	`volume_weight` real,
	`numb3` real,
	`increased_liability_cover` real,
	`sub_total` real,
	`spacer3` text,
	`numb4` real,
	`sender_reference` text,
	`numb5` real,
	`percentage_fuel_surcharge` real,
	`percentage_resourcing_surcharge` real,
	`spacer4` text,
	`senders_postcode` text,
	`vat_amount` real,
	`vat_percent` real,
	`upload_code` text,
	`ms_created` integer DEFAULT 0,
	`invoice_ready` integer DEFAULT 0,
	`upload_ts` text,
	`created_at` integer
);
--> statement-breakpoint
CREATE TABLE `sales_archive` (
	`id` integer PRIMARY KEY AUTOINCREMENT NOT NULL,
	`invoice_number` text NOT NULL,
	`invoice_date` text,
	`customer_account` text NOT NULL,
	`customer_name` text,
	`address1` text,
	`address2` text,
	`town` text,
	`country` text,
	`postcode` text,
	`job_number` text,
	`job_date` text,
	`destination` text,
	`town2` text,
	`postcode2` text,
	`service_type` text,
	`items2` real,
	`volume_weight` real,
	`sub_total` real,
	`sender_reference` text,
	`percentage_fuel_surcharge` real,
	`percentage_resourcing_surcharge` real,
	`vat_amount` real,
	`vat_percent` real,
	`invoice_total` real,
	`archived_at` integer
);
--> statement-breakpoint
CREATE TABLE `settings` (
	`id` integer PRIMARY KEY AUTOINCREMENT NOT NULL,
	`company_name` text,
	`logo` text,
	`company_address1` text,
	`company_address2` text,
	`state` text,
	`city` text,
	`postcode` text,
	`country` text,
	`phone` text,
	`fax` text,
	`cemail` text,
	`website` text,
	`primary_contact` text,
	`base_currency` text,
	`vat_number` text,
	`invoice_due_date` integer,
	`invoice_due_payment_by` text,
	`message_title` text,
	`default_message` text,
	`default_message2` text,
	`send_limit` integer DEFAULT 50,
	`fuel_surcharge_percent` real DEFAULT 3.5,
	`resourcing_surcharge_percent` real DEFAULT 0,
	`vat_percent` real DEFAULT 20
);
--> statement-breakpoint
CREATE TABLE `uploaded_csv` (
	`id` integer PRIMARY KEY AUTOINCREMENT NOT NULL,
	`filename` text NOT NULL,
	`upload_ts` text NOT NULL,
	`row_count` integer DEFAULT 0,
	`status` text DEFAULT 'uploaded',
	`created_at` integer
);
--> statement-breakpoint
CREATE TABLE `users` (
	`id` text PRIMARY KEY NOT NULL,
	`name` text,
	`email` text NOT NULL,
	`password` text NOT NULL,
	`role` text DEFAULT 'user' NOT NULL,
	`username` text,
	`created_at` integer,
	`updated_at` integer
);
--> statement-breakpoint
CREATE UNIQUE INDEX `users_email_unique` ON `users` (`email`);--> statement-breakpoint
CREATE UNIQUE INDEX `users_username_unique` ON `users` (`username`);
# Invoice Manager - Deployment Guide

## Overview

Each company runs as a **completely separate instance** with its own:
- Database (SQLite file)
- Admin login
- Settings (company name, logo, email templates)
- Customers, invoices, and uploaded data
- URL path (e.g. `/APC-Overnight`)

---

## Prerequisites

- Node.js 20+ or Bun 1.0+
- Nginx (for reverse proxy / subpath routing)
- Linux server (Ubuntu 22.04 recommended)

---

## Step 1: Get the Code

```bash
git clone https://builder.kiloapps.io/apps/db68994f-c0fb-48ba-8e15-6168ac1c4217.git invoice-manager
cd invoice-manager
bun install
```

---

## Step 2: Set Up Each Company

For each company, create a separate folder and `.env` file.

### Example: APC-Overnight

```bash
mkdir -p /var/www/invoice-manager/APC-Overnight
cp -r invoice-manager/* /var/www/invoice-manager/APC-Overnight/
cd /var/www/invoice-manager/APC-Overnight
```

Create `.env`:
```env
# Company identity
BASE_PATH=/APC-Overnight
COMPANY_NAME=APC Overnight

# Database - each company gets its own file
DATABASE_URL=file:./data/apc-overnight.db

# Auth - use a different secret per company
NEXTAUTH_SECRET=apc-overnight-secret-change-this-to-random-string
NEXTAUTH_URL=https://yourdomain.com/APC-Overnight

# Admin account (set before first run, change after login)
ADMIN_EMAIL=admin@apc-overnight.com
ADMIN_PASSWORD=ChangeMe123!

# SMTP - can be shared or per-company
SMTP_HOST=smtp.yourdomain.com
SMTP_PORT=587
SMTP_USER=invoices@apc-overnight.com
SMTP_PASS=your-smtp-password
```

Set up database and build:
```bash
node scripts/setup-db.js
bun run build
```

### Example: AbacusExpress

```bash
mkdir -p /var/www/invoice-manager/AbacusExpress
cp -r invoice-manager/* /var/www/invoice-manager/AbacusExpress/
cd /var/www/invoice-manager/AbacusExpress
```

Create `.env`:
```env
BASE_PATH=/AbacusExpress
COMPANY_NAME=Abacus Express
DATABASE_URL=file:./data/abacus-express.db
NEXTAUTH_SECRET=abacus-express-secret-change-this-to-random-string
NEXTAUTH_URL=https://yourdomain.com/AbacusExpress
ADMIN_EMAIL=admin@abacusexpress.com
ADMIN_PASSWORD=ChangeMe123!
SMTP_HOST=smtp.yourdomain.com
SMTP_PORT=587
SMTP_USER=invoices@abacusexpress.com
SMTP_PASS=your-smtp-password
```

```bash
node scripts/setup-db.js
bun run build
```

---

## Step 3: Run Each Company as a Service

Use **PM2** to manage each instance:

```bash
npm install -g pm2
```

Create `/var/www/invoice-manager/ecosystem.config.js`:
```js
module.exports = {
  apps: [
    {
      name: "apc-overnight",
      cwd: "/var/www/invoice-manager/APC-Overnight",
      script: "node_modules/.bin/next",
      args: "start --port 3001",
      env_file: ".env",
    },
    {
      name: "abacus-express",
      cwd: "/var/www/invoice-manager/AbacusExpress",
      script: "node_modules/.bin/next",
      args: "start --port 3002",
      env_file: ".env",
    },
    // Add more companies here on ports 3003, 3004...
  ],
};
```

Start all:
```bash
pm2 start ecosystem.config.js
pm2 save
pm2 startup  # auto-start on server reboot
```

---

## Step 4: Nginx Reverse Proxy

Edit `/etc/nginx/sites-available/invoice-manager`:

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl;
    server_name yourdomain.com;

    ssl_certificate     /etc/letsencrypt/live/yourdomain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourdomain.com/privkey.pem;

    # APC-Overnight
    location /APC-Overnight {
        proxy_pass http://localhost:3001;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
        client_max_body_size 50M;
    }

    # AbacusExpress
    location /AbacusExpress {
        proxy_pass http://localhost:3002;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
        client_max_body_size 50M;
    }

    # Add more companies here...
}
```

Enable and reload:
```bash
ln -s /etc/nginx/sites-available/invoice-manager /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx
```

---

## Step 5: SSL Certificate

```bash
apt install certbot python3-certbot-nginx
certbot --nginx -d yourdomain.com
```

---

## Adding a New Company

1. Copy the app folder
2. Create a new `.env` with new `BASE_PATH`, `DATABASE_URL`, port
3. Run `node scripts/setup-db.js`
4. Run `bun run build`
5. Add to PM2 `ecosystem.config.js`
6. Add Nginx `location` block
7. Reload Nginx

That's it — fully isolated, independent instance.

---

## URL Structure

| Company | URL | Port |
|---------|-----|------|
| APC Overnight | `yourdomain.com/APC-Overnight` | 3001 |
| Abacus Express | `yourdomain.com/AbacusExpress` | 3002 |
| Next Company | `yourdomain.com/NextCompany` | 3003 |

Each company's login: `yourdomain.com/COMPANY/login`

---

## Backups

Each company's data is a single SQLite file:
```bash
# Backup all companies
for company in APC-Overnight AbacusExpress; do
  cp /var/www/invoice-manager/$company/data/*.db \
     /backup/invoice-manager/$company-$(date +%Y%m%d).db
done
```

---

## Environment Variables Reference

| Variable | Required | Description |
|----------|----------|-------------|
| `BASE_PATH` | Yes | URL subpath e.g. `/APC-Overnight` |
| `DATABASE_URL` | Yes | SQLite file e.g. `file:./data/company.db` |
| `NEXTAUTH_SECRET` | Yes | Random string, unique per company |
| `NEXTAUTH_URL` | Yes | Full URL e.g. `https://domain.com/APC-Overnight` |
| `COMPANY_NAME` | Yes | Company display name |
| `ADMIN_EMAIL` | Yes (first run) | Initial admin email |
| `ADMIN_PASSWORD` | Yes (first run) | Initial admin password |
| `SMTP_HOST` | For email | SMTP server hostname |
| `SMTP_PORT` | For email | Usually 587 |
| `SMTP_USER` | For email | SMTP username |
| `SMTP_PASS` | For email | SMTP password |

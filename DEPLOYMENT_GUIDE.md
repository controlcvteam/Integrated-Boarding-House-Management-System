# Live Website Deployment Guide
### Integrated Boarding House Management System (IBHMS)

This guide provides end-to-end instructions for deploying the **Integrated Boarding House Management System** to a live production server, including database importation, web server configuration (cPanel/Apache and Nginx VPS), and security hardening.

---

## 📦 1. Included Deployment Assets

The project is pre-packaged with the following production-ready deployment assets:

| File | Purpose |
|---|---|
| [`database/live_deploy_database.sql`](file:///c:/Users/USER/OneDrive/Documents/Integrated-Boarding-House-Management-System/database/live_deploy_database.sql) | **Universal Database Dump** (Compatible with any database name in phpMyAdmin or CLI, no hardcoded database names). |
| [`database/boarding_house.sql`](file:///c:/Users/USER/OneDrive/Documents/Integrated-Boarding-House-Management-System/database/boarding_house.sql) | **Full MySQL Dump** (Includes `CREATE DATABASE IF NOT EXISTS boarding_house`). |
| [`.env.production.example`](file:///c:/Users/USER/OneDrive/Documents/Integrated-Boarding-House-Management-System/.env.production.example) | Pre-configured production environment file template with secure session defaults. |
| [`.htaccess`](file:///c:/Users/USER/OneDrive/Documents/Integrated-Boarding-House-Management-System/.htaccess) | Root `.htaccess` for cPanel / shared hosting (redirects traffic to `/public` while blocking `.env` and sensitive files). |
| [`public/.htaccess`](file:///c:/Users/USER/OneDrive/Documents/Integrated-Boarding-House-Management-System/public/.htaccess) | Production Apache/LiteSpeed URL rewrite configuration for clean routing. |
| [`deploy/nginx.conf`](file:///c:/Users/USER/OneDrive/Documents/Integrated-Boarding-House-Management-System/deploy/nginx.conf) | Production Nginx server block with PHP-FPM, static caching, and security headers. |
| [`deploy.sh`](file:///c:/Users/USER/OneDrive/Documents/Integrated-Boarding-House-Management-System/deploy.sh) | Automated 1-command deployment script for Linux servers and VPS. |

---

## 🌐 2. Method A: cPanel / Shared Hosting (Hostinger, Namecheap, GoDaddy, etc.)

Shared hosting with cPanel is the most common deployment environment.

### Step 1: Create the MySQL Database
1. Log in to your **cPanel**.
2. Go to **MySQL Databases** (or **MySQL Database Wizard**).
3. Create a new database, e.g., `u123456_boardinghouse`.
4. Create a database user, e.g., `u123456_admin`, with a strong password.
5. Add the user to the database and grant **ALL PRIVILEGES**. Note down the Database Name, User, and Password.

### Step 2: Import the Database
1. In cPanel, open **phpMyAdmin**.
2. Select your newly created database on the left navigation panel.
3. Click the **Import** tab at the top.
4. Click **Choose File** and select `database/live_deploy_database.sql`.
5. Click **Import** (or **Go**).
6. Verify that all 12 tables (users, rooms, tenants, payments, payment_edit_histories, maintenance_requests, settings, etc.) are imported successfully.

### Step 3: Upload the Application Files
1. In your local project, zip the files (exclude `node_modules` and local `.env`).
2. In cPanel **File Manager**, navigate to `public_html` (or your domain folder).
3. Upload and extract the zip file.
4. The root `.htaccess` included in this repository will automatically route traffic to the `public/` directory while protecting your `.env` and source code.

### Step 4: Configure Production Environment (`.env`)
1. In the File Manager, make sure **Show Hidden Files (dotfiles)** is enabled in Settings.
2. Duplicate `.env.production.example` and rename it to `.env`.
3. Edit `.env` with your live credentials:
   ```env
   APP_NAME="Integrated Boarding House Management System"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-domain.com

   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=u123456_boardinghouse
   DB_USERNAME=u123456_admin
   DB_PASSWORD=your_strong_password
   ```
4. If you have terminal access in cPanel, run:
   ```bash
   php artisan key:generate --force
   php artisan storage:link
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```
   *(If terminal is not available, you can generate an APP_KEY locally and paste it into `.env`).*

---

## 🖥️ 3. Method B: Ubuntu / Debian VPS (Nginx + PHP-FPM + MySQL)

For dedicated performance on DigitalOcean, AWS EC2, Linode, or Vultr.

### Step 1: Server Requirements
Ensure the following packages are installed on your VPS:
```bash
sudo apt update && sudo apt install -y nginx mysql-server php8.2-fpm php8.2-cli \
    php8.2-mysql php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-curl \
    php8.2-zip php8.2-gd composer unzip git
```

### Step 2: Setup Database
```bash
sudo mysql
```
```sql
CREATE DATABASE boarding_house CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'ibms_user'@'localhost' IDENTIFIED BY 'StrongPassword123!';
GRANT ALL PRIVILEGES ON boarding_house.* TO 'ibms_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Import the database:
```bash
mysql -u ibms_user -p boarding_house < database/live_deploy_database.sql
```

### Step 3: Deploy Application Code
```bash
cd /var/www
git clone <your-repository-url> integrated-boarding-house
cd integrated-boarding-house
cp .env.production.example .env
```

Configure `.env` with your domain and database credentials:
```bash
nano .env
```

Run the automated deployment script:
```bash
chmod +x deploy.sh
./deploy.sh
```

### Step 4: Configure Nginx & SSL
Copy the provided Nginx template:
```bash
sudo cp deploy/nginx.conf /etc/nginx/sites-available/boarding-house
sudo ln -s /etc/nginx/sites-available/boarding-house /etc/nginx/sites-enabled/
```
Edit `/etc/nginx/sites-available/boarding-house` to replace `your-domain.com` with your actual domain name. Then test and restart Nginx:
```bash
sudo nginx -t
sudo systemctl reload nginx
```

Install free SSL certificate via Let's Encrypt:
```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

---

## 🔑 4. Default Live Credentials & Initial Setup

Once deployed, access your live domain: `https://your-domain.com/login`

### Initial Admin Credentials:
* **Email:** `admin@boardinghouse.local`
* **Password:** `password`
* *Action Required upon first login:* Go to **Settings > Password & Security** to change your password immediately.
* Go to **Settings > GCash Account Information** to update your GCash receiver name, mobile number, and upload your official GCash QR code.

### Sample Tenant Account:
* **Email:** `xowijae@gmail.com`
* **Password:** `password`

---

## 🔒 5. Production Security Verification Checklist

- [x] `APP_DEBUG=false` in production `.env` (prevents revealing stack traces and database schemas on error).
- [x] Root `.htaccess` active (denies HTTP access to `.env`, `.git`, and `composer.json`).
- [x] `public/.htaccess` active (URL rewriting handles all routing via `public/index.php`).
- [x] `storage/` and `bootstrap/cache/` directories writable (`chmod -R 775`).
- [x] Storage symlink active (`php artisan storage:link`) for receipt uploads and QR codes.
- [x] Payment audit trail enabled (`payment_edit_histories` records cannot be deleted; payment edit reasons are mandatory).

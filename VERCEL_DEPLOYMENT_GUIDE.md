# Free Vercel Deployment Guide (Laravel 11 + Supabase PostgreSQL)

This application is fully pre-configured to run **100% free** on **Vercel** connected to your cloud **Supabase PostgreSQL** database.

---

## What Has Been Pre-Configured For You

1. **Serverless Entrypoint (`api/index.php`)**:
   - Automatically initializes writable `/tmp` directories for Blade views and framework caches.
   - Prevents read-only filesystem crashes on Vercel's serverless environment.

2. **Vercel Runtime Config (`vercel.json`)**:
   - Configured with `vercel-php@0.7.3` (PHP 8.3 with native `pdo_pgsql` PostgreSQL support).
   - Routes static assets (`/css`, `/js`, `/images`, `/storage`, `/favicon.svg`, `/logo.svg`) directly to Vercel's CDN.
   - Routes all dynamic requests to `api/index.php`.

3. **Cloud Session Storage (Supabase)**:
   - A `sessions` table migration was created and migrated into your Supabase database.
   - `SESSION_DRIVER=database` ensures users stay logged in across serverless invocations.

4. **Static Public Assets (`public/storage`)**:
   - Seeded avatars, rooms, and icons are stored directly in `public/storage` so Vercel CDN serves them immediately.

5. **Exclusion Files (`.gitignore` & `.vercelignore`)**:
   - Protects your local `.env` from being uploaded.
   - Excludes heavy local `vendor` and `node_modules` folders so Vercel performs a clean Linux cloud build.

---

## Deployment Option 1: Via GitHub (Recommended)

This is the easiest and most reliable method for continuous free deployment.

### Step 1: Initialize Git and Commit
Open PowerShell in the project directory and run:
```powershell
git init
git add .
git commit -m "Prepare Integrated Boarding House Management System for Vercel deployment"
```

### Step 2: Push to GitHub
1. Go to [GitHub](https://github.com) and click **New Repository**.
2. Name it (e.g., `integrated-boarding-house-management-system`) and make it **Private** (or Public).
3. Push your repository:
```powershell
git remote add origin https://github.com/YOUR_USERNAME/YOUR_REPO_NAME.git
git branch -M main
git push -u origin main
```

### Step 3: Import Project into Vercel
1. Go to [Vercel](https://vercel.com) and log in (or sign up for free using your GitHub account).
2. Click **Add New...** > **Project**.
3. Find your GitHub repository and click **Import**.
4. In the configuration screen:
   - **Framework Preset**: Other / None
   - **Root Directory**: `./` (leave default)
   - Expand the **Environment Variables** section.

### Step 4: Add Environment Variables in Vercel
Copy and paste each variable below into the Vercel Environment Variables box:

| Variable Name | Value |
| :--- | :--- |
| `APP_NAME` | `Integrated Boarding House Management System` |
| `APP_ENV` | `production` |
| `APP_KEY` | `base64:R0QIUG27vDJMrAviTP+cV66e/mjDr9ILhYok1k/O8d8=` |
| `APP_DEBUG` | `false` |
| `APP_TIMEZONE` | `Asia/Manila` |
| `APP_URL` | `https://your-project-name.vercel.app` *(update once deployed)* |
| `DB_CONNECTION` | `pgsql` |
| `DB_HOST` | `aws-0-ap-northeast-1.pooler.supabase.com` |
| `DB_PORT` | `6543` |
| `DB_DATABASE` | `postgres` |
| `DB_USERNAME` | `postgres.uhchcotdkdndcrpzpgur` |
| `DB_PASSWORD` | `integratedboardinghouse2026` |
| `DB_SSLMODE` | `require` |
| `SESSION_DRIVER` | `database` |
| `SESSION_LIFETIME` | `120` |
| `CACHE_STORE` | `array` |
| `QUEUE_CONNECTION` | `sync` |
| `FILESYSTEM_DISK` | `public` |
| `LOG_CHANNEL` | `stderr` |
| `VIEW_COMPILED_PATH` | `/tmp/views` |
| `APP_CONFIG_CACHE` | `/tmp/config.php` |
| `APP_EVENTS_CACHE` | `/tmp/events.php` |
| `APP_PACKAGES_CACHE` | `/tmp/packages.php` |
| `APP_ROUTES_CACHE` | `/tmp/routes.php` |
| `APP_SERVICES_CACHE` | `/tmp/services.php` |

*(You can also open `.env.vercel.example` in this project to copy everything).*

### Step 5: Deploy
Click **Deploy**. Vercel will install the dependencies and deploy your website to a live `.vercel.app` URL for free!

---

## Deployment Option 2: Via Vercel CLI (Direct Terminal)

If you prefer deploying directly without pushing to GitHub first:

1. In PowerShell, run:
```powershell
npx vercel login
```
Follow the browser prompt to log into your free Vercel account.

2. Link and deploy a preview:
```powershell
npx vercel
```
Answer the interactive prompts:
- *Set up and deploy?* -> `Y`
- *Which scope?* -> (Select your account)
- *Link to existing project?* -> `N`
- *What's your project's name?* -> `integrated-boarding-house`
- *In which directory is your code located?* -> `./`
- *Want to modify settings?* -> `N`

3. Add your environment variables in the Vercel Dashboard (Project Settings > Environment Variables).

4. Deploy directly to production:
```powershell
npx vercel --prod
```

---

## Default Seeded Logins on Your Live Site

- **Admin Account**:
  - Email: `admin@boardinghouse.local`
  - Password: `password`
- **Tenant Account**:
  - Email: `tenant@boardinghouse.local`
  - Password: `password`

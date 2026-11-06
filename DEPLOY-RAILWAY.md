# Deploy ke Railway (5 menit)

## 1. Buka Railway
https://railway.app

Klik **"Login with GitHub"**

## 2. Deploy Project
- Klik **"New Project"**
- Pilih **"Deploy from GitHub repo"**
- Pilih: `rakhasukendra/Sistem-Reservasi-Camping`
- Klik **"Deploy Now"**

## 3. Add Database
- Di dashboard project, klik **"+ New"**
- Klik **"Database"** → **"Add PostgreSQL"**
- Railway otomatis set env vars: `DATABASE_URL`

## 4. Set Environment Variables
Klik service Laravel → **"Variables"** → tambah:

```
APP_NAME=ReservasiCamping
APP_ENV=production
APP_KEY=base64:RPkwnWWcEGQ1WC1WoYYR8PIy7hrFE+j8TVS3SupotfE=
APP_DEBUG=false
APP_URL=${{RAILWAY_PUBLIC_DOMAIN}}

DB_CONNECTION=pgsql
DB_HOST=${{PGHOST}}
DB_PORT=${{PGPORT}}
DB_DATABASE=${{PGDATABASE}}
DB_USERNAME=${{PGUSER}}
DB_PASSWORD=${{PGPASSWORD}}

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

Railway otomatis inject `PGHOST`, `PGPORT`, dll dari PostgreSQL service.

## 5. Generate Domain
Klik **"Settings"** → **"Networking"** → **"Generate Domain"**

Copy URL → buka di browser.

## DONE! 🎉

Migrations auto-run saat deploy.

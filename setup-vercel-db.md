# Setup Database untuk Vercel Production

## Step 1: Buat Database di Supabase (Gratis)

1. Buka https://supabase.com
2. Sign up/Login
3. Klik **New Project**
4. Isi:
   - Name: `reservasi-camping`
   - Database Password: `[buat password kuat]`
   - Region: `Southeast Asia (Singapore)` atau terdekat
   - Pricing Plan: **Free**
5. Tunggu ~2 menit sampai database ready
6. Setelah ready, buka **Settings → Database**
7. Salin info berikut:
   - **Host**: `db.xxx.supabase.co`
   - **Port**: `5432`
   - **Database name**: `postgres`
   - **User**: `postgres`
   - **Password**: `[yang tadi dibuat]`

## Step 2: Set Environment Variables di Vercel

### Via Vercel CLI (Recommended)

```bash
# Install Vercel CLI jika belum
npm i -g vercel

# Login
vercel login

# Link project
vercel link

# Set environment variables
vercel env add DB_CONNECTION
# Masukkan: pgsql

vercel env add DB_HOST
# Masukkan: db.xxx.supabase.co

vercel env add DB_PORT
# Masukkan: 5432

vercel env add DB_DATABASE
# Masukkan: postgres

vercel env add DB_USERNAME
# Masukkan: postgres

vercel env add DB_PASSWORD
# Masukkan: [password dari Supabase]

vercel env add DB_SSLMODE
# Masukkan: require

vercel env add APP_ENV
# Masukkan: production

vercel env add APP_DEBUG
# Masukkan: false

vercel env add LOG_CHANNEL
# Masukkan: stderr
```

### Via Vercel Dashboard (Alternative)

1. Buka https://vercel.com/dashboard
2. Pilih project `reservasi-camping`
3. Masuk **Settings → Environment Variables**
4. Tambahkan satu-satu:
   - `DB_CONNECTION` = `pgsql`
   - `DB_HOST` = `db.xxx.supabase.co`
   - `DB_PORT` = `5432`
   - `DB_DATABASE` = `postgres`
   - `DB_USERNAME` = `postgres`
   - `DB_PASSWORD` = `[password]`
   - `DB_SSLMODE` = `require`
   - `APP_ENV` = `production`
   - `APP_DEBUG` = `false`
   - `LOG_CHANNEL` = `stderr`

## Step 3: Run Migrations ke Database Baru

### Opsi A: Via Local (Temporary Connection)

```bash
# Update .env dengan kredensial Supabase
DB_CONNECTION=pgsql
DB_HOST=db.xxx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=[password]
DB_SSLMODE=require

# Run migrations
php artisan migrate --force

# Seed data (optional)
php artisan db:seed --force
```

### Opsi B: Via Vercel Deployment Script

Tambahkan ke `composer.json`:

```json
"scripts": {
    "post-deploy": [
        "@php artisan migrate --force"
    ]
}
```

## Step 4: Redeploy

```bash
# Commit changes jika ada
git add .
git commit -m "Setup PostgreSQL database"
git push

# Atau deploy langsung
vercel --prod
```

## Step 5: Verify

Akses: https://reservasi-camping.vercel.app

Seharusnya tidak ada error database lagi.

## Troubleshooting

### Error: "SQLSTATE[08006] Connection refused"
- Cek DB_HOST benar
- Pastikan DB_SSLMODE=require

### Error: "password authentication failed"
- Cek DB_PASSWORD benar
- Reset password di Supabase jika perlu

### Error masih: "database.sqlite not found"
- Environment variables belum ter-apply
- Redeploy setelah set env vars

## Alternative: Vercel Postgres

Jika mau pakai Vercel Postgres (butuh payment method):

```bash
vercel postgres create
vercel env pull
vercel --prod
```

Tapi Supabase free tier lebih dari cukup untuk production.

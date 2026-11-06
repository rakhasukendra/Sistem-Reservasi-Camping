# 🔥 CARA SETUP DATABASE UNTUK VERCEL (STEP-BY-STEP)

## ✅ SOLUTION: Pakai Supabase PostgreSQL (GRATIS)

---

## 📌 APA MASALAHNYA?

Error: `Database file at path [/app/database/database.sqlite] does not exist`

**Penyebab:**
- Local pakai SQLite (database file)
- Vercel serverless tidak support SQLite (file tidak persisten)
- Aplikasi coba baca session dari SQLite → error

**Solusi:**
- Pakai PostgreSQL (database cloud)
- Supabase free tier cocok untuk production

---

## 🚀 STEP 1: BUAT DATABASE SUPABASE

### 1. Buka Supabase
```
https://supabase.com
```

### 2. Sign Up / Login
- Klik **Get Started** atau **Login**
- Gunakan GitHub/Google/Email

### 3. Buat Project Baru

Klik tombol **New Project** atau **Create a project**

**Isi form:**
```
Project Name: reservasi-camping
Region: Southeast Asia (Singapore)
Database Password: [Buat password, contoh: MyStrongPass2026!]
Pricing Plan: Free
```

⚠️ **PENTING:** Catat `Database Password`! Nanti diperlukan.

### 4. Tunggu Provisioning
- Tunggu ~2-5 menit
- Status: **Creating...** → **Running**

### 5. Copy Database Credentials

Setelah running, klik **Settings** → **Database**

Copy data berikut:

| Field | Contoh | Keterangan |
|-------|--------|------------|
| Host | `db.xxxxx.supabase.co` | URL database |
| Port | `5432` | Default PostgreSQL |
| Database | `postgres` | Nama database |
| User | `postgres` | Username default |
| Password | `***` | Yang tadi dibuat |
| Connection string | `postgresql://postgres:xxx@db.xxx.supabase.co:5432/postgres` | Format lengkap |

---

## 📋 STEP 2: SET DI VERCEL

### Opsi A: Via Vercel Dashboard (Paling Mudah)

1. Buka: https://vercel.com/dashboard
2. Pilih project **reservasi-camping**
3. Klik tab **Settings**
4. Scroll ke **Environment Variables**
5. Klik **Add Variable**

**Isi satu per satu:**

| Variable Name | Value | Environment |
|--------------|-------|-------------|
| `DB_CONNECTION` | `pgsql` | Production |
| `DB_HOST` | `db.xxxxx.supabase.co` | Production |
| `DB_PORT` | `5432` | Production |
| `DB_DATABASE` | `postgres` | Production |
| `DB_USERNAME` | `postgres` | Production |
| `DB_PASSWORD` | `[password Supabase]` | Production |
| `DB_SSLMODE` | `require` | Production |
| `APP_ENV` | `production` | Production |
| `APP_DEBUG` | `false` | Production |
| `LOG_CHANNEL` | `stderr` | Production |

💡 **Tips:**
- Pastikan pilih **Production** environment
- Klik **Save** setiap variable

### Opsi B: Via Vercel CLI (Jika Sudah Punya)

Jalankan commands ini di terminal:

```bash
cd C:\Users\HP\Music\reservasi-camping

# Set setiap variable
vercel env add DB_CONNECTION production
# Input: pgsql

vercel env add DB_HOST production
# Input: db.xxxxx.supabase.co

vercel env add DB_PORT production
# Input: 5432

vercel env add DB_DATABASE production
# Input: postgres

vercel env add DB_USERNAME production
# Input: postgres

vercel env add DB_PASSWORD production
# Input: [password Supabase]

vercel env add DB_SSLMODE production
# Input: require

vercel env add APP_ENV production
# Input: production

vercel env add APP_DEBUG production
# Input: false

vercel env add LOG_CHANNEL production
# Input: stderr
```

---

## 🔄 STEP 3: DEPLOY KE VERCEL

### Cara 1: Push ke GitHub (Auto Deploy)

```bash
cd C:\Users\HP\Music\reservasi-camping

# Commit changes
git add .
git commit -m "chore: setup PostgreSQL database for production"
git push origin main
```

Vercel otomatis detect push dan deploy ulang.

### Cara 2: Manual Deploy

```bash
cd C:\Users\HP\Music\reservasi-camping

# Deploy langsung
vercel --prod
```

Tunggu sampai muncul:
```
✅ Deployment successfully!
https://reservasi-camping.vercel.app
```

---

## 🗄️ STEP 4: RUN MIGRATIONS

### Opsi 1: Local (Sementara)

Update file `.env` local dengan kredensial Supabase:

```bash
# Edit .env
DB_CONNECTION=pgsql
DB_HOST=db.xxxxx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=[password Supabase]
```

Lalu jalankan:

```bash
php artisan migrate --force
php artisan db:seed --force
```

Setelah selesai, **kembalikan** `.env` ke local settings.

### Opsi 2: Via Database Tool (Supabase SQL Editor)

1. Buka Supabase Dashboard
2. Masuk ke **SQL Editor**
3. Copy paste isi file `database/migrations/*.php` (tabel-tabelnya)
4. Jalankan satu per satu

### Opsi 3: Via Laravel Horizon/Queue (Advanced)

Setup supervisor untuk auto run migrations saat deploy.

---

## ✅ STEP 5: VERIFICATION

### Cek 1: Akses Aplikasi

```
https://reservasi-camping.vercel.app
```

**Expected:** Tidak ada error database lagi!

### Cek 2: Test Login

```
https://reservasi-camping.vercel.app/login
```

**Expected:** Bisa login normal, session tersimpan di database.

### Cek 3: Cek Log Vercel

```
https://vercel.com/rakha16/reservasi-camping/logs
```

**Expected:** Hanya log normal, tidak ada error `database.sqlite`.

---

## 🧪 DEBUGGING

### Error: "SQLSTATE[08006] Connection refused"

**Penyebab:**
- DB_HOST salah
- Port salah (harus 5432)
- Supabase project belum running

**Fix:**
- Cek DB_HOST di Supabase → Settings → Database
- Pastikan project status: **Running**

### Error: "password authentication failed"

**Penyebab:**
- DB_PASSWORD salah
- Special character di password

**Fix:**
- Reset password di Supabase → Settings → Database → Reset Password
- Update `DB_PASSWORD` di Vercel
- Deploy ulang

### Error: "relation does not exist"

**Penyebab:**
- Migrations belum dijalankan

**Fix:**
```bash
php artisan migrate --force
```

---

## 📊 DATABASE SCHEMA

Database Supabase akan otomatis punya tabel ini setelah migrations:

| Tabel | Fungsi |
|-------|--------|
| `users` | Data pengguna aplikasi |
| `sessions` | Session management |
| `migrations` | Track migrations |
| `cache`, `jobs`, `failed_jobs` | Queue & cache |

---

## 🎁 BONUS: Backups & Monitoring

### Auto Backups (Supabase)
- Settings → Backups
- Default: every 15 menit
- Retention: 7 days (Free tier)

### Database Monitoring
- Dashboard → Logs
- Dashboard → Stats
- Query performance insights

---

## 📞 BUTUH BANTUAN LAGI?

**Jika masih error:**
1. Check Vercel logs
2. Check Supabase logs
3. Verify env variables sudah benar
4. Pastikan migrations sudah run

**Info Tambahan:**
- Supabase Docs: https://supabase.com/docs
- Vercel Docs: https://vercel.com/docs
- Laravel Database: https://laravel.com/docs/database

---

## ✨ DONE!

Setelah semua langkah diikuti:

✅ Database pakai PostgreSQL (bukan SQLite)
✅ Environment variables sudah ter-set di Vercel
✅ Migrations sudah dijalankan
✅ Aplikasi bisa diakses di production

**URL Production:**
```
https://reservasi-camping.vercel.app
```

Selamat mencoba! 🎉

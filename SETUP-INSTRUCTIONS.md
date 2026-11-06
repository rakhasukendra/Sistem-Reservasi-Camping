# Vercel Environment Variables Setup

⚠️ **PENTING:** Set ini di Vercel Dashboard secara manual

## 🔗 URL Setup:
```
https://vercel.com/rakha16/reservasi-camping/settings/environment-variables
```

## 📋 Variables yang Harus Ditambahkan:

Klik **Add Variable** untuk setiap item di bawah:

### Database Configuration

| Variable Name | Value | Environment |
|--------------|-------|-------------|
| `DB_CONNECTION` | `pgsql` | Production ✅ |
| `DB_HOST` | `db.wgbpcdqmkwpsfxrwwybi.supabase.co` | Production ✅ |
| `DB_PORT` | `5432` | Production ✅ |
| `DB_DATABASE` | `postgres` | Production ✅ |
| `DB_USERNAME` | `postgres` | Production ✅ |
| `DB_PASSWORD` | `[MASUKKAN PASSWORD PROJECT]` | Production ✅ |
| `DB_SSLMODE` | `require` | Production ✅ |

### Application Configuration

| Variable Name | Value | Environment |
|--------------|-------|-------------|
| `APP_ENV` | `production` | Production ✅ |
| `APP_DEBUG` | `false` | Production ✅ |
| `LOG_CHANNEL` | `stderr` | Production ✅ |
| `SESSION_DRIVER` | `database` | Production ✅ |
| `CACHE_STORE` | `database` | Production ✅ |

---

## ⚠️ Database Password

**API Key yang kamu berikan (`sb_secret_...`) bukan database password!**

### Cara Dapat Database Password:

**Opsi 1: Cek Email**
- Buka email yang dipakai sign up Supabase
- Cari email dari Supabase dengan subject "Your new project"
- Password ada di situ

**Opsi 2: Reset Password Baru**
1. Di halaman Supabase Settings (yang sudah dibuka)
2. Scroll ke section **"Database password"**
3. Klik tombol **"Reset password"**
4. Supabase akan generate password baru
5. **Copy password itu** (hanya muncul 1x!)
6. Paste ke `DB_PASSWORD` di Vercel

---

## 🚀 Setelah Semua Variables Di-Set:

### 1. Redeploy di Vercel

Buka:
```
https://vercel.com/rakha16/reservasi-camping
```

Klik tab **"Deployments"** → Pilih deployment terakhir → Klik **"⋯"** → **"Redeploy"**

### 2. Run Migrations (Buat Tabel)

Setelah redeploy selesai, run ini di terminal:

```bash
cd C:\Users\HP\Music\reservasi-camping

# Update .env local
# Ganti DB_CONNECTION jadi pgsql
# Uncomment DB_HOST, DB_PORT, dll
# Isi dengan data Supabase

# Run migrations
php artisan migrate --force
```

Atau edit `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=db.wgbpcdqmkwpsfxrwwybi.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=[password-dari-supabase]
DB_SSLMODE=require
```

Lalu:
```bash
php artisan migrate --force
php artisan db:seed --force
```

---

## ✅ Verification

1. **Cek aplikasi:**
   ```
   https://reservasi-camping.vercel.app
   ```
   
2. **Cek database:**
   - Buka Supabase → Database → Tables
   - Seharusnya ada tabel: users, sessions, migrations, dll

3. **Cek Vercel logs:**
   ```
   https://vercel.com/rakha16/reservasi-camping/logs
   ```
   - Tidak ada error `database.sqlite`

---

## 🆘 Butuh Password Database?

**Cara Tercepat: Reset Password**

1. Buka halaman Settings Supabase (yang sudah dibuka)
2. Cari section **"Database password"**
3. Klik **"Reset password"**
4. Copy password baru yang muncul
5. Paste ke `DB_PASSWORD` di Vercel
6. Save
7. Redeploy

**Password hanya muncul 1 kali, simpan baik-baik!**

---

## 📌 Project Info

- Project ID: `wgbpcdqmkwpsfxrwwybi`
- Host: `db.wgbpcdqmkwpsfxrwwybi.supabase.co`
- Region: Southeast Asia
- Vercel: `reservasi-camping.vercel.app`

Ready untuk production! 🎉

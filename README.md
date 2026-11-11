# Sistem Reservasi Camping

Aplikasi web untuk mengelola reservasi alat camping dan paket panduan alam.

## Fitur

- **Pelanggan**: Daftar, login, browse alat camping, pesan, upload bukti bayar
- **Staff**: Dashboard, kelola inventori, artikel panduan, pesanan, analytics
- **Admin**: Kelola user, alat camping, artikel, review booking

## Requirement

- PHP ^8.4
- Composer
- Node.js + npm
- SQLite (atau MySQL/PostgreSQL)

## Instalasi

```bash
git clone https://github.com/rakhasukendra/Sistem-Reservasi-Camping.git
cd reservasi-camping

composer install
cp .env.example .env
php artisan key:generate

touch database/database.sqlite
php artisan migrate --force
php artisan db:seed

npm install
npm run build
```

Jalankan server development:

```bash
php artisan serve
```

Akses aplikasi di `http://localhost:8000`

## Kredensial Default (setelah seed)

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@example.com | password |
| Staff | staff@example.com | password |
| User | user@example.com | password |

## Struktur Folder Penting

- `app/` - Logic aplikasi (Models, Controllers, Middleware)
- `database/migrations/` - Schema database
- `database/seeders/` - Data awal
- `public/` - File statis (images, fonts)
- `resources/views/` - Blade templates
- `routes/web.php` - Routing

## Script Composer

- `composer run setup` - Instalasi lengkap (dependencies + migrate + seed)
- `composer run dev` - Jalankan server development
- `composer run test` - Jalankan test suite

## License

MIT

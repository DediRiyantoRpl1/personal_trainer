# Coach Dedi — Personal Trainer (Laravel)

Website personal trainer yang dikonversi dari HTML/CSS/JS statis ke **Laravel**.
Booking sekarang disimpan di **database** (bukan localStorage), jadi slot yang sudah dipesan
otomatis terkunci untuk semua pengunjung, dan Coach Dedi bisa melihat semua booking di halaman admin.

## Cara menjalankan (Laragon + VS Code)

Kebutuhan: Laragon dengan **PHP 8.3 atau lebih baru** (Laragon → Menu → PHP → pilih versi) dan Composer (sudah ada di Laragon).

1. Ekstrak folder ini ke `C:\laragon\www\personal-trainer`.
2. Buka Laragon → **Start All** (Apache/Nginx + MySQL).
3. Buat database kosong bernama `personal_trainer`:
   Laragon → **Database** (HeidiSQL) → klik kanan → *Create new → Database*.
4. Buka folder di VS Code (`File → Open Folder`), lalu buka terminal (`Ctrl + ~`).
   Kalau `php`/`composer` tidak dikenali, gunakan **Terminal Laragon** (tombol *Terminal* di Laragon, lalu `cd C:\laragon\www\personal-trainer`).
5. Jalankan:

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
```

6. Buat akun admin (terpisah dari akun klien): `php artisan admin:create` — isi email, nama, dan password (min. 10 karakter).
7. Buka **http://personal-trainer.test** (Laragon membuat alamat ini otomatis; kalau belum muncul, klik kanan Laragon → *Apache → Reload* atau Stop lalu Start All).
   Alternatif tanpa virtual host: `php artisan serve` lalu buka http://127.0.0.1:8000.

Akun klien: klien mendaftar di **/daftar** (nama, email, WhatsApp, password) dan masuk di **/masuk**. Booking hanya bisa dilakukan setelah login; form otomatis terisi nama dan WhatsApp akun, dan daftar "Booking kamu" muncul dari perangkat mana pun.

### Admin (terpisah dari klien)

Admin punya **tabel, guard, dan sesi sendiri** (`admins`), jadi akun klien tidak pernah bisa membuka area admin dan sebaliknya.

- **Dengan subdomain**: isi `ADMIN_DOMAIN=admin.personal-trainer-laravel.test` di `.env` (sesuaikan nama folder). Admin masuk di `http://admin.personal-trainer-laravel.test/masuk`.
  Kalau subdomain belum terbuka di Laragon, tambahkan baris `127.0.0.1 admin.personal-trainer-laravel.test` ke file hosts Windows (`C:\Windows\System32\drivers\etc\hosts`, buka Notepad *Run as administrator*), lalu Reload Apache.
- **Tanpa subdomain**: biarkan `ADMIN_DOMAIN` kosong. Admin masuk di `http://personal-trainer-laravel.test/admin/masuk`.
- Jangan isi `SESSION_DOMAIN` di `.env` (biarkan `null`) supaya cookie login admin tidak ikut terkirim ke website utama.
- Setelah login, buka tab **Keamanan** untuk mengaktifkan verifikasi 2 langkah (Google Authenticator/Authy), ganti password, dan melihat riwayat login.
- Lupa password admin / HP hilang: `php artisan admin:create email@admin` (reset password) atau `php artisan admin:reset-2fa email@admin`.

Fitur keamanan: login dibatasi 5x gagal (klien dikunci 1 menit, admin 5 menit) dengan hitung mundur, 2FA TOTP + kode pemulihan, sesi admin berakhir otomatis setelah `ADMIN_IDLE_MINUTES` (default 30), semua login tercatat di tabel `login_logs`.

> Tanpa MySQL? Di `.env` ganti `DB_CONNECTION=sqlite`, hapus baris `DB_HOST` sampai `DB_PASSWORD`, buat file kosong `database/database.sqlite`, lalu `php artisan migrate`.

## Struktur penting

| File | Fungsi |
|---|---|
| `config/trainer.php` | **Semua konten**: layanan, harga, foto, testimoni, jam sesi, kuota slot. Edit di sini. |
| `routes/web.php` | Daftar URL |
| `app/Http/Controllers/HomeController.php` | Halaman utama |
| `app/Http/Controllers/BookingController.php` | Cek slot, simpan & batalkan booking (validasi di server) |
| `app/Http/Controllers/AdminController.php` | Halaman admin (booking) |
| `app/Http/Controllers/AdminAuthController.php`, `AdminSecurityController.php` | Login admin, 2FA, ganti password |
| `app/Models/Admin.php`, `config/auth.php` | Akun & guard admin |
| `app/Models/Booking.php`, `database/migrations/…bookings…` | Tabel booking (kombinasi tanggal + jam unik) |
| `resources/views/home.blade.php` + `partials/*` | Tampilan, dipecah per bagian |
| `resources/views/admin/*` | Tampilan admin |
| `public/assets/css/style.css`, `public/assets/js/app.js` | CSS & JS (tidak perlu npm/Vite) |

## Catatan

- Foto memakai Unsplash dan font memakai Google Fonts, jadi butuh internet. Ganti URL foto di `config/trainer.php` dengan foto sendiri (taruh di `public/images/` lalu pakai `asset('images/nama.jpg')`).
- Klien hanya bisa membatalkan booking miliknya sendiri (terhubung ke akun). Akun klien tidak bisa membuka area admin (akun admin terpisah).
- Sebelum online (hosting): set `APP_ENV=production`, `APP_DEBUG=false`, aktifkan 2FA admin, dan atur email asli di `.env` (`MAIL_*`) supaya fitur lupa password mengirim email sungguhan.

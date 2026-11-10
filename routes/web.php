<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminMessageController;
use App\Http\Controllers\AdminPostController;
use App\Http\Controllers\AdminSecurityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PostController;
use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\NoStore;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────────────────────
// ADMIN — akun, guard, dan sesi terpisah dari klien.
// Kalau ADMIN_DOMAIN diisi (mis. admin.personal-trainer-laravel.test) admin hidup di subdomain itu
// dengan alamat /masuk, /booking, dst. Kalau kosong, admin ada di /admin/masuk, /admin/booking, dst.
// Blok ini sengaja didaftarkan PERTAMA supaya subdomain admin tidak ikut menampilkan halaman publik.
// ─────────────────────────────────────────────────────────────
$adminDomain = config('trainer.admin_domain');
$admin       = $adminDomain ? Route::domain($adminDomain) : Route::prefix('admin');

$admin->middleware(NoStore::class)->group(function () use ($adminDomain) {
    Route::get('/masuk', [AdminAuthController::class, 'loginForm'])->name('admin.login');
    Route::post('/masuk', [AdminAuthController::class, 'login'])->middleware('throttle:30,1')->name('admin.login.submit');
    Route::get('/verifikasi', [AdminAuthController::class, 'twoFactorForm'])->name('admin.2fa');
    Route::post('/verifikasi', [AdminAuthController::class, 'twoFactor'])->middleware('throttle:30,1')->name('admin.2fa.submit');
    Route::post('/verifikasi/kirim-ulang', [AdminAuthController::class, 'resend'])->middleware('throttle:10,1')->name('admin.2fa.resend');
    Route::post('/keluar', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Wajib 2FA: admin yang belum mengaktifkan verifikasi 2 langkah hanya bisa membuka halaman keamanan
    Route::middleware([AdminAuth::class, 'admin.2fa.wajib'])->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('admin.home');

        Route::get('/booking', [AdminController::class, 'index'])->name('admin.bookings');
        Route::delete('/booking/riwayat', [AdminController::class, 'clearHistory'])->name('admin.bookings.clear');
        Route::patch('/booking/{booking}/konfirmasi', [AdminController::class, 'confirm'])->name('admin.confirm');
        Route::delete('/booking/{booking}', [AdminController::class, 'destroy'])->name('admin.destroy');

        // Artikel blog
        Route::get('/artikel', [AdminPostController::class, 'index'])->name('admin.posts');
        Route::get('/artikel/baru', [AdminPostController::class, 'create'])->name('admin.posts.create');
        Route::post('/artikel', [AdminPostController::class, 'store'])->name('admin.posts.store');
        Route::get('/artikel/{post}/edit', [AdminPostController::class, 'edit'])->name('admin.posts.edit');
        Route::put('/artikel/{post}', [AdminPostController::class, 'update'])->name('admin.posts.update');
        Route::delete('/artikel/{post}', [AdminPostController::class, 'destroy'])->name('admin.posts.destroy');

        // Pesan dari form kontak
        Route::get('/pesan', [AdminMessageController::class, 'index'])->name('admin.messages');
        Route::delete('/pesan/riwayat', [AdminMessageController::class, 'clearHistory'])->name('admin.messages.clear');
        Route::patch('/pesan/{message}/dibaca', [AdminMessageController::class, 'read'])->name('admin.messages.read');
        Route::delete('/pesan/{message}', [AdminMessageController::class, 'destroy'])->name('admin.messages.destroy');

        // Keamanan: 2FA, ganti password, riwayat login
        Route::get('/keamanan', [AdminSecurityController::class, 'show'])->name('admin.security');
        Route::post('/keamanan/2fa/mulai', [AdminSecurityController::class, 'start'])->name('admin.security.2fa.start');
        Route::post('/keamanan/2fa/konfirmasi', [AdminSecurityController::class, 'confirm'])->name('admin.security.2fa.confirm');
        Route::post('/keamanan/2fa/batal', [AdminSecurityController::class, 'cancel'])->name('admin.security.2fa.cancel');
        Route::post('/keamanan/2fa/kode-baru', [AdminSecurityController::class, 'regenerate'])->name('admin.security.2fa.codes');
        Route::post('/keamanan/2fa/matikan', [AdminSecurityController::class, 'disable'])->name('admin.security.2fa.disable');
        Route::put('/keamanan/password', [AdminSecurityController::class, 'password'])->name('admin.security.password');
        Route::delete('/keamanan/riwayat', [AdminSecurityController::class, 'clearLogs'])->name('admin.security.logs.clear');
    });

    if ($adminDomain) {
        // Alamat lain di subdomain admin tidak menampilkan website publik
        Route::any('/{any}', fn () => redirect()->route('admin.home'))->where('any', '.*');
    }
});

// ─────────────────────────────────────────────────────────────
// WEBSITE PUBLIK & AKUN KLIEN
// ─────────────────────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

// Halaman umum
Route::get('/blog', [PostController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [PostController::class, 'show'])->name('blog.show');
Route::get('/tentang', [PageController::class, 'about'])->name('about');
Route::get('/layanan', [PageController::class, 'services'])->name('services');
Route::get('/booking', [PageController::class, 'booking'])->name('booking.page');
// alamat lama
Route::redirect('/galeri', '/tentang#galeri', 301)->name('gallery');
Route::redirect('/faq', '/layanan#faq', 301)->name('faq');
Route::get('/kontak', [PageController::class, 'contact'])->name('contact');
Route::post('/kontak', [PageController::class, 'contactStore'])->middleware('throttle:5,1')->name('contact.store');
Route::get('/syarat-ketentuan', [PageController::class, 'terms'])->name('terms');
Route::get('/kebijakan-privasi', [PageController::class, 'privacy'])->name('privacy');

// Akun klien
Route::middleware('guest')->group(function () {
    Route::get('/masuk', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/masuk', [AuthController::class, 'login'])->middleware('throttle:30,1');
    Route::get('/daftar', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register'])->middleware('throttle:8,1');

    // Lupa password
    Route::get('/lupa-password', [PasswordResetController::class, 'requestForm'])->name('password.request');
    Route::post('/lupa-password', [PasswordResetController::class, 'sendLink'])->middleware('throttle:6,1')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->middleware('throttle:6,1')->name('password.update');
});
Route::post('/keluar', [AuthController::class, 'logout'])->name('logout');

// Booking (dipanggil lewat fetch dari assets/js/app.js)
Route::get('/slot', [BookingController::class, 'slots'])->name('booking.slots');
Route::middleware('auth')->group(function () {
    Route::get('/booking-saya', [BookingController::class, 'mine'])->name('booking.mine');
    Route::post('/booking', [BookingController::class, 'store'])->middleware('throttle:20,1')->name('booking.store');
    Route::delete('/booking/{booking}', [BookingController::class, 'destroy'])->name('booking.destroy');
});


// Alamat lama /admin di website utama -> arahkan ke area admin
if ($adminDomain) {
    Route::get('/admin', fn () => redirect()->route('admin.home'));
}
<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\LoginLog;
use App\Models\User;
use App\Support\LoginThrottle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    private const MAX_ATTEMPTS = 5;   // gagal 5x -> terkunci
    private const LOCK_SECONDS = 60;  // selama 60 detik

    public function registerForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'min:2', 'max:100'],
            'email'    => ['required', 'email:filter', 'max:150', 'unique:users,email'],
            'wa'       => ['required', 'regex:/^(\+62|62|0)8\d{7,12}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms'    => ['accepted'],
        ], [
            'name.min'           => 'Nama minimal 2 huruf.',
            'email.unique'       => 'Email ini sudah terdaftar. Silakan masuk.',
            'wa.regex'           => 'Nomor WhatsApp tidak valid (contoh 0812xxxxxxx).',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'terms.accepted'     => 'Centang persetujuan syarat & kebijakan privasi dulu.',
        ]);

        unset($data['terms']);
        $data['wa'] = preg_replace('/[\s-]/', '', $data['wa']);

        $user = User::create($data); // password di-hash otomatis oleh model

        Auth::login($user);
        $request->session()->regenerate();
        LoginLog::record('user', $user->email, $user->id, $request, true, 'daftar');

        return redirect(route('booking.page'))->with('welcome', "Akun berhasil dibuat. Halo, {$user->name}!");
    }

    public function loginForm()
    {
        // Admin yang sudah masuk langsung diarahkan ke dashboard
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.home');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $cred = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Satu pintu masuk: kalau email ini milik admin, proses lewat alur admin
        // (kunci 5 menit, 2FA, riwayat login, batas idle) lalu ke dashboard admin.
        if (Admin::where('email', Str::lower($cred['email']))->exists()) {
            return app(AdminAuthController::class)->login($request);
        }

        $key = LoginThrottle::key('user', $request);

        if ($wait = LoginThrottle::lockedFor($key, self::MAX_ATTEMPTS)) {
            LoginLog::record('user', $cred['email'], null, $request, false, 'terkunci');

            return back()->withInput($request->only('email'))
                ->withErrors(['email' => "Terlalu banyak percobaan. Coba lagi dalam {$wait} detik."])
                ->with('lockout', $wait);
        }

        if (! Auth::attempt($cred, $request->boolean('remember'))) {
            LoginThrottle::fail($key, self::LOCK_SECONDS);
            LoginLog::record('user', $cred['email'], null, $request, false, 'password salah');

            $left = LoginThrottle::remaining($key, self::MAX_ATTEMPTS);
            $msg  = 'Email atau password salah.' . ($left > 0 ? " Sisa percobaan: {$left}." : '');
            $back = back()->withInput($request->only('email'))->withErrors(['email' => $msg]);

            return $left === 0 ? $back->with('lockout', LoginThrottle::lockedFor($key, self::MAX_ATTEMPTS)) : $back;
        }

        LoginThrottle::clear($key);
        $request->session()->regenerate();

        $user = $request->user();
        LoginLog::record('user', $user->email, $user->id, $request, true);

        return redirect()->intended(route('booking.page'))
            ->with('welcome', "Selamat datang kembali, {$user->name}!");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}

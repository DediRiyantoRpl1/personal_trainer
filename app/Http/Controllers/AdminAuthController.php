<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\LoginLog;
use App\Support\LoginThrottle;
use App\Support\EmailOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminAuthController extends Controller
{
    private const MAX_ATTEMPTS = 5;
    private const LOCK_SECONDS = 300; // admin dikunci 5 menit setelah 5x gagal

    public function loginForm()
    {
        if (Auth::guard('admin')->check()) {
            // admin.home akan meminta kode 2FA lagi kalau verifikasinya sudah dicabut
            return redirect()->route('admin.home');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $key = LoginThrottle::key('admin', $request);

        if ($wait = LoginThrottle::lockedFor($key, self::MAX_ATTEMPTS)) {
            LoginLog::record('admin', $data['email'], null, $request, false, 'terkunci');

            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Terlalu banyak percobaan. Akses dikunci sementara.'])
                ->with('lockout', $wait);
        }

        $admin = Admin::where('email', Str::lower($data['email']))->first();

        if (! $admin || ! Hash::check($data['password'], $admin->password)) {
            LoginThrottle::fail($key, self::LOCK_SECONDS);
            LoginLog::record('admin', $data['email'], $admin?->id, $request, false, 'password salah');

            $left = LoginThrottle::remaining($key, self::MAX_ATTEMPTS);
            $back = back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password salah.' . ($left > 0 ? " Sisa percobaan: {$left}." : '')]);

            return $left === 0 ? $back->with('lockout', LoginThrottle::lockedFor($key, self::MAX_ATTEMPTS)) : $back;
        }

        LoginThrottle::clear($key);

        if ($admin->hasTwoFactor()) {
            $request->session()->put('admin_2fa', ['id' => $admin->id, 'at' => time()]);

            try {
                EmailOtp::send($admin, 'login', force: true);
                $request->session()->flash('otp_sent', true);
            } catch (\Throwable $e) {
                report($e);
                $request->session()->flash('otp_failed', true);
            }

            return redirect()->route('admin.2fa');
        }

        return $this->finish($request, $admin);
    }

    public function twoFactorForm(Request $request)
    {
        $pending = $this->pending($request);

        if (! $pending) {
            return redirect()->route(config('trainer.admin_domain') ? 'admin.login' : 'login');
        }

        $admin = Admin::find($pending['id']);
        $email = (string) $admin?->email;
        $at    = (int) strpos($email, '@');

        return view('admin.auth.two-factor', [
            'maskedEmail' => $at > 2 ? Str::mask($email, '*', 2, $at - 2) : $email,
        ]);
    }

    public function resend(Request $request)
    {
        $pending = $this->pending($request);
        $admin   = $pending ? Admin::find($pending['id']) : null;

        if (! $admin) {
            return redirect()->route(config('trainer.admin_domain') ? 'admin.login' : 'login');
        }

        try {
            $wait = EmailOtp::send($admin, 'login');
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors(['code' => 'Email gagal dikirim. Coba lagi sebentar, atau pakai kode pemulihan.']);
        }

        if ($wait > 0) {
            return back()->withErrors(['code' => "Tunggu {$wait} detik sebelum minta kode lagi."]);
        }

        return back()->with('otp_sent', true);
    }

    public function twoFactor(Request $request)
    {
        $pending = $this->pending($request);
        if (! $pending) {
            return redirect()->route(config('trainer.admin_domain') ? 'admin.login' : 'login')
                ->withErrors(['email' => 'Verifikasi kedaluwarsa. Masuk ulang ya.']);
        }

        $admin = Admin::find($pending['id']);
        $input = trim((string) $request->input('code'));
        $key   = LoginThrottle::key('admin2fa', $request, (string) $pending['id']);

        if ($wait = LoginThrottle::lockedFor($key, self::MAX_ATTEMPTS)) {
            return back()->withErrors(['code' => 'Terlalu banyak percobaan.'])->with('lockout', $wait);
        }

        if ($admin && $this->codeIsValid($admin, $input)) {
            LoginThrottle::clear($key);
            $request->session()->forget('admin_2fa');

            return $this->finish($request, $admin, ! empty($pending['recheck']) ? '2FA ulang' : '2FA');
        }

        LoginThrottle::fail($key, self::LOCK_SECONDS);
        LoginLog::record('admin', $admin?->email ?? '-', $admin?->id, $request, false, 'kode 2FA salah');

        $left = LoginThrottle::remaining($key, self::MAX_ATTEMPTS);
        $back = back()->withErrors(['code' => 'Kode salah atau sudah dipakai.' . ($left > 0 ? " Sisa percobaan: {$left}." : '')]);

        return $left === 0 ? $back->with('lockout', LoginThrottle::lockedFor($key, self::MAX_ATTEMPTS)) : $back;
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route(config('trainer.admin_domain') ? 'admin.login' : 'login')->with('status', 'Kamu sudah keluar dari area admin.');
    }

    /** Selesaikan login: simpan sesi, catat riwayat, arahkan ke dashboard. */
    private function finish(Request $request, Admin $admin, ?string $note = null)
    {
        Auth::guard('admin')->login($admin);
        $request->session()->regenerate();
        $request->session()->put('admin_last_activity', time());
        $request->session()->put('admin_verified', true); // lolos 2FA (atau admin ini tanpa 2FA)

        $admin->forceFill(['last_login_at' => now(), 'last_login_ip' => $request->ip()])->saveQuietly();
        LoginLog::record('admin', $admin->email, $admin->id, $request, true, $note);

        return redirect()->intended(route('admin.home'))
            ->with('ok', "Selamat datang, {$admin->name}.");
    }

    /** Data login tahap-1 yang menunggu kode 2FA (berlaku 10 menit). */
    private function pending(Request $request): ?array
    {
        $p = $request->session()->get('admin_2fa');

        if (! is_array($p) || time() - ($p['at'] ?? 0) > 600) {
            $request->session()->forget('admin_2fa');

            return null;
        }

        return $p;
    }

    private function codeIsValid(Admin $admin, string $input): bool
    {
        $digits = preg_replace('/\s+/', '', $input);

        if (preg_match('/^\d{6}$/', $digits)) {
            // kode dari email (sekali pakai, kedaluwarsa 10 menit)
            return EmailOtp::verify($admin, 'login', $digits);
        }

        // kode pemulihan, mis. "ab12-cd34"
        $recovery = strtolower($input);
        $codes    = $admin->two_factor_recovery_codes ?? [];
        $index    = array_search($recovery, $codes, true);

        if ($index === false) {
            return false;
        }

        unset($codes[$index]);
        $admin->forceFill(['two_factor_recovery_codes' => array_values($codes)])->saveQuietly();

        return true;
    }
}

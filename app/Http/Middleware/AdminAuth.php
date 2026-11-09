<?php

namespace App\Http\Middleware;

use App\Support\EmailOtp;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/** Hanya akun admin (guard "admin"). Sesi otomatis berakhir jika tidak aktif. */
class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $guard = Auth::guard('admin');

        if (! $guard->check()) {
            return redirect()->guest(route(config('trainer.admin_domain') ? 'admin.login' : 'login'));
        }

        // 2FA wajib: sebelum aktif, hanya halaman Keamanan dan keluar yang boleh dibuka
        if (config('trainer.admin_require_2fa', true)
            && ! $guard->user()->hasTwoFactor()
            && ! $request->routeIs('admin.security*', 'admin.logout')) {
            return redirect()->route('admin.security')
                ->withErrors(['code' => 'Aktifkan verifikasi 2 langkah dulu untuk membuka halaman admin lainnya.']);
        }

        $idle = (int) config('trainer.admin_idle_minutes', 30) * 60;
        $last = (int) $request->session()->get('admin_last_activity', time());

        if (time() - $last > $idle) {
            $guard->logout();
            $request->session()->forget('admin_last_activity');
            $request->session()->regenerate();

            return redirect()->route(config('trainer.admin_domain') ? 'admin.login' : 'login')
                ->withErrors(['email' => 'Sesi berakhir karena lama tidak aktif. Silakan masuk lagi.']);
        }

        // Verifikasi 2FA berlaku selama admin tetap di area admin. Begitu keluar ke halaman lain
        // (lihat ForgetAdminVerification), admin harus memasukkan kode 2FA lagi untuk masuk kembali.
        if ($guard->user()->hasTwoFactor() && ! $request->session()->get('admin_verified')) {
            return $this->challenge($request, $guard->user());
        }

        $request->session()->put('admin_last_activity', time());

        return $next($request);
    }

    /** Minta kode 2FA lagi (admin masih login, tapi verifikasinya sudah dicabut). */
    private function challenge(Request $request, $admin): Response
    {
        $session = $request->session();

        if ($request->isMethod('GET') && ! $request->expectsJson()) {
            $session->put('url.intended', $request->fullUrl());
        }

        $p     = $session->get('admin_2fa');
        $fresh = is_array($p) && ($p['id'] ?? null) === $admin->id && time() - ($p['at'] ?? 0) <= 600;

        if (! $fresh) {
            $session->put('admin_2fa', ['id' => $admin->id, 'at' => time(), 'recheck' => true]);

            try {
                EmailOtp::send($admin, 'login', force: true);
                $session->flash('otp_sent', true);
            } catch (\Throwable $e) {
                report($e);
                $session->flash('otp_failed', true);
            }
        }

        return redirect()->route('admin.2fa');
    }
}

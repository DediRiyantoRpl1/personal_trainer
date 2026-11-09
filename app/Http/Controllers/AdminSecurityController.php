<?php

namespace App\Http\Controllers;

use App\Models\LoginLog;
use App\Support\EmailOtp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSecurityController extends Controller
{
    public function show(Request $request)
    {
        $admin  = Auth::guard('admin')->user();
        return view('admin.security', [
            'admin'  => $admin,
            'logs'   => LoginLog::where('guard', 'admin')->where('account_id', $admin->id)->latest('created_at')->limit(15)->get(),
            'setup'  => (bool) $request->session()->get('admin_2fa_setup'),
            'codes'  => $request->session()->get('recovery_codes'),
        ]);
    }

    public function start(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        try {
            $wait = EmailOtp::send($admin, 'setup');
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors(['code' => 'Email gagal dikirim. Periksa pengaturan MAIL_* di .env, lalu coba lagi.']);
        }

        $request->session()->put('admin_2fa_setup', true);

        return redirect()->route('admin.security')->with('ok', $wait > 0
            ? "Kode sebelumnya masih berlaku. Untuk kirim ulang, tunggu {$wait} detik."
            : "Kode verifikasi dikirim ke {$admin->email}. Cek inbox (dan spam).");
    }

    public function confirm(Request $request)
    {
        $request->validate(['code' => ['required', 'digits:6']], ['code.digits' => 'Kode harus 6 angka.']);

        if (! $request->session()->get('admin_2fa_setup')) {
            return redirect()->route('admin.security');
        }

        if (! EmailOtp::verify(Auth::guard('admin')->user(), 'setup', $request->input('code'))) {
            return back()->withErrors(['code' => 'Kode salah atau sudah kedaluwarsa. Kirim ulang kode kalau perlu.']);
        }

        Auth::guard('admin')->user()->forceFill([
            'two_factor_secret'         => null,
            'two_factor_recovery_codes' => $this->newRecoveryCodes(),
            'two_factor_confirmed_at'   => now(),
        ])->save();

        $request->session()->forget('admin_2fa_setup');
        $request->session()->put('admin_verified', true); // baru saja membuktikan akses email

        return redirect()->route('admin.security')
            ->with('recovery_codes', Auth::guard('admin')->user()->two_factor_recovery_codes)
            ->with('ok', 'Verifikasi 2 langkah aktif. Simpan kode pemulihan di bawah.');
    }

    public function cancel(Request $request)
    {
        $request->session()->forget('admin_2fa_setup');

        return redirect()->route('admin.security');
    }

    public function regenerate(Request $request)
    {
        $this->requirePassword($request);

        $admin = Auth::guard('admin')->user();
        abort_unless($admin->hasTwoFactor(), 404);

        $admin->forceFill(['two_factor_recovery_codes' => $this->newRecoveryCodes()])->save();

        return redirect()->route('admin.security')
            ->with('recovery_codes', $admin->two_factor_recovery_codes)
            ->with('ok', 'Kode pemulihan baru dibuat. Kode lama tidak berlaku lagi.');
    }

    public function disable(Request $request)
    {
        $this->requirePassword($request);

        Auth::guard('admin')->user()->forceFill([
            'two_factor_secret'         => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at'   => null,
        ])->save();

        return redirect()->route('admin.security')->with('ok', 'Verifikasi 2 langkah dimatikan.');
    }

    public function password(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'string', 'min:10', 'confirmed', 'different:current_password'],
        ], [
            'password.min'       => 'Password admin minimal 10 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sama.',
            'password.different' => 'Password baru harus berbeda dari yang lama.',
        ]);

        $admin = Auth::guard('admin')->user();

        if (! Hash::check($request->input('current_password'), $admin->password)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'current_password' => 'Password saat ini salah.',
            ]);
        }

        $admin->forceFill(['password' => $request->input('password'), 'remember_token' => Str::random(60)])->save();

        return redirect()->route('admin.security')->with('ok', 'Password berhasil diganti.');
    }

    /** Hapus seluruh riwayat login milik admin ini (butuh password). */
    public function clearLogs(Request $request)
    {
        $this->requirePassword($request);

        $admin = Auth::guard('admin')->user();

        $count = LoginLog::where('guard', 'admin')
            ->where(fn ($q) => $q->where('account_id', $admin->id)->orWhere('email', Str::lower($admin->email)))
            ->delete();

        return redirect()->route('admin.security')->with('ok', "Riwayat login dihapus ({$count} catatan).");
    }

    private function requirePassword(Request $request): void
    {
        $request->validate(['current_password' => ['required']]);

        if (! Hash::check($request->input('current_password'), Auth::guard('admin')->user()->password)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'current_password' => 'Password saat ini salah.',
            ]);
        }
    }

    private function newRecoveryCodes(): array
    {
        return collect(range(1, 8))
            ->map(fn () => strtolower(Str::random(4) . '-' . Str::random(4)))
            ->all();
    }
}

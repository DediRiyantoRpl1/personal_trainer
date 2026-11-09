<?php

namespace App\Support;

use App\Models\Admin;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

/**
 * Kode verifikasi 6 angka yang dikirim lewat email ke admin.
 * - berlaku 10 menit, sekali pakai
 * - salah 5x -> kode dihanguskan (harus kirim ulang)
 * - kirim ulang dibatasi 1x per 60 detik
 */
class EmailOtp
{
    public const TTL       = 600;
    public const COOLDOWN  = 60;
    public const MAX_TRIES = 5;

    /**
     * Kirim kode baru. Mengembalikan 0 jika terkirim, atau jumlah detik yang
     * harus ditunggu jika masih dalam masa jeda (kecuali $force).
     * Melempar exception jika email gagal dikirim.
     */
    public static function send(Admin $admin, string $purpose, bool $force = false): int
    {
        if (! $force && ($wait = self::cooldown($admin, $purpose)) > 0) {
            return $wait;
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $key  = self::key($admin, $purpose);

        Cache::put($key, ['hash' => Hash::make($code), 'tries' => 0, 'exp' => time() + self::TTL], self::TTL);

        try {
            $name = config('trainer.name', config('app.name'));
            $body = "Halo {$admin->name},\n\n"
                  . "Kode verifikasi admin {$name} kamu:\n\n    {$code}\n\n"
                  . "Berlaku 10 menit dan hanya bisa dipakai sekali. "
                  . "Jangan bagikan kode ini ke siapa pun.\n\n"
                  . "Kalau kamu tidak sedang login, abaikan email ini dan segera ganti password admin.";

            Mail::raw($body, fn ($m) => $m->to($admin->email)->subject("Kode verifikasi admin: {$code}"));
        } catch (\Throwable $e) {
            Cache::forget($key);
            throw $e;
        }

        Cache::put($key . ':sent', time(), self::COOLDOWN);

        return 0;
    }

    public static function verify(Admin $admin, string $purpose, string $code): bool
    {
        $key  = self::key($admin, $purpose);
        $data = Cache::get($key);

        if (! $data || ! preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        if (Hash::check($code, $data['hash'])) {
            Cache::forget($key);

            return true;
        }

        $data['tries']++;
        if ($data['tries'] >= self::MAX_TRIES) {
            Cache::forget($key);
        } else {
            Cache::put($key, $data, max(1, $data['exp'] - time()));
        }

        return false;
    }

    public static function cooldown(Admin $admin, string $purpose): int
    {
        $sent = Cache::get(self::key($admin, $purpose) . ':sent');

        return $sent ? max(0, self::COOLDOWN - (time() - (int) $sent)) : 0;
    }

    private static function key(Admin $admin, string $purpose): string
    {
        return "admin_otp:{$purpose}:{$admin->id}";
    }
}

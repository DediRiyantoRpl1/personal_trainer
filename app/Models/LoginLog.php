<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class LoginLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['guard', 'email', 'account_id', 'ip', 'user_agent', 'success', 'note'];

    protected function casts(): array
    {
        return ['success' => 'boolean'];
    }

    public static function record(string $guard, string $email, ?int $accountId, Request $request, bool $success, ?string $note = null): void
    {
        try {
            static::create([
                'guard'      => $guard,
                'email'      => mb_substr(strtolower($email), 0, 190),
                'account_id' => $accountId,
                'ip'         => $request->ip(),
                'user_agent' => mb_substr((string) $request->userAgent(), 0, 250),
                'success'    => $success,
                'note'       => $note,
            ]);
        } catch (\Throwable $e) {
            report($e); // log gagal tidak boleh menghalangi login
        }
    }

    /** "Chrome · Windows" dari user agent, tanpa library tambahan */
    public function device(): string
    {
        $ua = (string) $this->user_agent;
        $browser = match (true) {
            str_contains($ua, 'Edg/')    => 'Edge',
            str_contains($ua, 'OPR/')    => 'Opera',
            str_contains($ua, 'Firefox') => 'Firefox',
            str_contains($ua, 'Chrome')  => 'Chrome',
            str_contains($ua, 'Safari')  => 'Safari',
            default                      => 'Browser lain',
        };
        $os = match (true) {
            str_contains($ua, 'Windows') => 'Windows',
            str_contains($ua, 'Android') => 'Android',
            str_contains($ua, 'iPhone'), str_contains($ua, 'iPad') => 'iOS',
            str_contains($ua, 'Mac OS')  => 'macOS',
            str_contains($ua, 'Linux')   => 'Linux',
            default                      => 'OS lain',
        };

        return "$browser · $os";
    }
}

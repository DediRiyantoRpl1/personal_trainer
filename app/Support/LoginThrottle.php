<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

/** Kunci percobaan login per (email + IP), dengan hitung mundur untuk ditampilkan ke pengguna. */
final class LoginThrottle
{
    public static function key(string $scope, Request $request, ?string $who = null): string
    {
        return $scope . '|' . Str::lower($who ?? (string) $request->input('email')) . '|' . $request->ip();
    }

    /** Detik tersisa jika sedang terkunci, atau null jika boleh mencoba. */
    public static function lockedFor(string $key, int $max): ?int
    {
        return RateLimiter::tooManyAttempts($key, $max) ? RateLimiter::availableIn($key) : null;
    }

    public static function fail(string $key, int $decaySeconds): void
    {
        RateLimiter::hit($key, $decaySeconds);
    }

    public static function remaining(string $key, int $max): int
    {
        return RateLimiter::remaining($key, $max);
    }

    public static function clear(string $key): void
    {
        RateLimiter::clear($key);
    }
}

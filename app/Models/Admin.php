<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

/** Akun admin: tabel & guard terpisah dari akun klien (User). */
class Admin extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes'];

    protected function casts(): array
    {
        return [
            'password'                  => 'hashed',
            'two_factor_secret'         => 'encrypted',
            'two_factor_recovery_codes' => 'encrypted:array',
            'two_factor_confirmed_at'   => 'datetime',
            'last_login_at'             => 'datetime',
        ];
    }

    public function hasTwoFactor(): bool
    {
        // 2FA = kode verifikasi lewat email; cukup ditandai oleh two_factor_confirmed_at
        return $this->two_factor_confirmed_at !== null;
    }
}

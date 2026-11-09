<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AdminReset2fa extends Command
{
    protected $signature = 'admin:reset-2fa {email : Email admin}';

    protected $description = 'Matikan verifikasi 2 langkah admin (kalau HP hilang dan kode pemulihan habis)';

    public function handle(): int
    {
        $admin = Admin::where('email', Str::lower($this->argument('email')))->first();

        if (! $admin) {
            $this->error('Admin tidak ditemukan.');

            return self::FAILURE;
        }

        $admin->forceFill([
            'two_factor_secret'         => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at'   => null,
        ])->save();

        $this->info("2FA untuk {$admin->email} dimatikan. Aktifkan lagi dari menu Keamanan setelah masuk.");

        return self::SUCCESS;
    }
}

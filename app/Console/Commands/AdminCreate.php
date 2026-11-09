<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class AdminCreate extends Command
{
    protected $signature = 'admin:create {email? : Email admin} {--name= : Nama admin}';

    protected $description = 'Buat akun admin baru, atau reset password admin yang sudah ada';

    public function handle(): int
    {
        $email = Str::lower($this->argument('email') ?: $this->ask('Email admin'));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Format email tidak valid.');

            return self::FAILURE;
        }

        $admin = Admin::where('email', $email)->first();
        $name  = $this->option('name') ?: ($admin?->name ?? $this->ask('Nama', 'Coach Dedi'));

        $password = $this->secret('Password (min. 10 karakter)');
        if (strlen((string) $password) < 10) {
            $this->error('Password minimal 10 karakter.');

            return self::FAILURE;
        }
        if ($password !== $this->secret('Ulangi password')) {
            $this->error('Konfirmasi password tidak sama.');

            return self::FAILURE;
        }

        if ($admin) {
            $admin->forceFill(['name' => $name, 'password' => $password])->save();
            $this->info("Password admin {$email} diperbarui.");
        } else {
            Admin::create(['name' => $name, 'email' => $email, 'password' => $password]);
            $this->info("Admin {$email} dibuat.");
        }

        return self::SUCCESS;
    }
}

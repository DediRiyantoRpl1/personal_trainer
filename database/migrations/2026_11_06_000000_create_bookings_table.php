<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('wa', 20);
            $table->string('paket', 60);
            $table->date('tanggal');
            $table->string('jam', 5);
            $table->string('target', 60);
            $table->string('status', 20)->default('pending'); // pending | confirmed
            $table->timestamps();

            // satu slot hanya boleh dipesan satu orang
            $table->unique(['tanggal', 'jam']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

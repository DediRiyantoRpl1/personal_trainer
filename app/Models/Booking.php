<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    public const PENDING = 'pending';
    public const CONFIRMED = 'confirmed';

    protected $fillable = ['user_id', 'nama', 'wa', 'paket', 'tanggal', 'jam', 'target', 'status'];

    /** Nomor WA format internasional tanpa plus (628xxx) untuk link wa.me */
    public function waLink(): string
    {
        $n = preg_replace('/\D/', '', $this->wa);
        if (str_starts_with($n, '0')) {
            $n = '62' . substr($n, 1);
        }

        return 'https://wa.me/' . $n;
    }
}

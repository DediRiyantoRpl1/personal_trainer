<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['nama', 'email', 'telp', 'pesan', 'dibaca'];

    protected function casts(): array
    {
        return ['dibaca' => 'boolean'];
    }
}

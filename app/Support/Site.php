<?php

namespace App\Support;

use Illuminate\Support\Str;

final class Site
{
    /** URL website utama; kalau admin di subdomain (admin.contoh.test), kembali ke contoh.test */
    public static function mainUrl(string $path = '/'): string
    {
        $admin = config('trainer.admin_domain');

        if (! $admin) {
            return url($path);
        }

        $req  = request();
        $port = $req->getPort();
        $std  = in_array($port, [80, 443], true);

        return $req->getScheme() . '://' . Str::after($admin, '.') . ($std ? '' : ':' . $port) . $path;
    }
}

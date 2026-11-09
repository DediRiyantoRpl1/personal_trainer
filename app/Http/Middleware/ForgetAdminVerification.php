<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin membuka halaman di luar area admin (website publik, akun klien, dst)
 * -> verifikasi 2FA-nya dicabut. Masuk ke admin lagi harus memasukkan kode 2FA.
 *
 * Halaman publik juga dikirim no-store selama admin login, supaya tombol Back/Forward
 * tidak menampilkan salinan cache (yang tidak menyentuh server) dan melewati pencabutan ini.
 */
class ForgetAdminVerification
{
    public function handle(Request $request, Closure $next): Response
    {
        $inAdmin = $request->routeIs('admin.*');
        $named   = $request->route()?->getName() !== null;
        $page    = $request->isMethod('GET') && ! $request->expectsJson() && ! $request->ajax();

        $admin = Auth::guard('admin')->user();

        if ($admin && ! $inAdmin && $named) {
            if ($page) {
                $request->session()->forget('admin_verified');
            }

            $response = $next($request);
            $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private');

            return $response;
        }

        return $next($request);
    }
}

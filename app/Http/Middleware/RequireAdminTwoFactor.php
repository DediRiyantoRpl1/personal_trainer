<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequireAdminTwoFactor
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = Auth::guard('admin')->user();

        if ($admin
            && ! $admin->hasTwoFactor()
            && ! $request->routeIs('admin.security*', 'admin.logout')
        ) {
            return redirect()->route('admin.security')->withErrors([
                'code' => 'Aktifkan verifikasi 2 langkah dulu untuk membuka halaman admin lainnya.',
            ]);
        }

        return $next($request);
    }
}
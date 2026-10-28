<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckProfileComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // 1. Jika belum login atau merupakan pengurus/admin RT, tidak terikat onboarding warga
        if (!$user || $user->isAdmin()) {
            return $next($request);
        }

        // 2. Jika rute saat ini adalah halaman Onboarding atau Logout
        if ($request->routeIs('warga.onboarding*') || $request->routeIs('logout')) {
            // Jika profil SUDAH lengkap tetapi mencoba mengakses onboarding lagi, arahkan langsung ke dashboard
            if ($user->is_profile_complete && $request->routeIs('warga.onboarding*')) {
                return redirect()->route('warga.dashboard');
            }

            return $next($request);
        }

        // 3. Jika profil warga BELUM lengkap, kunci akses dan paksa ke halaman Onboarding
        if (!$user->is_profile_complete) {
            return redirect()->route('warga.onboarding')
                ->with('warning', 'Silakan lengkapi data profil kependudukan Anda terlebih dahulu untuk mengaktifkan seluruh layanan.');
        }

        return $next($request);
    }
}


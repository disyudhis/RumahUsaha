<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApprovedUmkmMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Cek apakah user adalah pemilik UMKM
        if ($user->user_type === 'pemilik_umkm') {
            // Jika belum di-approve, logout dan redirect ke login dengan pesan
            if (!$user->is_approved) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('error', 'Akun Anda masih menunggu persetujuan dari admin. Anda akan menerima notifikasi melalui email setelah akun disetujui.')->with('pending_approval', true)->with('user_email', $user->email);
            }
        }

        return $next($request);
    }
}

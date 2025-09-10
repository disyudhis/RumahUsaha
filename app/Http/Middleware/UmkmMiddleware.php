<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UmkmMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user has the required role
        if ($user->user_type !== $role) {
            // Log unauthorized access attempt (optional)
            \Log::warning('Unauthorized access attempt', [
                'user_id' => $user->id,
                'user_type' => $user->user_type,
                'required_role' => $role,
                'url' => $request->url(),
            ]);

            // Redirect based on user's actual role
            return $this->redirectBasedOnRole($user->user_type);
        }

        return $next($request);
    }

    /**
     * Redirect user based on their role
     */
    private function redirectBasedOnRole(string $userType): Response
    {
        switch ($userType) {
            case 'admin':
                return redirect()->route('admin.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');

            case 'pemilik_umkm':
                return redirect()->route('umkm.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');

            default:
                // customer/umum atau user type lainnya
                return redirect()->route('home')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }
    }
}
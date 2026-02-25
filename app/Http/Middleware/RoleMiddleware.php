<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Cek apakah user yang login memiliki role yang diizinkan.
     * Penggunaan: middleware('role:upt,bbkhit,pusat')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userRole = trim(strtolower(auth()->user()->role));
        $allowedRoles = array_map(fn($r) => trim(strtolower($r)), $roles);

        if (!in_array($userRole, $allowedRoles)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}

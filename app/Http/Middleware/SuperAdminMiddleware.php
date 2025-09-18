<?php

/**
 * Author: Eng.Fahed
 * SuperAdminMiddleware for HR System
 * Ensures only super admin users can access super admin routes
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!Auth::user()->isSuperAdmin()) {
            return redirect()->route('dashboard')->with('error', 'Access denied. Super admin privileges required.');
        }

        return $next($request);
    }
}

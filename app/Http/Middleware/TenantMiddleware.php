<?php

/**
 * Author: Eng.Fahed
 * TenantMiddleware for HR System Multi-Tenant Architecture
 * Handles tenant isolation and data separation
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip tenant middleware for super admin routes
        if ($request->is('super-admin/*')) {
            return $next($request);
        }

        // Get current user
        $user = Auth::user();

        if ($user) {
            // Super admin users skip tenant middleware
            if ($user->isSuperAdmin()) {
                return $next($request);
            }

            // Set tenant context for authenticated users
            if ($user->tenant_id) {
                $tenant = Tenant::find($user->tenant_id);
                
                if ($tenant && $tenant->isActive() && $tenant->isSubscriptionValid()) {
                    // Set tenant in session and request
                    Session::put('tenant_id', $tenant->id);
                    $request->merge(['tenant_id' => $tenant->id]);
                    
                    // Make tenant available globally
                    app()->instance('tenant', $tenant);
                } else {
                    // Tenant is inactive or subscription expired
                    Auth::logout();
                    return redirect()->route('login')->with('error', 'Your account has been suspended or subscription expired.');
                }
            } else {
                // User has no tenant and is not super admin
                Auth::logout();
                return redirect()->route('login')->with('error', 'Invalid user configuration.');
            }
        }

        return $next($request);
    }
}

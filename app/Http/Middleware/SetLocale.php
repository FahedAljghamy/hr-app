<?php

/**
 * Author: Eng.Fahed
 * SetLocale Middleware for HR System
 * Handles locale setting based on session
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get locale from session first, then cookie, then default
        $locale = session('locale');
        
        if (!$locale) {
            $locale = $request->cookie('locale');
        }
        
        if (!$locale) {
            $locale = config('app.locale');
        }
        
        // Validate and set locale
        if (in_array($locale, array_keys(config('app.available_locales')))) {
            app()->setLocale($locale);
            
            // Also store in session if not already there
            if (!session('locale')) {
                session(['locale' => $locale]);
            }
            
        }

        return $next($request);
    }
}

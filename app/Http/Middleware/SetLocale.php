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
        $locale = session('locale', config('app.locale'));
        
        if (in_array($locale, array_keys(config('app.available_locales')))) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}

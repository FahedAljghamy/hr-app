<?php

/**
 * Author: Eng.Fahed
 * Language Controller for HR System
 * Handles language switching functionality
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch application language
     * 
     * @param Request $request
     * @param string $locale
     * @return RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function switch(Request $request, string $locale)
    {
        // Validate locale
        if (!in_array($locale, ['en', 'ar'])) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Invalid language selected.'], 400);
            }
            return redirect()->back()->with('error', 'Invalid language selected.');
        }
        
        // Set application locale
        App::setLocale($locale);
        
        // Store in session with debugging
        Session::put('locale', $locale);
        Session::save(); // Force save session
        
        
        // For AJAX requests, return JSON
        if ($request->ajax()) {
            $response = response()->json([
                'success' => true,
                'locale' => $locale,
                'message' => 'Language changed successfully.'
            ]);
            $response->cookie('locale', $locale, 60 * 24 * 365); // 1 year
            return $response;
        }
        
        // For regular requests, redirect back
        $response = redirect()->back()->with('success', 'Language changed successfully.');
        $response->cookie('locale', $locale, 60 * 24 * 365); // 1 year
        
        return $response;
    }
    
    /**
     * Get current locale
     * 
     * @return string
     */
    public function current(): string
    {
        return App::getLocale();
    }
}

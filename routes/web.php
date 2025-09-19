<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Language switching routes
Route::get('/locale/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        app()->setLocale($locale);
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('locale');

// Authentication Routes (Simple for testing)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->only('email', 'password');
    
    if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended(route('dashboard'));
    }
    
    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ]);
});

Route::post('/logout', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// Super Admin Routes (Protected by auth and super admin check)
Route::prefix('super-admin')->name('super-admin.')->middleware(['auth', 'superadmin'])->group(function () {
    Route::get('/', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [App\Http\Controllers\SuperAdmin\DashboardController::class, 'index'])->name('dashboard');
    
    // Tenant Management Routes
    Route::resource('tenants', App\Http\Controllers\SuperAdmin\TenantController::class);
    
    // Additional Tenant Routes
    Route::patch('tenants/{tenant}/activate', [App\Http\Controllers\SuperAdmin\TenantController::class, 'activate'])->name('tenants.activate');
    Route::patch('tenants/{tenant}/suspend', [App\Http\Controllers\SuperAdmin\TenantController::class, 'suspend'])->name('tenants.suspend');
    Route::post('tenants/{tenant}/extend-subscription', [App\Http\Controllers\SuperAdmin\TenantController::class, 'extendSubscription'])->name('tenants.extend-subscription');
    Route::get('tenants/{tenant}/statistics', [App\Http\Controllers\SuperAdmin\TenantController::class, 'statistics'])->name('tenants.statistics');
});

// Tenant Routes (Protected by auth and tenant middleware)
Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
});

// Default route for testing (without auth for now)
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/test', [App\Http\Controllers\DashboardController::class, 'index'])->name('test');

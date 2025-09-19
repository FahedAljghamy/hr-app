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
Route::get('/locale/{locale}', [App\Http\Controllers\LanguageController::class, 'switch'])->name('locale');
Route::get('/locale', [App\Http\Controllers\LanguageController::class, 'current'])->name('locale.current');


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
    
    // Tenant Dashboard Routes
    Route::prefix('tenant-dashboard')->name('tenant-dashboard.')->group(function () {
        Route::get('/', [App\Http\Controllers\TenantDashboardController::class, 'index'])->name('index');
        Route::get('/detailed-report', [App\Http\Controllers\TenantDashboardController::class, 'detailedReport'])->name('detailed-report');
        Route::get('/export-report', [App\Http\Controllers\TenantDashboardController::class, 'exportReport'])->name('export-report');
        
        // API Routes for Dashboard
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('users-stats', [App\Http\Controllers\TenantDashboardController::class, 'getUsersStats'])->name('users-stats');
            Route::get('roles-stats', [App\Http\Controllers\TenantDashboardController::class, 'getRolesStats'])->name('roles-stats');
            Route::get('user-activity', [App\Http\Controllers\TenantDashboardController::class, 'getUserActivity'])->name('user-activity');
        });
    });
    
    // Roles & Permissions Management Routes
    Route::resource('roles', App\Http\Controllers\RoleController::class);
    Route::resource('permissions', App\Http\Controllers\PermissionController::class);
    Route::resource('user-management', App\Http\Controllers\UserManagementController::class)->parameters([
        'user-management' => 'user'
    ]);
    
    // Roles Permissions Map
    Route::get('roles-permissions-map', function() {
        return view('roles.permissions-map');
    })->name('roles.permissions-map');
    
    // Additional API Routes for Roles & Permissions
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('roles', [App\Http\Controllers\RoleController::class, 'apiIndex'])->name('roles.index');
        Route::post('roles/{role}/assign-permissions', [App\Http\Controllers\RoleController::class, 'assignPermissions'])->name('roles.assign-permissions');
        Route::get('permissions', [App\Http\Controllers\PermissionController::class, 'apiIndex'])->name('permissions.index');
        
        // User Role & Permission Management
        Route::post('users/{user}/assign-role', [App\Http\Controllers\UserManagementController::class, 'assignRole'])->name('users.assign-role');
        Route::post('users/{user}/revoke-role', [App\Http\Controllers\UserManagementController::class, 'revokeRole'])->name('users.revoke-role');
        Route::post('users/{user}/assign-permission', [App\Http\Controllers\UserManagementController::class, 'assignPermission'])->name('users.assign-permission');
        Route::post('users/{user}/revoke-permission', [App\Http\Controllers\UserManagementController::class, 'revokePermission'])->name('users.revoke-permission');
    });
    
    // Bulk Permission Creation
    Route::post('permissions/bulk-create', [App\Http\Controllers\PermissionController::class, 'bulkCreate'])->name('permissions.bulk-create');
});

// Default route for testing (without auth for now)
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/test', [App\Http\Controllers\DashboardController::class, 'index'])->name('test');

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

        // Company Management Routes
        Route::resource('branches', App\Http\Controllers\BranchController::class);
        Route::resource('company-settings', App\Http\Controllers\CompanySettingController::class);
        Route::resource('legal-documents', App\Http\Controllers\LegalDocumentController::class);
        
        // Company Settings as singular route (redirect to index)
        Route::get('company-setting', function() {
            return redirect()->route('company-settings.index');
        })->name('company-setting.index');
        
        // Legal Documents additional routes
        Route::patch('legal-documents/{legalDocument}/status', [App\Http\Controllers\LegalDocumentController::class, 'updateStatus'])->name('legal-documents.update-status');
        Route::get('legal-documents/{legalDocument}/download', [App\Http\Controllers\LegalDocumentController::class, 'download'])->name('legal-documents.download');

        // Employee Management Routes
        Route::resource('employees', App\Http\Controllers\EmployeeController::class);
        Route::resource('payrolls', App\Http\Controllers\PayrollController::class);
        Route::patch('payrolls/{payroll}/mark-as-paid', [App\Http\Controllers\PayrollController::class, 'markAsPaid'])->name('payrolls.mark-as-paid');

        // Leave Management Routes
        Route::resource('leaves', App\Http\Controllers\LeaveController::class)->parameters(['leaves' => 'leave']);
        Route::patch('leaves/{leave}/approve', [App\Http\Controllers\LeaveController::class, 'approve'])->name('leaves.approve');
        Route::patch('leaves/{leave}/reject', [App\Http\Controllers\LeaveController::class, 'reject'])->name('leaves.reject');
        Route::patch('leaves/{leave}/cancel', [App\Http\Controllers\LeaveController::class, 'cancel'])->name('leaves.cancel');
        
        // Leave Comments Routes
        Route::post('leaves/{leave}/comments', [App\Http\Controllers\LeaveCommentController::class, 'store'])->name('leave-comments.store');
        Route::get('leaves/{leave}/comments', [App\Http\Controllers\LeaveCommentController::class, 'getComments'])->name('leave-comments.get');
        Route::put('leave-comments/{comment}', [App\Http\Controllers\LeaveCommentController::class, 'update'])->name('leave-comments.update');
        Route::delete('leave-comments/{comment}', [App\Http\Controllers\LeaveCommentController::class, 'destroy'])->name('leave-comments.destroy');
        
        // Employee Dashboard Routes (for employees to view their own data)
        Route::prefix('employee-dashboard')->name('employee-dashboard.')->group(function () {
            Route::get('/', [App\Http\Controllers\EmployeeDashboardController::class, 'index'])->name('index');
            Route::get('/profile', [App\Http\Controllers\EmployeeDashboardController::class, 'profile'])->name('profile');
            Route::get('/payrolls', [App\Http\Controllers\EmployeeDashboardController::class, 'payrolls'])->name('payrolls');
            Route::get('/payrolls/{payroll}', [App\Http\Controllers\EmployeeDashboardController::class, 'payrollDetails'])->name('payroll-details');
            Route::get('/documents', [App\Http\Controllers\EmployeeDashboardController::class, 'documents'])->name('documents');
        });
        
        // API route for manual expiry check
        Route::post('api/legal-documents/check-expiry', function() {
            try {
                \Artisan::call('documents:check-expiry', ['--days' => 30]);
                $output = \Artisan::output();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Document expiry check completed successfully.',
                    'output' => $output
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error running expiry check: ' . $e->getMessage()
                ], 500);
            }
        })->name('api.legal-documents.check-expiry');
    
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

<?php

/**
 * Author: Eng.Fahed
 * SuperAdmin Dashboard Controller for HR System
 * Handles super admin dashboard functionality
 */

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display super admin dashboard
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get statistics for super admin dashboard
        $stats = [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('status', 'active')->count(),
            'total_users' => User::count(),
            'total_employees' => User::where('user_type', 'employee')->count(),
            'subscription_revenue' => Tenant::sum('monthly_fee'),
            'expired_subscriptions' => Tenant::where('subscription_end_date', '<', now())->count(),
        ];

        // Get recent tenants
        $recentTenants = Tenant::with('users')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get tenants by subscription plan
        $tenantsByPlan = Tenant::selectRaw('subscription_plan, count(*) as count')
            ->groupBy('subscription_plan')
            ->get();

        return view('super-admin.dashboard', compact('stats', 'recentTenants', 'tenantsByPlan'));
    }
}

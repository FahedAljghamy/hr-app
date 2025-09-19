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
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display super admin dashboard
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Main Statistics
        $stats = [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('status', 'active')->count(),
            'suspended_tenants' => Tenant::where('status', 'suspended')->count(),
            'total_users' => User::count(),
            'total_employees' => User::where('user_type', 'employee')->count(),
            'total_admins' => User::where('user_type', 'tenant_admin')->count(),
            'subscription_revenue' => Tenant::sum('monthly_fee'),
            'expired_subscriptions' => Tenant::where('subscription_end_date', '<', now())->count(),
            'expiring_soon' => Tenant::where('subscription_end_date', '<=', now()->addDays(7))
                ->where('subscription_end_date', '>', now())
                ->count(),
        ];

        // Recent Activity
        $recentTenants = Tenant::withCount('users')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentUsers = User::with('tenant')
            ->where('user_type', '!=', 'super_admin')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Subscription Statistics
        $tenantsByPlan = [
            'basic' => Tenant::where('subscription_plan', 'basic')->count(),
            'premium' => Tenant::where('subscription_plan', 'premium')->count(),
            'enterprise' => Tenant::where('subscription_plan', 'enterprise')->count(),
        ];

        // Monthly Revenue Chart Data (Last 12 months)
        $monthlyRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenue = Tenant::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('monthly_fee');
            
            $monthlyRevenue[] = [
                'month' => $month->format('M Y'),
                'revenue' => $revenue
            ];
        }

        // Expiring Subscriptions
        $expiringSubscriptions = Tenant::where('subscription_end_date', '<=', now()->addDays(30))
            ->where('subscription_end_date', '>', now())
            ->orderBy('subscription_end_date', 'asc')
            ->take(10)
            ->get();

        // Top Tenants by User Count
        $topTenants = Tenant::withCount('users')
            ->orderBy('users_count', 'desc')
            ->take(10)
            ->get();

        return view('super-admin.dashboard', compact(
            'stats', 
            'recentTenants', 
            'recentUsers',
            'tenantsByPlan', 
            'monthlyRevenue',
            'expiringSubscriptions',
            'topTenants'
        ));
    }

    /**
     * Get dashboard statistics as JSON
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats()
    {
        $stats = [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('status', 'active')->count(),
            'total_users' => User::count(),
            'monthly_revenue' => Tenant::sum('monthly_fee'),
        ];

        return response()->json($stats);
    }
}

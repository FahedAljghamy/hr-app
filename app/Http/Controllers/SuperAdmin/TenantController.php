<?php

/**
 * Author: Eng.Fahed
 * SuperAdmin Tenant Controller for HR System
 * Handles tenant management functionality
 */

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    /**
     * Display a listing of tenants
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $tenants = Tenant::with('users')->paginate(10);
        return view('super-admin.tenants.index', compact('tenants'));
    }

    /**
     * Show the form for creating a new tenant
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('super-admin.tenants.create');
    }

    /**
     * Store a newly created tenant
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:tenants',
            'subdomain' => 'nullable|string|max:255|unique:tenants',
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string',
            'address' => 'nullable|string',
            'subscription_plan' => 'required|in:basic,premium,enterprise',
            'subscription_start_date' => 'required|date',
            'subscription_end_date' => 'required|date|after:subscription_start_date',
            'monthly_fee' => 'required|numeric|min:0',
            'max_employees' => 'required|integer|min:1',
            'admin_name' => 'required|string|max:255',
            'admin_email' => 'required|email|unique:users',
            'admin_password' => 'required|string|min:8',
        ]);

        // Create tenant
        $tenant = Tenant::create([
            'name' => $request->name,
            'company_name' => $request->company_name,
            'domain' => $request->domain,
            'subdomain' => $request->subdomain,
            'database_name' => 'tenant_' . Str::random(10),
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
            'address' => $request->address,
            'status' => 'active',
            'subscription_plan' => $request->subscription_plan,
            'subscription_start_date' => $request->subscription_start_date,
            'subscription_end_date' => $request->subscription_end_date,
            'monthly_fee' => $request->monthly_fee,
            'max_employees' => $request->max_employees,
            'features' => $this->getFeaturesByPlan($request->subscription_plan),
        ]);

        // Create tenant admin user
        User::create([
            'name' => $request->admin_name,
            'email' => $request->admin_email,
            'password' => Hash::make($request->admin_password),
            'tenant_id' => $tenant->id,
            'user_type' => 'tenant_admin',
        ]);

        return redirect()->route('super-admin.tenants.index')
            ->with('success', 'Tenant created successfully!');
    }

    /**
     * Display the specified tenant
     *
     * @param  \App\Models\Tenant  $tenant
     * @return \Illuminate\View\View
     */
    public function show(Tenant $tenant)
    {
        $tenant->load('users');
        return view('super-admin.tenants.show', compact('tenant'));
    }

    /**
     * Show the form for editing the specified tenant
     *
     * @param  \App\Models\Tenant  $tenant
     * @return \Illuminate\View\View
     */
    public function edit(Tenant $tenant)
    {
        return view('super-admin.tenants.edit', compact('tenant'));
    }

    /**
     * Update the specified tenant
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tenant  $tenant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'domain' => 'required|string|max:255|unique:tenants,domain,' . $tenant->id,
            'subdomain' => 'nullable|string|max:255|unique:tenants,subdomain,' . $tenant->id,
            'contact_email' => 'required|email',
            'contact_phone' => 'nullable|string',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive,suspended',
            'subscription_plan' => 'required|in:basic,premium,enterprise',
            'subscription_start_date' => 'required|date',
            'subscription_end_date' => 'required|date|after:subscription_start_date',
            'monthly_fee' => 'required|numeric|min:0',
            'max_employees' => 'required|integer|min:1',
        ]);

        $tenant->update($request->all());

        return redirect()->route('super-admin.tenants.index')
            ->with('success', 'Tenant updated successfully!');
    }

    /**
     * Remove the specified tenant
     *
     * @param  \App\Models\Tenant  $tenant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return redirect()->route('super-admin.tenants.index')
            ->with('success', 'Tenant deleted successfully!');
    }

    /**
     * Activate tenant
     *
     * @param  \App\Models\Tenant  $tenant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activate(Tenant $tenant)
    {
        $tenant->update(['status' => 'active']);

        return redirect()->back()
            ->with('success', __('Tenant activated successfully.'));
    }

    /**
     * Suspend tenant
     *
     * @param  \App\Models\Tenant  $tenant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function suspend(Tenant $tenant)
    {
        $tenant->update(['status' => 'suspended']);

        return redirect()->back()
            ->with('success', __('Tenant suspended successfully.'));
    }

    /**
     * Extend subscription
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tenant  $tenant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function extendSubscription(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'extension_days' => 'required|integer|min:1|max:3650'
        ]);

        $currentEndDate = $tenant->subscription_end_date;
        $newEndDate = $currentEndDate->addDays($validated['extension_days']);
        
        $tenant->update(['subscription_end_date' => $newEndDate]);

        return redirect()->back()
            ->with('success', __('Subscription extended successfully.'));
    }

    /**
     * Get tenant statistics
     *
     * @param  \App\Models\Tenant  $tenant
     * @return \Illuminate\Http\JsonResponse
     */
    public function statistics(Tenant $tenant)
    {
        $stats = [
            'total_users' => $tenant->users()->count(),
            'active_users' => $tenant->users()->where('updated_at', '>=', now()->subDays(30))->count(),
            'admin_users' => $tenant->users()->where('user_type', 'tenant_admin')->count(),
            'employee_users' => $tenant->users()->where('user_type', 'employee')->count(),
            'days_remaining' => max(0, $tenant->subscription_end_date->diffInDays(now())),
            'subscription_status' => $tenant->isSubscriptionValid() ? 'valid' : 'expired',
        ];

        return response()->json($stats);
    }

    /**
     * Get features by subscription plan
     *
     * @param  string  $plan
     * @return array
     */
    private function getFeaturesByPlan($plan)
    {
        $features = [
            'basic' => ['employee_management', 'basic_reports'],
            'premium' => ['employee_management', 'advanced_reports', 'attendance_tracking', 'leave_management'],
            'enterprise' => ['employee_management', 'advanced_reports', 'attendance_tracking', 'leave_management', 'payroll_management', 'custom_reports', 'api_access'],
        ];

        return $features[$plan] ?? $features['basic'];
    }
}

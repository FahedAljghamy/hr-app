<?php

/**
 * Author: Eng.Fahed
 * Tenant Dashboard Controller for HR System
 * لوحة تحكم خاصة بمدير المؤسسة مع إحصائيات شاملة
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class TenantDashboardController extends Controller
{
    /**
     * إنشاء instance جديد من الكنترولر
     */
    public function __construct()
    {
        $this->middleware(['auth', 'tenant']);
    }

    /**
     * عرض لوحة التحكم الرئيسية للـ tenant admin
     */
    public function index(): View
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;

        // إحصائيات المستخدمين (بدون السوبر أدمن)
        $totalUsers = User::where('tenant_id', $tenantId)
                         ->where('user_type', '!=', 'super_admin')
                         ->count();
        $activeUsers = User::where('tenant_id', $tenantId)
                          ->where('user_type', '!=', 'super_admin')
                          ->where('created_at', '>=', now()->subDays(30))
                          ->count();
        
        // إحصائيات الأدوار
        $totalRoles = Role::count();
        $rolesWithUsers = Role::whereHas('users', function($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId)->where('user_type', '!=', 'super_admin');
        })->count();

        // إحصائيات الصلاحيات
        $totalPermissions = Permission::count();
        
        // المستخدمون حسب الأدوار (بدون السوبر أدمن)
        $usersByRole = User::where('tenant_id', $tenantId)
                          ->where('user_type', '!=', 'super_admin')
                          ->with('roles')
                          ->get()
                          ->groupBy(function($user) {
                              return $user->roles->first()->name ?? 'بدون دور';
                          })
                          ->map(function($users) {
                              return $users->count();
                          });

        // المستخدمون الجدد (آخر 7 أيام، بدون السوبر أدمن)
        $newUsers = User::where('tenant_id', $tenantId)
                       ->where('user_type', '!=', 'super_admin')
                       ->where('created_at', '>=', now()->subDays(7))
                       ->with('roles')
                       ->orderBy('created_at', 'desc')
                       ->limit(5)
                       ->get();

        // الأدوار الأكثر استخداماً (بدون السوبر أدمن)
        $popularRoles = Role::withCount(['users' => function($query) use ($tenantId) {
                             $query->where('tenant_id', $tenantId)->where('user_type', '!=', 'super_admin');
                         }])
                         ->having('users_count', '>', 0)
                         ->orderBy('users_count', 'desc')
                         ->limit(5)
                         ->get();

        // نشاط المستخدمين (آخر 30 يوم، بدون السوبر أدمن)
        $userActivity = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = User::where('tenant_id', $tenantId)
                        ->where('user_type', '!=', 'super_admin')
                        ->whereDate('created_at', $date)
                        ->count();
            $userActivity[] = [
                'date' => $date->format('Y-m-d'),
                'count' => $count
            ];
        }

        // إحصائيات الصلاحيات المستخدمة
        $permissionStats = Permission::withCount(['roles' => function($query) use ($tenantId) {
                                    $query->whereHas('users', function($q) use ($tenantId) {
                                        $q->where('tenant_id', $tenantId);
                                    });
                                }])
                                ->having('roles_count', '>', 0)
                                ->orderBy('roles_count', 'desc')
                                ->limit(10)
                                ->get();

        return view('tenant-dashboard.index', compact(
            'totalUsers',
            'activeUsers', 
            'totalRoles',
            'rolesWithUsers',
            'totalPermissions',
            'usersByRole',
            'newUsers',
            'popularRoles',
            'userActivity',
            'permissionStats'
        ));
    }

    /**
     * API endpoint لجلب إحصائيات المستخدمين
     */
    public function getUsersStats()
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;

        $stats = [
            'total' => User::where('tenant_id', $tenantId)->where('user_type', '!=', 'super_admin')->count(),
            'active' => User::where('tenant_id', $tenantId)
                           ->where('user_type', '!=', 'super_admin')
                           ->where('created_at', '>=', now()->subDays(30))
                           ->count(),
            'by_type' => User::where('tenant_id', $tenantId)
                            ->where('user_type', '!=', 'super_admin')
                            ->groupBy('user_type')
                            ->selectRaw('user_type, count(*) as count')
                            ->pluck('count', 'user_type')
                            ->toArray(),
            'recent' => User::where('tenant_id', $tenantId)
                           ->where('user_type', '!=', 'super_admin')
                           ->orderBy('created_at', 'desc')
                           ->limit(5)
                           ->get(['id', 'name', 'email', 'created_at'])
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * API endpoint لجلب إحصائيات الأدوار
     */
    public function getRolesStats()
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;

        $roles = Role::withCount(['users' => function($query) use ($tenantId) {
                      $query->where('tenant_id', $tenantId);
                  }])
                  ->with(['permissions' => function($query) {
                      $query->select('id', 'name');
                  }])
                  ->get();

        return response()->json([
            'success' => true,
            'data' => $roles
        ]);
    }

    /**
     * API endpoint لجلب نشاط المستخدمين
     */
    public function getUserActivity(Request $request)
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;
        $days = $request->get('days', 30);

        $activity = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = User::where('tenant_id', $tenantId)
                        ->whereDate('created_at', $date)
                        ->count();
            $activity[] = [
                'date' => $date->format('Y-m-d'),
                'count' => $count,
                'label' => $date->format('d/m')
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $activity
        ]);
    }

    /**
     * عرض تقرير مفصل للمستخدمين والأدوار
     */
    public function detailedReport(): View
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;

        // المستخدمون مع أدوارهم وصلاحياتهم
        $users = User::where('tenant_id', $tenantId)
                    ->with(['roles.permissions'])
                    ->orderBy('created_at', 'desc')
                    ->get();

        // الأدوار مع عدد المستخدمين والصلاحيات
        $roles = Role::withCount(['users' => function($query) use ($tenantId) {
                      $query->where('tenant_id', $tenantId);
                  }])
                  ->with('permissions')
                  ->get();

        // الصلاحيات مع الأدوار المرتبطة
        $permissions = Permission::with(['roles' => function($query) use ($tenantId) {
                                   $query->whereHas('users', function($q) use ($tenantId) {
                                       $q->where('tenant_id', $tenantId);
                                   });
                               }])
                               ->get()
                               ->filter(function($permission) {
                                   return $permission->roles->count() > 0;
                               });

        return view('tenant-dashboard.detailed-report', compact('users', 'roles', 'permissions'));
    }

    /**
     * تصدير تقرير المستخدمين والأدوار
     */
    public function exportReport(Request $request)
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;
        $format = $request->get('format', 'json');

        $data = [
            'tenant' => $user->tenant->name ?? 'غير محدد',
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'users' => User::where('tenant_id', $tenantId)
                          ->with(['roles.permissions'])
                          ->get()
                          ->map(function($user) {
                              return [
                                  'id' => $user->id,
                                  'name' => $user->name,
                                  'email' => $user->email,
                                  'user_type' => $user->user_type,
                                  'roles' => $user->roles->pluck('name'),
                                  'permissions_count' => $user->getAllPermissions()->count(),
                                  'created_at' => $user->created_at->format('Y-m-d H:i:s')
                              ];
                          }),
            'roles' => Role::withCount(['users' => function($query) use ($tenantId) {
                            $query->where('tenant_id', $tenantId);
                        }])
                        ->with('permissions')
                        ->get()
                        ->map(function($role) {
                            return [
                                'name' => $role->name,
                                'users_count' => $role->users_count,
                                'permissions' => $role->permissions->pluck('name'),
                                'permissions_count' => $role->permissions->count()
                            ];
                        })
        ];

        if ($format === 'json') {
            return response()->json($data);
        }

        // يمكن إضافة تصدير Excel أو PDF هنا
        return response()->json([
            'success' => false,
            'message' => 'تنسيق التصدير غير مدعوم حالياً'
        ], 400);
    }
}
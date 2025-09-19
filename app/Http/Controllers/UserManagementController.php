<?php

/**
 * Author: Eng.Fahed
 * User Management Controller for HR System - Dynamic User Roles & Permissions Management
 */

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserManagementController extends Controller
{
    /**
     * إنشاء instance جديد من الكنترولر
     */
    public function __construct()
    {
        // التحقق من الصلاحيات لكل عملية
        $this->middleware('permission:users.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:users.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:users.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:users.delete', ['only' => ['destroy']]);
        $this->middleware('permission:roles.assign', ['only' => ['assignRole', 'revokeRole']]);
        $this->middleware('permission:permissions.assign', ['only' => ['assignPermission', 'revokePermission']]);
    }

    /**
     * عرض قائمة المستخدمين
     */
    public function index(Request $request): View
    {
        $currentUser = auth()->user();
        $query = User::with(['roles', 'permissions', 'tenant']);

        // إخفاء السوبر أدمن من قائمة المستخدمين للـ tenant admin
        $query->where('user_type', '!=', 'super_admin');

        // إذا كان المستخدم الحالي tenant admin، إظهار مستخدمي نفس المؤسسة فقط
        if ($currentUser->user_type === 'tenant_admin' && $currentUser->tenant_id) {
            $query->where('tenant_id', $currentUser->tenant_id);
        }

        // تطبيق الفلاتر
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        if ($request->filled('user_type')) {
            $query->where('user_type', $request->user_type);
        }

        $users = $query->paginate(15)->withQueryString();
        
        // جلب البيانات الإضافية للفلاتر (بدون super_admin)
        $roles = Role::all();
        $userTypes = ['tenant_admin', 'employee']; // إزالة super_admin من الخيارات
        
        return view('user-management.index', compact('users', 'roles', 'userTypes'));
    }

    /**
     * إظهار صفحة إنشاء مستخدم جديد
     */
    public function create(): View
    {
        $currentUser = auth()->user();
        
        $roles = Role::all();
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });
        
        // إذا كان المستخدم tenant admin، إظهار مؤسسته فقط
        if ($currentUser->user_type === 'tenant_admin' && $currentUser->tenant_id) {
            $tenants = Tenant::where('id', $currentUser->tenant_id)->get();
        } else {
            $tenants = Tenant::all();
        }
        
        return view('user-management.create', compact('roles', 'permissions', 'tenants'));
    }

    /**
     * حفظ مستخدم جديد
     */
    public function store(Request $request): RedirectResponse
    {
        // التحقق من صحة البيانات
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'required|in:tenant_admin,employee',
            'tenant_id' => 'nullable|exists:tenants,id',
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ], [
            'name.required' => 'الاسم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.unique' => 'هذا البريد الإلكتروني مستخدم مسبقاً',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
            'user_type.required' => 'نوع المستخدم مطلوب',
            'user_type.in' => 'نوع المستخدم غير صالح',
            'tenant_id.exists' => 'المؤسسة المحددة غير موجودة',
            'roles.*.exists' => 'إحدى الأدوار المحددة غير موجودة',
            'permissions.*.exists' => 'إحدى الصلاحيات المحددة غير موجودة'
        ]);

        try {
            DB::beginTransaction();

            // إنشاء المستخدم
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'user_type' => $validated['user_type'],
                'tenant_id' => $validated['tenant_id'] ?? null,
            ]);

            // إسناد الأدوار
            if (isset($validated['roles'])) {
                $user->assignRole($validated['roles']);
            }

            // إسناد الصلاحيات المباشرة
            if (isset($validated['permissions'])) {
                $user->givePermissionTo($validated['permissions']);
            }

            DB::commit();

            return redirect()->route('user-management.index')
                ->with('success', 'تم إنشاء المستخدم بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء المستخدم: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل مستخدم محدد
     */
    public function show(User $user): View
    {
        $user->load(['roles.permissions', 'permissions', 'tenant']);
        
        // جلب كل الصلاحيات (من الأدوار + المباشرة)
        $allPermissions = $user->getAllPermissions();
        
        return view('user-management.show', compact('user', 'allPermissions'));
    }

    /**
     * إظهار صفحة تعديل المستخدم
     */
    public function edit(User $user): View
    {
        $user->load(['roles', 'permissions']);
        
        $roles = Role::all();
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });
        
        // فلترة المؤسسات للـ tenant admin
        $currentUser = auth()->user();
        if ($currentUser->user_type === 'tenant_admin' && $currentUser->tenant_id) {
            $tenants = Tenant::where('id', $currentUser->tenant_id)->get();
        } else {
            $tenants = Tenant::all();
        }
        
        $userRoles = $user->roles->pluck('name')->toArray();
        $userPermissions = $user->permissions->pluck('name')->toArray();
        
        // جلب كل الصلاحيات (من الأدوار + المباشرة)
        $allPermissions = $user->getAllPermissions();
        
        return view('user-management.edit', compact(
            'user', 'roles', 'permissions', 'tenants', 'userRoles', 'userPermissions', 'allPermissions'
        ));
    }

    /**
     * تحديث المستخدم
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        // التحقق من صحة البيانات
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'user_type' => 'required|in:tenant_admin,employee',
            'tenant_id' => 'nullable|exists:tenants,id',
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        try {
            DB::beginTransaction();

            // تحديث بيانات المستخدم الأساسية
            $updateData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
                'user_type' => $validated['user_type'],
                'tenant_id' => $validated['tenant_id'] ?? null,
            ];

            // تحديث كلمة المرور إذا تم إدخالها
            if (!empty($validated['password'])) {
                $updateData['password'] = Hash::make($validated['password']);
            }

            $user->update($updateData);

            // تحديث الأدوار
            if (isset($validated['roles'])) {
                $user->syncRoles($validated['roles']);
            } else {
                $user->syncRoles([]);
            }

            // تحديث الصلاحيات المباشرة
            if (isset($validated['permissions'])) {
                $user->syncPermissions($validated['permissions']);
            } else {
                $user->syncPermissions([]);
            }

            DB::commit();

            return redirect()->route('user-management.index')
                ->with('success', 'تم تحديث المستخدم بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث المستخدم: ' . $e->getMessage());
        }
    }

    /**
     * حذف المستخدم
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            // التحقق من عدم حذف المستخدم الحالي
            if ($user->id === auth()->id()) {
                return redirect()->back()
                    ->with('error', 'لا يمكنك حذف حسابك الشخصي');
            }

            $user->delete();

            return redirect()->route('user-management.index')
                ->with('success', 'تم حذف المستخدم بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف المستخدم: ' . $e->getMessage());
        }
    }
}

<?php

/**
 * Author: Eng.Fahed
 * Role Controller for HR System - Dynamic Roles & Permissions Management
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * إنشاء instance جديد من الكنترولر
     */
    public function __construct()
    {
        // التحقق من الصلاحيات لكل عملية
        $this->middleware('permission:roles.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:roles.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:roles.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:roles.delete', ['only' => ['destroy']]);
    }

    /**
     * عرض قائمة الأدوار
     */
    public function index(): View
    {
        $roles = Role::with('permissions')->paginate(10);
        
        return view('roles.index', compact('roles'));
    }

    /**
     * إظهار صفحة إنشاء دور جديد
     */
    public function create(): View
    {
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });
        
        return view('roles.create', compact('permissions'));
    }

    /**
     * حفظ دور جديد
     */
    public function store(Request $request): RedirectResponse
    {
        // التحقق من صحة البيانات
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ], [
            'name.required' => 'اسم الدور مطلوب',
            'name.unique' => 'هذا الدور موجود مسبقاً',
            'name.max' => 'اسم الدور يجب أن يكون أقل من 255 حرف',
            'permissions.array' => 'الصلاحيات يجب أن تكون مصفوفة',
            'permissions.*.exists' => 'إحدى الصلاحيات المحددة غير موجودة'
        ]);

        try {
            DB::beginTransaction();

            // إنشاء الدور
            $role = Role::create(['name' => $validated['name']]);

            // إسناد الصلاحيات للدور إذا تم تحديدها
            if (isset($validated['permissions'])) {
                $role->syncPermissions($validated['permissions']);
            }

            DB::commit();

            return redirect()->route('roles.index')
                ->with('success', 'تم إنشاء الدور بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الدور: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل دور محدد
     */
    public function show(Role $role): View
    {
        $role->load('permissions');
        
        return view('roles.show', compact('role'));
    }

    /**
     * إظهار صفحة تعديل الدور
     */
    public function edit(Role $role): View
    {
        $role->load('permissions');
        
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });
        
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * تحديث الدور
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        // التحقق من صحة البيانات
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ], [
            'name.required' => 'اسم الدور مطلوب',
            'name.unique' => 'هذا الدور موجود مسبقاً',
            'name.max' => 'اسم الدور يجب أن يكون أقل من 255 حرف',
            'permissions.array' => 'الصلاحيات يجب أن تكون مصفوفة',
            'permissions.*.exists' => 'إحدى الصلاحيات المحددة غير موجودة'
        ]);

        try {
            DB::beginTransaction();

            // تحديث اسم الدور
            $role->update(['name' => $validated['name']]);

            // تحديث صلاحيات الدور
            if (isset($validated['permissions'])) {
                $role->syncPermissions($validated['permissions']);
            } else {
                // إزالة كل الصلاحيات إذا لم يتم تحديد أي صلاحية
                $role->syncPermissions([]);
            }

            DB::commit();

            return redirect()->route('roles.index')
                ->with('success', 'تم تحديث الدور بنجاح');

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الدور: ' . $e->getMessage());
        }
    }

    /**
     * حذف الدور
     */
    public function destroy(Role $role): RedirectResponse
    {
        try {
            // التحقق من عدم وجود مستخدمين مرتبطين بهذا الدور
            $usersCount = $role->users()->count();
            
            if ($usersCount > 0) {
                return redirect()->back()
                    ->with('error', 'لا يمكن حذف هذا الدور لوجود ' . $usersCount . ' مستخدم مرتبط به');
            }

            $role->delete();

            return redirect()->route('roles.index')
                ->with('success', 'تم حذف الدور بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف الدور: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint لجلب الأدوار مع الصلاحيات
     */
    public function apiIndex()
    {
        $roles = Role::with('permissions')->get();
        
        return response()->json([
            'success' => true,
            'data' => $roles
        ]);
    }

    /**
     * API endpoint لإسناد صلاحيات لدور محدد
     */
    public function assignPermissions(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        try {
            $role->syncPermissions($validated['permissions']);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث صلاحيات الدور بنجاح',
                'role' => $role->load('permissions')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الصلاحيات: ' . $e->getMessage()
            ], 500);
        }
    }
}
<?php

/**
 * Author: Eng.Fahed
 * Permission Controller for HR System - Dynamic Permissions Management
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    /**
     * إنشاء instance جديد من الكنترولر
     */
    public function __construct()
    {
        // التحقق من الصلاحيات لكل عملية
        $this->middleware('permission:permissions.view', ['only' => ['index', 'show']]);
        $this->middleware('permission:permissions.create', ['only' => ['create', 'store']]);
        $this->middleware('permission:permissions.edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:permissions.delete', ['only' => ['destroy']]);
    }

    /**
     * عرض قائمة الصلاحيات
     */
    public function index(): View
    {
        $permissions = Permission::with('roles')->paginate(15);
        
        // تجميع الصلاحيات حسب النوع
        $groupedPermissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });
        
        return view('permissions.index', compact('permissions', 'groupedPermissions'));
    }

    /**
     * إظهار صفحة إنشاء صلاحية جديدة
     */
    public function create(): View
    {
        // الأنواع المتاحة للصلاحيات
        $permissionTypes = [
            'users' => 'إدارة المستخدمين',
            'roles' => 'إدارة الأدوار',
            'permissions' => 'إدارة الصلاحيات',
            'employees' => 'إدارة الموظفين',
            'attendance' => 'إدارة الحضور والانصراف',
            'leaves' => 'إدارة الإجازات',
            'payroll' => 'إدارة الرواتب',
            'reports' => 'التقارير',
            'dashboard' => 'لوحة التحكم'
        ];

        $actions = ['view', 'create', 'edit', 'delete', 'assign', 'approve', 'export'];
        
        return view('permissions.create', compact('permissionTypes', 'actions'));
    }

    /**
     * حفظ صلاحية جديدة
     */
    public function store(Request $request): RedirectResponse
    {
        // التحقق من صحة البيانات
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'display_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500'
        ], [
            'name.required' => 'اسم الصلاحية مطلوب',
            'name.unique' => 'هذه الصلاحية موجودة مسبقاً',
            'name.max' => 'اسم الصلاحية يجب أن يكون أقل من 255 حرف',
            'display_name.max' => 'الاسم المعروض يجب أن يكون أقل من 255 حرف',
            'description.max' => 'الوصف يجب أن يكون أقل من 500 حرف'
        ]);

        try {
            // إنشاء الصلاحية
            Permission::create([
                'name' => $validated['name'],
                'guard_name' => 'web'
            ]);

            return redirect()->route('permissions.index')
                ->with('success', 'تم إنشاء الصلاحية بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الصلاحية: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل صلاحية محددة
     */
    public function show(Permission $permission): View
    {
        $permission->load('roles');
        
        return view('permissions.show', compact('permission'));
    }

    /**
     * إظهار صفحة تعديل الصلاحية
     */
    public function edit(Permission $permission): View
    {
        $permissionTypes = [
            'users' => 'إدارة المستخدمين',
            'roles' => 'إدارة الأدوار',
            'permissions' => 'إدارة الصلاحيات',
            'employees' => 'إدارة الموظفين',
            'attendance' => 'إدارة الحضور والانصراف',
            'leaves' => 'إدارة الإجازات',
            'payroll' => 'إدارة الرواتب',
            'reports' => 'التقارير',
            'dashboard' => 'لوحة التحكم'
        ];

        $actions = ['view', 'create', 'edit', 'delete', 'assign', 'approve', 'export'];
        
        return view('permissions.edit', compact('permission', 'permissionTypes', 'actions'));
    }

    /**
     * تحديث الصلاحية
     */
    public function update(Request $request, Permission $permission): RedirectResponse
    {
        // التحقق من صحة البيانات
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'display_name' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500'
        ], [
            'name.required' => 'اسم الصلاحية مطلوب',
            'name.unique' => 'هذه الصلاحية موجودة مسبقاً',
            'name.max' => 'اسم الصلاحية يجب أن يكون أقل من 255 حرف',
            'display_name.max' => 'الاسم المعروض يجب أن يكون أقل من 255 حرف',
            'description.max' => 'الوصف يجب أن يكون أقل من 500 حرف'
        ]);

        try {
            // تحديث الصلاحية
            $permission->update([
                'name' => $validated['name']
            ]);

            return redirect()->route('permissions.index')
                ->with('success', 'تم تحديث الصلاحية بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الصلاحية: ' . $e->getMessage());
        }
    }

    /**
     * حذف الصلاحية
     */
    public function destroy(Permission $permission): RedirectResponse
    {
        try {
            // التحقق من عدم وجود أدوار مرتبطة بهذه الصلاحية
            $rolesCount = $permission->roles()->count();
            
            if ($rolesCount > 0) {
                return redirect()->back()
                    ->with('error', 'لا يمكن حذف هذه الصلاحية لوجود ' . $rolesCount . ' دور مرتبط بها');
            }

            $permission->delete();

            return redirect()->route('permissions.index')
                ->with('success', 'تم حذف الصلاحية بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف الصلاحية: ' . $e->getMessage());
        }
    }

    /**
     * API endpoint لجلب الصلاحيات مجمعة حسب النوع
     */
    public function apiIndex()
    {
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('.', $permission->name)[0];
        });
        
        return response()->json([
            'success' => true,
            'data' => $permissions
        ]);
    }

    /**
     * إنشاء صلاحيات متعددة بناءً على النوع والأعمال
     */
    public function bulkCreate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|max:50',
            'actions' => 'required|array',
            'actions.*' => 'string|in:view,create,edit,delete,assign,approve,export'
        ], [
            'type.required' => 'نوع الصلاحية مطلوب',
            'actions.required' => 'يجب اختيار عمل واحد على الأقل',
            'actions.*.in' => 'العمل المحدد غير صالح'
        ]);

        try {
            DB::beginTransaction();

            $createdCount = 0;
            foreach ($validated['actions'] as $action) {
                $permissionName = $validated['type'] . '.' . $action;
                
                // التحقق من عدم وجود الصلاحية مسبقاً
                if (!Permission::where('name', $permissionName)->exists()) {
                    Permission::create([
                        'name' => $permissionName,
                        'guard_name' => 'web'
                    ]);
                    $createdCount++;
                }
            }

            DB::commit();

            if ($createdCount > 0) {
                return redirect()->route('permissions.index')
                    ->with('success', 'تم إنشاء ' . $createdCount . ' صلاحية جديدة بنجاح');
            } else {
                return redirect()->route('permissions.index')
                    ->with('info', 'جميع الصلاحيات المحددة موجودة مسبقاً');
            }

        } catch (\Exception $e) {
            DB::rollback();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء الصلاحيات: ' . $e->getMessage());
        }
    }
}
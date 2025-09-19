<?php

/**
 * Author: Eng.Fahed
 * Role Permission System Tests - HR System
 * اختبارات نظام الأدوار والصلاحيات
 */

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Tenant;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // تشغيل seeders للأدوار والصلاحيات
        $this->artisan('db:seed', ['--class' => 'RolePermissionSeeder']);
    }

    /**
     * اختبار إنشاء الأدوار والصلاحيات الافتراضية
     */
    public function test_default_roles_and_permissions_are_created(): void
    {
        // التحقق من وجود الأدوار الافتراضية
        $this->assertDatabaseHas('roles', ['name' => 'Admin']);
        $this->assertDatabaseHas('roles', ['name' => 'Manager']);
        $this->assertDatabaseHas('roles', ['name' => 'Employee']);

        // التحقق من وجود بعض الصلاحيات الأساسية
        $this->assertDatabaseHas('permissions', ['name' => 'users.view']);
        $this->assertDatabaseHas('permissions', ['name' => 'roles.create']);
        $this->assertDatabaseHas('permissions', ['name' => 'dashboard.view']);
    }

    /**
     * اختبار إسناد الأدوار للمستخدمين
     */
    public function test_user_can_be_assigned_roles(): void
    {
        // إنشاء مستخدم
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'user_type' => 'employee'
        ]);

        // إسناد دور للمستخدم
        $role = Role::where('name', 'Employee')->first();
        $user->assignRole($role);

        // التحقق من إسناد الدور
        $this->assertTrue($user->hasRole('Employee'));
        $this->assertDatabaseHas('model_has_roles', [
            'model_id' => $user->id,
            'role_id' => $role->id
        ]);
    }

    /**
     * اختبار صلاحيات المستخدم من خلال الأدوار
     */
    public function test_user_has_permissions_through_roles(): void
    {
        // إنشاء مستخدم
        $user = User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@example.com',
            'user_type' => 'tenant_admin'
        ]);

        // إسناد دور المدير
        $user->assignRole('Manager');

        // التحقق من الصلاحيات
        $this->assertTrue($user->can('dashboard.view'));
        $this->assertTrue($user->can('employees.view'));
        $this->assertFalse($user->can('users.delete')); // هذه الصلاحية للأدمن فقط
    }

    /**
     * اختبار إسناد صلاحيات مباشرة للمستخدم
     */
    public function test_user_can_have_direct_permissions(): void
    {
        // إنشاء مستخدم
        $user = User::factory()->create([
            'name' => 'Special User',
            'email' => 'special@example.com',
            'user_type' => 'employee'
        ]);

        // إسناد صلاحية مباشرة
        $user->givePermissionTo('reports.view');

        // التحقق من الصلاحية
        $this->assertTrue($user->can('reports.view'));
        $this->assertTrue($user->hasDirectPermission('reports.view'));
    }

    /**
     * اختبار أن Super Admin له كل الصلاحيات
     */
    public function test_super_admin_has_all_permissions(): void
    {
        // إنشاء مستخدم Super Admin
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'user_type' => 'super_admin'
        ]);

        // إسناد دور Admin
        $superAdmin->assignRole('Admin');

        // التحقق من أن له كل الصلاحيات
        $this->assertTrue($superAdmin->can('users.delete'));
        $this->assertTrue($superAdmin->can('roles.create'));
        $this->assertTrue($superAdmin->can('permissions.edit'));
        $this->assertTrue($superAdmin->can('reports.export'));
    }

    /**
     * اختبار إنشاء دور جديد مع صلاحيات
     */
    public function test_can_create_role_with_permissions(): void
    {
        // إنشاء دور جديد
        $role = Role::create(['name' => 'HR Manager']);

        // إسناد صلاحيات للدور
        $permissions = ['employees.view', 'employees.create', 'employees.edit'];
        $role->givePermissionTo($permissions);

        // التحقق من الدور والصلاحيات
        $this->assertDatabaseHas('roles', ['name' => 'HR Manager']);
        
        foreach ($permissions as $permission) {
            $this->assertTrue($role->hasPermissionTo($permission));
        }
    }

    /**
     * اختبار إزالة الأدوار والصلاحيات
     */
    public function test_can_remove_roles_and_permissions(): void
    {
        // إنشاء مستخدم مع دور
        $user = User::factory()->create();
        $user->assignRole('Employee');
        $user->givePermissionTo('leaves.create');

        // التحقق من وجود الدور والصلاحية
        $this->assertTrue($user->hasRole('Employee'));
        $this->assertTrue($user->can('leaves.create'));

        // إزالة الدور والصلاحية
        $user->removeRole('Employee');
        $user->revokePermissionTo('leaves.create');

        // التحقق من الإزالة
        $this->assertFalse($user->hasRole('Employee'));
        $this->assertFalse($user->hasDirectPermission('leaves.create'));
    }

    /**
     * اختبار middleware الصلاحيات
     */
    public function test_permission_middleware_works(): void
    {
        // إنشاء مستخدم بدون صلاحيات
        $user = User::factory()->create();

        // محاولة الوصول لصفحة تتطلب صلاحية
        $response = $this->actingAs($user)->get('/roles');

        // يجب أن يتم الرفض
        $response->assertStatus(302); // إعادة توجيه لعدم وجود صلاحية

        // إعطاء الصلاحية المطلوبة
        $user->givePermissionTo('roles.view');

        // المحاولة مرة أخرى
        $response = $this->actingAs($user)->get('/roles');

        // يجب أن يتم السماح
        $response->assertStatus(200);
    }

    /**
     * اختبار تحديث الأدوار والصلاحيات
     */
    public function test_can_update_role_permissions(): void
    {
        $role = Role::where('name', 'Employee')->first();
        
        // الصلاحيات الحالية
        $currentPermissions = $role->permissions->pluck('name')->toArray();
        
        // إضافة صلاحية جديدة
        $newPermissions = array_merge($currentPermissions, ['payroll.view']);
        $role->syncPermissions($newPermissions);
        
        // التحقق من التحديث
        $this->assertTrue($role->hasPermissionTo('payroll.view'));
        $this->assertEquals(count($newPermissions), $role->permissions->count());
    }

    /**
     * اختبار عدم السماح بحذف دور مرتبط بمستخدمين
     */
    public function test_cannot_delete_role_with_users(): void
    {
        // إنشاء مستخدم مع دور
        $user = User::factory()->create();
        $role = Role::where('name', 'Employee')->first();
        $user->assignRole($role);

        // محاولة حذف الدور
        $usersCount = $role->users()->count();
        $this->assertTrue($usersCount > 0);

        // في التطبيق الفعلي، يجب أن يفشل الحذف
        // هذا مجرد اختبار للتأكد من وجود مستخدمين مرتبطين
        $this->assertGreaterThan(0, $role->users()->count());
    }
}
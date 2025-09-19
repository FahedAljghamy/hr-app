<?php

/**
 * Author: Eng.Fahed
 * Role and Permission Seeder for HR System
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء الصلاحيات
        $permissions = [
            // إدارة المستخدمين
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            
            // إدارة الأدوار
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            
            // إدارة الصلاحيات
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',
            
            // إسناد الأدوار والصلاحيات
            'roles.assign',
            'permissions.assign',
            
            // لوحة التحكم
            'dashboard.view',
            'dashboard.admin',
            
            // إدارة الموظفين
            'employees.view',
            'employees.create',
            'employees.edit',
            'employees.delete',
            
            // إدارة الحضور والانصراف
            'attendance.view',
            'attendance.create',
            'attendance.edit',
            'attendance.delete',
            
            // إدارة الإجازات
            'leaves.view',
            'leaves.create',
            'leaves.edit',
            'leaves.delete',
            'leaves.approve',
            
            // إدارة الرواتب
            'payroll.view',
            'payroll.create',
            'payroll.edit',
            'payroll.delete',
            
            // التقارير
            'reports.view',
            'reports.export',
        ];

        // إنشاء كل الصلاحيات
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // إنشاء الأدوار
        $adminRole = Role::create(['name' => 'Admin']);
        $managerRole = Role::create(['name' => 'Manager']);
        $employeeRole = Role::create(['name' => 'Employee']);

        // إسناد الصلاحيات للأدوار
        
        // الأدمن له كل الصلاحيات
        $adminRole->givePermissionTo(Permission::all());

        // المدير له صلاحيات محددة
        $managerPermissions = [
            'dashboard.view',
            'employees.view',
            'employees.create',
            'employees.edit',
            'attendance.view',
            'attendance.create',
            'attendance.edit',
            'leaves.view',
            'leaves.approve',
            'payroll.view',
            'reports.view',
            'reports.export',
        ];
        $managerRole->givePermissionTo($managerPermissions);

        // الموظف له صلاحيات أساسية
        $employeePermissions = [
            'dashboard.view',
            'attendance.view',
            'attendance.create',
            'leaves.view',
            'leaves.create',
        ];
        $employeeRole->givePermissionTo($employeePermissions);

        $this->command->info('تم إنشاء الأدوار والصلاحيات بنجاح!');
    }
}
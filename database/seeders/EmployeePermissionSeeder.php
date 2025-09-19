<?php

/**
 * Author: Eng.Fahed
 * Employee Permission Seeder - HR System
 * إضافة صلاحيات إدارة الموظفين والرواتب
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EmployeePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('👥 Adding Employee & Payroll Permissions...');

        // الصلاحيات الجديدة للموظفين والرواتب
        $newPermissions = [
            // إدارة الموظفين (محدّثة)
            'employees.view',
            'employees.create', 
            'employees.edit',
            'employees.delete',
            'employees.export',
            
            // إدارة الرواتب (محدّثة)
            'payrolls.view',
            'payrolls.create',
            'payrolls.edit', 
            'payrolls.delete',
            'payrolls.approve',
            'payrolls.process',
            'payrolls.export',
            
            // dashboard الموظف
            'employee.dashboard.view',
            'employee.profile.view',
            'employee.payrolls.view',
            'employee.documents.view',
        ];

        // إنشاء الصلاحيات الجديدة فقط
        foreach ($newPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
            $this->command->info("  ✅ Permission created: {$permission}");
        }

        // إسناد الصلاحيات للأدوار الموجودة
        $adminRole = Role::where('name', 'Admin')->first();
        $managerRole = Role::where('name', 'Manager')->first();
        $employeeRole = Role::where('name', 'Employee')->first();

        if ($adminRole) {
            // الأدمن له كل الصلاحيات
            $adminRole->givePermissionTo($newPermissions);
            $this->command->info("✅ Admin role updated with all employee & payroll permissions");
        }

        if ($managerRole) {
            // المدير له صلاحيات إدارية للموظفين والرواتب
            $managerPermissions = [
                'employees.view',
                'employees.create',
                'employees.edit',
                'employees.export',
                'payrolls.view',
                'payrolls.create',
                'payrolls.edit',
                'payrolls.approve',
                'payrolls.export',
            ];
            $managerRole->givePermissionTo($managerPermissions);
            $this->command->info("✅ Manager role updated with employee & payroll management permissions");
        }

        if ($employeeRole) {
            // الموظف له صلاحيات عرض بياناته الشخصية فقط
            $employeePermissions = [
                'employee.dashboard.view',
                'employee.profile.view', 
                'employee.payrolls.view',
                'employee.documents.view',
            ];
            $employeeRole->givePermissionTo($employeePermissions);
            $this->command->info("✅ Employee role updated with personal dashboard permissions");
        }

        $this->command->info('🎉 Employee & Payroll permissions added successfully!');
    }
}
<?php

/**
 * Author: Eng.Fahed
 * Company Management Seeder for HR System
 * إضافة صلاحيات إدارة الشركة والفروع
 */

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CompanyManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // الصلاحيات الجديدة فقط
        $newPermissions = [
            // إدارة الفروع
            'branches.view',
            'branches.create',
            'branches.edit',
            'branches.delete',
            
            // إعدادات الشركة
            'company.settings.view',
            'company.settings.edit',
            
            // المستندات القانونية
            'legal.documents.view',
            'legal.documents.create',
            'legal.documents.edit',
            'legal.documents.delete',
            'legal.documents.download',
        ];

        // إنشاء الصلاحيات الجديدة فقط
        foreach ($newPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // إسناد الصلاحيات للأدوار الموجودة
        $adminRole = Role::where('name', 'Admin')->first();
        $managerRole = Role::where('name', 'Manager')->first();

        if ($adminRole) {
            $adminRole->givePermissionTo($newPermissions);
            echo "✅ Admin role updated with company management permissions\n";
        }

        if ($managerRole) {
            // المدير يحصل على صلاحيات محدودة
            $managerPermissions = [
                'branches.view',
                'company.settings.view',
            ];
            $managerRole->givePermissionTo($managerPermissions);
            echo "✅ Manager role updated with limited company management permissions\n";
        }

        echo "🎉 Company Management permissions added successfully!\n";
    }
}
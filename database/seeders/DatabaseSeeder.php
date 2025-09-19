<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting HR System Professional Seeding...');
        $this->command->info('');

        // Run tenant seeder first
        $this->call([
            TenantSeeder::class,
            RolePermissionSeeder::class,
            UserSeeder::class,
            CompanyManagementSeeder::class,
            EmployeePermissionSeeder::class,
            LegalDocumentSeeder::class,
            EmployeeSeeder::class,
            LeaveSeeder::class,
            EmployeeDashboardSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('✅ Professional seeding completed successfully!');
        $this->command->info('');
        $this->command->info('📋 Test Accounts Created:');
        $this->command->info('   Super Admin: superadmin@hrsystem.com / password123');
        $this->command->info('   Tenant Admins: [tenant_email] / admin123');
        $this->command->info('   Employees: [employee_email] / employee123');
        $this->command->info('');
        $this->command->info('🌐 Access URLs:');
        $this->command->info('   Super Admin: http://localhost:8000/super-admin');
        $this->command->info('   Test Dashboard: http://localhost:8000/test');
    }
}

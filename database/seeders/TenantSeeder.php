<?php

/**
 * Author: Eng.Fahed
 * Professional Tenant Seeder for HR System Testing
 */

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Tenant;
use Illuminate\Support\Str;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tenants = [
            [
                'name' => 'Tech Solutions Inc',
                'company_name' => 'Tech Solutions Inc',
                'domain' => 'techsolutions.com',
                'subdomain' => 'techsolutions',
                'database_name' => 'tenant_techsolutions_' . Str::random(8),
                'contact_email' => 'admin@techsolutions.com',
                'contact_phone' => '+1-555-0123',
                'address' => '123 Technology Street, Silicon Valley, CA 94000',
                'status' => 'active',
                'subscription_plan' => 'enterprise',
                'subscription_start_date' => now()->subMonths(6),
                'subscription_end_date' => now()->addMonths(6),
                'monthly_fee' => 299.99,
                'max_employees' => 500,
                'features' => ['employee_management', 'advanced_reports', 'attendance_tracking', 'leave_management', 'payroll_management', 'custom_reports', 'api_access'],
            ],
            [
                'name' => 'Healthcare Plus',
                'company_name' => 'Healthcare Plus Medical Center',
                'domain' => 'healthcareplus.com',
                'subdomain' => 'healthcareplus',
                'database_name' => 'tenant_healthcareplus_' . Str::random(8),
                'contact_email' => 'hr@healthcareplus.com',
                'contact_phone' => '+1-555-0456',
                'address' => '456 Medical Avenue, New York, NY 10001',
                'status' => 'active',
                'subscription_plan' => 'premium',
                'subscription_start_date' => now()->subMonths(3),
                'subscription_end_date' => now()->addMonths(9),
                'monthly_fee' => 199.99,
                'max_employees' => 200,
                'features' => ['employee_management', 'advanced_reports', 'attendance_tracking', 'leave_management'],
            ],
            [
                'name' => 'StartupHub',
                'company_name' => 'StartupHub Innovation Lab',
                'domain' => 'startuphub.io',
                'subdomain' => 'startuphub',
                'database_name' => 'tenant_startuphub_' . Str::random(8),
                'contact_email' => 'contact@startuphub.io',
                'contact_phone' => '+1-555-0789',
                'address' => '789 Innovation Drive, Austin, TX 78701',
                'status' => 'active',
                'subscription_plan' => 'basic',
                'subscription_start_date' => now()->subMonths(1),
                'subscription_end_date' => now()->addMonths(11),
                'monthly_fee' => 99.99,
                'max_employees' => 50,
                'features' => ['employee_management', 'basic_reports'],
            ],
            [
                'name' => 'Global Finance Corp',
                'company_name' => 'Global Finance Corporation',
                'domain' => 'globalfinance.com',
                'subdomain' => 'globalfinance',
                'database_name' => 'tenant_globalfinance_' . Str::random(8),
                'contact_email' => 'hr@globalfinance.com',
                'contact_phone' => '+1-555-0321',
                'address' => '321 Wall Street, New York, NY 10005',
                'status' => 'active',
                'subscription_plan' => 'enterprise',
                'subscription_start_date' => now()->subYear(),
                'subscription_end_date' => now()->addYear(),
                'monthly_fee' => 499.99,
                'max_employees' => 1000,
                'features' => ['employee_management', 'advanced_reports', 'attendance_tracking', 'leave_management', 'payroll_management', 'custom_reports', 'api_access'],
            ],
            [
                'name' => 'EduTech Academy',
                'company_name' => 'EduTech Online Academy',
                'domain' => 'edutech.edu',
                'subdomain' => 'edutech',
                'database_name' => 'tenant_edutech_' . Str::random(8),
                'contact_email' => 'admin@edutech.edu',
                'contact_phone' => '+1-555-0654',
                'address' => '654 Education Boulevard, Boston, MA 02101',
                'status' => 'active',
                'subscription_plan' => 'premium',
                'subscription_start_date' => now()->subMonths(8),
                'subscription_end_date' => now()->addMonths(4),
                'monthly_fee' => 179.99,
                'max_employees' => 150,
                'features' => ['employee_management', 'advanced_reports', 'attendance_tracking', 'leave_management'],
            ],
            [
                'name' => 'Green Energy Solutions',
                'company_name' => 'Green Energy Solutions LLC',
                'domain' => 'greenenergy.com',
                'subdomain' => 'greenenergy',
                'database_name' => 'tenant_greenenergy_' . Str::random(8),
                'contact_email' => 'hr@greenenergy.com',
                'contact_phone' => '+1-555-0987',
                'address' => '987 Renewable Way, Portland, OR 97201',
                'status' => 'suspended',
                'subscription_plan' => 'basic',
                'subscription_start_date' => now()->subMonths(2),
                'subscription_end_date' => now()->subDays(5), // Expired
                'monthly_fee' => 89.99,
                'max_employees' => 25,
                'features' => ['employee_management', 'basic_reports'],
            ],
            [
                'name' => 'RetailMax',
                'company_name' => 'RetailMax Chain Stores',
                'domain' => 'retailmax.com',
                'subdomain' => 'retailmax',
                'database_name' => 'tenant_retailmax_' . Str::random(8),
                'contact_email' => 'operations@retailmax.com',
                'contact_phone' => '+1-555-0147',
                'address' => '147 Commerce Street, Chicago, IL 60601',
                'status' => 'inactive',
                'subscription_plan' => 'premium',
                'subscription_start_date' => now()->subMonths(4),
                'subscription_end_date' => now()->addMonths(8),
                'monthly_fee' => 219.99,
                'max_employees' => 300,
                'features' => ['employee_management', 'advanced_reports', 'attendance_tracking', 'leave_management'],
            ],
            [
                'name' => 'CloudTech Services',
                'company_name' => 'CloudTech Cloud Services',
                'domain' => 'cloudtech.services',
                'subdomain' => 'cloudtech',
                'database_name' => 'tenant_cloudtech_' . Str::random(8),
                'contact_email' => 'support@cloudtech.services',
                'contact_phone' => '+1-555-0258',
                'address' => '258 Cloud Avenue, Seattle, WA 98101',
                'status' => 'active',
                'subscription_plan' => 'enterprise',
                'subscription_start_date' => now()->subMonths(12),
                'subscription_end_date' => now()->addMonths(12),
                'monthly_fee' => 399.99,
                'max_employees' => 750,
                'features' => ['employee_management', 'advanced_reports', 'attendance_tracking', 'leave_management', 'payroll_management', 'custom_reports', 'api_access'],
            ],
            [
                'name' => 'Creative Agency',
                'company_name' => 'Creative Digital Agency',
                'domain' => 'creativeagency.design',
                'subdomain' => 'creativeagency',
                'database_name' => 'tenant_creativeagency_' . Str::random(8),
                'contact_email' => 'hello@creativeagency.design',
                'contact_phone' => '+1-555-0369',
                'address' => '369 Creative Lane, Los Angeles, CA 90210',
                'status' => 'active',
                'subscription_plan' => 'basic',
                'subscription_start_date' => now()->subDays(15),
                'subscription_end_date' => now()->addMonths(11)->subDays(15),
                'monthly_fee' => 79.99,
                'max_employees' => 30,
                'features' => ['employee_management', 'basic_reports'],
            ],
            [
                'name' => 'Manufacturing Pro',
                'company_name' => 'Manufacturing Pro Industries',
                'domain' => 'manufacturingpro.com',
                'subdomain' => 'manufacturingpro',
                'database_name' => 'tenant_manufacturingpro_' . Str::random(8),
                'contact_email' => 'hr@manufacturingpro.com',
                'contact_phone' => '+1-555-0741',
                'address' => '741 Industrial Park, Detroit, MI 48201',
                'status' => 'active',
                'subscription_plan' => 'premium',
                'subscription_start_date' => now()->subMonths(7),
                'subscription_end_date' => now()->addMonths(5),
                'monthly_fee' => 249.99,
                'max_employees' => 400,
                'features' => ['employee_management', 'advanced_reports', 'attendance_tracking', 'leave_management'],
            ]
        ];

        foreach ($tenants as $tenantData) {
            Tenant::create($tenantData);
        }

        $this->command->info('✅ Created ' . count($tenants) . ' professional tenants for testing!');
    }
}

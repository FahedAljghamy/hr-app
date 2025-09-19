<?php

/**
 * Author: Eng.Fahed
 * Employee Dashboard Seeder - HR System
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\EmployeeCertificate;
use App\Models\EmployeeNotification;
use Carbon\Carbon;

class EmployeeDashboardSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🏢 Creating Employee Dashboard sample data...');

        $employees = Employee::where('employment_status', 'active')->take(10)->get();
        $totalCertificates = 0;
        $totalNotifications = 0;

        foreach ($employees as $employee) {
            // إنشاء شهادات
            $certificateTypes = ['salary_certificate', 'employment_certificate', 'experience_certificate'];
            $statuses = ['pending', 'approved', 'completed', 'rejected'];
            
            for ($i = 0; $i < rand(2, 4); $i++) {
                EmployeeCertificate::create([
                    'employee_id' => $employee->id,
                    'tenant_id' => $employee->tenant_id,
                    'requested_by' => $employee->user_id ?? 1,
                    'certificate_type' => $certificateTypes[array_rand($certificateTypes)],
                    'purpose' => 'Bank loan application',
                    'priority' => 'normal',
                    'status' => $statuses[array_rand($statuses)],
                    'created_at' => Carbon::now()->subDays(rand(1, 30))
                ]);
                $totalCertificates++;
            }

            // إنشاء إشعارات
            $notificationTypes = ['payroll_ready', 'leave_approved', 'certificate_ready', 'system_announcement'];
            
            for ($i = 0; $i < rand(3, 8); $i++) {
                $isRead = rand(0, 1);
                $createdAt = Carbon::now()->subDays(rand(1, 60));
                
                EmployeeNotification::create([
                    'employee_id' => $employee->id,
                    'tenant_id' => $employee->tenant_id,
                    'type' => $notificationTypes[array_rand($notificationTypes)],
                    'title' => 'Sample Notification',
                    'message' => 'This is a sample notification message.',
                    'priority' => 'normal',
                    'is_read' => $isRead,
                    'read_at' => $isRead ? $createdAt->addHours(rand(1, 24)) : null,
                    'created_at' => $createdAt
                ]);
                $totalNotifications++;
            }
        }

        $this->command->info("✅ Created {$totalCertificates} certificates and {$totalNotifications} notifications");
    }
}

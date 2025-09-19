<?php

/**
 * Author: Eng.Fahed
 * Employee Seeder - HR System
 * إنشاء موظفين واقعيين لكل فرع مع بيانات شاملة
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Tenant;
use App\Models\Branch;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('👥 Creating employees for all branches...');

        $tenants = Tenant::all();
        $totalEmployees = 0;
        $totalPayrolls = 0;

        foreach ($tenants as $tenant) {
            $this->command->info("🏢 Processing tenant: {$tenant->name}");
            
            $branches = Branch::where('tenant_id', $tenant->id)->get();
            
            foreach ($branches as $branch) {
                $this->command->info("  🏪 Creating employees for branch: {$branch->name}");
                
                // عدد الموظفين لكل فرع (5-12 موظف)
                $employeeCount = rand(5, 12);
                
                for ($i = 1; $i <= $employeeCount; $i++) {
                    $employee = $this->createEmployee($tenant, $branch);
                    $totalEmployees++;
                    
                    // إنشاء رواتب للموظف (آخر 4 أشهر)
                    $payrollsCreated = $this->createPayrollsForEmployee($employee);
                    $totalPayrolls += $payrollsCreated;
                    
                    // تأخير صغير لضمان الفرادة
                    usleep(10000); // 10ms
                }
            }
        }

        $this->command->info("✅ Created {$totalEmployees} employees with {$totalPayrolls} payroll records");
    }

    private function createEmployee(Tenant $tenant, ?Branch $branch): Employee
    {
        $firstNames = ['Ahmed', 'Mohammed', 'Omar', 'Ali', 'Hassan', 'Fatima', 'Aisha', 'Maryam', 'Noura', 'Sara'];
        $lastNames = ['Al-Mahmoud', 'Al-Rashid', 'Al-Zahra', 'Al-Mansouri', 'Al-Shamsi'];
        
        $firstName = $firstNames[array_rand($firstNames)];
        $lastName = $lastNames[array_rand($lastNames)];
        $gender = in_array($firstName, ['Fatima', 'Aisha', 'Maryam', 'Noura', 'Sara']) ? 'female' : 'male';
        
        $departments = ['hr', 'finance', 'it', 'marketing', 'sales', 'operations'];
        $department = $departments[array_rand($departments)];
        
        $jobTitles = [
            'hr' => ['HR Manager', 'HR Specialist', 'Recruitment Specialist'],
            'finance' => ['Finance Manager', 'Accountant', 'Financial Analyst'],
            'it' => ['IT Manager', 'Software Developer', 'System Administrator'],
            'marketing' => ['Marketing Manager', 'Digital Marketing Specialist'],
            'sales' => ['Sales Manager', 'Sales Executive', 'Account Manager'],
            'operations' => ['Operations Manager', 'Operations Coordinator']
        ];
        
        $jobTitle = $jobTitles[$department][array_rand($jobTitles[$department])];
        $basicSalary = rand(5000, 25000);
        $hireDate = Carbon::now()->subDays(rand(30, 800));
        
        return Employee::create([
            'tenant_id' => $tenant->id,
            'branch_id' => $branch?->id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => strtolower($firstName . '.' . str_replace('Al-', '', $lastName)) . rand(1000, 9999) . time() . '@' . $tenant->domain,
            'phone' => '+971' . rand(50, 56) . rand(1000000, 9999999),
            'date_of_birth' => Carbon::now()->subYears(rand(25, 50)),
            'gender' => $gender,
            'marital_status' => ['single', 'married'][array_rand(['single', 'married'])],
            'nationality' => ['UAE', 'Egyptian', 'Indian', 'Pakistani'][array_rand(['UAE', 'Egyptian', 'Indian', 'Pakistani'])],
            'address' => 'Dubai, UAE',
            'passport_number' => strtoupper(substr($firstName, 0, 2)) . rand(1000000, 9999999) . time(),
            'passport_expiry' => Carbon::now()->addYears(rand(2, 5)),
            'passport_country' => 'UAE',
            'visa_number' => '784' . rand(100000000000, 999999999999),
            'visa_expiry' => Carbon::now()->addMonths(rand(6, 24)),
            'job_title' => $jobTitle,
            'department' => $department,
            'employment_type' => 'full_time',
            'employment_status' => 'active',
            'hire_date' => $hireDate,
            'contract_start_date' => $hireDate,
            'contract_end_date' => Carbon::now()->addYears(rand(1, 3)),
            'basic_salary' => $basicSalary,
            'salary_currency' => 'AED',
            'housing_allowance' => $basicSalary * 0.3,
            'transport_allowance' => rand(500, 1500),
            'food_allowance' => rand(300, 800),
            'other_allowances' => rand(0, 500),
            'is_manager' => str_contains($jobTitle, 'Manager'),
        ]);
    }

    private function createPayrollsForEmployee(Employee $employee): int
    {
        $payrollsCreated = 0;
        $currentDate = Carbon::now();
        
        // إنشاء رواتب لآخر 4 أشهر
        for ($i = 3; $i >= 0; $i--) {
            $payDate = $currentDate->copy()->subMonths($i);
            $year = $payDate->year;
            $month = $payDate->month;
            
            if ($payDate->isFuture() || $payDate->lt($employee->hire_date)) {
                continue;
            }

            $payPeriodStart = Carbon::create($year, $month, 1);
            $payPeriodEnd = $payPeriodStart->copy()->endOfMonth();
            $payDateActual = $payPeriodEnd->copy()->addDays(5);

            $workingDays = 22; // تبسيط
            $attendedDays = rand(20, 22);
            $absentDays = $workingDays - $attendedDays;
            
            $overtimeHours = rand(0, 15);
            $overtimeRate = 50; // 50 AED per hour
            $overtimeAllowance = $overtimeHours * $overtimeRate;
            
            $absenceDeduction = $absentDays * ($employee->basic_salary / $workingDays);
            $insuranceDeduction = $employee->basic_salary * 0.05;
            
            $paymentStatus = $i === 0 ? 'pending' : 'paid'; // الشهر الحالي معلق
            $paidAt = $paymentStatus === 'paid' ? $payDateActual->copy()->addDays(2) : null;

            Payroll::create([
                'employee_id' => $employee->id,
                'tenant_id' => $employee->tenant_id,
                'pay_year' => $year,
                'pay_month' => $month,
                'pay_date' => $payDateActual,
                'pay_period_start' => $payPeriodStart,
                'pay_period_end' => $payPeriodEnd,
                'basic_salary' => $employee->basic_salary,
                'currency' => $employee->salary_currency,
                'housing_allowance' => $employee->housing_allowance,
                'transport_allowance' => $employee->transport_allowance,
                'food_allowance' => $employee->food_allowance,
                'overtime_allowance' => $overtimeAllowance,
                'performance_bonus' => rand(0, 1) ? rand(500, 2000) : 0,
                'other_allowances' => $employee->other_allowances,
                'absence_deduction' => $absenceDeduction,
                'insurance_deduction' => $insuranceDeduction,
                'other_deductions' => rand(0, 300),
                'working_days' => $workingDays,
                'attended_days' => $attendedDays,
                'absent_days' => $absentDays,
                'overtime_hours' => $overtimeHours,
                'overtime_rate' => $overtimeRate,
                'payment_method' => 'bank_transfer',
                'payment_status' => $paymentStatus,
                'paid_at' => $paidAt,
                'payment_reference' => $paymentStatus === 'paid' ? 'TXN' . rand(100000, 999999) : null,
            ]);

            $payrollsCreated++;
        }

        return $payrollsCreated;
    }
}

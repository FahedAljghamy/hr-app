<?php

/**
 * Author: Eng.Fahed
 * Check Employee Documents Command - HR System
 * فحص انتهاء مستندات الموظفين وإرسال التنبيهات
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckEmployeeDocuments extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'employees:check-documents 
                            {--days=30 : Number of days before expiry to check}
                            {--type= : Type of document to check (passport|visa|emirates_id|contract)}';

    /**
     * The console command description.
     */
    protected $description = 'Check for expiring employee documents and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $type = $this->option('type');
        
        $this->info("🔍 Checking for employee documents expiring in the next {$days} days...");
        
        if ($type) {
            $this->info("📋 Checking only: {$type}");
        }
        
        $notifications = 0;
        $employees = Employee::where('employment_status', 'active')->get();
        
        foreach ($employees as $employee) {
            $alerts = $this->checkEmployeeDocuments($employee, $days, $type);
            
            if (!empty($alerts)) {
                $this->sendNotifications($employee, $alerts);
                $notifications += count($alerts);
                
                // عرض التفاصيل
                foreach ($alerts as $alert) {
                    $this->warn("⚠️  {$employee->full_name} ({$employee->employee_id}): {$alert['message']}");
                }
            }
        }
        
        if ($notifications === 0) {
            $this->info("✅ No employee documents expiring in the next {$days} days.");
        } else {
            $this->info("📊 Summary:");
            $this->info("✅ Notifications sent: {$notifications}");
        }
        
        Log::info('Employee documents check completed', [
            'days' => $days,
            'type' => $type,
            'notifications_sent' => $notifications,
            'checked_at' => now()
        ]);
        
        return 0;
    }

    /**
     * فحص مستندات موظف محدد
     */
    private function checkEmployeeDocuments(Employee $employee, int $days, ?string $type = null): array
    {
        $alerts = [];
        $checkDate = now()->addDays($days);
        
        // فحص جواز السفر
        if ((!$type || $type === 'passport') && $employee->passport_expiry) {
            if ($employee->passport_expiry->lte($checkDate)) {
                $daysLeft = $employee->passport_expiry->diffInDays(now(), false);
                $alerts[] = [
                    'type' => 'passport',
                    'document_name' => 'Passport',
                    'number' => $employee->passport_number,
                    'expiry_date' => $employee->passport_expiry,
                    'days_left' => $daysLeft,
                    'message' => "Passport ({$employee->passport_number}) expires on {$employee->passport_expiry->format('Y-m-d')}",
                    'urgency' => $daysLeft <= 0 ? 'expired' : ($daysLeft <= 30 ? 'high' : 'medium')
                ];
            }
        }
        
        // فحص الفيزا/الإقامة
        if ((!$type || $type === 'visa') && $employee->visa_expiry) {
            if ($employee->visa_expiry->lte($checkDate)) {
                $daysLeft = $employee->visa_expiry->diffInDays(now(), false);
                $alerts[] = [
                    'type' => 'visa',
                    'document_name' => 'Visa/Residence',
                    'number' => $employee->visa_number,
                    'expiry_date' => $employee->visa_expiry,
                    'days_left' => $daysLeft,
                    'message' => "Visa/Residence ({$employee->visa_number}) expires on {$employee->visa_expiry->format('Y-m-d')}",
                    'urgency' => $daysLeft <= 0 ? 'expired' : ($daysLeft <= 7 ? 'high' : 'medium')
                ];
            }
        }
        
        // فحص الهوية الإماراتية
        if ((!$type || $type === 'emirates_id') && $employee->emirates_id_expiry) {
            if ($employee->emirates_id_expiry->lte($checkDate)) {
                $daysLeft = $employee->emirates_id_expiry->diffInDays(now(), false);
                $alerts[] = [
                    'type' => 'emirates_id',
                    'document_name' => 'Emirates ID',
                    'number' => $employee->emirates_id,
                    'expiry_date' => $employee->emirates_id_expiry,
                    'days_left' => $daysLeft,
                    'message' => "Emirates ID ({$employee->emirates_id}) expires on {$employee->emirates_id_expiry->format('Y-m-d')}",
                    'urgency' => $daysLeft <= 0 ? 'expired' : ($daysLeft <= 7 ? 'high' : 'medium')
                ];
            }
        }
        
        // فحص العقد
        if ((!$type || $type === 'contract') && $employee->contract_end_date) {
            if ($employee->contract_end_date->lte($checkDate)) {
                $daysLeft = $employee->contract_end_date->diffInDays(now(), false);
                $alerts[] = [
                    'type' => 'contract',
                    'document_name' => 'Employment Contract',
                    'number' => $employee->employee_id,
                    'expiry_date' => $employee->contract_end_date,
                    'days_left' => $daysLeft,
                    'message' => "Employment Contract ({$employee->employee_id}) expires on {$employee->contract_end_date->format('Y-m-d')}",
                    'urgency' => $daysLeft <= 0 ? 'expired' : ($daysLeft <= 14 ? 'high' : 'medium')
                ];
            }
        }
        
        return $alerts;
    }

    /**
     * إرسال التنبيهات للمدراء والموظف
     */
    private function sendNotifications(Employee $employee, array $alerts): void
    {
        try {
            // الحصول على المدراء والـ HR
            $recipients = User::where('tenant_id', $employee->tenant_id)
                             ->where(function ($query) {
                                 $query->whereHas('roles', function ($q) {
                                     $q->whereIn('name', ['Admin', 'Manager']);
                                 })
                                 ->orWhere('user_type', 'tenant_admin');
                             })
                             ->get();

            // إضافة الموظف نفسه للتنبيهات
            if ($employee->user_id) {
                $employeeUser = User::find($employee->user_id);
                if ($employeeUser) {
                    $recipients->push($employeeUser);
                }
            }

            foreach ($recipients as $recipient) {
                foreach ($alerts as $alert) {
                    // هنا يمكن إرسال إيميل أو notification
                    // لكن سنكتفي بالـ logging حالياً
                    
                    Log::info('Employee document expiry notification', [
                        'recipient_id' => $recipient->id,
                        'recipient_email' => $recipient->email,
                        'employee_id' => $employee->id,
                        'employee_name' => $employee->full_name,
                        'document_type' => $alert['type'],
                        'document_number' => $alert['number'],
                        'expiry_date' => $alert['expiry_date']->format('Y-m-d'),
                        'days_left' => $alert['days_left'],
                        'urgency' => $alert['urgency'],
                        'sent_at' => now()
                    ]);
                    
                    // يمكن إضافة إرسال إيميل هنا
                    // Mail::to($recipient->email)->send(new EmployeeDocumentExpiryMail($employee, $alert));
                }
            }
            
        } catch (\Exception $e) {
            Log::error('Error sending employee document notifications', [
                'employee_id' => $employee->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->error("❌ Error sending notifications for {$employee->full_name}: " . $e->getMessage());
        }
    }
}
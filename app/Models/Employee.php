<?php

/**
 * Author: Eng.Fahed
 * Employee Model - HR System
 * نموذج الموظف مع جميع العلاقات والوظائف المساعدة
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // معرف الموظف والربط
        'employee_id', 'tenant_id', 'branch_id', 'user_id',
        
        // البيانات الشخصية
        'first_name', 'last_name', 'middle_name', 'full_name_ar',
        'email', 'phone', 'phone_secondary', 'date_of_birth',
        'gender', 'marital_status', 'nationality', 'address',
        'emergency_contact',
        
        // بيانات الهوية والإقامة
        'passport_number', 'passport_expiry', 'passport_country',
        'visa_number', 'visa_expiry', 'emirates_id', 'emirates_id_expiry',
        'labor_card_number', 'labor_card_expiry',
        
        // بيانات العقد والعمل
        'job_title', 'department', 'employment_type', 'employment_status',
        'hire_date', 'contract_start_date', 'contract_end_date',
        'probation_period_months', 'probation_end_date',
        'annual_leave_days', 'sick_leave_days',
        
        // بيانات الراتب
        'basic_salary', 'salary_currency', 'salary_frequency',
        'housing_allowance', 'transport_allowance', 'food_allowance', 'other_allowances',
        
        // بيانات بنكية
        'bank_name', 'bank_account_number', 'iban', 'swift_code',
        
        // المستندات والملفات
        'profile_photo', 'passport_copy', 'visa_copy', 'emirates_id_copy',
        'labor_card_copy', 'contract_copy', 'educational_certificates', 'other_documents',
        
        // معلومات إضافية
        'skills', 'languages', 'notes', 'is_manager', 'manager_id'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'passport_expiry' => 'date',
        'visa_expiry' => 'date',
        'emirates_id_expiry' => 'date',
        'labor_card_expiry' => 'date',
        'hire_date' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'probation_end_date' => 'date',
        'basic_salary' => 'decimal:2',
        'housing_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'food_allowance' => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'is_manager' => 'boolean',
        'emergency_contact' => 'array',
        'educational_certificates' => 'array',
        'other_documents' => 'array',
        'skills' => 'array',
        'languages' => 'array',
    ];

    protected $appends = [
        'full_name',
        'age',
        'years_of_service',
        'contract_status',
        'document_expiry_alerts',
        'total_salary'
    ];

    /**
     * تطبيق نطاق المؤسسة تلقائياً
     */
    protected static function booted()
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check() && auth()->user()->tenant_id) {
                $builder->where('tenant_id', auth()->user()->tenant_id);
            }
        });

        // تعيين معرف الموظف تلقائياً
        static::creating(function ($employee) {
            if (!$employee->employee_id) {
                $employee->employee_id = self::generateEmployeeId($employee->tenant_id);
            }
            
            if (!$employee->tenant_id && auth()->check()) {
                $employee->tenant_id = auth()->user()->tenant_id;
            }
        });
    }

    /**
     * العلاقات
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'manager_id');
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(EmployeeCertificate::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(EmployeeNotification::class);
    }

    /**
     * نطاقات الاستعلام
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('employment_status', 'active');
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('employment_status', 'inactive');
    }

    public function scopeTerminated(Builder $query): Builder
    {
        return $query->where('employment_status', 'terminated');
    }

    public function scopeContractExpiringSoon(Builder $query, int $days = 30): Builder
    {
        return $query->whereNotNull('contract_end_date')
                    ->where('contract_end_date', '<=', now()->addDays($days))
                    ->where('contract_end_date', '>=', now());
    }

    public function scopeVisaExpiringSoon(Builder $query, int $days = 30): Builder
    {
        return $query->whereNotNull('visa_expiry')
                    ->where('visa_expiry', '<=', now()->addDays($days))
                    ->where('visa_expiry', '>=', now());
    }

    public function scopePassportExpiringSoon(Builder $query, int $days = 90): Builder
    {
        return $query->where('passport_expiry', '<=', now()->addDays($days))
                    ->where('passport_expiry', '>=', now());
    }

    public function scopeEmiratesIdExpiringSoon(Builder $query, int $days = 30): Builder
    {
        return $query->whereNotNull('emirates_id_expiry')
                    ->where('emirates_id_expiry', '<=', now()->addDays($days))
                    ->where('emirates_id_expiry', '>=', now());
    }

    public function scopeByDepartment(Builder $query, string $department): Builder
    {
        return $query->where('department', $department);
    }

    public function scopeByBranch(Builder $query, int $branchId): Builder
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * الخصائص المحسوبة
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . ($this->middle_name ? $this->middle_name . ' ' : '') . $this->last_name);
    }

    public function getAgeAttribute(): int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : 0;
    }

    public function getYearsOfServiceAttribute(): float
    {
        return $this->hire_date ? $this->hire_date->diffInYears(now(), true) : 0;
    }

    public function getContractStatusAttribute(): string
    {
        if (!$this->contract_end_date) {
            return 'permanent';
        }

        $daysLeft = now()->diffInDays($this->contract_end_date, false);
        
        if ($daysLeft < 0) {
            return 'expired';
        } elseif ($daysLeft <= 30) {
            return 'expiring_soon';
        } else {
            return 'active';
        }
    }

    public function getDocumentExpiryAlertsAttribute(): array
    {
        $alerts = [];

        // فحص انتهاء جواز السفر
        if ($this->passport_expiry && $this->passport_expiry->diffInDays(now(), false) <= 90) {
            $alerts[] = [
                'type' => 'passport',
                'message' => 'Passport expires on ' . $this->passport_expiry->format('Y-m-d'),
                'days_left' => $this->passport_expiry->diffInDays(now(), false),
                'urgency' => $this->passport_expiry->diffInDays(now(), false) <= 30 ? 'high' : 'medium'
            ];
        }

        // فحص انتهاء الفيزا
        if ($this->visa_expiry && $this->visa_expiry->diffInDays(now(), false) <= 30) {
            $alerts[] = [
                'type' => 'visa',
                'message' => 'Visa expires on ' . $this->visa_expiry->format('Y-m-d'),
                'days_left' => $this->visa_expiry->diffInDays(now(), false),
                'urgency' => $this->visa_expiry->diffInDays(now(), false) <= 7 ? 'high' : 'medium'
            ];
        }

        // فحص انتهاء الهوية الإماراتية
        if ($this->emirates_id_expiry && $this->emirates_id_expiry->diffInDays(now(), false) <= 30) {
            $alerts[] = [
                'type' => 'emirates_id',
                'message' => 'Emirates ID expires on ' . $this->emirates_id_expiry->format('Y-m-d'),
                'days_left' => $this->emirates_id_expiry->diffInDays(now(), false),
                'urgency' => $this->emirates_id_expiry->diffInDays(now(), false) <= 7 ? 'high' : 'medium'
            ];
        }

        // فحص انتهاء العقد
        if ($this->contract_end_date && $this->contract_end_date->diffInDays(now(), false) <= 30) {
            $alerts[] = [
                'type' => 'contract',
                'message' => 'Contract expires on ' . $this->contract_end_date->format('Y-m-d'),
                'days_left' => $this->contract_end_date->diffInDays(now(), false),
                'urgency' => $this->contract_end_date->diffInDays(now(), false) <= 7 ? 'high' : 'medium'
            ];
        }

        return $alerts;
    }

    public function getTotalSalaryAttribute(): float
    {
        return $this->basic_salary + $this->housing_allowance + $this->transport_allowance + 
               $this->food_allowance + $this->other_allowances;
    }

    /**
     * وظائف مساعدة
     */
    public static function generateEmployeeId(int $tenantId): string
    {
        $year = date('Y');
        $prefix = 'EMP';
        
        // البحث عن آخر معرف للموظف عالمياً (ليس فقط للـ tenant)
        $lastEmployee = self::withoutGlobalScope('tenant')
                             ->where('employee_id', 'LIKE', $prefix . $year . '%')
                             ->orderBy('employee_id', 'desc')
                             ->first();

        if ($lastEmployee) {
            $lastNumber = (int) substr($lastEmployee->employee_id, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * حساب رصيد الإجازات
     */
    public function getAnnualLeaveBalance(): array
    {
        $currentYear = date('Y');
        
        // الإجازة السنوية المستحقة (30 يوم سنوياً في الإمارات)
        $annualEntitlement = 30;
        
        // حساب الإجازات المستخدمة هذا العام
        $usedAnnual = $this->leaves()
                          ->where('leave_type', 'annual')
                          ->where('status', 'approved')
                          ->whereYear('start_date', $currentYear)
                          ->sum('total_days');
        
        $remainingAnnual = max(0, $annualEntitlement - $usedAnnual);
        
        return [
            'entitled' => $annualEntitlement,
            'used' => $usedAnnual,
            'remaining' => $remainingAnnual,
            'year' => $currentYear
        ];
    }

    public function getSickLeaveBalance(): array
    {
        $currentYear = date('Y');
        
        // الإجازة المرضية المستحقة (90 يوم في 3 سنوات في الإمارات)
        $sickEntitlement = 30; // 30 يوم سنوياً كحد أقصى
        
        // حساب الإجازات المرضية المستخدمة هذا العام
        $usedSick = $this->leaves()
                        ->where('leave_type', 'sick')
                        ->where('status', 'approved')
                        ->whereYear('start_date', $currentYear)
                        ->sum('total_days');
        
        $remainingSick = max(0, $sickEntitlement - $usedSick);
        
        return [
            'entitled' => $sickEntitlement,
            'used' => $usedSick,
            'remaining' => $remainingSick,
            'year' => $currentYear
        ];
    }

    public function getEmergencyLeaveBalance(): array
    {
        $currentYear = date('Y');
        
        // الإجازة الطارئة (5 أيام سنوياً)
        $emergencyEntitlement = 5;
        
        $usedEmergency = $this->leaves()
                             ->where('leave_type', 'emergency')
                             ->where('status', 'approved')
                             ->whereYear('start_date', $currentYear)
                             ->sum('total_days');
        
        $remainingEmergency = max(0, $emergencyEntitlement - $usedEmergency);
        
        return [
            'entitled' => $emergencyEntitlement,
            'used' => $usedEmergency,
            'remaining' => $remainingEmergency,
            'year' => $currentYear
        ];
    }

    public function getAllLeaveBalances(): array
    {
        return [
            'annual' => $this->getAnnualLeaveBalance(),
            'sick' => $this->getSickLeaveBalance(),
            'emergency' => $this->getEmergencyLeaveBalance()
        ];
    }

    /**
     * إشعارات الموظف
     */
    public function getUnreadNotificationsCount(): int
    {
        return $this->notifications()->unread()->count();
    }

    public function getRecentNotifications(int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return $this->notifications()
                   ->notExpired()
                   ->orderBy('created_at', 'desc')
                   ->limit($limit)
                   ->get();
    }

    public function hasUrgentNotifications(): bool
    {
        return $this->notifications()
                   ->unread()
                   ->where('priority', 'urgent')
                   ->exists();
    }

    /**
     * الشهادات والطلبات
     */
    public function getPendingCertificatesCount(): int
    {
        return $this->certificates()->pending()->count();
    }

    public function getCompletedCertificatesCount(): int
    {
        return $this->certificates()->completed()->count();
    }

    public function getLatestPayroll(): ?Payroll
    {
        return $this->payrolls()->latest('pay_date')->first();
    }

    public function getPayrollForMonth(int $year, int $month): ?Payroll
    {
        return $this->payrolls()
                    ->where('pay_year', $year)
                    ->where('pay_month', $month)
                    ->first();
    }

    public function hasExpiredDocuments(): bool
    {
        return count($this->document_expiry_alerts) > 0;
    }

    public function isContractExpired(): bool
    {
        return $this->contract_end_date && $this->contract_end_date->isPast();
    }

    public function isVisaExpired(): bool
    {
        return $this->visa_expiry && $this->visa_expiry->isPast();
    }

    public function isPassportExpired(): bool
    {
        return $this->passport_expiry && $this->passport_expiry->isPast();
    }

    /**
     * الحصول على قائمة الأقسام المتاحة
     */
    public static function getDepartments(): array
    {
        return [
            'hr' => 'Human Resources',
            'finance' => 'Finance',
            'it' => 'Information Technology',
            'marketing' => 'Marketing',
            'sales' => 'Sales',
            'operations' => 'Operations',
            'admin' => 'Administration',
            'legal' => 'Legal',
            'procurement' => 'Procurement',
            'customer_service' => 'Customer Service'
        ];
    }

    /**
     * الحصول على قائمة أنواع التوظيف
     */
    public static function getEmploymentTypes(): array
    {
        return [
            'full_time' => 'Full Time',
            'part_time' => 'Part Time',
            'contract' => 'Contract',
            'intern' => 'Intern'
        ];
    }

    /**
     * الحصول على قائمة حالات التوظيف
     */
    public static function getEmploymentStatuses(): array
    {
        return [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'terminated' => 'Terminated',
            'resigned' => 'Resigned'
        ];
    }
}
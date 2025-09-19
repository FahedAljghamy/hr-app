<?php

/**
 * Author: Eng.Fahed
 * Payroll Model - HR System
 * نموذج الراتب مع جميع العلاقات والحسابات
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Payroll extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id', 'tenant_id', 'pay_year', 'pay_month', 'pay_date', 
        'pay_period_start', 'pay_period_end', 'basic_salary', 'currency',
        'housing_allowance', 'transport_allowance', 'food_allowance',
        'overtime_allowance', 'performance_bonus', 'commission', 'other_allowances',
        'allowances_details', 'tax_deduction', 'insurance_deduction', 
        'loan_deduction', 'advance_deduction', 'absence_deduction', 
        'late_deduction', 'other_deductions', 'deductions_details',
        'working_days', 'attended_days', 'absent_days', 'late_days',
        'overtime_hours', 'overtime_rate', 'gross_salary', 'total_allowances', 
        'total_deductions', 'net_salary', 'payment_method', 'payment_reference', 
        'payment_status', 'paid_at', 'notes', 'payslip_data',
        'approved_by', 'approved_at', 'processed_by', 'processed_at'
    ];

    protected $casts = [
        'pay_date' => 'date',
        'pay_period_start' => 'date',
        'pay_period_end' => 'date',
        'paid_at' => 'datetime',
        'approved_at' => 'datetime',
        'processed_at' => 'datetime',
        'basic_salary' => 'decimal:2',
        'housing_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'food_allowance' => 'decimal:2',
        'overtime_allowance' => 'decimal:2',
        'performance_bonus' => 'decimal:2',
        'commission' => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'tax_deduction' => 'decimal:2',
        'insurance_deduction' => 'decimal:2',
        'loan_deduction' => 'decimal:2',
        'advance_deduction' => 'decimal:2',
        'absence_deduction' => 'decimal:2',
        'late_deduction' => 'decimal:2',
        'other_deductions' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'total_allowances' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'overtime_rate' => 'decimal:2',
        'allowances_details' => 'array',
        'deductions_details' => 'array',
        'payslip_data' => 'array',
    ];

    protected $appends = [
        'pay_period_display',
        'attendance_percentage',
        'is_overdue',
        'formatted_net_salary'
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

        static::saving(function ($payroll) {
            $payroll->calculateTotals();
            
            if (!$payroll->tenant_id && auth()->check()) {
                $payroll->tenant_id = auth()->user()->tenant_id;
            }
        });
    }

    /**
     * العلاقات
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * نطاقات الاستعلام
     */
    public function scopeForYear(Builder $query, int $year): Builder
    {
        return $query->where('pay_year', $year);
    }

    public function scopeForMonth(Builder $query, int $month): Builder
    {
        return $query->where('pay_month', $month);
    }

    public function scopeForPeriod(Builder $query, int $year, int $month): Builder
    {
        return $query->where('pay_year', $year)->where('pay_month', $month);
    }

    public function scopePaid(Builder $query): Builder
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('payment_status', 'pending');
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('payment_status', 'pending')
                    ->where('pay_date', '<', now());
    }

    /**
     * الخصائص المحسوبة
     */
    public function getPayPeriodDisplayAttribute(): string
    {
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        return $months[$this->pay_month] . ' ' . $this->pay_year;
    }

    public function getAttendancePercentageAttribute(): float
    {
        if ($this->working_days == 0) {
            return 0;
        }

        return round(($this->attended_days / $this->working_days) * 100, 2);
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->payment_status === 'pending' && $this->pay_date->isPast();
    }

    public function getFormattedNetSalaryAttribute(): string
    {
        return number_format($this->net_salary, 2) . ' ' . $this->currency;
    }

    /**
     * وظائف الحسابات
     */
    public function calculateTotals(): void
    {
        $this->total_allowances = 
            $this->housing_allowance + 
            $this->transport_allowance + 
            $this->food_allowance + 
            $this->overtime_allowance + 
            $this->performance_bonus + 
            $this->commission + 
            $this->other_allowances;

        $this->total_deductions = 
            $this->tax_deduction + 
            $this->insurance_deduction + 
            $this->loan_deduction + 
            $this->advance_deduction + 
            $this->absence_deduction + 
            $this->late_deduction + 
            $this->other_deductions;

        $this->gross_salary = $this->basic_salary + $this->total_allowances;
        $this->net_salary = $this->gross_salary - $this->total_deductions;
    }

    /**
     * وظائف مساعدة
     */
    public function approve(int $userId): void
    {
        $this->approved_by = $userId;
        $this->approved_at = now();
        $this->save();
    }

    public function markAsPaid(string $paymentReference = null): void
    {
        $this->payment_status = 'paid';
        $this->paid_at = now();
        
        if ($paymentReference) {
            $this->payment_reference = $paymentReference;
        }
        
        $this->save();
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * إنشاء راتب من بيانات الموظف
     */
    public static function createFromEmployee(Employee $employee, int $year, int $month): self
    {
        $payPeriodStart = Carbon::create($year, $month, 1);
        $payPeriodEnd = $payPeriodStart->copy()->endOfMonth();
        $payDate = $payPeriodEnd->copy()->addDays(5);

        $workingDays = 0;
        $currentDate = $payPeriodStart->copy();
        
        while ($currentDate->lte($payPeriodEnd)) {
            if (!$currentDate->isFriday() && !$currentDate->isSaturday()) {
                $workingDays++;
            }
            $currentDate->addDay();
        }

        return self::create([
            'employee_id' => $employee->id,
            'tenant_id' => $employee->tenant_id,
            'pay_year' => $year,
            'pay_month' => $month,
            'pay_date' => $payDate,
            'pay_period_start' => $payPeriodStart,
            'pay_period_end' => $payPeriodEnd,
            'basic_salary' => $employee->basic_salary,
            'currency' => $employee->salary_currency,
            'housing_allowance' => $employee->housing_allowance,
            'transport_allowance' => $employee->transport_allowance,
            'food_allowance' => $employee->food_allowance,
            'other_allowances' => $employee->other_allowances,
            'working_days' => $workingDays,
            'attended_days' => $workingDays,
            'absent_days' => 0,
            'late_days' => 0,
            'overtime_hours' => 0,
            'overtime_rate' => 0,
            'payment_method' => 'bank_transfer',
            'payment_status' => 'pending'
        ]);
    }

    public static function getPaymentMethods(): array
    {
        return [
            'bank_transfer' => 'Bank Transfer',
            'cash' => 'Cash',
            'cheque' => 'Cheque'
        ];
    }

    public static function getPaymentStatuses(): array
    {
        return [
            'pending' => 'Pending',
            'paid' => 'Paid',
            'cancelled' => 'Cancelled'
        ];
    }
}
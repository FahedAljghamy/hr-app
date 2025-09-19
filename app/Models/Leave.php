<?php

/**
 * Author: Eng.Fahed
 * Leave Model - HR System
 * نموذج الإجازات مع جميع العلاقات ونظام الموافقات
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Leave extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id', 'tenant_id', 'leave_type', 'start_date', 'end_date',
        'total_days', 'day_type', 'start_time', 'end_time', 'reason', 'description',
        'emergency_contact', 'attachments', 'is_medical', 'medical_certificate',
        'status', 'rejection_reason', 'admin_notes', 'approved_by', 'approved_at',
        'rejected_by', 'rejected_at', 'covering_employee_id', 'handover_notes',
        'is_paid', 'notify_manager', 'notify_hr', 'notify_covering_employee',
        'cancelled_by', 'cancelled_at', 'cancellation_reason',
        'remaining_annual_days', 'remaining_sick_days'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'attachments' => 'array',
        'is_medical' => 'boolean',
        'is_paid' => 'boolean',
        'notify_manager' => 'boolean',
        'notify_hr' => 'boolean',
        'notify_covering_employee' => 'boolean',
    ];

    protected $appends = [
        'duration_display',
        'status_badge_class',
        'can_be_edited',
        'can_be_cancelled',
        'is_current',
        'days_until_start'
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

        static::creating(function ($leave) {
            if (!$leave->tenant_id && auth()->check()) {
                $leave->tenant_id = auth()->user()->tenant_id;
            }
            
            // حساب إجمالي الأيام تلقائياً
            if ($leave->start_date && $leave->end_date) {
                $leave->total_days = $leave->calculateTotalDays();
            }
        });

        static::updating(function ($leave) {
            // إعادة حساب إجمالي الأيام عند التحديث
            if ($leave->isDirty(['start_date', 'end_date', 'day_type'])) {
                $leave->total_days = $leave->calculateTotalDays();
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

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function coveringEmployee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'covering_employee_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(LeaveComment::class)->orderBy('created_at', 'asc');
    }

    /**
     * نطاقات الاستعلام
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejected');
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeByEmployee(Builder $query, int $employeeId): Builder
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('leave_type', $type);
    }

    public function scopeCurrentYear(Builder $query): Builder
    {
        return $query->whereYear('start_date', date('Y'));
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('start_date', '>', now())
                    ->where('status', 'approved');
    }

    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->where('status', 'approved');
    }

    /**
     * الخصائص المحسوبة
     */
    public function getDurationDisplayAttribute(): string
    {
        if ($this->day_type === 'full_day') {
            return $this->total_days . ' ' . ($this->total_days == 1 ? 'day' : 'days');
        } elseif ($this->day_type === 'half_day') {
            return '0.5 day';
        } else {
            return '0.25 day';
        }
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'pending' => 'badge-warning',
            'approved' => 'badge-success',
            'rejected' => 'badge-danger',
            'cancelled' => 'badge-secondary',
            default => 'badge-light'
        };
    }

    public function getCanBeEditedAttribute(): bool
    {
        return $this->status === 'pending' && $this->start_date->isFuture();
    }

    public function getCanBeCancelledAttribute(): bool
    {
        return in_array($this->status, ['pending', 'approved']) && $this->start_date->isFuture();
    }

    public function getIsCurrentAttribute(): bool
    {
        return $this->start_date->lte(now()) && $this->end_date->gte(now()) && $this->status === 'approved';
    }

    public function getDaysUntilStartAttribute(): int
    {
        return $this->start_date->diffInDays(now(), false);
    }

    /**
     * وظائف مساعدة
     */
    public function calculateTotalDays(): int
    {
        if (!$this->start_date || !$this->end_date) {
            return 0;
        }

        $start = Carbon::parse($this->start_date);
        $end = Carbon::parse($this->end_date);
        
        // حساب أيام العمل (استثناء الجمعة والسبت)
        $totalDays = 0;
        $currentDate = $start->copy();
        
        while ($currentDate->lte($end)) {
            if (!$currentDate->isFriday() && !$currentDate->isSaturday()) {
                if ($this->day_type === 'full_day') {
                    $totalDays += 1;
                } elseif ($this->day_type === 'half_day') {
                    $totalDays += 0.5;
                } else { // quarter_day
                    $totalDays += 0.25;
                }
            }
            $currentDate->addDay();
        }

        return (int) ceil($totalDays);
    }

    public function approve(int $userId, string $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
            'admin_notes' => $notes,
        ]);

        // إضافة تعليق تلقائي
        $this->comments()->create([
            'user_id' => $userId,
            'tenant_id' => $this->tenant_id,
            'comment' => $notes ?: 'Leave request approved.',
            'comment_type' => 'approval',
            'is_system_generated' => true,
            'system_action' => 'approved',
        ]);
    }

    public function reject(int $userId, string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'rejected_by' => $userId,
            'rejected_at' => now(),
            'rejection_reason' => $reason,
        ]);

        // إضافة تعليق تلقائي
        $this->comments()->create([
            'user_id' => $userId,
            'tenant_id' => $this->tenant_id,
            'comment' => 'Leave request rejected: ' . $reason,
            'comment_type' => 'rejection',
            'is_system_generated' => true,
            'system_action' => 'rejected',
        ]);
    }

    public function cancel(int $userId, string $reason): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_by' => $userId,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        // إضافة تعليق تلقائي
        $this->comments()->create([
            'user_id' => $userId,
            'tenant_id' => $this->tenant_id,
            'comment' => 'Leave request cancelled: ' . $reason,
            'comment_type' => 'cancellation',
            'is_system_generated' => true,
            'system_action' => 'cancelled',
        ]);
    }

    /**
     * الحصول على أنواع الإجازات
     */
    public static function getLeaveTypes(): array
    {
        return [
            'annual' => 'Annual Leave',
            'sick' => 'Sick Leave',
            'maternity' => 'Maternity Leave',
            'paternity' => 'Paternity Leave',
            'emergency' => 'Emergency Leave',
            'bereavement' => 'Bereavement Leave',
            'study' => 'Study Leave',
            'pilgrimage' => 'Pilgrimage Leave',
            'unpaid' => 'Unpaid Leave',
            'other' => 'Other'
        ];
    }

    /**
     * الحصول على حالات الإجازة
     */
    public static function getStatuses(): array
    {
        return [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled'
        ];
    }

    /**
     * الحصول على أنواع اليوم
     */
    public static function getDayTypes(): array
    {
        return [
            'full_day' => 'Full Day',
            'half_day' => 'Half Day',
            'quarter_day' => 'Quarter Day'
        ];
    }
}
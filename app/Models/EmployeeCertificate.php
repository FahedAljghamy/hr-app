<?php

/**
 * Author: Eng.Fahed
 * Employee Certificate Model - HR System
 * نموذج شهادات الموظفين
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class EmployeeCertificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'tenant_id', 'requested_by', 'certificate_type', 'purpose',
        'additional_details', 'special_requirements', 'medical_start_date', 'medical_end_date',
        'medical_diagnosis', 'doctor_name', 'hospital_name', 'status', 'rejection_reason',
        'admin_notes', 'processed_by', 'processed_at', 'completed_at', 'certificate_file',
        'certificate_number', 'priority'
    ];

    protected $casts = [
        'medical_start_date' => 'date',
        'medical_end_date' => 'date',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

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

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scopes
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', 'approved');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeUrgent(Builder $query): Builder
    {
        return $query->where('priority', 'urgent');
    }

    public function scopeForEmployee(Builder $query, int $employeeId): Builder
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Accessors & Mutators
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'badge-warning',
            'approved' => 'badge-info',
            'completed' => 'badge-success',
            'rejected' => 'badge-danger',
            default => 'badge-secondary'
        };
    }

    public function getPriorityBadgeClassAttribute(): string
    {
        return match ($this->priority) {
            'urgent' => 'badge-danger',
            'high' => 'badge-warning',
            'normal' => 'badge-info',
            default => 'badge-secondary'
        };
    }

    public function getTypeDisplayNameAttribute(): string
    {
        return match ($this->certificate_type) {
            'salary_certificate' => 'Salary Certificate',
            'employment_certificate' => 'Employment Certificate',
            'experience_certificate' => 'Experience Certificate',
            'medical_leave_certificate' => 'Medical Leave Certificate',
            default => 'Unknown Certificate'
        };
    }

    public function getProcessingTimeAttribute(): ?string
    {
        if (!$this->processed_at) {
            return null;
        }

        return $this->created_at->diffForHumans($this->processed_at, true);
    }

    /**
     * Helper Methods
     */
    public function canBeProcessed(): bool
    {
        return $this->status === 'pending';
    }

    public function canBeCompleted(): bool
    {
        return $this->status === 'approved';
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'approved']);
    }

    public function isOverdue(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $maxProcessingDays = match ($this->priority) {
            'urgent' => 1,
            'high' => 2,
            'normal' => 5,
            default => 7
        };

        return $this->created_at->addDays($maxProcessingDays)->isPast();
    }

    /**
     * Actions
     */
    public function approve(int $userId, ?string $notes = null): void
    {
        $this->update([
            'status' => 'approved',
            'processed_by' => $userId,
            'processed_at' => now(),
            'admin_notes' => $notes,
        ]);

        // إرسال إشعار للموظف
        EmployeeNotification::create([
            'employee_id' => $this->employee_id,
            'tenant_id' => $this->tenant_id,
            'created_by' => $userId,
            'type' => 'certificate_approved',
            'title' => 'Certificate Request Approved',
            'message' => "Your {$this->type_display_name} request has been approved and is being processed.",
            'priority' => 'normal',
            'action_url' => route('employee-dashboard.certificates'),
            'action_text' => 'View Certificate'
        ]);
    }

    public function reject(int $userId, string $reason): void
    {
        $this->update([
            'status' => 'rejected',
            'processed_by' => $userId,
            'processed_at' => now(),
            'rejection_reason' => $reason,
        ]);

        // إرسال إشعار للموظف
        EmployeeNotification::create([
            'employee_id' => $this->employee_id,
            'tenant_id' => $this->tenant_id,
            'created_by' => $userId,
            'type' => 'certificate_rejected',
            'title' => 'Certificate Request Rejected',
            'message' => "Your {$this->type_display_name} request has been rejected. Reason: {$reason}",
            'priority' => 'high',
            'action_url' => route('employee-dashboard.certificates'),
            'action_text' => 'View Details'
        ]);
    }

    public function complete(int $userId, string $certificateFile, ?string $certificateNumber = null): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'certificate_file' => $certificateFile,
            'certificate_number' => $certificateNumber,
        ]);

        // إرسال إشعار للموظف
        EmployeeNotification::create([
            'employee_id' => $this->employee_id,
            'tenant_id' => $this->tenant_id,
            'created_by' => $userId,
            'type' => 'certificate_ready',
            'title' => 'Certificate Ready for Download',
            'message' => "Your {$this->type_display_name} is ready for download.",
            'priority' => 'high',
            'requires_action' => true,
            'action_url' => route('employee-dashboard.certificates'),
            'action_text' => 'Download Certificate'
        ]);
    }

    /**
     * Static Methods
     */
    public static function getCertificateTypes(): array
    {
        return [
            'salary_certificate' => 'Salary Certificate',
            'employment_certificate' => 'Employment Certificate',
            'experience_certificate' => 'Experience Certificate',
            'medical_leave_certificate' => 'Medical Leave Certificate'
        ];
    }

    public static function getPriorities(): array
    {
        return [
            'normal' => 'Normal',
            'high' => 'High',
            'urgent' => 'Urgent'
        ];
    }

    public static function getStatuses(): array
    {
        return [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'completed' => 'Completed',
            'rejected' => 'Rejected'
        ];
    }
}
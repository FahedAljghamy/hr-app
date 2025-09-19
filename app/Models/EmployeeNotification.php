<?php

/**
 * Author: Eng.Fahed
 * Employee Notification Model - HR System
 * نموذج إشعارات الموظفين
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class EmployeeNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id', 'tenant_id', 'created_by', 'type', 'title', 'message',
        'data', 'priority', 'is_read', 'read_at', 'is_important', 'requires_action',
        'action_url', 'action_text', 'expires_at', 'remind_at'
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'is_important' => 'boolean',
        'requires_action' => 'boolean',
        'read_at' => 'datetime',
        'expires_at' => 'datetime',
        'remind_at' => 'datetime',
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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scopes
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    public function scopeRead(Builder $query): Builder
    {
        return $query->where('is_read', true);
    }

    public function scopeImportant(Builder $query): Builder
    {
        return $query->where('is_important', true);
    }

    public function scopeRequiresAction(Builder $query): Builder
    {
        return $query->where('requires_action', true);
    }

    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeForEmployee(Builder $query, int $employeeId): Builder
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Accessors & Mutators
     */
    public function getPriorityBadgeClassAttribute(): string
    {
        return match ($this->priority) {
            'urgent' => 'badge-danger',
            'high' => 'badge-warning',
            'normal' => 'badge-info',
            'low' => 'badge-secondary',
            default => 'badge-secondary'
        };
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->type) {
            'document_expiry' => 'badge-warning',
            'contract_expiry' => 'badge-danger',
            'leave_approved' => 'badge-success',
            'leave_rejected' => 'badge-danger',
            'payroll_ready' => 'badge-info',
            'certificate_ready' => 'badge-success',
            'system_announcement' => 'badge-primary',
            'birthday_reminder' => 'badge-pink',
            'work_anniversary' => 'badge-purple',
            'training_reminder' => 'badge-info',
            'meeting_reminder' => 'badge-warning',
            default => 'badge-secondary'
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'document_expiry' => 'fas fa-file-alt',
            'contract_expiry' => 'fas fa-file-contract',
            'leave_approved' => 'fas fa-check-circle',
            'leave_rejected' => 'fas fa-times-circle',
            'payroll_ready' => 'fas fa-money-bill-wave',
            'certificate_ready' => 'fas fa-certificate',
            'system_announcement' => 'fas fa-bullhorn',
            'birthday_reminder' => 'fas fa-birthday-cake',
            'work_anniversary' => 'fas fa-award',
            'training_reminder' => 'fas fa-graduation-cap',
            'meeting_reminder' => 'fas fa-calendar-check',
            default => 'fas fa-bell'
        };
    }

    public function getTypeDisplayNameAttribute(): string
    {
        return match ($this->type) {
            'document_expiry' => 'Document Expiry',
            'contract_expiry' => 'Contract Expiry',
            'leave_approved' => 'Leave Approved',
            'leave_rejected' => 'Leave Rejected',
            'payroll_ready' => 'Payroll Ready',
            'certificate_ready' => 'Certificate Ready',
            'system_announcement' => 'System Announcement',
            'birthday_reminder' => 'Birthday Reminder',
            'work_anniversary' => 'Work Anniversary',
            'training_reminder' => 'Training Reminder',
            'meeting_reminder' => 'Meeting Reminder',
            'general' => 'General',
            default => 'Notification'
        };
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Helper Methods
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
    }

    public function markAsUnread(): void
    {
        $this->update([
            'is_read' => false,
            'read_at' => null
        ]);
    }

    public function shouldRemind(): bool
    {
        return $this->remind_at && $this->remind_at->isPast() && !$this->is_read;
    }

    /**
     * Static Methods
     */
    public static function createForEmployee(
        int $employeeId,
        int $tenantId,
        string $type,
        string $title,
        string $message,
        array $options = []
    ): self {
        return self::create(array_merge([
            'employee_id' => $employeeId,
            'tenant_id' => $tenantId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
        ], $options));
    }

    public static function getNotificationTypes(): array
    {
        return [
            'document_expiry' => 'Document Expiry',
            'contract_expiry' => 'Contract Expiry',
            'leave_approved' => 'Leave Approved',
            'leave_rejected' => 'Leave Rejected',
            'payroll_ready' => 'Payroll Ready',
            'certificate_ready' => 'Certificate Ready',
            'system_announcement' => 'System Announcement',
            'birthday_reminder' => 'Birthday Reminder',
            'work_anniversary' => 'Work Anniversary',
            'training_reminder' => 'Training Reminder',
            'meeting_reminder' => 'Meeting Reminder',
            'general' => 'General'
        ];
    }

    public static function getPriorities(): array
    {
        return [
            'low' => 'Low',
            'normal' => 'Normal',
            'high' => 'High',
            'urgent' => 'Urgent'
        ];
    }

    /**
     * Bulk Operations
     */
    public static function markAllAsReadForEmployee(int $employeeId): void
    {
        self::where('employee_id', $employeeId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }

    public static function deleteExpiredForEmployee(int $employeeId, int $days = 30): void
    {
        self::where('employee_id', $employeeId)
            ->where('expires_at', '<', now()->subDays($days))
            ->delete();
    }
}
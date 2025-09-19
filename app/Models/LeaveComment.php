<?php

/**
 * Author: Eng.Fahed
 * Leave Comment Model - HR System
 * نموذج تعليقات الإجازات - نظام شات داخلي
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class LeaveComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'leave_id', 'user_id', 'tenant_id', 'comment', 'comment_type',
        'attachments', 'is_edited', 'edited_at', 'edit_history',
        'is_internal', 'notify_employee', 'notify_manager', 'read_by',
        'visibility', 'is_system_generated', 'system_action'
    ];

    protected $casts = [
        'attachments' => 'array',
        'edit_history' => 'array',
        'read_by' => 'array',
        'is_edited' => 'boolean',
        'edited_at' => 'datetime',
        'is_internal' => 'boolean',
        'notify_employee' => 'boolean',
        'notify_manager' => 'boolean',
        'is_system_generated' => 'boolean',
    ];

    protected $appends = [
        'formatted_created_at',
        'can_be_edited',
        'can_be_deleted',
        'is_read_by_current_user'
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

        static::creating(function ($comment) {
            if (!$comment->tenant_id && auth()->check()) {
                $comment->tenant_id = auth()->user()->tenant_id;
            }
            
            if (!$comment->user_id && auth()->check()) {
                $comment->user_id = auth()->id();
            }
        });
    }

    /**
     * العلاقات
     */
    public function leave(): BelongsTo
    {
        return $this->belongsTo(Leave::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * نطاقات الاستعلام
     */
    public function scopePublic(Builder $query): Builder
    {
        return $query->where('visibility', 'public');
    }

    public function scopeInternal(Builder $query): Builder
    {
        return $query->where('is_internal', true);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('comment_type', $type);
    }

    public function scopeSystemGenerated(Builder $query): Builder
    {
        return $query->where('is_system_generated', true);
    }

    public function scopeUserGenerated(Builder $query): Builder
    {
        return $query->where('is_system_generated', false);
    }

    /**
     * الخصائص المحسوبة
     */
    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    public function getCanBeEditedAttribute(): bool
    {
        return !$this->is_system_generated && 
               $this->user_id === auth()->id() && 
               $this->created_at->diffInMinutes(now()) <= 30; // يمكن التعديل خلال 30 دقيقة
    }

    public function getCanBeDeletedAttribute(): bool
    {
        return !$this->is_system_generated && 
               $this->user_id === auth()->id() && 
               $this->created_at->diffInHours(now()) <= 2; // يمكن الحذف خلال ساعتين
    }

    public function getIsReadByCurrentUserAttribute(): bool
    {
        $readBy = $this->read_by ?? [];
        return in_array(auth()->id(), $readBy);
    }

    /**
     * وظائف مساعدة
     */
    public function markAsRead(int $userId): void
    {
        $readBy = $this->read_by ?? [];
        
        if (!in_array($userId, $readBy)) {
            $readBy[] = $userId;
            $this->update(['read_by' => $readBy]);
        }
    }

    public function editComment(string $newComment): void
    {
        $editHistory = $this->edit_history ?? [];
        
        // حفظ النسخة القديمة في التاريخ
        $editHistory[] = [
            'old_comment' => $this->comment,
            'edited_at' => now()->toISOString(),
            'edited_by' => auth()->id()
        ];

        $this->update([
            'comment' => $newComment,
            'is_edited' => true,
            'edited_at' => now(),
            'edit_history' => $editHistory
        ]);
    }

    /**
     * الحصول على أنواع التعليقات
     */
    public static function getCommentTypes(): array
    {
        return [
            'general' => 'General Comment',
            'approval' => 'Approval',
            'rejection' => 'Rejection',
            'modification' => 'Modification',
            'cancellation' => 'Cancellation'
        ];
    }

    /**
     * الحصول على مستويات الرؤية
     */
    public static function getVisibilityLevels(): array
    {
        return [
            'public' => 'Public',
            'private' => 'Private',
            'managers_only' => 'Managers Only'
        ];
    }
}
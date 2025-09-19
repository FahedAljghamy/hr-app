<?php

/**
 * Author: Eng.Fahed
 * Branch Model for HR System
 * نموذج الفروع لنظام الموارد البشرية
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Scopes\TenantScope;

class Branch extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address', 
        'location',
        'working_hours',
        'phone',
        'email',
        'manager_name',
        'is_active',
        'description',
        'tenant_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'working_hours' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }

    /**
     * Get the tenant that owns the branch.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get formatted working hours
     */
    public function getFormattedWorkingHoursAttribute(): string
    {
        if (!$this->working_hours) {
            return 'Not specified';
        }

        $hours = $this->working_hours;
        return ($hours['start'] ?? '09:00') . ' - ' . ($hours['end'] ?? '17:00');
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return $this->is_active ? 'badge-success' : 'badge-secondary';
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }
}
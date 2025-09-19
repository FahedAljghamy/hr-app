<?php

/**
 * Author: Eng.Fahed
 * Company Settings Model for HR System
 * نموذج إعدادات الشركة لنظام الموارد البشرية
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Scopes\TenantScope;

class CompanySetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_name',
        'logo_path',
        'email',
        'phone',
        'address',
        'website',
        'official_working_hours',
        'timezone',
        'currency',
        'description',
        'social_media',
        'tax_number',
        'registration_number',
        'tenant_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'official_working_hours' => 'array',
        'social_media' => 'array',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }

    /**
     * Get the tenant that owns the company settings.
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
        if (!$this->official_working_hours) {
            return 'Not specified';
        }

        $hours = $this->official_working_hours;
        return ($hours['start'] ?? '09:00') . ' - ' . ($hours['end'] ?? '17:00');
    }

    /**
     * Get logo URL
     */
    public function getLogoUrlAttribute(): string
    {
        if ($this->logo_path && file_exists(public_path($this->logo_path))) {
            return asset($this->logo_path);
        }
        
        return asset('assets/img/default-logo.png');
    }
}
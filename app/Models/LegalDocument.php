<?php

/**
 * Author: Eng.Fahed
 * Legal Document Model for HR System
 * نموذج المستندات القانونية لنظام الموارد البشرية
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Scopes\TenantScope;
use Carbon\Carbon;

class LegalDocument extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'document_type',
        'document_number',
        'document_name',
        'description',
        'issue_date',
        'expiry_date',
        'renewal_date',
        'issuing_authority',
        'issuing_location',
        'file_path',
        'file_type',
        'file_size',
        'status',
        'is_mandatory',
        'renewal_reminder_days',
        'renewal_cost',
        'currency',
        'notes',
        'metadata',
        'tenant_id',
        'branch_id',
        'company_setting_id',
        'last_notification_sent',
        'renewed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'renewal_date' => 'date',
        'is_mandatory' => 'boolean',
        'renewal_cost' => 'decimal:2',
        'metadata' => 'array',
        'last_notification_sent' => 'datetime',
        'renewed_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new TenantScope);
    }

    /**
     * Get the tenant that owns the document.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Get the branch that owns the document.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the company setting that owns the document.
     */
    public function companySetting(): BelongsTo
    {
        return $this->belongsTo(CompanySetting::class);
    }

    /**
     * UAE Legal Document Types
     */
    public static function getUAEDocumentTypes(): array
    {
        return [
            // الرخص الأساسية
            'trade_license' => 'Trade License (الرخصة التجارية)',
            'commercial_registration' => 'Commercial Registration (السجل التجاري)',
            'establishment_card' => 'Establishment Card (بطاقة المنشأة)',
            
            // الضرائب والرسوم
            'vat_certificate' => 'VAT Registration Certificate (شهادة تسجيل ضريبة القيمة المضافة)',
            'tax_registration' => 'Tax Registration Number (رقم التسجيل الضريبي)',
            
            // تراخيص العمل
            'labor_permit' => 'Labor Permit (تصريح العمل)',
            'work_permit' => 'Work Permit (إذن العمل)',
            'employment_visa' => 'Employment Visa (تأشيرة العمل)',
            
            // التأمينات والحماية
            'health_insurance' => 'Health Insurance Certificate (شهادة التأمين الصحي)',
            'liability_insurance' => 'Liability Insurance (تأمين المسؤولية)',
            'workers_compensation' => 'Workers Compensation Insurance (تأمين إصابات العمل)',
            
            // البيئة والسلامة
            'environmental_permit' => 'Environmental Permit (تصريح بيئي)',
            'safety_certificate' => 'Safety Certificate (شهادة السلامة)',
            'fire_safety' => 'Fire Safety Certificate (شهادة السلامة من الحريق)',
            
            // التراخيص المهنية
            'professional_license' => 'Professional License (الرخصة المهنية)',
            'industry_license' => 'Industry License (رخصة صناعية)',
            'import_export_license' => 'Import/Export License (رخصة الاستيراد والتصدير)',
            
            // مستندات أخرى
            'municipality_permit' => 'Municipality Permit (تصريح البلدية)',
            'chamber_membership' => 'Chamber of Commerce Membership (عضوية غرفة التجارة)',
            'quality_certificate' => 'Quality Certificate (شهادة الجودة)',
        ];
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'active' => 'badge-success',
            'expired' => 'badge-danger',
            'pending_renewal' => 'badge-warning',
            'cancelled' => 'badge-secondary',
            default => 'badge-info'
        };
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'active' => 'Active',
            'expired' => 'Expired',
            'pending_renewal' => 'Pending Renewal',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    /**
     * Check if document is expiring soon
     */
    public function getIsExpiringSoonAttribute(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }
        
        return $this->expiry_date->diffInDays(now()) <= $this->renewal_reminder_days;
    }

    /**
     * Get days until expiry
     */
    public function getDaysUntilExpiryAttribute(): int
    {
        if (!$this->expiry_date) {
            return 0;
        }
        
        return max(0, $this->expiry_date->diffInDays(now()));
    }

    /**
     * Get formatted file size
     */
    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'N/A';
        }
        
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Scope for expiring documents
     */
    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
                    ->where('expiry_date', '>', now())
                    ->where('status', 'active');
    }

    /**
     * Scope for expired documents
     */
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', now())
                    ->where('status', '!=', 'cancelled');
    }

    /**
     * Scope for mandatory documents
     */
    public function scopeMandatory($query)
    {
        return $query->where('is_mandatory', true);
    }
}
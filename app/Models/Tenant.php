<?php

/**
 * Author: Eng.Fahed
 * Tenant Model for HR System Multi-Tenant Architecture
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'domain',
        'subdomain',
        'database_name',
        'contact_email',
        'contact_phone',
        'company_name',
        'address',
        'logo',
        'status',
        'subscription_plan',
        'subscription_start_date',
        'subscription_end_date',
        'monthly_fee',
        'max_employees',
        'features',
        'settings',
    ];

    protected $casts = [
        'subscription_start_date' => 'date',
        'subscription_end_date' => 'date',
        'monthly_fee' => 'decimal:2',
        'features' => 'array',
        'settings' => 'array',
    ];

    /**
     * Get all users belonging to this tenant
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if tenant is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if tenant subscription is valid
     */
    public function isSubscriptionValid(): bool
    {
        return $this->subscription_end_date >= now();
    }

    /**
     * Get tenant by domain or subdomain
     */
    public static function findByDomain(string $domain): ?self
    {
        return static::where('domain', $domain)
                    ->orWhere('subdomain', $domain)
                    ->first();
    }
}

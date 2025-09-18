<?php

/**
 * Author: Eng.Fahed
 * TenantScope for HR System Multi-Tenant Architecture
 * Automatically filters data by tenant_id
 */

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Session;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Skip tenant filtering for super admin users
        if (auth()->check() && auth()->user()->isSuperAdmin()) {
            return;
        }

        // Get tenant_id from session or request
        $tenantId = Session::get('tenant_id') ?? request('tenant_id');

        if ($tenantId) {
            $builder->where('tenant_id', $tenantId);
        }
    }

    /**
     * Extend the query builder with the needed functions.
     */
    public function extend(Builder $builder): void
    {
        $builder->macro('withoutTenantScope', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });

        $builder->macro('withTenantScope', function (Builder $builder, $tenantId) {
            return $builder->withoutGlobalScope($this)->where('tenant_id', $tenantId);
        });
    }
}

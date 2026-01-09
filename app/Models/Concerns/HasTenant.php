<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Tenant;

trait HasTenant
{
    /**
     * Boot the trait and apply global scope
     */
    protected static function bootHasTenant()
    {
        // Apply global scope to filter by tenant
        static::addGlobalScope('tenant', function (Builder $builder) {
            $user = auth()->user();
            
            // Super admin can see all tenants
            if ($user && $user->hasRole('super_admin')) {
                return;
            }
            
            // Tenant users can only see their tenant's data
            if ($user && $user->tenant_id) {
                $builder->where('tenant_id', $user->tenant_id);
            }
        });
    }

    /**
     * Initialize the trait
     */
    public function initializeHasTenant()
    {
        // This method is called when the trait is used
    }

    /**
     * Get the tenant that owns the model
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Scope a query to only include records for a specific tenant
     */
    public function scopeForTenant(Builder $query, $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }
}

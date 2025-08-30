<?php

declare(strict_types=1);

namespace App\Domain\Tenant\Models;

use App\Domain\Module\Models\Module;
use App\Domain\User\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Tenant extends Model
{
    use HasFactory;
    use HasUuids;
    use LogsActivity;

    protected $fillable = [
        'name',
        'domain',
        'plan',
        'feature_overrides',
        'settings',
        'is_active',
        'trial_ends_at',
        'subscription_ends_at',
    ];

    protected $casts = [
        'feature_overrides' => 'array',
        'settings' => 'array',
        'is_active' => 'boolean',
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'domain', 'plan', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function modules(): BelongsToMany
    {
        return $this->belongsToMany(Module::class, 'tenant_modules')
            ->withPivot(['enabled', 'limits'])
            ->withTimestamps();
    }

    public function hasFeature(string $feature): bool
    {
        // Check feature overrides first
        if (isset($this->feature_overrides[$feature])) {
            return $this->feature_overrides[$feature];
        }

        // Check if module is enabled for this tenant
        $module = $this->modules()->where('key', $feature)->first();
        
        if (!$module) {
            return false;
        }

        return $module->pivot->enabled ?? false;
    }

    public function getFeatureLimit(string $feature): ?int
    {
        $module = $this->modules()->where('key', $feature)->first();
        
        if (!$module) {
            return null;
        }

        return $module->pivot->limits[$feature] ?? null;
    }

    public function isOnTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function hasActiveSubscription(): bool
    {
        return !$this->subscription_ends_at || $this->subscription_ends_at->isFuture();
    }

    public function canAccessFeature(string $feature): bool
    {
        return $this->is_active && 
               $this->hasFeature($feature) && 
               ($this->isOnTrial() || $this->hasActiveSubscription());
    }

    public function getPlanFeatures(): array
    {
        $planFeatures = config("plans.{$this->plan}.features", []);
        
        // Merge with tenant-specific overrides
        if ($this->feature_overrides) {
            $planFeatures = array_merge($planFeatures, $this->feature_overrides);
        }

        return $planFeatures;
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Module\Models;

use App\Domain\Tenant\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Module extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'key',
        'label',
        'description',
        'icon',
        'defaults',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'defaults' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['key', 'label', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_modules')
            ->withPivot(['enabled', 'limits'])
            ->withTimestamps();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('label');
    }

    public function getDefaultLimit(string $feature): ?int
    {
        return $this->defaults['limits'][$feature] ?? null;
    }

    public function getDefaultSettings(): array
    {
        return $this->defaults['settings'] ?? [];
    }
}

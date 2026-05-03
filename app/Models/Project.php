<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'owner_id',
        'status',
        'priority',
        'plan_start_date',
        'plan_end_date',
        'actual_start_date',
        'actual_end_date',
        'budget',
        'progress',
    ];

    protected function casts(): array
    {
        return [
            'plan_start_date' => 'date',
            'plan_end_date' => 'date',
            'actual_start_date' => 'date',
            'actual_end_date' => 'date',
            'budget' => 'decimal:2',
            'progress' => 'decimal:2',
        ];
    }

    public const STATUSES = [
        'planning' => 'Planning',
        'active' => 'Active',
        'on_hold' => 'On Hold',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public const PRIORITIES = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'critical' => 'Critical',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function sprints(): HasMany
    {
        return $this->hasMany(Sprint::class)->orderBy('sort_order');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function calculateProgress(): float
    {
        $tasks = $this->tasks()->whereNull('parent_id')->get();
        if ($tasks->isEmpty()) {
            return 0;
        }
        return round($tasks->avg('progress'), 2);
    }

    public function recalculateProgress(): void
    {
        $this->update(['progress' => $this->calculateProgress()]);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'planning' => 'bg-blue-100 text-blue-800',
            'active' => 'bg-green-100 text-green-800',
            'on_hold' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-gray-100 text-gray-800',
            'cancelled' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}

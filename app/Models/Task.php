<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'sprint_id',
        'parent_id',
        'assigned_to',
        'created_by',
        'title',
        'description',
        'status',
        'priority',
        'type',
        'story_points',
        'plan_start_date',
        'plan_end_date',
        'actual_start_date',
        'actual_end_date',
        'progress',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'plan_start_date' => 'date',
            'plan_end_date' => 'date',
            'actual_start_date' => 'date',
            'actual_end_date' => 'date',
            'progress' => 'decimal:2',
        ];
    }

    public const STATUSES = [
        'backlog' => 'Backlog',
        'todo' => 'To Do',
        'in_progress' => 'In Progress',
        'in_review' => 'In Review',
        'done' => 'Done',
    ];

    public const PRIORITIES = [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High',
        'critical' => 'Critical',
    ];

    public const TYPES = [
        'epic' => 'Epic',
        'story' => 'Story',
        'task' => 'Task',
        'bug' => 'Bug',
        'subtask' => 'Sub-task',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function sprint(): BelongsTo
    {
        return $this->belongsTo(Sprint::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class)->orderByDesc('created_at');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'backlog' => 'bg-gray-100 text-gray-800',
            'todo' => 'bg-blue-100 text-blue-800',
            'in_progress' => 'bg-yellow-100 text-yellow-800',
            'in_review' => 'bg-purple-100 text-purple-800',
            'done' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getPriorityBadgeAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'bg-gray-100 text-gray-600',
            'medium' => 'bg-blue-100 text-blue-700',
            'high' => 'bg-orange-100 text-orange-700',
            'critical' => 'bg-red-100 text-red-700',
            default => 'bg-gray-100 text-gray-600',
        };
    }
}

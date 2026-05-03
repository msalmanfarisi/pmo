<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class TaskReportExport implements FromArray, WithHeadings, WithTitle
{
    public function __construct(protected Project $project) {}

    public function array(): array
    {
        $this->project->load(['tasks.assignee', 'tasks.sprint']);

        return $this->project->tasks->map(function ($task) {
            return [
                $task->id,
                $task->title,
                $task->type,
                $task->status,
                $task->priority,
                $task->assignee?->name ?? 'Unassigned',
                $task->sprint?->name ?? 'No Sprint',
                $task->story_points ?? '',
                $task->plan_start_date?->format('Y-m-d') ?? '',
                $task->plan_end_date?->format('Y-m-d') ?? '',
                $task->actual_start_date?->format('Y-m-d') ?? '',
                $task->actual_end_date?->format('Y-m-d') ?? '',
                $task->progress . '%',
            ];
        })->toArray();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Type',
            'Status',
            'Priority',
            'Assignee',
            'Sprint',
            'Story Points',
            'Plan Start',
            'Plan End',
            'Actual Start',
            'Actual End',
            'Progress',
        ];
    }

    public function title(): string
    {
        return 'Tasks';
    }
}

<?php

namespace App\Exports;

use App\Models\Project;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProjectReportExport implements FromArray, WithHeadings, WithTitle
{
    public function __construct(protected Project $project) {}

    public function array(): array
    {
        $this->project->load(['tasks.assignee', 'sprints']);
        $rows = [];

        $rows[] = [
            $this->project->name,
            $this->project->code,
            $this->project->status,
            $this->project->priority,
            $this->project->owner->name ?? '',
            $this->project->plan_start_date?->format('Y-m-d') ?? '',
            $this->project->plan_end_date?->format('Y-m-d') ?? '',
            $this->project->progress . '%',
            $this->project->tasks->count(),
            $this->project->tasks->where('status', 'done')->count(),
        ];

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Project Name',
            'Code',
            'Status',
            'Priority',
            'Owner',
            'Plan Start',
            'Plan End',
            'Progress',
            'Total Tasks',
            'Completed Tasks',
        ];
    }

    public function title(): string
    {
        return 'Project Summary';
    }
}

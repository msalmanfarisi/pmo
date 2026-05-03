<?php

namespace App\Http\Controllers;

use App\Exports\ProjectReportExport;
use App\Exports\TaskReportExport;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $query = Project::query();

        if (!$user->hasRole('admin')) {
            $projectIds = $user->projects()->pluck('projects.id')
                ->merge($user->ownedProjects()->pluck('id'))
                ->unique();
            $query->whereIn('id', $projectIds);
        }

        $projects = $query->with(['owner', 'tasks'])->withCount('tasks')->get();

        return view('reports.index', compact('projects'));
    }

    public function projectPdf(Project $project)
    {
        $project->load(['owner', 'members', 'sprints.tasks.assignee', 'tasks' => function ($q) {
            $q->orderBy('sort_order');
        }]);

        $pdf = Pdf::loadView('reports.project-pdf', compact('project'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download("project-{$project->code}-report.pdf");
    }

    public function projectExcel(Project $project)
    {
        return Excel::download(new ProjectReportExport($project), "project-{$project->code}-report.xlsx");
    }

    public function tasksPdf(Request $request, Project $project)
    {
        $tasks = $project->tasks()
            ->with(['assignee', 'sprint'])
            ->orderBy('status')
            ->orderBy('priority', 'desc')
            ->get();

        $pdf = Pdf::loadView('reports.tasks-pdf', compact('project', 'tasks'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->download("project-{$project->code}-tasks.pdf");
    }

    public function tasksExcel(Project $project)
    {
        return Excel::download(new TaskReportExport($project), "project-{$project->code}-tasks.xlsx");
    }
}

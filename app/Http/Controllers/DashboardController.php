<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $projectIds = $user->projects()->pluck('projects.id')
            ->merge($user->ownedProjects()->pluck('id'))
            ->unique();

        $totalProjects = Project::whereIn('id', $projectIds)->count();
        $activeProjects = Project::whereIn('id', $projectIds)->where('status', 'active')->count();
        $myTasks = Task::where('assigned_to', $user->id)->whereNot('status', 'done')->count();
        $overdueTasks = Task::where('assigned_to', $user->id)
            ->whereNot('status', 'done')
            ->whereNotNull('plan_end_date')
            ->where('plan_end_date', '<', now())
            ->count();

        $recentProjects = Project::whereIn('id', $projectIds)
            ->with('owner')
            ->latest()
            ->take(5)
            ->get();

        $upcomingTasks = Task::where('assigned_to', $user->id)
            ->whereNot('status', 'done')
            ->with(['project', 'sprint'])
            ->orderBy('plan_end_date')
            ->take(10)
            ->get();

        $projectStatusCounts = Project::whereIn('id', $projectIds)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('dashboard', compact(
            'totalProjects',
            'activeProjects',
            'myTasks',
            'overdueTasks',
            'recentProjects',
            'upcomingTasks',
            'projectStatusCounts'
        ));
    }
}

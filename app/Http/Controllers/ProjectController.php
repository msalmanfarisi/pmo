<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProjectController extends Controller
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

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $projects = $query->with('owner')
            ->withCount('tasks')
            ->latest()
            ->paginate(12)
            ->appends($request->query());

        return view('projects.index', compact('projects'));
    }

    public function create(): View
    {
        $users = User::where('is_active', true)->orderBy('name')->get();
        return view('projects.create', compact('users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:projects,code', 'alpha_dash:ascii'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'string', 'in:' . implode(',', array_keys(Project::STATUSES))],
            'priority' => ['required', 'string', 'in:' . implode(',', array_keys(Project::PRIORITIES))],
            'plan_start_date' => ['nullable', 'date'],
            'plan_end_date' => ['nullable', 'date', 'after_or_equal:plan_start_date'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'members' => ['nullable', 'array'],
            'members.*' => ['exists:users,id'],
        ]);

        $validated['owner_id'] = Auth::id();
        $project = Project::create($validated);

        if (!empty($validated['members'])) {
            $project->members()->attach($validated['members'], ['role' => 'member']);
        }
        $project->members()->syncWithoutDetaching([Auth::id() => ['role' => 'manager']]);

        ActivityLogService::log($project, 'created', "Project '{$project->name}' was created");

        return redirect()->route('projects.show', $project)->with('success', 'Project created successfully.');
    }

    public function show(Project $project): View
    {
        $this->authorizeProject($project);
        $project->load(['owner', 'members', 'sprints', 'tasks' => function ($q) {
            $q->withCount('comments')->orderBy('sort_order');
        }]);
        $project->recalculateProgress();
        return view('projects.show', compact('project'));
    }

    public function edit(Project $project): View
    {
        $this->authorizeProject($project);
        $users = User::where('is_active', true)->orderBy('name')->get();
        $project->load('members');
        return view('projects.edit', compact('project', 'users'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeProject($project);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:20', 'unique:projects,code,' . $project->id, 'alpha_dash:ascii'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', 'string', 'in:' . implode(',', array_keys(Project::STATUSES))],
            'priority' => ['required', 'string', 'in:' . implode(',', array_keys(Project::PRIORITIES))],
            'plan_start_date' => ['nullable', 'date'],
            'plan_end_date' => ['nullable', 'date', 'after_or_equal:plan_start_date'],
            'actual_start_date' => ['nullable', 'date'],
            'actual_end_date' => ['nullable', 'date', 'after_or_equal:actual_start_date'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'members' => ['nullable', 'array'],
            'members.*' => ['exists:users,id'],
        ]);

        $project->update($validated);

        if (isset($validated['members'])) {
            $members = collect($validated['members'])->mapWithKeys(fn ($id) => [$id => ['role' => 'member']]);
            $members[$project->owner_id] = ['role' => 'manager'];
            $project->members()->sync($members);
        }

        ActivityLogService::log($project, 'updated', "Project '{$project->name}' was updated");

        return redirect()->route('projects.show', $project)->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorizeProject($project);
        ActivityLogService::log($project, 'deleted', "Project '{$project->name}' was deleted");
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }

    public function gantt(Project $project): View
    {
        $this->authorizeProject($project);
        $tasks = $project->tasks()
            ->whereNull('parent_id')
            ->with('children', 'assignee')
            ->orderBy('sort_order')
            ->get();
        return view('projects.gantt', compact('project', 'tasks'));
    }

    public function scurve(Project $project): View
    {
        $this->authorizeProject($project);
        $project->load('tasks');
        return view('projects.scurve', compact('project'));
    }

    public function kanban(Project $project): View
    {
        $this->authorizeProject($project);
        $tasks = $project->tasks()
            ->with(['assignee', 'sprint'])
            ->orderBy('sort_order')
            ->get()
            ->groupBy('status');
        $members = $project->members;
        return view('projects.kanban', compact('project', 'tasks', 'members'));
    }

    protected function authorizeProject(Project $project): void
    {
        $user = Auth::user();
        if ($user->hasRole('admin')) {
            return;
        }
        $isMember = $project->members()->where('user_id', $user->id)->exists();
        $isOwner = $project->owner_id === $user->id;
        if (!$isMember && !$isOwner) {
            abort(403, 'You are not authorized to access this project.');
        }
    }
}

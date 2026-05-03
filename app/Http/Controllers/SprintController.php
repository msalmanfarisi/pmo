<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Sprint;
use App\Services\ActivityLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SprintController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeProject($project);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'goal' => ['nullable', 'string', 'max:2000'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', 'in:' . implode(',', array_keys(Sprint::STATUSES))],
        ]);

        $validated['project_id'] = $project->id;
        $validated['sort_order'] = $project->sprints()->count();
        $sprint = Sprint::create($validated);

        ActivityLogService::log($sprint, 'created', "Sprint '{$sprint->name}' was created");

        return redirect()->route('projects.show', $project)->with('success', 'Sprint created successfully.');
    }

    public function update(Request $request, Project $project, Sprint $sprint): RedirectResponse
    {
        $this->authorizeProject($project);
        abort_if($sprint->project_id !== $project->id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'goal' => ['nullable', 'string', 'max:2000'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', 'in:' . implode(',', array_keys(Sprint::STATUSES))],
        ]);

        $sprint->update($validated);

        ActivityLogService::log($sprint, 'updated', "Sprint '{$sprint->name}' was updated");

        return redirect()->route('projects.show', $project)->with('success', 'Sprint updated successfully.');
    }

    public function destroy(Project $project, Sprint $sprint): RedirectResponse
    {
        $this->authorizeProject($project);
        abort_if($sprint->project_id !== $project->id, 404);

        $sprint->tasks()->update(['sprint_id' => null]);
        ActivityLogService::log($sprint, 'deleted', "Sprint '{$sprint->name}' was deleted");
        $sprint->delete();

        return redirect()->route('projects.show', $project)->with('success', 'Sprint deleted successfully.');
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
            abort(403);
        }
    }
}

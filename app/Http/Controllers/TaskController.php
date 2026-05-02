<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskComment;
use App\Notifications\TaskAssigned;
use App\Services\ActivityLogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function create(Project $project): View
    {
        $this->authorizeProject($project);
        $project->load(['members', 'sprints']);
        $parentTasks = $project->tasks()->whereNull('parent_id')->orderBy('title')->get();
        return view('tasks.create', compact('project', 'parentTasks'));
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeProject($project);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'sprint_id' => ['nullable', 'exists:sprints,id'],
            'parent_id' => ['nullable', 'exists:tasks,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'status' => ['required', 'in:' . implode(',', array_keys(Task::STATUSES))],
            'priority' => ['required', 'in:' . implode(',', array_keys(Task::PRIORITIES))],
            'type' => ['required', 'in:' . implode(',', array_keys(Task::TYPES))],
            'story_points' => ['nullable', 'integer', 'min:0', 'max:100'],
            'plan_start_date' => ['nullable', 'date'],
            'plan_end_date' => ['nullable', 'date', 'after_or_equal:plan_start_date'],
        ]);

        $validated['project_id'] = $project->id;
        $validated['created_by'] = Auth::id();
        $validated['sort_order'] = $project->tasks()->count();

        $task = Task::create($validated);

        ActivityLogService::log($task, 'created', "Task '{$task->title}' was created");

        if ($task->assigned_to && $task->assigned_to !== Auth::id()) {
            try {
                $task->assignee->notify(new TaskAssigned($task));
            } catch (\Exception $e) {
                // Notification failure should not block task creation
            }
        }

        return redirect()->route('projects.show', $project)->with('success', 'Task created successfully.');
    }

    public function show(Project $project, Task $task): View
    {
        $this->authorizeProject($project);
        abort_if($task->project_id !== $project->id, 404);

        $task->load(['assignee', 'creator', 'sprint', 'parent', 'children.assignee', 'comments.user']);
        $project->load(['members', 'sprints']);

        return view('tasks.show', compact('project', 'task'));
    }

    public function update(Request $request, Project $project, Task $task): RedirectResponse
    {
        $this->authorizeProject($project);
        abort_if($task->project_id !== $project->id, 404);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:10000'],
            'sprint_id' => ['nullable', 'exists:sprints,id'],
            'parent_id' => ['nullable', 'exists:tasks,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'status' => ['required', 'in:' . implode(',', array_keys(Task::STATUSES))],
            'priority' => ['required', 'in:' . implode(',', array_keys(Task::PRIORITIES))],
            'type' => ['required', 'in:' . implode(',', array_keys(Task::TYPES))],
            'story_points' => ['nullable', 'integer', 'min:0', 'max:100'],
            'plan_start_date' => ['nullable', 'date'],
            'plan_end_date' => ['nullable', 'date', 'after_or_equal:plan_start_date'],
            'actual_start_date' => ['nullable', 'date'],
            'actual_end_date' => ['nullable', 'date', 'after_or_equal:actual_start_date'],
            'progress' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $oldAssignee = $task->assigned_to;

        if ($validated['status'] === 'done') {
            $validated['progress'] = 100;
            $validated['actual_end_date'] = $validated['actual_end_date'] ?? now()->toDateString();
        }

        if ($validated['status'] === 'in_progress' && $task->status === 'backlog') {
            $validated['actual_start_date'] = $validated['actual_start_date'] ?? now()->toDateString();
        }

        $task->update($validated);
        $project->recalculateProgress();

        ActivityLogService::log($task, 'updated', "Task '{$task->title}' was updated");

        if ($task->assigned_to && $task->assigned_to !== $oldAssignee && $task->assigned_to !== Auth::id()) {
            try {
                $task->assignee->notify(new TaskAssigned($task));
            } catch (\Exception $e) {
                // Notification failure should not block task update
            }
        }

        return redirect()->route('projects.tasks.show', [$project, $task])->with('success', 'Task updated successfully.');
    }

    public function destroy(Project $project, Task $task): RedirectResponse
    {
        $this->authorizeProject($project);
        abort_if($task->project_id !== $project->id, 404);

        ActivityLogService::log($task, 'deleted', "Task '{$task->title}' was deleted");
        $task->delete();
        $project->recalculateProgress();

        return redirect()->route('projects.show', $project)->with('success', 'Task deleted successfully.');
    }

    public function updateStatus(Request $request, Project $project, Task $task): JsonResponse
    {
        $this->authorizeProject($project);
        abort_if($task->project_id !== $project->id, 404);

        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys(Task::STATUSES))],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($validated['status'] === 'done') {
            $validated['progress'] = 100;
            $validated['actual_end_date'] = now()->toDateString();
        }

        if ($validated['status'] === 'in_progress' && !$task->actual_start_date) {
            $validated['actual_start_date'] = now()->toDateString();
        }

        $task->update($validated);
        $project->recalculateProgress();

        return response()->json(['success' => true]);
    }

    public function storeComment(Request $request, Project $project, Task $task): RedirectResponse
    {
        $this->authorizeProject($project);
        abort_if($task->project_id !== $project->id, 404);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $comment = TaskComment::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'body' => strip_tags($validated['body'], '<b><i><u><br><p><ul><ol><li><a><strong><em>'),
        ]);

        ActivityLogService::log($task, 'commented', "Comment added to task '{$task->title}'");

        return redirect()->route('projects.tasks.show', [$project, $task])->with('success', 'Comment added.');
    }

    public function backlog(Project $project): View
    {
        $this->authorizeProject($project);
        $tasks = $project->tasks()
            ->where('status', 'backlog')
            ->with(['assignee', 'sprint'])
            ->orderBy('priority', 'desc')
            ->orderBy('sort_order')
            ->get();
        $sprints = $project->sprints()->where('status', '!=', 'completed')->get();
        return view('tasks.backlog', compact('project', 'tasks', 'sprints'));
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

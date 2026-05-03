@extends('layouts.app')
@section('title', $task->title)

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('projects.show', $project) }}" class="text-sm text-indigo-600 hover:text-indigo-500">&larr; Back to {{ $project->name }}</a>
        <div class="flex gap-2">
            <form method="POST" action="{{ route('projects.tasks.destroy', [$project, $task]) }}" onsubmit="return confirm('Are you sure you want to delete this task?')">
                @csrf @method('DELETE')
                <button type="submit" class="rounded-lg border border-red-300 bg-white px-3 py-2 text-sm font-medium text-red-700 shadow-sm hover:bg-red-50">Delete</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Task Details Form --}}
            <form method="POST" action="{{ route('projects.tasks.update', [$project, $task]) }}">
                @csrf @method('PUT')
                <div class="rounded-xl bg-white shadow ring-1 ring-gray-200">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-base font-semibold text-gray-900">Task Details</h3>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $task->title) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description', $task->description) }}</textarea>
                        </div>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                                <select name="type" id="type" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @foreach(\App\Models\Task::TYPES as $key => $label)
                                        <option value="{{ $key }}" {{ old('type', $task->type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                <select name="status" id="status" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @foreach(\App\Models\Task::STATUSES as $key => $label)
                                        <option value="{{ $key }}" {{ old('status', $task->status) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700">Priority</label>
                                <select name="priority" id="priority" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @foreach(\App\Models\Task::PRIORITIES as $key => $label)
                                        <option value="{{ $key }}" {{ old('priority', $task->priority) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                            <div>
                                <label for="assigned_to" class="block text-sm font-medium text-gray-700">Assignee</label>
                                <select name="assigned_to" id="assigned_to" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Unassigned</option>
                                    @foreach($project->members as $member)
                                        <option value="{{ $member->id }}" {{ old('assigned_to', $task->assigned_to) == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="sprint_id" class="block text-sm font-medium text-gray-700">Sprint</label>
                                <select name="sprint_id" id="sprint_id" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">No Sprint</option>
                                    @foreach($project->sprints as $sprint)
                                        <option value="{{ $sprint->id }}" {{ old('sprint_id', $task->sprint_id) == $sprint->id ? 'selected' : '' }}>{{ $sprint->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="story_points" class="block text-sm font-medium text-gray-700">Story Points</label>
                                <input type="number" name="story_points" id="story_points" value="{{ old('story_points', $task->story_points) }}" min="0" max="100" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label for="plan_start_date" class="block text-sm font-medium text-gray-700">Plan Start</label>
                                <input type="date" name="plan_start_date" value="{{ old('plan_start_date', $task->plan_start_date?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="plan_end_date" class="block text-sm font-medium text-gray-700">Plan End</label>
                                <input type="date" name="plan_end_date" value="{{ old('plan_end_date', $task->plan_end_date?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label for="actual_start_date" class="block text-sm font-medium text-gray-700">Actual Start</label>
                                <input type="date" name="actual_start_date" value="{{ old('actual_start_date', $task->actual_start_date?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div>
                                <label for="actual_end_date" class="block text-sm font-medium text-gray-700">Actual End</label>
                                <input type="date" name="actual_end_date" value="{{ old('actual_end_date', $task->actual_end_date?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>
                        <div>
                            <label for="progress" class="block text-sm font-medium text-gray-700">Progress (%)</label>
                            <input type="number" name="progress" id="progress" value="{{ old('progress', $task->progress) }}" min="0" max="100" step="5" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                    </div>
                    <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end">
                        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Update Task</button>
                    </div>
                </div>
            </form>

            {{-- Comments --}}
            <div class="rounded-xl bg-white shadow ring-1 ring-gray-200">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-base font-semibold text-gray-900">Comments ({{ $task->comments->count() }})</h3>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('projects.tasks.comments.store', [$project, $task]) }}" class="mb-6">
                        @csrf
                        <textarea name="body" rows="3" required placeholder="Add a comment..." class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                        @error('body') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        <div class="mt-2 flex justify-end">
                            <button type="submit" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Post Comment</button>
                        </div>
                    </form>

                    <div class="space-y-4">
                        @forelse($task->comments as $comment)
                        <div class="flex gap-3">
                            <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-indigo-100 text-xs font-medium text-indigo-700">{{ $comment->user->initials }}</div>
                            <div class="flex-1 rounded-lg bg-gray-50 px-4 py-3">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-900">{{ $comment->user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</p>
                                </div>
                                <div class="mt-1 text-sm text-gray-700">{!! nl2br(e($comment->body)) !!}</div>
                            </div>
                        </div>
                        @empty
                        <p class="text-center text-sm text-gray-500 py-4">No comments yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            <div class="rounded-xl bg-white shadow ring-1 ring-gray-200 p-5">
                <h4 class="text-sm font-semibold text-gray-900 mb-3">Information</h4>
                <dl class="space-y-3 text-sm">
                    <div><dt class="text-gray-500">Created by</dt><dd class="font-medium text-gray-900">{{ $task->creator->name }}</dd></div>
                    <div><dt class="text-gray-500">Created</dt><dd class="text-gray-700">{{ $task->created_at->format('M d, Y H:i') }}</dd></div>
                    <div><dt class="text-gray-500">Updated</dt><dd class="text-gray-700">{{ $task->updated_at->diffForHumans() }}</dd></div>
                    @if($task->parent)
                    <div><dt class="text-gray-500">Parent Task</dt><dd><a href="{{ route('projects.tasks.show', [$project, $task->parent]) }}" class="text-indigo-600 hover:text-indigo-500">{{ $task->parent->title }}</a></dd></div>
                    @endif
                </dl>
            </div>

            @if($task->children->isNotEmpty())
            <div class="rounded-xl bg-white shadow ring-1 ring-gray-200 p-5">
                <h4 class="text-sm font-semibold text-gray-900 mb-3">Sub-tasks</h4>
                <ul class="space-y-2">
                    @foreach($task->children as $child)
                    <li class="flex items-center justify-between">
                        <a href="{{ route('projects.tasks.show', [$project, $child]) }}" class="text-sm text-indigo-600 hover:text-indigo-500">{{ $child->title }}</a>
                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $child->status_badge }}">{{ \App\Models\Task::STATUSES[$child->status] }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

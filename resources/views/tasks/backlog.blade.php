@extends('layouts.app')
@section('title', $project->name . ' - Backlog')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <a href="{{ route('projects.show', $project) }}" class="text-sm text-indigo-600 hover:text-indigo-500">&larr; Back to {{ $project->name }}</a>
        <a href="{{ route('projects.tasks.create', $project) }}" class="inline-flex items-center gap-x-1 rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">+ Add Task</a>
    </div>

    <div class="rounded-xl bg-white shadow ring-1 ring-gray-200">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="text-base font-semibold text-gray-900">Backlog ({{ $tasks->count() }} items)</h3>
        </div>
        @if($tasks->isNotEmpty())
        <ul class="divide-y divide-gray-100">
            @foreach($tasks as $task)
            <li class="px-6 py-4 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <a href="{{ route('projects.tasks.show', [$project, $task]) }}" class="text-sm font-medium text-gray-900 hover:text-indigo-600">{{ $task->title }}</a>
                        <div class="mt-1 flex items-center gap-2">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $task->priority_badge }}">{{ ucfirst($task->priority) }}</span>
                            <span class="text-xs text-gray-500">{{ \App\Models\Task::TYPES[$task->type] ?? $task->type }}</span>
                            @if($task->story_points)
                                <span class="text-xs text-gray-400">{{ $task->story_points }} pts</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        @if($task->assignee)
                            <span class="text-xs text-gray-500">{{ $task->assignee->name }}</span>
                        @endif
                    </div>
                </div>
            </li>
            @endforeach
        </ul>
        @else
        <p class="px-6 py-8 text-center text-sm text-gray-500">No backlog items. All tasks have been moved to sprints.</p>
        @endif
    </div>
</div>
@endsection

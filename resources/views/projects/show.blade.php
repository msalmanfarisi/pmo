@extends('layouts.app')
@section('title', $project->name)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $project->status_badge }}">{{ \App\Models\Project::STATUSES[$project->status] ?? $project->status }}</span>
                <span class="text-sm text-gray-500">{{ $project->code }}</span>
            </div>
            <p class="mt-1 text-sm text-gray-600">{{ $project->description }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('projects.kanban', $project) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">Kanban</a>
            <a href="{{ route('projects.gantt', $project) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">Gantt</a>
            <a href="{{ route('projects.scurve', $project) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">S-Curve</a>
            <a href="{{ route('projects.backlog', $project) }}" class="inline-flex items-center rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">Backlog</a>
            <a href="{{ route('projects.edit', $project) }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Edit</a>
        </div>
    </div>

    {{-- Project Info Cards --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-4">
        <div class="rounded-xl bg-white p-5 shadow ring-1 ring-gray-200">
            <p class="text-sm text-gray-500">Progress</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($project->progress, 0) }}%</p>
            <div class="mt-2 h-2 rounded-full bg-gray-200"><div class="h-2 rounded-full bg-indigo-600" style="width: {{ $project->progress }}%"></div></div>
        </div>
        <div class="rounded-xl bg-white p-5 shadow ring-1 ring-gray-200">
            <p class="text-sm text-gray-500">Plan Dates</p>
            <p class="mt-1 text-sm font-medium text-gray-900">{{ $project->plan_start_date?->format('M d, Y') ?? 'Not set' }}</p>
            <p class="text-sm text-gray-600">to {{ $project->plan_end_date?->format('M d, Y') ?? 'Not set' }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow ring-1 ring-gray-200">
            <p class="text-sm text-gray-500">Total Tasks</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $project->tasks->count() }}</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow ring-1 ring-gray-200">
            <p class="text-sm text-gray-500">Team Members</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ $project->members->count() }}</p>
        </div>
    </div>

    {{-- Sprints Section --}}
    <div class="rounded-xl bg-white shadow ring-1 ring-gray-200" x-data="{ showSprintForm: false }">
        <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900">Sprints</h3>
            <button @click="showSprintForm = !showSprintForm" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">+ Add Sprint</button>
        </div>
        <div x-show="showSprintForm" x-cloak class="border-b border-gray-200 p-6 bg-gray-50">
            <form method="POST" action="{{ route('projects.sprints.store', $project) }}" class="grid grid-cols-1 gap-4 sm:grid-cols-5">
                @csrf
                <input type="text" name="name" placeholder="Sprint name" required class="rounded-lg border-gray-300 text-sm">
                <input type="date" name="start_date" required class="rounded-lg border-gray-300 text-sm">
                <input type="date" name="end_date" required class="rounded-lg border-gray-300 text-sm">
                <select name="status" class="rounded-lg border-gray-300 text-sm">
                    @foreach(\App\Models\Sprint::STATUSES as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Create</button>
            </form>
        </div>
        @if($project->sprints->isNotEmpty())
        <ul class="divide-y divide-gray-100">
            @foreach($project->sprints as $sprint)
            <li class="px-6 py-4 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $sprint->name }}</p>
                    <p class="text-xs text-gray-500">{{ $sprint->start_date->format('M d') }} - {{ $sprint->end_date->format('M d, Y') }} &middot; {{ $sprint->tasks->count() }} tasks</p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $sprint->status === 'active' ? 'bg-green-100 text-green-800' : ($sprint->status === 'completed' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800') }}">{{ \App\Models\Sprint::STATUSES[$sprint->status] }}</span>
                    <form method="POST" action="{{ route('projects.sprints.destroy', [$project, $sprint]) }}" onsubmit="return confirm('Delete this sprint?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-400 hover:text-red-600 text-xs">Delete</button>
                    </form>
                </div>
            </li>
            @endforeach
        </ul>
        @else
        <p class="px-6 py-8 text-center text-sm text-gray-500">No sprints yet.</p>
        @endif
    </div>

    {{-- Tasks Section --}}
    <div class="rounded-xl bg-white shadow ring-1 ring-gray-200">
        <div class="border-b border-gray-200 px-6 py-4 flex items-center justify-between">
            <h3 class="text-base font-semibold text-gray-900">Tasks</h3>
            <a href="{{ route('projects.tasks.create', $project) }}" class="inline-flex items-center gap-x-1 rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add Task
            </a>
        </div>
        @if($project->tasks->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Assignee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sprint</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach($project->tasks->whereNull('parent_id') as $task)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('projects.tasks.show', [$project, $task]) }}" class="font-medium text-indigo-600 hover:text-indigo-500">{{ $task->title }}</a>
                            @if($task->comments_count) <span class="ml-1 text-xs text-gray-400">({{ $task->comments_count }})</span> @endif
                        </td>
                        <td class="px-6 py-4"><span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $task->status_badge }}">{{ \App\Models\Task::STATUSES[$task->status] }}</span></td>
                        <td class="px-6 py-4"><span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $task->priority_badge }}">{{ ucfirst($task->priority) }}</span></td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $task->assignee?->name ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $task->sprint?->name ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="h-1.5 w-16 rounded-full bg-gray-200"><div class="h-1.5 rounded-full bg-indigo-600" style="width: {{ $task->progress }}%"></div></div>
                                <span class="text-xs text-gray-500">{{ number_format($task->progress, 0) }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="px-6 py-8 text-center text-sm text-gray-500">No tasks yet. <a href="{{ route('projects.tasks.create', $project) }}" class="text-indigo-600 hover:text-indigo-500">Create one</a></p>
        @endif
    </div>
</div>
@endsection

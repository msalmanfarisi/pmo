@extends('layouts.app')
@section('title', 'Projects')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <form method="GET" class="flex items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search projects..." class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-64">
            <select name="status" onchange="this.form.submit()" class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">All Statuses</option>
                @foreach(\App\Models\Project::STATUSES as $key => $label)
                    <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </form>
        <a href="{{ route('projects.create') }}" class="inline-flex items-center gap-x-1.5 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors">
            <svg class="-ml-0.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            New Project
        </a>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($projects as $project)
        <div class="overflow-hidden rounded-xl bg-white shadow ring-1 ring-gray-200 hover:shadow-md transition-shadow">
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div class="min-w-0 flex-1">
                        <a href="{{ route('projects.show', $project) }}" class="text-base font-semibold text-gray-900 hover:text-indigo-600">{{ $project->name }}</a>
                        <p class="mt-1 text-xs font-medium text-gray-500">{{ $project->code }}</p>
                    </div>
                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $project->status_badge }}">{{ \App\Models\Project::STATUSES[$project->status] ?? $project->status }}</span>
                </div>
                @if($project->description)
                    <p class="mt-3 text-sm text-gray-600 line-clamp-2">{{ $project->description }}</p>
                @endif
                <div class="mt-4">
                    <div class="flex items-center justify-between text-xs text-gray-500 mb-1.5">
                        <span>Progress</span>
                        <span class="font-medium">{{ number_format($project->progress, 0) }}%</span>
                    </div>
                    <div class="h-2 w-full rounded-full bg-gray-200">
                        <div class="h-2 rounded-full bg-indigo-600 transition-all" style="width: {{ $project->progress }}%"></div>
                    </div>
                </div>
                <div class="mt-4 flex items-center justify-between text-xs text-gray-500">
                    <span>{{ $project->tasks_count }} tasks</span>
                    <span>{{ $project->owner->name }}</span>
                </div>
                @if($project->plan_start_date && $project->plan_end_date)
                <div class="mt-2 text-xs text-gray-400">
                    {{ $project->plan_start_date->format('M d, Y') }} - {{ $project->plan_end_date->format('M d, Y') }}
                </div>
                @endif
            </div>
            <div class="border-t border-gray-100 px-6 py-3 bg-gray-50 flex items-center justify-between">
                <div class="flex gap-2">
                    <a href="{{ route('projects.kanban', $project) }}" class="text-xs text-indigo-600 hover:text-indigo-500 font-medium">Kanban</a>
                    <span class="text-gray-300">|</span>
                    <a href="{{ route('projects.gantt', $project) }}" class="text-xs text-indigo-600 hover:text-indigo-500 font-medium">Gantt</a>
                    <span class="text-gray-300">|</span>
                    <a href="{{ route('projects.scurve', $project) }}" class="text-xs text-indigo-600 hover:text-indigo-500 font-medium">S-Curve</a>
                </div>
                <a href="{{ route('projects.backlog', $project) }}" class="text-xs text-gray-600 hover:text-indigo-600 font-medium">Backlog</a>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/></svg>
            <h3 class="mt-2 text-sm font-semibold text-gray-900">No projects</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new project.</p>
            <div class="mt-6">
                <a href="{{ route('projects.create') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    New Project
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $projects->links() }}</div>
</div>
@endsection

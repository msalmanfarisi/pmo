@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Stats --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="overflow-hidden rounded-xl bg-white shadow ring-1 ring-gray-200">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 rounded-lg bg-indigo-50 p-3">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z"/></svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500">Total Projects</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalProjects }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="overflow-hidden rounded-xl bg-white shadow ring-1 ring-gray-200">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 rounded-lg bg-green-50 p-3">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500">Active Projects</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $activeProjects }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="overflow-hidden rounded-xl bg-white shadow ring-1 ring-gray-200">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 rounded-lg bg-yellow-50 p-3">
                        <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15a2.25 2.25 0 012.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500">My Open Tasks</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $myTasks }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="overflow-hidden rounded-xl bg-white shadow ring-1 ring-gray-200">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 rounded-lg bg-red-50 p-3">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500">Overdue Tasks</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $overdueTasks }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Recent Projects --}}
        <div class="rounded-xl bg-white shadow ring-1 ring-gray-200">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-base font-semibold text-gray-900">Recent Projects</h3>
            </div>
            <ul role="list" class="divide-y divide-gray-100">
                @forelse($recentProjects as $project)
                <li class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('projects.show', $project) }}" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">{{ $project->name }}</a>
                            <p class="mt-1 text-xs text-gray-500">{{ $project->code }} &middot; {{ $project->owner->name }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-24">
                                <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                                    <span>{{ number_format($project->progress, 0) }}%</span>
                                </div>
                                <div class="h-1.5 w-full rounded-full bg-gray-200">
                                    <div class="h-1.5 rounded-full bg-indigo-600 transition-all" style="width: {{ $project->progress }}%"></div>
                                </div>
                            </div>
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $project->status_badge }}">{{ \App\Models\Project::STATUSES[$project->status] ?? $project->status }}</span>
                        </div>
                    </div>
                </li>
                @empty
                <li class="px-6 py-8 text-center text-sm text-gray-500">No projects yet. <a href="{{ route('projects.create') }}" class="text-indigo-600 hover:text-indigo-500">Create one</a></li>
                @endforelse
            </ul>
        </div>

        {{-- Upcoming Tasks --}}
        <div class="rounded-xl bg-white shadow ring-1 ring-gray-200">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-base font-semibold text-gray-900">My Upcoming Tasks</h3>
            </div>
            <ul role="list" class="divide-y divide-gray-100">
                @forelse($upcomingTasks as $task)
                <li class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('projects.tasks.show', [$task->project_id, $task]) }}" class="text-sm font-medium text-gray-900 hover:text-indigo-600">{{ $task->title }}</a>
                            <p class="mt-1 text-xs text-gray-500">{{ $task->project->name }} @if($task->sprint) &middot; {{ $task->sprint->name }} @endif</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $task->priority_badge }}">{{ ucfirst($task->priority) }}</span>
                            @if($task->plan_end_date)
                                <span class="text-xs {{ $task->plan_end_date->isPast() ? 'text-red-600 font-semibold' : 'text-gray-500' }}">{{ $task->plan_end_date->format('M d') }}</span>
                            @endif
                        </div>
                    </div>
                </li>
                @empty
                <li class="px-6 py-8 text-center text-sm text-gray-500">No upcoming tasks.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')
@section('title', $project->name . ' - Kanban Board')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <a href="{{ route('projects.show', $project) }}" class="text-sm text-indigo-600 hover:text-indigo-500">&larr; Back to Project</a>
        <a href="{{ route('projects.tasks.create', $project) }}" class="inline-flex items-center gap-x-1 rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">+ Add Task</a>
    </div>

    <div class="flex gap-4 overflow-x-auto pb-4" id="kanban-board">
        @foreach(\App\Models\Task::STATUSES as $statusKey => $statusLabel)
        <div class="flex-shrink-0 w-72">
            <div class="rounded-xl bg-gray-100 p-3">
                <div class="flex items-center justify-between mb-3 px-1">
                    <h3 class="text-sm font-semibold text-gray-700">{{ $statusLabel }}</h3>
                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-full bg-gray-200 text-xs font-medium text-gray-600">{{ ($tasks[$statusKey] ?? collect())->count() }}</span>
                </div>
                <div class="space-y-2 min-h-[4rem] kanban-column" data-status="{{ $statusKey }}">
                    @foreach(($tasks[$statusKey] ?? collect()) as $task)
                    <div class="kanban-card rounded-lg bg-white p-3 shadow-sm ring-1 ring-gray-200 cursor-move hover:shadow-md transition-shadow" data-task-id="{{ $task->id }}">
                        <a href="{{ route('projects.tasks.show', [$project, $task]) }}" class="text-sm font-medium text-gray-900 hover:text-indigo-600 block">{{ $task->title }}</a>
                        <div class="mt-2 flex items-center justify-between">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $task->priority_badge }}">{{ ucfirst($task->priority) }}</span>
                            @if($task->assignee)
                            <div class="flex h-6 w-6 items-center justify-center rounded-full bg-indigo-100 text-xs font-medium text-indigo-700" title="{{ $task->assignee->name }}">{{ $task->assignee->initials }}</div>
                            @endif
                        </div>
                        @if($task->plan_end_date)
                        <p class="mt-1.5 text-xs {{ $task->plan_end_date->isPast() && $task->status !== 'done' ? 'text-red-500' : 'text-gray-400' }}">Due: {{ $task->plan_end_date->format('M d') }}</p>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script nonce="{{ csp_nonce() }}">
document.addEventListener('DOMContentLoaded', function() {
    const columns = document.querySelectorAll('.kanban-column');
    const projectId = {{ $project->id }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    if (typeof Sortable !== 'undefined') {
        columns.forEach(column => {
            new Sortable(column, {
                group: 'kanban',
                animation: 200,
                ghostClass: 'opacity-50',
                dragClass: 'shadow-lg',
                onEnd: function(evt) {
                    const taskId = evt.item.dataset.taskId;
                    const newStatus = evt.to.dataset.status;
                    const newIndex = evt.newIndex;

                    fetch(`/projects/${projectId}/tasks/${taskId}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ status: newStatus, sort_order: newIndex }),
                    });
                }
            });
        });
    }
});
</script>
@endpush
@endsection

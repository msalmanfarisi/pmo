@extends('layouts.app')
@section('title', $project->name . ' - Gantt Chart')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <a href="{{ route('projects.show', $project) }}" class="text-sm text-indigo-600 hover:text-indigo-500">&larr; Back to Project</a>
    </div>

    <div class="rounded-xl bg-white shadow ring-1 ring-gray-200 p-6">
        <div id="gantt-chart" class="w-full" style="min-height: 400px;"></div>
    </div>
</div>

@push('scripts')
<script nonce="{{ csp_nonce() }}">
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('gantt-chart');
    const tasks = @json($tasks);
    const projectStart = '{{ $project->plan_start_date?->format("Y-m-d") ?? now()->format("Y-m-d") }}';
    const projectEnd = '{{ $project->plan_end_date?->format("Y-m-d") ?? now()->addMonths(3)->format("Y-m-d") }}';

    if (tasks.length === 0) {
        container.innerHTML = '<p class="text-center text-gray-500 py-8">No tasks with dates to display. Add tasks with plan dates to see the Gantt chart.</p>';
        return;
    }

    const start = new Date(projectStart);
    const end = new Date(projectEnd);
    const totalDays = Math.max(Math.ceil((end - start) / (1000 * 60 * 60 * 24)), 30);

    let html = '<div class="overflow-x-auto">';
    html += '<table class="w-full border-collapse" style="min-width: ' + Math.max(800, totalDays * 8) + 'px;">';

    // Header with months
    html += '<thead><tr><th class="border border-gray-200 bg-gray-50 px-4 py-2 text-left text-xs font-medium text-gray-500 w-56 sticky left-0 z-10">Task</th>';
    let current = new Date(start);
    while (current <= end) {
        const monthDays = new Date(current.getFullYear(), current.getMonth() + 1, 0).getDate();
        const remainDays = Math.min(monthDays - current.getDate() + 1, Math.ceil((end - current) / (1000 * 60 * 60 * 24)) + 1);
        html += '<th colspan="' + remainDays + '" class="border border-gray-200 bg-gray-50 px-2 py-2 text-center text-xs font-medium text-gray-500">' + current.toLocaleString('en', { month: 'short', year: 'numeric' }) + '</th>';
        current = new Date(current.getFullYear(), current.getMonth() + 1, 1);
    }
    html += '</tr></thead><tbody>';

    tasks.forEach(function(task) {
        const taskStart = task.plan_start_date ? new Date(task.plan_start_date) : null;
        const taskEnd = task.plan_end_date ? new Date(task.plan_end_date) : null;

        html += '<tr>';
        html += '<td class="border border-gray-200 px-4 py-2 text-sm font-medium text-gray-900 w-56 sticky left-0 bg-white z-10">';
        html += '<a href="/projects/' + {{ $project->id }} + '/tasks/' + task.id + '" class="hover:text-indigo-600">' + escapeHtml(task.title) + '</a>';
        if (task.assignee) html += '<br><span class="text-xs text-gray-400">' + escapeHtml(task.assignee.name) + '</span>';
        html += '</td>';

        let dayIter = new Date(start);
        while (dayIter <= end) {
            let cls = 'border border-gray-100 h-8';
            if (taskStart && taskEnd && dayIter >= taskStart && dayIter <= taskEnd) {
                const progress = task.progress || 0;
                const isFirst = dayIter.toDateString() === taskStart.toDateString();
                const isLast = dayIter.toDateString() === taskEnd.toDateString();
                cls += ' relative';
                html += '<td class="' + cls + '">';
                html += '<div class="absolute inset-y-1 bg-indigo-200 ' + (isFirst ? 'left-0 rounded-l-md' : 'left-0') + ' ' + (isLast ? 'right-0 rounded-r-md' : 'right-0') + '" style="left:0;right:0;">';
                html += '<div class="h-full bg-indigo-500 rounded-inherit" style="width:' + progress + '%;' + (isFirst ? 'border-radius:0.375rem 0 0 0.375rem;' : '') + (isLast && progress >= 100 ? 'border-radius:0 0.375rem 0.375rem 0;' : '') + '"></div>';
                html += '</div></td>';
            } else {
                const isToday = dayIter.toDateString() === new Date().toDateString();
                if (isToday) cls += ' bg-indigo-50';
                html += '<td class="' + cls + '"></td>';
            }
            dayIter.setDate(dayIter.getDate() + 1);
        }
        html += '</tr>';
    });

    html += '</tbody></table></div>';
    container.innerHTML = html;

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>
@endpush
@endsection

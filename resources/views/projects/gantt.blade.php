@extends('layouts.app')
@section('title', $project->name . ' - Gantt Chart')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <a href="{{ route('projects.show', $project) }}" class="text-sm text-indigo-600 hover:text-indigo-500">&larr; Back to Project</a>
        <div class="flex items-center gap-1 rounded-lg bg-white shadow ring-1 ring-gray-200 p-1">
            <button id="btn-daily" class="gantt-view-btn rounded-md px-3 py-1.5 text-xs font-medium transition">Daily</button>
            <button id="btn-weekly" class="gantt-view-btn rounded-md px-3 py-1.5 text-xs font-medium transition">Weekly</button>
            <button id="btn-monthly" class="gantt-view-btn rounded-md px-3 py-1.5 text-xs font-medium transition">Monthly</button>
        </div>
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

    let currentView = 'monthly';

    function setGanttView(view) {
        currentView = view;
        document.querySelectorAll('.gantt-view-btn').forEach(function(btn) {
            btn.classList.remove('bg-indigo-600', 'text-white');
            btn.classList.add('text-gray-600', 'hover:bg-gray-100');
        });
        const activeBtn = document.getElementById('btn-' + view);
        activeBtn.classList.add('bg-indigo-600', 'text-white');
        activeBtn.classList.remove('text-gray-600', 'hover:bg-gray-100');
        renderGantt();
    }

    function renderGantt() {
        if (tasks.length === 0) {
            container.innerHTML = '<p class="text-center text-gray-500 py-8">No tasks with dates to display. Add tasks with plan dates to see the Gantt chart.</p>';
            return;
        }

        const start = new Date(projectStart);
        const end = new Date(projectEnd);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (currentView === 'daily') {
            renderDaily(start, end, today);
        } else if (currentView === 'weekly') {
            renderWeekly(start, end, today);
        } else {
            renderMonthly(start, end, today);
        }
    }

    function renderDaily(start, end, today) {
        const totalDays = Math.max(Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1, 7);
        let html = '<div class="overflow-x-auto">';
        html += '<table class="w-full border-collapse" style="min-width: ' + Math.max(800, totalDays * 32) + 'px;">';

        // Month header row
        html += '<thead><tr><th rowspan="2" class="border border-gray-200 bg-gray-50 px-4 py-2 text-left text-xs font-medium text-gray-500 w-56 sticky left-0 z-20">Task</th>';
        let cur = new Date(start);
        while (cur <= end) {
            const monthEnd = new Date(cur.getFullYear(), cur.getMonth() + 1, 0);
            const colEnd = monthEnd > end ? end : monthEnd;
            const span = Math.ceil((colEnd - cur) / (1000 * 60 * 60 * 24)) + 1;
            html += '<th colspan="' + span + '" class="border border-gray-200 bg-gray-50 px-1 py-1 text-center text-xs font-medium text-gray-500">' + cur.toLocaleString('en', { month: 'short', year: 'numeric' }) + '</th>';
            cur = new Date(cur.getFullYear(), cur.getMonth() + 1, 1);
        }
        html += '</tr>';

        // Day header row
        html += '<tr>';
        cur = new Date(start);
        while (cur <= end) {
            const dayNum = cur.getDay();
            const isWeekend = (dayNum === 0 || dayNum === 6);
            const isToday = cur.toDateString() === today.toDateString();
            let bgClass = 'bg-gray-50';
            let textClass = 'text-gray-500';
            if (isWeekend) {
                bgClass = 'bg-red-50';
                textClass = 'text-red-500';
            }
            if (isToday) {
                bgClass = 'bg-indigo-100';
                textClass = 'text-indigo-700';
            }
            html += '<th class="border border-gray-200 ' + bgClass + ' px-0 py-1 text-center text-[10px] font-medium ' + textClass + '" style="min-width:28px;">' + cur.getDate() + '</th>';
            cur.setDate(cur.getDate() + 1);
        }
        html += '</tr></thead><tbody>';

        tasks.forEach(function(task) {
            html += renderTaskRow(task, start, end, today, 'daily');
        });

        html += '</tbody></table></div>';
        container.innerHTML = html;
    }

    function renderWeekly(start, end, today) {
        // Collect weeks
        const weeks = [];
        let cur = new Date(start);
        // Align to start of week (Monday)
        const startDay = cur.getDay();
        const weekStart = new Date(cur);
        if (startDay !== 1) {
            weekStart.setDate(weekStart.getDate() - ((startDay + 6) % 7));
        }
        cur = new Date(weekStart);
        while (cur <= end) {
            const wEnd = new Date(cur);
            wEnd.setDate(wEnd.getDate() + 6);
            weeks.push({ start: new Date(cur), end: wEnd > end ? new Date(end) : wEnd });
            cur.setDate(cur.getDate() + 7);
        }

        let html = '<div class="overflow-x-auto">';
        html += '<table class="w-full border-collapse" style="min-width: ' + Math.max(800, weeks.length * 80) + 'px;">';

        // Month header
        html += '<thead><tr><th rowspan="2" class="border border-gray-200 bg-gray-50 px-4 py-2 text-left text-xs font-medium text-gray-500 w-56 sticky left-0 z-20">Task</th>';
        let monthMap = {};
        weeks.forEach(function(w, i) {
            const key = w.start.getFullYear() + '-' + w.start.getMonth();
            if (!monthMap[key]) monthMap[key] = { label: w.start.toLocaleString('en', { month: 'short', year: 'numeric' }), count: 0 };
            monthMap[key].count++;
        });
        Object.values(monthMap).forEach(function(m) {
            html += '<th colspan="' + m.count + '" class="border border-gray-200 bg-gray-50 px-1 py-1 text-center text-xs font-medium text-gray-500">' + m.label + '</th>';
        });
        html += '</tr>';

        // Week header
        html += '<tr>';
        weeks.forEach(function(w) {
            const isCurrentWeek = today >= w.start && today <= w.end;
            const bgClass = isCurrentWeek ? 'bg-indigo-100' : 'bg-gray-50';
            const label = 'W' + getWeekNumber(w.start);
            html += '<th class="border border-gray-200 ' + bgClass + ' px-1 py-1 text-center text-[10px] font-medium text-gray-500" style="min-width:70px;">' + (w.start.getDate()) + '-' + (w.end.getDate()) + '</th>';
        });
        html += '</tr></thead><tbody>';

        tasks.forEach(function(task) {
            const taskStart = task.plan_start_date ? new Date(task.plan_start_date) : null;
            const taskEnd = task.plan_end_date ? new Date(task.plan_end_date) : null;
            html += '<tr>';
            html += '<td class="border border-gray-200 px-4 py-2 text-sm font-medium text-gray-900 w-56 sticky left-0 bg-white z-10">';
            html += '<a href="/projects/' + {{ $project->id }} + '/tasks/' + task.id + '" class="hover:text-indigo-600">' + escapeHtml(task.title) + '</a>';
            if (task.assignee) html += '<br><span class="text-xs text-gray-400">' + escapeHtml(task.assignee.name) + '</span>';
            html += '</td>';

            weeks.forEach(function(w) {
                let cls = 'border border-gray-100 h-8';
                if (taskStart && taskEnd && taskEnd >= w.start && taskStart <= w.end) {
                    const progress = task.progress || 0;
                    const isFirst = taskStart >= w.start && taskStart <= w.end;
                    const isLast = taskEnd >= w.start && taskEnd <= w.end;
                    html += '<td class="' + cls + ' relative">';
                    html += '<div class="absolute inset-y-1 bg-indigo-200 ' + (isFirst ? 'rounded-l-md' : '') + ' ' + (isLast ? 'rounded-r-md' : '') + '" style="left:0;right:0;">';
                    html += '<div class="h-full bg-indigo-500" style="width:' + progress + '%;' + (isFirst ? 'border-radius:0.375rem 0 0 0.375rem;' : '') + '"></div>';
                    html += '</div></td>';
                } else {
                    const isCurrentWeek = today >= w.start && today <= w.end;
                    if (isCurrentWeek) cls += ' bg-indigo-50/30';
                    html += '<td class="' + cls + '"></td>';
                }
            });
            html += '</tr>';
        });

        html += '</tbody></table></div>';
        container.innerHTML = html;
    }

    function renderMonthly(start, end, today) {
        const totalDays = Math.max(Math.ceil((end - start) / (1000 * 60 * 60 * 24)), 30);
        let html = '<div class="overflow-x-auto">';
        html += '<table class="w-full border-collapse" style="min-width: ' + Math.max(800, totalDays * 8) + 'px;">';

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
            html += renderTaskRow(task, start, end, today, 'monthly');
        });

        html += '</tbody></table></div>';
        container.innerHTML = html;
    }

    function renderTaskRow(task, start, end, today, view) {
        const taskStart = task.plan_start_date ? new Date(task.plan_start_date) : null;
        const taskEnd = task.plan_end_date ? new Date(task.plan_end_date) : null;

        let html = '<tr>';
        html += '<td class="border border-gray-200 px-4 py-2 text-sm font-medium text-gray-900 w-56 sticky left-0 bg-white z-10">';
        html += '<a href="/projects/' + {{ $project->id }} + '/tasks/' + task.id + '" class="hover:text-indigo-600">' + escapeHtml(task.title) + '</a>';
        if (task.assignee) html += '<br><span class="text-xs text-gray-400">' + escapeHtml(task.assignee.name) + '</span>';
        html += '</td>';

        let dayIter = new Date(start);
        while (dayIter <= end) {
            let cls = 'border border-gray-100 h-8';
            const dayNum = dayIter.getDay();
            const isWeekend = (dayNum === 0 || dayNum === 6);
            const isToday = dayIter.toDateString() === today.toDateString();

            if (taskStart && taskEnd && dayIter >= taskStart && dayIter <= taskEnd) {
                const progress = task.progress || 0;
                const isFirst = dayIter.toDateString() === taskStart.toDateString();
                const isLast = dayIter.toDateString() === taskEnd.toDateString();
                cls += ' relative';
                if (view === 'daily' && isWeekend) cls += ' bg-red-50';
                html += '<td class="' + cls + '">';
                html += '<div class="absolute inset-y-1 bg-indigo-200 ' + (isFirst ? 'left-0 rounded-l-md' : 'left-0') + ' ' + (isLast ? 'right-0 rounded-r-md' : 'right-0') + '" style="left:0;right:0;">';
                html += '<div class="h-full bg-indigo-500 rounded-inherit" style="width:' + progress + '%;' + (isFirst ? 'border-radius:0.375rem 0 0 0.375rem;' : '') + (isLast && progress >= 100 ? 'border-radius:0 0.375rem 0.375rem 0;' : '') + '"></div>';
                html += '</div></td>';
            } else {
                if (view === 'daily' && isWeekend) {
                    cls += ' bg-red-100';
                } else if (isToday) {
                    cls += ' bg-indigo-50';
                }
                html += '<td class="' + cls + '"></td>';
            }
            dayIter.setDate(dayIter.getDate() + 1);
        }
        html += '</tr>';
        return html;
    }

    function getWeekNumber(d) {
        const date = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
        date.setUTCDate(date.getUTCDate() + 4 - (date.getUTCDay() || 7));
        const yearStart = new Date(Date.UTC(date.getUTCFullYear(), 0, 1));
        return Math.ceil((((date - yearStart) / 86400000) + 1) / 7);
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Bind click events
    document.getElementById('btn-daily').addEventListener('click', function() { setGanttView('daily'); });
    document.getElementById('btn-weekly').addEventListener('click', function() { setGanttView('weekly'); });
    document.getElementById('btn-monthly').addEventListener('click', function() { setGanttView('monthly'); });

    // Initialize with monthly view
    setGanttView('monthly');
});
</script>
@endpush
@endsection

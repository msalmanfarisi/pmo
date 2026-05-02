@extends('layouts.app')
@section('title', $project->name . ' - S-Curve')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <a href="{{ route('projects.show', $project) }}" class="text-sm text-indigo-600 hover:text-indigo-500">&larr; Back to Project</a>
    </div>

    <div class="rounded-xl bg-white shadow ring-1 ring-gray-200 p-6">
        <canvas id="scurve-chart" height="300"></canvas>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div class="rounded-xl bg-white p-5 shadow ring-1 ring-gray-200">
            <p class="text-sm text-gray-500">Planned Progress</p>
            <p class="mt-1 text-2xl font-bold text-blue-600" id="planned-progress">0%</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow ring-1 ring-gray-200">
            <p class="text-sm text-gray-500">Actual Progress</p>
            <p class="mt-1 text-2xl font-bold text-green-600" id="actual-progress">{{ number_format($project->progress, 1) }}%</p>
        </div>
        <div class="rounded-xl bg-white p-5 shadow ring-1 ring-gray-200">
            <p class="text-sm text-gray-500">Deviation</p>
            <p class="mt-1 text-2xl font-bold" id="deviation">0%</p>
        </div>
    </div>
</div>

@push('scripts')
<script nonce="{{ csp_nonce() }}">
document.addEventListener('DOMContentLoaded', function() {
    const tasks = @json($project->tasks);
    const projectStart = '{{ $project->plan_start_date?->format("Y-m-d") ?? now()->format("Y-m-d") }}';
    const projectEnd = '{{ $project->plan_end_date?->format("Y-m-d") ?? now()->addMonths(3)->format("Y-m-d") }}';

    const start = new Date(projectStart);
    const end = new Date(projectEnd);
    const today = new Date();
    const totalDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) || 1;
    const totalTasks = tasks.length || 1;

    // Generate weekly intervals
    const labels = [];
    const plannedData = [];
    const actualData = [];
    let current = new Date(start);

    while (current <= end) {
        labels.push(current.toLocaleDateString('en', { month: 'short', day: 'numeric' }));
        const daysPassed = Math.ceil((current - start) / (1000 * 60 * 60 * 24));

        // Planned: count tasks whose plan_end_date <= current date
        let plannedComplete = 0;
        let actualComplete = 0;
        tasks.forEach(task => {
            if (task.plan_end_date && new Date(task.plan_end_date) <= current) {
                plannedComplete++;
            }
            if (task.actual_end_date && new Date(task.actual_end_date) <= current) {
                actualComplete++;
            } else if (task.status === 'done' && current >= today) {
                actualComplete++;
            }
        });

        plannedData.push(Math.round((plannedComplete / totalTasks) * 100 * 10) / 10);
        actualData.push(current <= today ? Math.round((actualComplete / totalTasks) * 100 * 10) / 10 : null);

        current.setDate(current.getDate() + 7);
    }

    // Update stats
    const latestPlanned = plannedData[plannedData.length - 1] || 0;
    const latestActual = actualData.filter(v => v !== null).pop() || 0;
    document.getElementById('planned-progress').textContent = latestPlanned + '%';
    const deviation = latestActual - (plannedData.filter((_, i) => actualData[i] !== null).pop() || 0);
    const devEl = document.getElementById('deviation');
    devEl.textContent = (deviation >= 0 ? '+' : '') + deviation.toFixed(1) + '%';
    devEl.className = 'mt-1 text-2xl font-bold ' + (deviation >= 0 ? 'text-green-600' : 'text-red-600');

    if (typeof Chart !== 'undefined') {
        new Chart(document.getElementById('scurve-chart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Planned Progress',
                        data: plannedData,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                    },
                    {
                        label: 'Actual Progress',
                        data: actualData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true,
                        spanGaps: false,
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: true, text: 'S-Curve: Planned vs Actual Progress (%)' }
                },
                scales: {
                    y: { beginAtZero: true, max: 100, title: { display: true, text: 'Progress (%)' } },
                    x: { title: { display: true, text: 'Date' } }
                }
            }
        });
    }
});
</script>
@endpush
@endsection

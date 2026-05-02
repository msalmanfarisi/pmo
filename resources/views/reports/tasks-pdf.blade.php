<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tasks Report - {{ $project->name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #333; margin: 20px; }
        h1 { font-size: 18px; color: #1e3a5f; border-bottom: 2px solid #1e3a5f; padding-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background-color: #f1f5f9; text-align: left; padding: 6px; font-size: 9px; text-transform: uppercase; color: #475569; border: 1px solid #e2e8f0; }
        td { padding: 5px 6px; border: 1px solid #e2e8f0; font-size: 9px; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .footer { margin-top: 30px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>
    <h1>Tasks Report: {{ $project->name }} ({{ $project->code }})</h1>
    <p>Total tasks: {{ $tasks->count() }} | Completed: {{ $tasks->where('status', 'done')->count() }} | In Progress: {{ $tasks->where('status', 'in_progress')->count() }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Type</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Assignee</th>
                <th>Sprint</th>
                <th>Plan Start</th>
                <th>Plan End</th>
                <th>Progress</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tasks as $task)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $task->title }}</td>
                <td>{{ \App\Models\Task::TYPES[$task->type] ?? $task->type }}</td>
                <td>{{ \App\Models\Task::STATUSES[$task->status] }}</td>
                <td>{{ ucfirst($task->priority) }}</td>
                <td>{{ $task->assignee?->name ?? '-' }}</td>
                <td>{{ $task->sprint?->name ?? '-' }}</td>
                <td>{{ $task->plan_start_date?->format('Y-m-d') ?? '-' }}</td>
                <td>{{ $task->plan_end_date?->format('Y-m-d') ?? '-' }}</td>
                <td>{{ number_format($task->progress, 0) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Generated on {{ now()->format('M d, Y H:i:s') }} | PMO - Project Management Office
    </div>
</body>
</html>

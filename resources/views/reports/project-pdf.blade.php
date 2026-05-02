<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Project Report - {{ $project->name }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; margin: 20px; }
        h1 { font-size: 20px; color: #1e3a5f; border-bottom: 2px solid #1e3a5f; padding-bottom: 8px; }
        h2 { font-size: 14px; color: #1e3a5f; margin-top: 20px; }
        .meta { margin: 10px 0; }
        .meta span { display: inline-block; margin-right: 20px; }
        .meta .label { color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #f1f5f9; text-align: left; padding: 8px; font-size: 10px; text-transform: uppercase; color: #475569; border: 1px solid #e2e8f0; }
        td { padding: 6px 8px; border: 1px solid #e2e8f0; font-size: 10px; }
        tr:nth-child(even) { background-color: #f8fafc; }
        .progress-bar { background-color: #e2e8f0; height: 8px; border-radius: 4px; }
        .progress-fill { background-color: #4f46e5; height: 8px; border-radius: 4px; }
        .footer { margin-top: 30px; text-align: center; font-size: 9px; color: #94a3b8; border-top: 1px solid #e2e8f0; padding-top: 10px; }
    </style>
</head>
<body>
    <h1>Project Report: {{ $project->name }}</h1>

    <div class="meta">
        <span><span class="label">Code:</span> {{ $project->code }}</span>
        <span><span class="label">Status:</span> {{ \App\Models\Project::STATUSES[$project->status] ?? $project->status }}</span>
        <span><span class="label">Priority:</span> {{ ucfirst($project->priority) }}</span>
        <span><span class="label">Owner:</span> {{ $project->owner->name }}</span>
    </div>
    <div class="meta">
        <span><span class="label">Plan Start:</span> {{ $project->plan_start_date?->format('M d, Y') ?? 'N/A' }}</span>
        <span><span class="label">Plan End:</span> {{ $project->plan_end_date?->format('M d, Y') ?? 'N/A' }}</span>
        <span><span class="label">Progress:</span> {{ number_format($project->progress, 1) }}%</span>
    </div>

    @if($project->description)
    <p>{{ $project->description }}</p>
    @endif

    <h2>Team Members ({{ $project->members->count() }})</h2>
    <table>
        <thead><tr><th>Name</th><th>Email</th><th>Role</th></tr></thead>
        <tbody>
            @foreach($project->members as $member)
            <tr>
                <td>{{ $member->name }}</td>
                <td>{{ $member->email }}</td>
                <td>{{ ucfirst($member->pivot->role) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    @if($project->sprints->isNotEmpty())
    <h2>Sprints ({{ $project->sprints->count() }})</h2>
    <table>
        <thead><tr><th>Sprint</th><th>Start</th><th>End</th><th>Status</th><th>Tasks</th></tr></thead>
        <tbody>
            @foreach($project->sprints as $sprint)
            <tr>
                <td>{{ $sprint->name }}</td>
                <td>{{ $sprint->start_date->format('M d, Y') }}</td>
                <td>{{ $sprint->end_date->format('M d, Y') }}</td>
                <td>{{ \App\Models\Sprint::STATUSES[$sprint->status] }}</td>
                <td>{{ $sprint->tasks->count() }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <h2>Tasks ({{ $project->tasks->count() }})</h2>
    <table>
        <thead><tr><th>Title</th><th>Type</th><th>Status</th><th>Priority</th><th>Assignee</th><th>Progress</th></tr></thead>
        <tbody>
            @foreach($project->tasks as $task)
            <tr>
                <td>{{ $task->title }}</td>
                <td>{{ \App\Models\Task::TYPES[$task->type] ?? $task->type }}</td>
                <td>{{ \App\Models\Task::STATUSES[$task->status] }}</td>
                <td>{{ ucfirst($task->priority) }}</td>
                <td>{{ $task->assignee?->name ?? '-' }}</td>
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

@extends('layouts.app')
@section('title', 'Reports')

@section('content')
<div class="space-y-6">
    <div class="rounded-xl bg-white shadow ring-1 ring-gray-200">
        <div class="border-b border-gray-200 px-6 py-4">
            <h3 class="text-base font-semibold text-gray-900">Project Reports</h3>
            <p class="mt-1 text-sm text-gray-500">Export project and task reports in PDF or Excel format.</p>
        </div>
        @if($projects->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Progress</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tasks</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Export</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach($projects as $project)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $project->name }}</p>
                                <p class="text-xs text-gray-500">{{ $project->code }}</p>
                            </div>
                        </td>
                        <td class="px-6 py-4"><span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium {{ $project->status_badge }}">{{ \App\Models\Project::STATUSES[$project->status] ?? $project->status }}</span></td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="h-1.5 w-20 rounded-full bg-gray-200"><div class="h-1.5 rounded-full bg-indigo-600" style="width: {{ $project->progress }}%"></div></div>
                                <span class="text-xs text-gray-600">{{ number_format($project->progress, 0) }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $project->tasks_count }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <div class="inline-flex rounded-lg shadow-sm">
                                    <a href="{{ route('reports.project.pdf', $project) }}" class="rounded-l-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50" title="Project Summary PDF">PDF</a>
                                    <a href="{{ route('reports.project.excel', $project) }}" class="rounded-r-lg border border-l-0 border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50" title="Project Summary Excel">Excel</a>
                                </div>
                                <div class="inline-flex rounded-lg shadow-sm">
                                    <a href="{{ route('reports.tasks.pdf', $project) }}" class="rounded-l-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-50" title="Tasks PDF">Tasks PDF</a>
                                    <a href="{{ route('reports.tasks.excel', $project) }}" class="rounded-r-lg border border-l-0 border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-indigo-700 hover:bg-indigo-50" title="Tasks Excel">Tasks Excel</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="px-6 py-8 text-center text-sm text-gray-500">No projects available for reporting.</p>
        @endif
    </div>
</div>
@endsection

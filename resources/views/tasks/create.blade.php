@extends('layouts.app')
@section('title', 'Create Task - ' . $project->name)

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-4">
        <a href="{{ route('projects.show', $project) }}" class="text-sm text-indigo-600 hover:text-indigo-500">&larr; Back to {{ $project->name }}</a>
    </div>
    <form method="POST" action="{{ route('projects.tasks.store', $project) }}" class="space-y-6">
        @csrf
        <div class="rounded-xl bg-white shadow ring-1 ring-gray-200">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-base font-semibold text-gray-900">New Task</h3>
            </div>
            <div class="p-6 space-y-5">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @error('title') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type *</label>
                        <select name="type" id="type" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @foreach(\App\Models\Task::TYPES as $key => $label)
                                <option value="{{ $key }}" {{ old('type', 'task') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                        <select name="status" id="status" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @foreach(\App\Models\Task::STATUSES as $key => $label)
                                <option value="{{ $key }}" {{ old('status', 'backlog') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700">Priority *</label>
                        <select name="priority" id="priority" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @foreach(\App\Models\Task::PRIORITIES as $key => $label)
                                <option value="{{ $key }}" {{ old('priority', 'medium') === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-gray-700">Assignee</label>
                        <select name="assigned_to" id="assigned_to" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">Unassigned</option>
                            @foreach($project->members as $member)
                                <option value="{{ $member->id }}" {{ old('assigned_to') == $member->id ? 'selected' : '' }}>{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="sprint_id" class="block text-sm font-medium text-gray-700">Sprint</label>
                        <select name="sprint_id" id="sprint_id" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">No Sprint</option>
                            @foreach($project->sprints as $sprint)
                                <option value="{{ $sprint->id }}" {{ old('sprint_id') == $sprint->id ? 'selected' : '' }}>{{ $sprint->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="story_points" class="block text-sm font-medium text-gray-700">Story Points</label>
                        <input type="number" name="story_points" id="story_points" value="{{ old('story_points') }}" min="0" max="100" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="parent_id" class="block text-sm font-medium text-gray-700">Parent Task</label>
                    <select name="parent_id" id="parent_id" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        <option value="">None (Top-level task)</option>
                        @foreach($parentTasks as $pt)
                            <option value="{{ $pt->id }}" {{ old('parent_id') == $pt->id ? 'selected' : '' }}>{{ $pt->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="plan_start_date" class="block text-sm font-medium text-gray-700">Plan Start Date</label>
                        <input type="date" name="plan_start_date" id="plan_start_date" value="{{ old('plan_start_date') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="plan_end_date" class="block text-sm font-medium text-gray-700">Plan End Date</label>
                        <input type="date" name="plan_end_date" id="plan_end_date" value="{{ old('plan_end_date') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end gap-3">
                <a href="{{ route('projects.show', $project) }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Create Task</button>
            </div>
        </div>
    </form>
</div>
@endsection

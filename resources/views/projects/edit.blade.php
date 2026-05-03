@extends('layouts.app')
@section('title', 'Edit Project: ' . $project->name)

@section('content')
<div class="mx-auto max-w-3xl">
    <form method="POST" action="{{ route('projects.update', $project) }}" class="space-y-6">
        @csrf
        @method('PUT')
        <div class="rounded-xl bg-white shadow ring-1 ring-gray-200">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-base font-semibold text-gray-900">Edit Project</h3>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Project Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $project->name) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="code" class="block text-sm font-medium text-gray-700">Project Code *</label>
                        <input type="text" name="code" id="code" value="{{ old('code', $project->code) }}" required maxlength="20" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm uppercase">
                        @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('description', $project->description) }}</textarea>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                        <select name="status" id="status" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @foreach(\App\Models\Project::STATUSES as $key => $label)
                                <option value="{{ $key }}" {{ old('status', $project->status) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700">Priority *</label>
                        <select name="priority" id="priority" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            @foreach(\App\Models\Project::PRIORITIES as $key => $label)
                                <option value="{{ $key }}" {{ old('priority', $project->priority) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="plan_start_date" class="block text-sm font-medium text-gray-700">Plan Start Date</label>
                        <input type="date" name="plan_start_date" id="plan_start_date" value="{{ old('plan_start_date', $project->plan_start_date?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="plan_end_date" class="block text-sm font-medium text-gray-700">Plan End Date</label>
                        <input type="date" name="plan_end_date" id="plan_end_date" value="{{ old('plan_end_date', $project->plan_end_date?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="actual_start_date" class="block text-sm font-medium text-gray-700">Actual Start Date</label>
                        <input type="date" name="actual_start_date" id="actual_start_date" value="{{ old('actual_start_date', $project->actual_start_date?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="actual_end_date" class="block text-sm font-medium text-gray-700">Actual End Date</label>
                        <input type="date" name="actual_end_date" id="actual_end_date" value="{{ old('actual_end_date', $project->actual_end_date?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                </div>

                <div>
                    <label for="budget" class="block text-sm font-medium text-gray-700">Budget</label>
                    <input type="number" name="budget" id="budget" value="{{ old('budget', $project->budget) }}" step="0.01" min="0" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Team Members</label>
                    <div class="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2 max-h-48 overflow-y-auto border rounded-lg p-3">
                        @php $currentMembers = $project->members->pluck('id')->toArray(); @endphp
                        @foreach($users as $user)
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" name="members[]" value="{{ $user->id }}" {{ in_array($user->id, old('members', $currentMembers)) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            {{ $user->name }}
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end gap-3">
                <a href="{{ route('projects.show', $project) }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">Cancel</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Update Project</button>
            </div>
        </div>
    </form>
</div>
@endsection

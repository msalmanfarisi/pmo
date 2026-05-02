@extends('layouts.app')
@section('title', 'SMTP Settings')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <form method="POST" action="{{ route('settings.smtp.update') }}">
        @csrf @method('PUT')
        <div class="rounded-xl bg-white shadow ring-1 ring-gray-200">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-base font-semibold text-gray-900">Email Configuration</h3>
                <p class="mt-1 text-sm text-gray-500">Configure the SMTP server used for sending email notifications.</p>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="mail_mailer" class="block text-sm font-medium text-gray-700">Mailer *</label>
                        <select name="mail_mailer" id="mail_mailer" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="smtp" {{ old('mail_mailer', $setting?->mail_mailer) === 'smtp' ? 'selected' : '' }}>SMTP</option>
                            <option value="sendmail" {{ old('mail_mailer', $setting?->mail_mailer) === 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                            <option value="log" {{ old('mail_mailer', $setting?->mail_mailer) === 'log' ? 'selected' : '' }}>Log (Testing)</option>
                        </select>
                    </div>
                    <div>
                        <label for="mail_host" class="block text-sm font-medium text-gray-700">SMTP Host *</label>
                        <input type="text" name="mail_host" id="mail_host" value="{{ old('mail_host', $setting?->mail_host) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="smtp.example.com">
                        @error('mail_host') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="mail_port" class="block text-sm font-medium text-gray-700">Port *</label>
                        <input type="number" name="mail_port" id="mail_port" value="{{ old('mail_port', $setting?->mail_port ?? 587) }}" required min="1" max="65535" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="mail_encryption" class="block text-sm font-medium text-gray-700">Encryption</label>
                        <select name="mail_encryption" id="mail_encryption" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="" {{ old('mail_encryption', $setting?->mail_encryption) === null ? 'selected' : '' }}>None</option>
                            <option value="tls" {{ old('mail_encryption', $setting?->mail_encryption) === 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ old('mail_encryption', $setting?->mail_encryption) === 'ssl' ? 'selected' : '' }}>SSL</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="mail_username" class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" name="mail_username" id="mail_username" value="{{ old('mail_username', $setting?->mail_username) }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label for="mail_password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="mail_password" id="mail_password" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="{{ $setting?->mail_password ? '********' : '' }}">
                        <p class="mt-1 text-xs text-gray-500">Leave blank to keep current password.</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="mail_from_address" class="block text-sm font-medium text-gray-700">From Address *</label>
                        <input type="email" name="mail_from_address" id="mail_from_address" value="{{ old('mail_from_address', $setting?->mail_from_address) }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="noreply@example.com">
                    </div>
                    <div>
                        <label for="mail_from_name" class="block text-sm font-medium text-gray-700">From Name *</label>
                        <input type="text" name="mail_from_name" id="mail_from_name" value="{{ old('mail_from_name', $setting?->mail_from_name ?? 'PMO') }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50 rounded-b-xl flex justify-end">
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Save Settings</button>
            </div>
        </div>
    </form>

    {{-- Test Email --}}
    <form method="POST" action="{{ route('settings.smtp.test') }}">
        @csrf
        <div class="rounded-xl bg-white shadow ring-1 ring-gray-200">
            <div class="border-b border-gray-200 px-6 py-4">
                <h3 class="text-base font-semibold text-gray-900">Send Test Email</h3>
            </div>
            <div class="p-6">
                <div class="flex gap-3">
                    <input type="email" name="test_email" required placeholder="recipient@example.com" class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <button type="submit" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50">Send Test</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sign In - PMO</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
    <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex justify-center">
                <div class="flex h-14 w-14 items-center justify-center rounded-xl bg-indigo-600 shadow-lg">
                    <span class="text-2xl font-bold text-white">P</span>
                </div>
            </div>
            <h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
                Project Management Office
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">Sign in to your account</p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white px-6 py-8 shadow-xl ring-1 ring-gray-900/5 sm:rounded-xl sm:px-12">
                <form method="POST" action="{{ route('login') }}" class="space-y-5" autocomplete="off">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email address</label>
                        <div class="mt-2">
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                                   class="block w-full rounded-lg border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                   placeholder="you@example.com">
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Password</label>
                        <div class="mt-2">
                            <input id="password" name="password" type="password" required
                                   class="block w-full rounded-lg border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"
                                   placeholder="Enter your password">
                        </div>
                    </div>

                    <div>
                        <label for="captcha" class="block text-sm font-medium leading-6 text-gray-900">Security Code</label>
                        <div class="mt-2 flex items-center gap-3">
                            <div class="flex-shrink-0 select-none rounded-lg bg-gray-800 px-4 py-2.5 font-mono text-lg font-bold tracking-[0.3em] text-green-400 shadow-inner" id="captcha-display" style="user-select: none; -webkit-user-select: none;">
                                {{ $captcha }}
                            </div>
                            <button type="button" id="refresh-captcha" class="rounded-lg border border-gray-300 p-2 text-gray-500 hover:bg-gray-50 hover:text-gray-700 transition-colors" title="Refresh captcha">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182M2.985 19.644l3.181-3.183"/></svg>
                            </button>
                        </div>
                        <div class="mt-2">
                            <input id="captcha" name="captcha" type="text" required maxlength="8"
                                   class="block w-full rounded-lg border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 font-mono tracking-wider"
                                   placeholder="Enter the 8-character code above" autocomplete="off">
                        </div>
                        @error('captcha')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                        <label for="remember" class="ml-2 block text-sm text-gray-900">Remember me</label>
                    </div>

                    <div>
                        <button type="submit" class="flex w-full justify-center rounded-lg bg-indigo-600 px-3 py-2.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors">
                            Sign in
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script nonce="{{ csp_nonce() }}">
        document.getElementById('refresh-captcha').addEventListener('click', function() {
            fetch('{{ route("captcha.refresh") }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(data => {
                document.getElementById('captcha-display').textContent = data.captcha;
            });
        });
    </script>
</body>
</html>

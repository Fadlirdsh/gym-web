<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-900">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link href="{{ asset('css/output.css') }}" rel="stylesheet">
</head>

<body class="h-full flex items-center justify-center">

    <div class="w-full max-w-sm p-6 bg-gray-800 rounded-lg shadow-lg">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-white">Login Admin</h2>
        </div>

        @if ($errors->any())
            <div class="mb-4 text-red-400 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-100">Email</label>
                <input id="email" name="email" type="email" required
                    class="mt-1 block w-full rounded-md bg-white/5 px-3 py-2 text-white placeholder-gray-400 focus:outline-indigo-500"
                    placeholder="admin@example.com">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-100">Password</label>
                <input id="password" name="password" type="password" required
                    class="mt-1 block w-full rounded-md bg-white/5 px-3 py-2 text-white placeholder-gray-400 focus:outline-indigo-500"
                    placeholder="••••••••">
            </div>

            <button type="submit"
                class="w-full py-2 px-4 bg-indigo-500 hover:bg-indigo-400 text-white font-semibold rounded-md">
                Sign In
            </button>
        </form>
    </div>

</body>

</html>

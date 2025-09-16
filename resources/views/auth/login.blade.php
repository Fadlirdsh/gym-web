<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-900">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="{{ asset('css/output.css') }}" rel="stylesheet">
</head>

<body class="h-full">

    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500" alt="Logo"
                class="mx-auto h-10 w-auto">
            <h2 class="mt-10 text-center text-2xl font-bold tracking-tight text-white">
                Sign in to your account
            </h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            @if ($errors->any())
                <div class="mb-4 text-red-400 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST"
                class="space-y-6 p-6 border border-gray-700 rounded-lg bg-gray-800 shadow-lg">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-100">Email address</label>
                    <input id="email" type="email" name="email" required autocomplete="email"
                        class="mt-2 block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white
                      placeholder-gray-400 outline-1 -outline-offset-1 outline-white/10
                      focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-100">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="mt-2 block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-white
                      placeholder-gray-400 outline-1 -outline-offset-1 outline-white/10
                      focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm">
                </div>

                <button type="submit"
                    class="flex w-full justify-center rounded-md bg-indigo-500 px-3 py-1.5 text-sm
                   font-semibold text-white hover:bg-indigo-400 focus-visible:outline-2
                   focus-visible:outline-offset-2 focus-visible:outline-indigo-500">
                    Sign in
                </button>
            </form>

        </div>
    </div>

</body>

</html>

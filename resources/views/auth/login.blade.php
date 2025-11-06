<!DOCTYPE html>
<html lang="id" class="h-full bg-gray-900">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>

    <!-- Import Tailwind CSS build -->
    <link href="{{ asset('css/output.css') }}" rel="stylesheet">

    <!-- CSS tambahan modern -->
    @vite('resources/css/admin/login.css')
</head>

<body class="h-full flex items-center justify-center fade-in">

    <div class="w-full max-w-sm p-6 login-card">
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

            <div class="input-group">
                <label for="email" class="label">Email</label>
                <input id="email" name="email" type="email" required autofocus class="input-field"
                    placeholder="admin@example.com">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-100 mb-1">Password</label>



                <div class="password-wrapper">
                    <input id="password" name="password" type="password" placeholder="••••••••" required>
                    <svg id="togglePassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor" class="eye-icon">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
            </div>

            <script>
                const togglePassword = document.getElementById('togglePassword');
                const passwordInput = document.getElementById('password');

                togglePassword.addEventListener('click', () => {
                    const isPassword = passwordInput.type === 'password';
                    passwordInput.type = isPassword ? 'text' : 'password';

                    // Ganti ikon saat diklik
                    togglePassword.innerHTML = isPassword
                        ? `<path stroke-linecap="round" stroke-linejoin="round"
          d="M3.98 8.223a10.477 10.477 0 0116.04 0M4.22 15.777a10.477 10.477 0 0016.04 0M12 12a3 3 0 100-6 3 3 0 000 6z" />`
                        : `<path stroke-linecap="round" stroke-linejoin="round"
          d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12s-3.75 6.75-9.75 6.75S2.25 12 2.25 12z" />
         <path stroke-linecap="round" stroke-linejoin="round"
          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />`;
                });
            </script>

            <button type="submit"
                class="w-full mt-6 py-2 px-4 bg-indigo-500 hover:bg-indigo-400 text-white font-semibold rounded-md transition duration-200 ease-in-out">
                Sign In
            </button>
        </form>
    </div>

</body>

</html>
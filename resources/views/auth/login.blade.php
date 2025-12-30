<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite('resources/css/admin/login.css')
</head>

<body>
<div class="auth-wrapper"
     data-has-error="{{ $errors->any() ? 'true' : 'false' }}"
     data-success="{{ session('login_success') ? 'true' : 'false' }}">

    <div class="background-shape"></div>
    <div class="secondary-shape"></div>

    <div class="credentials-panel signin">
        <h2 class="slide-element">Login Admin</h2>

        {{-- BACKEND ERROR (JS ONLY) --}}
        @if ($errors->any())
            <span id="backendError"
                  data-message="{{ $errors->first() }}"
                  hidden></span>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}">
            @csrf

            <div class="field-wrapper slide-element">
                <input type="email"
                       name="email"
                       id="email"
                       value="{{ old('email') }}"
                       data-error="{{ $errors->has('email') ? 'true' : 'false' }}"
                       required>
                <label>Email</label>
                <i class="fa-solid fa-envelope"></i>
            </div>

            <div class="field-wrapper slide-element password-field">
                <input type="password"
                       name="password"
                       id="password"
                       data-error="{{ $errors->has('password') ? 'true' : 'false' }}"
                       required>
                <label>Password</label>
                <i class="fa-solid fa-eye toggle-password" id="togglePassword"></i>
            </div>

            <div class="field-wrapper slide-element">
                <button type="submit" class="submit-button" id="loginBtn">
                    <span class="btn-text">Sign In</span>
                    <span class="btn-loader"></span>
                </button>
            </div>
        </form>
    </div>

    <div class="welcome-section signin">
        <h2 class="slide-element">WELCOME BACK!</h2>
        <p class="slide-element">Silakan login untuk mengakses dashboard admin</p>
    </div>
</div>

<div class="toast" id="toast"></div>

@vite('resources/js/admin/login.js')
</body>
</html>

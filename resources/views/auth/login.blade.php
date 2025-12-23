<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>

    <!-- Font & Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Custom CSS -->
    @vite('resources/css/admin/login.css')
</head>

<body>
    <div class="auth-wrapper">

        <!-- Background Shapes -->
        <div class="background-shape"></div>
        <div class="secondary-shape"></div>

        <!-- LOGIN FORM -->
        <div class="credentials-panel signin">
            <h2 class="slide-element">Login Admin</h2>

            @if ($errors->any())
                <p class="error-text slide-element">
                    {{ $errors->first() }}
                </p>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf

                <div class="field-wrapper slide-element">
                    <input type="email" name="email" required autofocus>
                    <label>Email</label>
                    <i class="fa-solid fa-envelope"></i>
                </div>

                <div class="field-wrapper slide-element password-field">
                    <input type="password" name="password" id="password" required>
                    <label>Password</label>
                    <i class="fa-solid fa-eye toggle-password" id="togglePassword"></i>
                </div>

                <div class="field-wrapper slide-element">
                    <button type="submit" class="submit-button">Sign In</button>
                </div>
            </form>
        </div>

        <!-- WELCOME SECTION -->
        <div class="welcome-section signin">
            <h2 class="slide-element">WELCOME BACK!</h2>
            <p class="slide-element">Silakan login untuk mengakses dashboard admin</p>
        </div>
    </div>

    <!-- JS -->
    @vite('resources/js/admin/login.js')
</body>
</html>

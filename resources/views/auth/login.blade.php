<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>

<body>

<div class="auth-container">

    <!-- LEFT LOGO -->
    <div class="auth-left">
        <img src="{{ asset('image/altie.png') }}" alt="Logo">
    </div>

    <!-- RIGHT FORM -->
    <div class="auth-right">

        <div class="auth-card">

            <!-- Tabs inside card -->
            <div class="auth-tabs">
                <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'active' : '' }}">
                    Login
                </a>
                <a href="{{ route('register.walisantri') }}" class="{{ request()->routeIs('register.walisantri') ? 'active' : '' }}">
                    Register
                </a>
            </div>

            <h2>Masuk</h2>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" type="email" class="form-input"
                        name="email" :value="old('email')" required autofocus />
                </div>

                <div class="form-group">
                    <x-input-label for="password" value="Password" />
                    <x-text-input id="password" type="password"
                        name="password" class="form-input" required />
                </div>

                <button class="btn-primary">Login</button>

            </form>

        </div>

    </div>

</div>

</body>
</html>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register Wali Santri</title>
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
                <a href="{{ route('login') }}"
                   class="{{ request()->routeIs('login') ? 'active' : '' }}">
                    Login
                </a>

                <a href="{{ route('register.walisantri') }}"
                   class="{{ request()->routeIs('register.walisantri') ? 'active' : '' }}">
                    Register
                </a>
            </div>

            <h2>Register</h2>

            <form method="POST" action="{{ route('register.walisantri.store') }}">
                @csrf

                <!-- Kode Keluarga -->
                <div class="form-group">
                    <x-input-label for="kode_keluarga" value="Kode Keluarga" />
                    <x-text-input id="kode_keluarga" type="text" 
                        class="form-input"
                        name="kode_keluarga" :value="old('kode_keluarga')" required />
                    <x-input-error :messages="$errors->get('kode_keluarga')" class="mt-2" />
                </div>

                <!-- Nama -->
                <div class="form-group">
                    <x-input-label for="nama" value="Nama Lengkap" />
                    <x-text-input id="nama" type="text"
                        class="form-input"
                        name="nama" :value="old('nama')" required />
                    <x-input-error :messages="$errors->get('nama')" class="mt-2" />
                </div>

                <!-- Email -->
                <div class="form-group">
                    <x-input-label for="email" value="Email" />
                    <x-text-input id="email" type="email"
                        class="form-input"
                        name="email" :value="old('email')" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- No HP -->
                <div class="form-group">
                    <x-input-label for="no_hp" value="No HP" />
                    <x-text-input id="no_hp" type="text"
                        class="form-input"
                        name="no_hp" :value="old('no_hp')" />
                    <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="form-group">
                    <x-input-label for="password" value="Password" />
                    <x-text-input id="password" type="password"
                        class="form-input"
                        name="password" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <x-input-label for="password_confirmation" value="Konfirmasi Password" />
                    <x-text-input id="password_confirmation" type="password"
                        class="form-input"
                        name="password_confirmation" required />
                </div>

                <button class="btn-primary">Daftar</button>

            </form>

        </div>

    </div>

</div>

</body>
</html>

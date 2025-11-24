<!DOCTYPE html>
<html lang="id" class="{{ session('darkMode') ? 'dark' : '' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pondok Pesantren Al-Ittihad</title>
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>

<body>

    <!-- TOP NAV -->
    <nav class="navbar">
        <div class="nav-left">
            <img src="/image/altie.png" class="nav-logo" alt="Logo">
            <span class="nav-title">Pondok Pesantren Al-Ittihad</span>
        </div>

        <div class="nav-right">
            <a href="{{ route('login') }}" class="nav-btn">Login</a>
            <a href="{{ route('register') }}" class="nav-btn register">Register</a>
        </div>
    </nav>


    <!-- HERO SECTION -->
    <section class="hero">

        <div class="hero-left">
            <h1>Selamat Datang di<br>Pondok Pesantren Al-Ittihad</h1>

            <p>
                Pendidikan Islami terpadu untuk membentuk generasi Qur’ani, berakhlak mulia, dan berwawasan luas.
            </p>

            <div class="hero-buttons">
                <a href="{{ route('login') }}" class="btn-primary">Masuk Sekarang</a>
                <a href="{{ route('register') }}" class="btn-outline">Daftar Wali Santri</a>
            </div>
        </div>

        <div class="hero-right">
            <img src="/image/altie.png" class="hero-logo" alt="Logo Pesantren">
        </div>

    </section>


    <!-- Background Decorations -->
    <div class="circle-decoration circle-1"></div>
    <div class="circle-decoration circle-2"></div>

</body>
</html>

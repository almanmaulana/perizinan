<!DOCTYPE html>
<html lang="id" class="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
body { transition: background-color 0.5s; }
.glass {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(8px);
    border: 1px solid rgba(34,197,94,0.3);
}
.dark body { background-color: #1f2937; }
.dark .glass { background: rgba(36,36,36,0.85); border: 1px solid rgba(34,197,94,0.5); }
.dark input { background-color: #242526; color: #e0e0e0; border-color: #4d4c4c; }
.dark input:focus { border-color: #22c55e; outline: none; box-shadow: 0 0 0 2px rgba(34,197,94,0.5); }
.dark .text-green-800 { color: #a3f7bf; }
.dark .text-green-700 { color: #6ee7b7; }
</style>
</head>

<body class="min-h-screen flex items-center justify-center p-4 transition-colors duration-500">

<div class="flex flex-col lg:flex-row w-full max-w-3xl shadow-xl rounded-xl overflow-hidden">

    <!-- LEFT: Logo -->
    <div class="lg:flex-1 bg-green-200 dark:bg-green-900 flex items-center justify-center p-6 hidden lg:flex">
        <img src="{{ asset('image/altie.png') }}" class="w-48 h-auto">
    </div>

    <!-- RIGHT: Login Form -->
    <div class="lg:flex-1 p-6 glass flex flex-col justify-between">
        <div>
            <h2 class="text-2xl lg:text-3xl font-bold text-green-800 dark:text-green-200 text-center mb-2">Masuk</h2>
            <p class="text-green-700 dark:text-green-300 text-center text-sm mb-6">Masukkan email dan password Anda.</p>

            <form method="POST" action="{{ route('login') }}" class="space-y-3">
                @csrf
                <!-- EMAIL -->
                <div>
                    <label class="block text-green-800 dark:text-green-200 font-medium mb-1">Email</label>
                    <input id="email" type="email" name="email" :value="old('email')"
                           class="w-full px-3 py-2 rounded-md border focus:ring-2 focus:ring-green-400 dark:focus:ring-green-500"
                           required autofocus>
                    @error('email')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PASSWORD -->
                <div>
                    <label class="block text-green-800 dark:text-green-200 font-medium mb-1">Password</label>
                    <input id="password" type="password" name="password"
                           class="w-full px-3 py-2 rounded-md border focus:ring-2 focus:ring-green-400 dark:focus:ring-green-500"
                           required>
                    @error('password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- General login error -->
                @if(session('error'))
                    <p class="text-red-600 text-sm mt-1 text-center">{{ session('error') }}</p>
                @endif

                <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white font-semibold py-2 rounded-md transition">
                    Login
                </button>
            </form>
        </div>

        <!-- Footer Links -->
        <div class="mt-4 text-center space-y-2">
            <p class="text-green-700 dark:text-green-300 text-sm">
                Belum punya akun?
                <a href="{{ route('register.walisantri') }}" class="text-green-800 dark:text-green-200 font-medium hover:underline">Daftar</a>
            </p>

    <a href="/" class="text-green-800 dark:text-green-200 hover:text-green-600 dark:hover:text-green-400 text-lg font-bold">
        &larr;
    </a>
        </div>
    </div>

</div>

<script>
if(localStorage.getItem('theme') === 'dark'){
    document.documentElement.classList.add('dark');
}
</script>
</body>
</html>

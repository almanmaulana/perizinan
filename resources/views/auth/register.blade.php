<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Wali Santri</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Efek glass di form */
        .glass {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(34,197,94,0.3);
        }

        /* Dark mode support */
        .dark body {
            background-color: #1f2937;
        }
        .dark .glass {
            background: rgba(36,36,36,0.85);
            border: 1px solid rgba(34,197,94,0.5);
        }
        .dark .text-green-800 { color: #a3f7bf; }
        .dark .text-green-700 { color: #6ee7b7; }
        .dark input { background-color: #242526; color: #e0e0e0; border-color: #4d4c4c; }
        .dark input:focus { border-color: #22c55e; outline: none; box-shadow: 0 0 0 2px rgba(34,197,94,0.5); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 transition-colors duration-500">

    <div class="flex flex-col lg:flex-row w-full max-w-3xl shadow-xl rounded-xl overflow-hidden">

        <!-- LEFT: Logo / Ilustrasi -->
        <div class="lg:flex-1 bg-green-200 dark:bg-green-900 flex items-center justify-center p-6 hidden lg:flex">
            <img src="{{ asset('image/altie.png') }}" class="w-48 h-auto">
        </div>

        <!-- RIGHT: Form -->
        <div class="lg:flex-1 p-6 glass">
            <h2 class="text-2xl lg:text-3xl font-bold text-green-800 dark:text-green-200 text-center mb-2">Register Wali Santri</h2>
            <p class="text-green-700 dark:text-green-300 text-center text-sm mb-6">Isi data di bawah untuk membuat akun.</p>

            <form method="POST" action="{{ route('register.walisantri.store') }}" class="space-y-3">
                @csrf

                <!-- KODE KELUARGA -->
                <div>
                    <label class="block text-green-800 dark:text-green-200 font-medium mb-1">Kode Keluarga</label>
                    <input type="text" id="kode_keluarga" name="kode_keluarga"
                           class="w-full px-3 py-2 rounded-md border focus:ring-2 focus:ring-green-400 dark:focus:ring-green-500"
                           value="{{ old('kode_keluarga') }}" required>
                    <p id="kodeStatus" class="text-sm mt-1"></p>
                    @error('kode_keluarga')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- NAMA -->
                <div>
                    <label class="block text-green-800 dark:text-green-200 font-medium mb-1">Nama Lengkap</label>
                    <input type="text" name="nama"
                           class="w-full px-3 py-2 rounded-md border focus:ring-2 focus:ring-green-400 dark:focus:ring-green-500"
                           value="{{ old('nama') }}" required>
                </div>

                <!-- EMAIL -->
                <div>
                    <label class="block text-green-800 dark:text-green-200 font-medium mb-1">Email</label>
                    <input type="email" name="email"
                           class="w-full px-3 py-2 rounded-md border focus:ring-2 focus:ring-green-400 dark:focus:ring-green-500"
                           value="{{ old('email') }}" required>
                    @error('email')
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    @enderror
                </div>

                <!-- NO HP -->
                <div>
                    <label class="block text-green-800 dark:text-green-200 font-medium mb-1">No HP</label>
                    <input type="text" name="no_hp"
                           class="w-full px-3 py-2 rounded-md border focus:ring-2 focus:ring-green-400 dark:focus:ring-green-500"
                           value="{{ old('no_hp') }}">
                </div>

                <!-- PASSWORD -->
                <div>
                    <label class="block text-green-800 dark:text-green-200 font-medium mb-1">Password</label>
                    <input type="password" name="password"
                           class="w-full px-3 py-2 rounded-md border focus:ring-2 focus:ring-green-400 dark:focus:ring-green-500"
                           required>
                </div>

                <!-- KONFIRMASI -->
                <div>
                    <label class="block text-green-800 dark:text-green-200 font-medium mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation"
                           class="w-full px-3 py-2 rounded-md border focus:ring-2 focus:ring-green-400 dark:focus:ring-green-500"
                           required>
                </div>

                <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 text-white font-semibold py-2 rounded-md transition">
                    Daftar
                </button>

                <p class="text-green-700 dark:text-green-300 text-sm text-center mt-3">
                    Sudah punya akun?
                    <a href="{{ route('login') }}" class="text-green-800 dark:text-green-200 font-medium hover:underline">Login</a>
                </p>
            
                <a href="/" class="text-green-800 dark:text-green-200 hover:text-green-600 dark:hover:text-green-400 text-small font-italic ">
                    &larr;  
                </a>
            </form>
        </div>

    </div>

    <!-- AJAX CEK KODE -->
    <script>
        document.getElementById("kode_keluarga").addEventListener("keyup", function () {
            let kode = this.value;
            let info = document.getElementById("kodeStatus");

            if (kode.length < 3) {
                info.innerHTML = "";
                return;
            }

            fetch(`/cek-kode-keluarga?kode=${kode}`)
                .then(res => res.json())
                .then(data => {
                    if (!data.santriAda) {
                        info.innerHTML = "❌ Kode keluarga tidak ditemukan";
                        info.className = "text-red-600 text-sm mt-1";
                    } else if (data.kodeDipakai) {
                        info.innerHTML = "❌ Kode keluarga sudah digunakan";
                        info.className = "text-red-600 text-sm mt-1";
                    } else {
                        info.innerHTML = "✔ Kode keluarga valid";
                        info.className = "text-green-600 text-sm mt-1";
                    }
                });
        });

        // Dark mode toggle otomatis berdasarkan localStorage
        if(localStorage.getItem('theme') === 'dark'){
            document.documentElement.classList.add('dark');
        }
    </script>
</body>
</html>

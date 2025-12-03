<!DOCTYPE html>
<html lang="id" class="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Selamat Datang - Pondok Pesantren Al-Ittihad</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
body { 
    transition: background-color 0.5s; 
    background-color: #1f2937; 
}

/* Glass card */
.glass {
    background: rgba(36,36,36,0.85);
    backdrop-filter: blur(10px);
    border-radius: 1rem;
    border: 1px solid rgba(34,197,94,0.5);
    box-shadow: 0 8px 24px rgba(0,0,0,0.3);
}

/* Buttons */
.btn-primary {
    width: 100%;
    background-color: #16a34a; /* bg-green-600 */
    color: white;
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    transition: background-color 0.3s;
}
.btn-primary:hover {
    background-color: #15803d; /* bg-green-700 */
}

.btn-secondary {
    width: 100%;
    background-color: #22c55e; /* dark:bg-green-500 */
    color: white;
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    transition: background-color 0.3s;
}
.btn-secondary:hover {
    background-color: #16a34a; /* dark:hover:bg-green-600 */
}

/* Background decorations */
.bg-circle {
    position: absolute;
    border-radius: 50%;
    opacity: 0.15;
    z-index: 0;
}
</style>
</head>
<body class="relative min-h-screen flex flex-col items-center justify-center p-4 overflow-hidden transition-colors duration-500">

<!-- Background circles -->
<div class="bg-circle w-60 h-60 bg-green-900 top-[-60px] left-[-60px]"></div>
<div class="bg-circle w-96 h-96 bg-green-800 bottom-[-80px] right-[-80px]"></div>

<!-- Logo -->
<div class="flex flex-col items-center mb-6 z-10">
    <img src="{{ asset('image/altie.png') }}" class="w-24 h-24 mb-3" alt="Logo">
    <h1 class="text-2xl md:text-3xl font-bold text-green-200 text-center">Pondok Pesantren Al-Ittihad</h1>
</div>

<!-- Glass Card -->
<div class="glass p-6 md:p-10 max-w-xl w-full text-center flex flex-col gap-4 z-10">
    <h2 class="text-xl md:text-2xl font-bold text-green-200">Selamat Datang!</h2>
    <p class="text-green-300">
        Pendidikan Islami terpadu untuk membentuk generasi Qur’ani, berakhlak mulia, dan berwawasan luas.
    </p>
    <div class="flex flex-col md:flex-row gap-4 mt-4 justify-center">
        <a href="{{ route('login') }}" class="btn-primary flex-1 ">Login</a>
        <a href="{{ route('register.walisantri') }}" class="btn-secondary flex-1">Register</a>
    </div>
</div>

</body>
</html>

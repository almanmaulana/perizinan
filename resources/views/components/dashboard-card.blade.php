@props(['title','value','icon','color'])

<div class="relative flex items-center justify-between p-5 bg-white dark:bg-gray-800 rounded-xl shadow-lg transition transform hover:-translate-y-1 hover:shadow-2xl">
    {{-- Konten teks --}}
    <div>
        <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
            {{ $title }}
        </h3>
        <span class="mt-1 block text-2xl font-bold text-gray-800 dark:text-gray-100">
            {{ $value }}
        </span>
    </div>

    {{-- Ikon --}}
    <div class="text-4xl text-{{ $color }}-500 dark:text-{{ $color }}-400 opacity-80">
        <i class="fa-solid {{ $icon }}"></i>
    </div>

    {{-- Garis bawah sebagai aksen --}}
    <div class="absolute bottom-0 left-0 w-full h-1 bg-{{ $color }}-200 rounded-b-xl"></div>
</div>

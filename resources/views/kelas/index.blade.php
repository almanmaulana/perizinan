<x-app-layout>
    <style>
        nav[role="navigation"] {
            position: static !important;
            display: flex !important;
            justify-content: flex-start !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 auto !important;
            background: transparent !important;
        }
    </style>

    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        {{-- 🧭 Header --}}
        <div class="flex flex-col sm:flex-row justify-between sm:items-center mb-6 gap-4">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <i class="fa-solid fa-chalkboard text-blue-600 dark:text-blue-400"></i>
                Daftar Kelas
            </h1>

            @if(Auth::user()->role === 'keamanan')
                <a href="{{ route('kelas.create') }}"
                   class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded-lg shadow transition">
                    <i class="fa-solid fa-plus"></i>
                    Tambah Kelas
                </a>
            @endif
        </div>

        {{-- 🎯 Filter + Search + Urutkan --}}
        <form method="GET" action="{{ route('kelas.index') }}" id="filterForm" class="flex flex-wrap items-center gap-3 mb-6">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Cari nama kelas..."
                class="border rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 flex-1"
            />

            <select name="jenjang" onchange="this.form.submit()"
                class="border rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                <option value="">Semua Jenjang</option>
                <option value="SMP" {{ request('jenjang') == 'SMP' ? 'selected' : '' }}>SMP</option>
                <option value="SMA" {{ request('jenjang') == 'SMA' ? 'selected' : '' }}>SMA</option>
                <option value="SMK" {{ request('jenjang') == 'SMK' ? 'selected' : '' }}>SMK</option>
            </select>

            <select name="sort" onchange="this.form.submit()"
                class="border rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                <option value="">Urutkan...</option>
                <option value="nama_asc" {{ request('sort') == 'nama_asc' ? 'selected' : '' }}>Nama Kelas A–Z</option>
                <option value="nama_desc" {{ request('sort') == 'nama_desc' ? 'selected' : '' }}>Nama Kelas Z–A</option>
                <option value="jenjang_asc" {{ request('sort') == 'jenjang_asc' ? 'selected' : '' }}>Jenjang: SMP → SMA → SMK</option>
                <option value="jenjang_desc" {{ request('sort') == 'jenjang_desc' ? 'selected' : '' }}>Jenjang: SMK → SMA → SMP</option>
            </select>

            @if(request()->has('sort') || request()->has('jenjang') || request()->has('search'))
                <a href="{{ route('kelas.index') }}"
                   class="text-sm text-gray-600 dark:text-gray-300 hover:text-blue-600">
                    Reset
                </a>
            @endif

            <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded-lg hover:bg-blue-700 transition">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>

        {{-- ✅ Alert sukses --}}
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/40 border border-green-300 dark:border-green-700 
                        text-green-800 dark:text-green-200 rounded-lg flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-green-600 dark:text-green-400"></i>
                {{ session('success') }}
            </div>
        @endif

        {{-- 📋 Card Tabel + Pagination --}}
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg border border-gray-300 dark:border-gray-700 overflow-x-auto">

            <table class="min-w-full text-sm text-left text-gray-600 dark:text-gray-200">
            <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 uppercase">
                <tr>
                    <th class="px-4 py-3">No</th>
                    <th class="px-4 py-3">Kelas</th>
                    <th class="px-4 py-3">Jenjang</th>
                    <th class="px-4 py-3">Wali Kelas</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kelas as $index => $k)
                    <tr class="border-b border-gray-300 dark:border-gray-700 transition hover:bg-blue-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-3">{{ $loop->iteration + ($kelas->currentPage()-1)*$kelas->perPage() }}</td>
                        <td class="px-4 py-3 font-medium">{{ $k->formatted_name }}</td>
                        <td class="px-4 py-3">{{ $k->jenjang }}</td>
                        <td class="px-4 py-3">{{ $k->waliKelas->nama ?? '-' }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center flex-wrap gap-2">
                                <a href="{{ route('kelas.show', $k->id) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md shadow transition">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                @if(Auth::user()->role === 'keamanan')
                                    <a href="{{ route('kelas.edit', $k->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md shadow transition">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    <form action="{{ route('kelas.destroy', $k->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Yakin mau hapus kelas ini?')"
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-md shadow transition">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-gray-500 dark:text-gray-400">Belum ada data kelas.</td>
                    </tr>
                @endforelse
            </tbody>

            </table>

            {{-- 📄 Pagination di dalam card --}}
            <div class="px-4 py-3 border-t border-gray-300 dark:border-gray-700 flex justify-center">
                {{ $kelas->appends(request()->query())->links('vendor.pagination.custom') }}
            </div>

        </div>
    </div>

    {{-- 🔧 Script auto-submit search/filter --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const filterForm = document.getElementById('filterForm');
            const searchInput = filterForm.querySelector('input[name="search"]');

            let typingTimer;
            const typingDelay = 500;
            searchInput.addEventListener('input', () => {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => filterForm.submit(), typingDelay);
            });

            filterForm.querySelectorAll('select').forEach(el => {
                el.addEventListener('change', () => filterForm.submit());
            });
        });
    </script>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 flex items-center gap-2">
                <i class="fa-solid fa-user-graduate"></i> Data Santri
            </h2>
            <a href="{{ route('santri.create') }}"
               class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition flex items-center gap-1">
                <i class="fa-solid fa-user-plus"></i> Tambah Santri
            </a>
        </div>
    </x-slot>

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

    <div class="py-6 px-6 lg:px-8 font-inter w-full">
        <div class="bg-gray-100 dark:bg-gray-800 shadow-lg sm:rounded-2xl p-6 text-gray-900 dark:text-gray-100">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-lg flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif
            
            @if(session('import_errors'))
                <div class="mb-4 p-3 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-lg">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation"></i> Terjadi error pada import:
                    </div>
                    <ul class="mt-2 list-disc list-inside text-sm text-red-700 dark:text-red-300">
                        @foreach(session('import_errors') as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            {{-- Toolbar Atas: Search + Filter + Pindah Kelas --}}
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">

                {{-- Search --}}
                <form method="GET" action="{{ route('santri.index') }}" class="flex flex-1 max-w-sm relative">
                    <input type="text" name="search" id="searchInput" placeholder="Cari NIS / Nama..."
                           value="{{ request('search') }}"
                           class="w-full border rounded px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-green-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                    @if(request('search'))
                        <button type="button" id="clearSearch"
                                class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-300 hover:text-gray-700 dark:hover:text-gray-100 transition"
                                title="Hapus Pencarian">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    @endif
                    <button type="submit"
                            class="ml-2 px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition flex items-center gap-1">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>

                {{-- Filter Per Page & Sort --}}
                <form method="GET" action="{{ route('santri.index') }}" class="flex gap-2">
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                    <select name="per_page" onchange="this.form.submit()"
                            class="border rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                        <option value="10" {{ request('per_page')==10?'selected':'' }}>1-10</option>
                        <option value="20" {{ request('per_page')==20?'selected':'' }}>1-20</option>
                        <option value="30" {{ request('per_page')==30?'selected':'' }}>1-30</option>
                        <option value="40" {{ request('per_page')==40?'selected':'' }}>1-40</option>
                        <option value="50" {{ request('per_page')==50?'selected':'' }}>1-50</option>
                        <option value="75" {{ request('per_page')==75?'selected':'' }}>1-75</option>
                        <option value="100" {{ request('per_page')==100?'selected':'' }}>1-100</option>
                    </select>
                    <select name="sort" onchange="this.form.submit()"
                            class="border rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                        <option value="asc" {{ request('sort')=='asc'?'selected':'' }}>A-Z</option>
                        <option value="desc" {{ request('sort')=='desc'?'selected':'' }}>Z-A</option>
                    </select>
                </form>

                {{-- Pindah Kelas --}}
                @if(in_array(auth()->user()->role, ['keamanan','wali_kelas']))
                    <form id="bulkMoveForm" action="{{ route('santri.bulkMove') }}" method="POST"
                          class="flex items-center gap-2 flex-wrap">
                        @csrf
                        <select name="kelas_id" id="kelasSelect" class="border rounded-lg px-3 py-2 dark:bg-gray-700 dark:text-gray-100">
                            <option value="">Pilih Kelas Tujuan</option>
                            @foreach($kelasList as $k)
                                <option value="{{ $k->id }}">{{ $k->tingkat }} {{ $k->nama_kelas }} {{ $k->jurusan }}</option>
                            @endforeach
                        </select>
                        <button type="submit" id="moveBtn" disabled
                                class="px-3 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 transition flex items-center gap-1 opacity-50 cursor-not-allowed">
                            <i class="fa-solid fa-right-left"></i> Pindah Kelas
                        </button>
                    </form>
                @endif
            </div>

            {{-- Table --}}
            <form id="bulkForm" method="POST">
                @csrf
                <div class="overflow-x-auto border rounded-lg">
                    <table class="w-full text-sm border-collapse">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                                @if(auth()->user()->role === 'keamanan')
                                    <th class="px-4 py-3 text-center">
                                        <input type="checkbox" id="selectAll" class="cursor-pointer">
                                    </th>
                                @endif
                                <th class="px-4 py-3 text-left">NIS</th>
                                <th class="px-4 py-3 text-left">Nama</th>
                                <th class="px-4 py-3 text-left">Kelas</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($santris as $s)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                                    @if(auth()->user()->role === 'keamanan')
                                        <td class="px-4 py-3 text-center">
                                            <input type="checkbox" name="ids[]" value="{{ $s->id }}" class="rowCheckbox cursor-pointer">
                                        </td>
                                    @endif
                                    <td class="px-4 py-3">{{ $s->nis }}</td>
                                    <td class="px-4 py-3">{{ $s->nama }}</td>
                                    <td class="px-4 py-3">{{ $s->kelas_label }}</td>
                                    <td class="px-4 py-3 text-center flex justify-center gap-2 flex-wrap">
                                        <a href="{{ route('santri.show', $s->id) }}" title="Lihat"
                                           class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a href="{{ route('santri.edit', $s->id) }}" title="Edit"
                                           class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 transition">
                                            <i class="fa-solid fa-pen"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ auth()->user()->role === 'keamanan' ? 5 : 4 }}" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        Tidak ada data santri.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Aksi Bawah Tabel: Hapus, Template, Import --}}
                <div class="mt-4 flex flex-col sm:flex-row gap-2 justify-between flex-wrap items-center">

                    {{-- Bulk Delete --}}
                    @if(auth()->user()->role === 'keamanan')
                        <button type="button" id="bulkDeleteBtn"
                                class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition flex items-center gap-1 opacity-50 cursor-not-allowed">
                            <i class="fa-solid fa-trash"></i> Hapus Terpilih
                        </button>
                    @endif

                    {{-- Template + Import --}}
                    <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto items-center">
                        <a href="{{ asset('files/template-santri.xlsx') }}"
                           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition flex items-center gap-1">
                            <i class="fa-solid fa-file-excel"></i> Template
                        </a>
                        <form action="{{ route('santri.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <label for="fileInput"
                                   class="cursor-pointer px-4 py-2 bg-gray-100 dark:bg-gray-700 border rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition text-gray-700 dark:text-gray-200 flex items-center gap-1">
                                <i class="fa-solid fa-file-import"></i> Import File
                            </label>
                            <input type="file" name="file" id="fileInput" class="hidden" onchange="this.form.submit()">
                        </form>
                    </div>

                </div>
            </form>

            {{-- Pagination --}}
            <div class="mt-6 flex flex-col sm:flex-row justify-between items-center text-sm text-gray-600 dark:text-gray-400 gap-2">
                <div class="mt-2 sm:mt-0">{{ $santris->links('vendor.pagination.custom') }}</div>
            </div>

        </div>
    </div>

    {{-- Script --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const clearBtn = document.getElementById('clearSearch');
            const searchInput = document.getElementById('searchInput');
            const selectAll = document.getElementById("selectAll");
            const checkboxes = document.querySelectorAll(".rowCheckbox");
            const bulkDeleteBtn = document.getElementById("bulkDeleteBtn");
            const bulkMoveBtn = document.getElementById("moveBtn");
            const kelasSelect = document.getElementById("kelasSelect");
            const bulkForm = document.getElementById("bulkForm");

            function updateButtons() {
                const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
                const kelasSelected = kelasSelect?.value;

                if(bulkDeleteBtn) {
                    bulkDeleteBtn.disabled = !anyChecked;
                    bulkDeleteBtn.classList.toggle('opacity-50', !anyChecked);
                    bulkDeleteBtn.classList.toggle('cursor-not-allowed', !anyChecked);
                }

                if(bulkMoveBtn) {
                    bulkMoveBtn.disabled = !(anyChecked && kelasSelected);
                    bulkMoveBtn.classList.toggle('opacity-50', !(anyChecked && kelasSelected));
                    bulkMoveBtn.classList.toggle('cursor-not-allowed', !(anyChecked && kelasSelected));
                }
            }

            selectAll?.addEventListener("change", () => {
                checkboxes.forEach(cb => cb.checked = selectAll.checked);
                updateButtons();
            });
            checkboxes.forEach(cb => cb.addEventListener("change", updateButtons));
            kelasSelect?.addEventListener("change", updateButtons);

            clearBtn?.addEventListener('click', () => {
                searchInput.value = '';
                window.location.href = "{{ route('santri.index') }}";
            });

            // Bulk Delete submit
            bulkDeleteBtn?.addEventListener("click", function() {
                bulkForm.action = "{{ route('santri.bulkDelete') }}";
                bulkForm.method = "POST";
                bulkForm.submit();
            });

            // Bulk Move submit
            bulkMoveBtn?.addEventListener("click", function() {
                bulkForm.action = "{{ route('santri.bulkMove') }}";
                bulkForm.method = "POST";
                bulkForm.submit();
            });
        });
    </script>
</x-app-layout>

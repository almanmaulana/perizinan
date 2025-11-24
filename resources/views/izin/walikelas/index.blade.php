<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight flex items-center gap-2">
            🏫 Validasi Izin Santri (Wali Kelas)
        </h2>
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

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto bg-gray-100 dark:bg-gray-800 shadow-xl rounded-xl p-5">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-lg flex items-center gap-2">
                    ✅ {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-lg flex items-center gap-2">
                    ⚠️ {{ session('error') }}
                </div>
            @endif

            {{-- Search & Filter --}}
            <form method="GET" id="filterForm" class="flex flex-wrap gap-2 mb-4 items-center bg-white dark:bg-gray-700 p-4 rounded-xl shadow-inner">
                <input type="text" name="q" placeholder="Cari NIS / Nama" value="{{ request('q') }}"
                       class="border rounded px-2 py-1 text-sm dark:bg-gray-600 dark:border-gray-500 dark:text-gray-100 flex-1">
                <select name="status" class="border rounded px-2 py-1 text-sm dark:bg-gray-600 dark:border-gray-500 dark:text-gray-100">
                    <option value="">Semua Status</option>
                    <option value="pending_wali_kelas" @selected(request('status')=='pending_wali_kelas')>Pending</option>
                    <option value="disetujui_wali_kelas" @selected(request('status')=='disetujui_wali_kelas')>Disetujui</option>
                    <option value="ditolak_wali_kelas" @selected(request('status')=='ditolak_wali_kelas')>Ditolak</option>
                </select>
                <label for="tanggal" class="text-sm font-semibold">Tanggal:</label>
                <input type="date" name="tanggal" id="tanggal" class="border rounded px-2 py-1 text-sm dark:bg-gray-600 dark:border-gray-500 dark:text-gray-100"
                       value="{{ request('tanggal') }}">
            </form>

            {{-- Table --}}
            <div class="table-container overflow-x-auto rounded-xl border p-4">
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                        <tr>
                            <th class="px-3 py-2">NIS</th>
                            <th class="px-3 py-2">Nama Santri</th>
                            <th class="px-3 py-2">Jenis</th>
                            <th class="px-3 py-2">Durasi</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @forelse($izinList as $izin)
                            @php
                                $kelas = $izin->santri->kelas;
                                $namaKelas = $kelas ? $kelas->tingkat.' '.$kelas->nama_kelas.' '.$kelas->jurusan : '-';
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                <td class="px-3 py-2 align-top">{{ $izin->santri->nis }}</td>
                                <td class="px-3 py-2 font-semibold align-top">
                                    {{ $izin->santri->nama }}
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $namaKelas }}</div>
                                </td>
                                <td class="px-3 py-2 align-top">
                                    <button x-data @click.stop="$dispatch('open-izin-modal', {{ $izin->id }})" class="text-blue-600 text-sm flex items-center gap-1">
                                        <i class="fa-solid fa-eye"></i>
                                        <span class="ml-1">{{ $izin->jenis_izin }}</span>
                                    </button>
                                </td>
                                <td class="px-3 py-2">
                                    {{$izin->tgl_mulai_disetujui?->format('d/m/Y') ?? '-' }}
                                    -
                                    {{ $izin->tgl_selesai_disetujui?->format('d/m/Y') ?? '-' }}
                                </td>

                                <td class="px-3 py-2 align-top">
                                    @php
                                        $statusMap = [
                                            'pending_wali_kelas' => ['bg-blue-100', 'text-blue-800', 'Pending Wali Kelas'],
                                            'pending_keamanan'   => ['bg-blue-100', 'text-blue-800', 'Pending Keamanan'],
                                            'disetujui_keamanan' => ['bg-green-100', 'text-green-800', 'Disetujui Keamanan'],
                                            'ditolak_wali_kelas' => ['bg-red-100', 'text-red-800', 'Ditolak Wali Kelas'],
                                            'ditolak_keamanan'   => ['bg-red-100', 'text-red-800', 'Ditolak Keamanan'],
                                        ];

                                        $bg = $statusMap[$izin->status][0] ?? 'bg-gray-100';
                                        $text = $statusMap[$izin->status][1] ?? 'text-gray-500';
                                        $label = $statusMap[$izin->status][2] ?? '-';
                                    @endphp

                                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $bg }} {{ $text }}">
                                        {{ $label }}
                                    </span>
                                </td>

                                <td class="px-3 py-2 text-center align-top">
                                    @if($izin->status=='pending_wali_kelas')
                                        <div class="flex flex-col gap-2 items-center">
                                            <form action="{{ route('izin.walikelas.approve',$izin->id) }}" method="POST" class="flex gap-1 items-center">
                                                @csrf
                                                <button class="px-3 py-1 bg-green-600 text-white rounded text-xs">✔</button>
                                            </form>
                                            <form action="{{ route('izin.walikelas.reject',$izin->id) }}" method="POST" class="flex gap-1 items-center">
                                                @csrf
                                                <input type="text" name="catatan" placeholder="Catatan" class="border rounded px-1 py-1 text-xs" required>
                                                <button class="px-3 py-1 bg-red-600 text-white rounded text-xs">✘</button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">Selesai</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-gray-500 dark:text-gray-400">Tidak ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4 w-full flex justify-center">
                <div class="inline-block">
                    {{ $izinList->appends(request()->query())->links('vendor.pagination.custom') }}
                </div>
            </div>

            {{-- Modal --}}
            @foreach($izinList as $izin)
                <div
                    x-data="{ open:false }"
                    x-on:open-izin-modal.window="if($event.detail === {{ $izin->id }}) open = true"
                    x-show="open"
                    x-cloak
                    class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
                    <div class="bg-white dark:bg-gray-800 p-5 rounded-lg w-80">
                        <h2 class="font-bold text-lg mb-2">📄 Alasan</h2>
                        <p class="text-gray-700 dark:text-gray-200 mb-4">{{ $izin->alasan ?? 'Tidak ada alasan.' }}</p>
                        <button @click="open=false" class="w-full py-2 bg-blue-600 text-white rounded">Tutup</button>
                    </div>
                </div>
            @endforeach

        </div>
    </div>

    {{-- Scripts --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const filterForm = document.getElementById('filterForm');
            const searchInput = filterForm.querySelector('input[name="q"]');
            let typingTimer;
            const typingDelay = 500;
            searchInput.addEventListener('input', () => {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => filterForm.submit(), typingDelay);
            });
            filterForm.querySelectorAll('select, input[type="date"]').forEach(el => {
                el.addEventListener('change', () => filterForm.submit());
            });
        });

        document.addEventListener('click', function(e){
            if(e.target.closest('[x-data]')) {
                e.stopPropagation();
            }
        });
    </script>
</x-app-layout>

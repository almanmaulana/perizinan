<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight flex items-center gap-2">
            🛡️ Validasi Izin Santri 
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

    {{-- Tombol Lihat Data Terhapus --}}
    <div class="me-5 flex justify-end pe-5">
        <a href="{{ route('izin.keamanan.trash') }}" class="px-4 py-2 bg-blue-700 text-white rounded hover:bg-blue-800 transition">
           <i class="fa-solid fa-trash-restore"></i> Sampah
        </a>
    </div>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto bg-gray-100 dark:bg-gray-800 shadow-xl rounded-xl p-5">

            {{-- FLASH MESSAGE --}}
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

            {{-- FILTER --}}
            <form method="GET" id="filterForm" class="flex flex-wrap gap-2 mb-4 items-center bg-white dark:bg-gray-700 p-4 rounded-xl shadow-inner">
                <input type="text" name="q" placeholder="Cari NIS / Nama" value="{{ request('q') }}"
                       class="border rounded px-2 py-1 text-sm dark:bg-gray-600 dark:border-gray-500 dark:text-gray-100 flex-1">

                <select name="status" class="border rounded px-2 py-1 text-sm dark:bg-gray-600 dark:border-gray-500 dark:text-gray-100">
                    <option value="">Semua Status</option>
                    <option value="pending_keamanan" @selected(request('status')=='pending_keamanan')>Pending</option>
                    <option value="disetujui_keamanan" @selected(request('status')=='disetujui_keamanan')>Disetujui</option>
                    <option value="ditolak_keamanan" @selected(request('status')=='ditolak_keamanan')>Ditolak</option>
                </select>

                <select name="status_denda" class="border rounded px-2 py-1 text-sm dark:bg-gray-600 dark:border-gray-500 dark:text-gray-100">
                    <option value="">Semua Denda</option>
                    <option value="belum_dibayar" @selected(request('status_denda')=='belum_dibayar')>Belum Bayar</option>
                    <option value="dibayar" @selected(request('status_denda')=='dibayar')>Sudah Bayar</option>
                </select>

                <label for="simulasi" class="text-sm font-semibold">Tanggal:</label>
                <input type="date" name="simulasi" id="simulasi" class="border rounded px-2 py-1 text-sm dark:bg-gray-600 dark:border-gray-500 dark:text-gray-100" value="{{ request('simulasi') }}">
            </form>

            {{-- TABLE --}}
            <div class="table-container overflow-x-auto rounded-xl border p-4">
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                        <tr>
                            <th class="px-3 py-2">NIS</th>
                            <th class="px-3 py-2">Nama Santri</th>
                            <th class="px-3 py-2">Jenis</th>
                            <th class="px-3 py-2">Durasi</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Lapor</th>
                            <th class="px-3 py-2">Denda</th>
                            <th class="px-3 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @forelse($izinList as $izin)

                            @php
                                $kelas = $izin->santri->kelas;
                                $namaKelas = $kelas ? $kelas->tingkat.' '.$kelas->nama_kelas.' '.$kelas->jurusan : '-';

                                $denda = $izin->status_lapor=='sudah_lapor'
                                    ? $izin->denda
                                    : $izin->denda_berjalan;

                                $warna = 'text-gray-400';

                                if ($izin->status=='disetujui_keamanan') {
                                    $warna = ($izin->status_lapor=='belum_lapor')
                                        ? ($denda>0 ? 'text-red-600' : 'text-gray-400')
                                        : ($izin->status_denda=='dibayar' ? 'text-green-600' : 'text-red-600');
                                }
                            @endphp

                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">

                                <td class="px-3 py-2 align-top">{{ $izin->santri->nis }}</td>

                                <td class="px-3 py-2 font-semibold align-top">
                                    {{ $izin->santri->nama }}
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $namaKelas }}</div>
                                </td>

                                <td class="px-3 py-2 align-top">
                                    <button x-data @click.stop="$dispatch('open-izin-modal', {{ $izin->id }})"
                                            class="text-blue-600 text-sm flex items-center gap-1">
                                        <i class="fa-solid fa-eye"></i> <span class="ml-1">{{ $izin->jenis_izin }}</span>
                                    </button>
                                </td>

                                <td class="px-3 py-2 align-top">
                                    {{ $izin->status=='disetujui_keamanan' ? $izin->durasi : '-' }}
                                </td>

                                {{-- STATUS --}}
                                <td class="px-3 py-2 align-top">
                                    @php
                                        $statusMap = [
                                            'pending_keamanan' => ['bg-blue-100', 'text-blue-800', 'Pending'],
                                            'disetujui_keamanan' => ['bg-green-100', 'text-green-800', 'Disetujui'],
                                            'ditolak_keamanan' => ['bg-red-100', 'text-red-800', 'Ditolak'],
                                        ];

                                        $bg = $statusMap[$izin->status][0] ?? 'bg-gray-100';
                                        $text = $statusMap[$izin->status][1] ?? 'text-gray-500';
                                        $label = $statusMap[$izin->status][2] ?? ucfirst(str_replace('_',' ', $izin->status));
                                    @endphp

                                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $bg }} {{ $text }}">
                                        {{ $label }}
                                    </span>
                                </td>

                                {{-- LAPOR --}}
                                <td class="px-3 py-2 align-top">
                                    @if($izin->status!='disetujui_keamanan')
                                        <span class="text-gray-400">-</span>

                                    @elseif($izin->status_lapor=='sudah_lapor')
                                        <span class="bg-green-600 text-white px-3 py-1 rounded text-xs">✔ Sudah</span>

                                    @else
                                        {{-- TOMBOL LAPOR (HANYA 1 SEKARANG) --}}
                                        <form action="{{ route('izin.keamanan.lapor',$izin->id) }}" method="POST">
                                            @csrf
                                            <button class="bg-red-600 text-white px-3 py-1 rounded text-xs">Belum</button>
                                        </form>
                                    @endif
                                </td>

                                {{-- DENDA --}}
                                <td class="px-3 py-2 font-bold align-top">
                                    @php
                                        $isBelumBayar = $izin->status_lapor=='sudah_lapor'
                                            && $izin->status_denda=='belum_dibayar'
                                            && $denda>0;
                                    @endphp

                                    @if($isBelumBayar)
                                        <span x-data @click.stop="$dispatch('open-bayar-modal', {{ $izin->id }})"
                                              class="cursor-pointer {{ $warna }}">
                                            {{ 'Rp '.number_format($denda,0,',','.') }}
                                        </span>
                                    @else
                                        <span class="{{ $warna }}">
                                            {{ $denda>0 ? 'Rp '.number_format($denda,0,',','.') : '-' }}
                                        </span>
                                    @endif
                                </td>

                                {{-- AKSI --}}
                                <td class="px-3 py-2 text-center align-top">

                                    @if($izin->trashed())
                                        <form action="{{ route('izin.keamanan.restore', $izin->id) }}" method="POST" class="mb-1">
                                            @csrf
                                            <button class="px-3 py-1 bg-blue-600 text-white rounded text-xs">Restore</button>
                                        </form>

                                        <form action="{{ route('izin.keamanan.forceDelete', $izin->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button class="px-3 py-1 bg-red-800 text-white rounded text-xs">Hapus Permanen</button>
                                        </form>

                                    @else

                                        @if($izin->status=='pending_keamanan')
                                            <div class="flex flex-col gap-2 items-center">

                                                <form action="{{ route('izin.keamanan.approve',$izin->id) }}" method="POST"
                                                      class="flex gap-1 items-center">
                                                    @csrf
                                                    <input type="date" name="tgl_mulai_disetujui" class="border rounded px-1 py-1 text-xs">
                                                    <input type="date" name="tgl_selesai_disetujui" class="border rounded px-1 py-1 text-xs">
                                                    <button class="px-3 py-1 bg-green-600 text-white rounded text-xs">✔</button>
                                                </form>

                                                <form action="{{ route('izin.keamanan.reject',$izin->id) }}" method="POST"
                                                      class="flex gap-1 items-center">
                                                    @csrf
                                                    <input type="text" name="catatan" placeholder="Catatan"
                                                           class="border rounded px-1 py-1 text-xs">
                                                    <button class="px-3 py-1 bg-red-600 text-white rounded text-xs">✘</button>
                                                </form>

                                            </div>
                                        @endif

                                        {{-- SOFT DELETE --}}
                                        <form action="{{ route('izin.keamanan.softDelete', $izin->id) }}" method="POST" class="mt-1">
                                            @csrf
                                            <button class="px-3 py-1 bg-red-600 text-white rounded text-xs"> <i class="fa-solid fa-trash"></i></button>
                                        </form>

                                    @endif
                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-gray-500 dark:text-gray-400">
                                    Tidak ada data.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            <div class="mt-4 w-full flex justify-center">
                <div class="inline-block">
                    {{ $izinList->appends(request()->query())->links('vendor.pagination.custom') }}
                </div>
            </div>

        </div>
    </div>

    {{-- MODAL - DIPINDAH KELUAR LOOP (AGAR TIDAK GANDA) --}}
    @foreach($izinList as $izin)
        <div x-data="{ open:false }"
             x-on:open-izin-modal.window="if($event.detail === {{ $izin->id }}) open = true"
             x-show="open" x-cloak
             class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">

            <div class="bg-white dark:bg-gray-800 p-5 rounded-lg w-80">
                <h2 class="font-bold text-lg mb-2">📄 Alasan</h2>
                <p class="text-gray-700 dark:text-gray-200 mb-4">{{ $izin->alasan ?? 'Tidak ada alasan.' }}</p>
                <button @click="open=false" class="w-full py-2 bg-blue-600 text-white rounded">Tutup</button>
            </div>
        </div>

        @php
            $denda = $izin->status_lapor=='sudah_lapor' ? $izin->denda : $izin->denda_berjalan;
            $isBelumBayar = $izin->status_lapor=='sudah_lapor' && $izin->status_denda=='belum_dibayar' && $denda>0;
        @endphp

        @if($isBelumBayar)
            <div x-data="{ open:false }"
                 x-on:open-bayar-modal.window="if($event.detail === {{ $izin->id }}) open = true"
                 x-show="open" x-cloak
                 class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">

                <div class="bg-white dark:bg-gray-800 p-5 rounded-lg w-80">
                    <h2 class="font-bold text-lg mb-3">💰 Konfirmasi Pembayaran</h2>
                    <p class="mb-4">Bayar denda sebesar <b>Rp {{ number_format($denda,0,',','.') }}</b> ?</p>

                    <form action="{{ route('izin.keamanan.bayar',$izin->id) }}" method="POST">
                        @csrf
                        <button class="w-full py-2 bg-green-600 text-white rounded mb-2">Bayar</button>
                    </form>

                    <button @click="open=false" class="w-full py-2 bg-gray-400 rounded text-white">Batal</button>
                </div>
            </div>
        @endif
    @endforeach

    {{-- SCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const filterForm = document.getElementById('filterForm');
            const searchInput = filterForm.querySelector('input[name="q"]');
            let typingTimer;

            searchInput.addEventListener('input', () => {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => filterForm.submit(), 500);
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

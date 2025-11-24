<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight">
            Laporan Izin Santri
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
        <div class="max-w-7xl mx-auto bg-white dark:bg-gray-800 shadow rounded-lg p-5">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mb-4 p-3 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-lg">
                    ✅ {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-3 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-lg">
                    ⚠️ {{ session('error') }}
                </div>
            @endif

            {{-- Filter --}}
            <form method="GET" id="filterForm" class="flex flex-wrap gap-2 mb-4 items-center">
                <input type="text" name="q" placeholder="Cari NIS/Nama" value="{{ request('q') }}"
                       class="border rounded px-2 py-1 text-sm flex-1 dark:bg-gray-700 dark:text-gray-100">

                <select name="kelas_id" class="border rounded px-2 py-1 text-sm dark:bg-gray-700 dark:text-gray-100">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas_id')==$kelas->id?'selected':'' }}>
                            {{ $kelas->tingkat.' '.$kelas->nama_kelas.' '.$kelas->jurusan }}
                        </option>
                    @endforeach
                </select>

                <select name="jenis_izin" class="border rounded px-2 py-1 text-sm dark:bg-gray-700 dark:text-gray-100">
                    <option value="">Semua Jenis</option>
                    <option value="Sakit" {{ request('jenis_izin')=='Sakit'?'selected':'' }}>Sakit</option>
                    <option value="Kegiatan" {{ request('jenis_izin')=='Kegiatan'?'selected':'' }}>Kegiatan</option>
                    <option value="Lainnya" {{ request('jenis_izin')=='Lainnya'?'selected':'' }}>Lainnya</option>
                </select>

                <select name="tingkat" class="border rounded px-2 py-1 text-sm dark:bg-gray-700 dark:text-gray-100">
                    <option value="">Semua Tingkat</option>
                    @for($i=7;$i<=12;$i++)
                        <option value="{{ $i }}" {{ request('tingkat')==$i?'selected':'' }}>{{ $i }}</option>
                    @endfor
                </select>

                <select name="periode" class="border rounded px-2 py-1 text-sm dark:bg-gray-700 dark:text-gray-100">
                    <option value="">Semua Periode</option>
                    <option value="minggu" {{ request('periode')=='minggu'?'selected':'' }}>Minggu ini</option>
                    <option value="bulan" {{ request('periode')=='bulan'?'selected':'' }}>Bulan ini</option>
                    <option value="tahun" {{ request('periode')=='tahun'?'selected':'' }}>Tahun ini</option>
                </select>
            </form>

            {{-- Tabel --}}
            <div class="overflow-x-auto rounded border p-4 bg-white dark:bg-gray-800">
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                        <tr>
                            <th class="px-3 py-2">No</th>
                            <th class="px-3 py-2">NIS</th>
                            <th class="px-3 py-2">Nama</th>
                            <th class="px-3 py-2">Kelas</th>
                            <th class="px-3 py-2">Jenis Izin</th>
                            <th class="px-3 py-2">Denda</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($izinList as $index => $izin)
                            @php
                                $kelas = $izin->santri->kelas;
                                $namaKelas = $kelas ? $kelas->tingkat.' '.$kelas->nama_kelas.' '.$kelas->jurusan : '-';

                                // Warna teks kelas
                                $kelasColor = 'text-gray-800';
                                if($kelas){
                                    $kelasColor = match($kelas->id){
                                        1=>'text-blue-600',
                                        2=>'text-green-600',
                                        3=>'text-yellow-600',
                                        4=>'text-pink-600',
                                        default=>'text-gray-800'
                                    };
                                }

                                // Warna teks jenis izin
                                $izinColor = match($izin->jenis_izin){
                                    'Sakit'=>'text-blue-600',
                                    'Kegiatan'=>'text-yellow-600',
                                    'Lainnya'=>'text-gray-600',
                                };

                                $denda = $izin->status_lapor=='sudah_lapor' ? $izin->denda : $izin->denda_berjalan;
                                $dendaColor = ($izin->status_lapor=='sudah_lapor' && $izin->status_denda=='dibayar') ? 'text-green-600' : 'text-red-600';
                            @endphp

                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                <td class="px-3 py-2">{{ $izinList->firstItem() + $index }}</td>
                                <td class="px-3 py-2">{{ $izin->santri->nis }}</td>
                                <td class="px-3 py-2">{{ $izin->santri->nama }}</td>
                                <td class="px-3 py-2 font-semibold {{ $kelasColor }}">{{ $namaKelas }}</td>
                                <td class="px-3 py-2 font-semibold {{ $izinColor }}">{{ $izin->jenis_izin }}</td>
                                <td class="px-3 py-2 font-bold {{ $dendaColor }}">{{ $denda>0 ? 'Rp '.number_format($denda,0,',','.') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-gray-500 dark:text-gray-400">
                                    Tidak ada data.
                                </td>
                            </tr>
                        @endforelse
                        <tr class="font-bold bg-gray-100 dark:bg-gray-700">
                            <td colspan="5" class="text-right px-3 py-2">Total Denda Dibayar:</td>
                            <td class="px-3 py-2 text-green-600">{{ number_format($totalDendaDibayar,0,',','.') }}</td>
                        </tr>
                        <tr class="font-bold bg-gray-100 dark:bg-gray-700">
                            <td colspan="5" class="text-right px-3 py-2">Total Denda Belum Dibayar:</td>
                            <td class="px-3 py-2 text-red-600">{{ number_format($totalDendaBelum,0,',','.') }}</td>
                        </tr>
                    </tbody>
                </table>

                {{-- Pagination --}}
                <div class="px-4 py-3 border-t flex justify-center">
                    {{ $izinList->appends(request()->query())->links('vendor.pagination.custom') }}
                </div>
            </div>

            {{-- Export PDF --}}
            <div class="mt-4">
                <a href="{{ route('laporan.exportPdf', request()->query()) }}"
                   class="block w-full text-center bg-red-600 hover:bg-red-700 text-white font-bold py-2 rounded">
                   Export PDF
                </a>
            </div>
        </div>
    </div>

    {{-- Scripts --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const filterForm = document.getElementById('filterForm');
            filterForm.querySelectorAll('select,input').forEach(el => {
                el.addEventListener('change', () => filterForm.submit());
            });
        });
    </script>
</x-app-layout>

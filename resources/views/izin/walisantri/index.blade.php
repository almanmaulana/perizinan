<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight flex items-center gap-2">
            📝 Data & Ajukan Izin Santri
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

    <div class="py-6 px-4 sm:px-6 lg:px-8" x-data="{ open: false }">
        <div class="max-w-6xl mx-auto bg-gray-100 dark:bg-gray-800 shadow-xl rounded-xl p-5">

            {{-- Tombol Ajukan --}}
            <div class="mb-6 flex justify-end">
                <button @click="open = true"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow transition duration-200">
                    ➕ Ajukan Izin
                </button>
            </div>

            {{-- Modal Ajukan Izin --}}
            <div x-show="open" x-transition
                 class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-lg p-6">
                    <form action="{{ route('izin.walisantri.store') }}" method="POST">
                        @csrf
                        <div class="flex justify-between items-center border-b pb-2 mb-4">
                            <h5 class="text-lg font-semibold dark:text-gray-100">Ajukan Izin</h5>
                            <button type="button" @click="open = false"
                                    class="text-gray-500 hover:text-gray-700 dark:text-gray-300">&times;</button>
                        </div>

                        <div class="space-y-4">
                            {{-- Pilih Santri --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">Santri</label>
                                <select name="santri_id" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" required>
                                    <option value="">Pilih Santri</option>
                                    @foreach($santriList as $santri)
                                        <option value="{{ $santri->id }}">{{ $santri->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Jenis Izin --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">Jenis Izin</label>
                                <select name="jenis_izin" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" required>
                                    <option value="Sakit">Sakit</option>
                                    <option value="Kegiatan">Kegiatan</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>

                            {{-- Alasan --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200">Alasan</label>
                                <textarea name="alasan" class="w-full rounded border-gray-300 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" rows="3"></textarea>
                            </div>
                        </div>

                        {{-- Footer --}}
                        <div class="flex justify-end mt-6 space-x-2">
                            <button type="submit"
                                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg shadow transition">
                                Ajukan
                            </button>
                            <button type="button" @click="open = false"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 dark:text-gray-100 px-4 py-2 rounded-lg shadow transition">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Search & Filter --}}
            <form method="GET" id="filterForm" class="flex flex-wrap gap-2 mb-4 items-center">
                <input type="text" name="q" placeholder="Cari Nama Santri" value="{{ request('q') }}"
                       class="border rounded px-3 py-2 text-sm flex-1 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" autofocus>

                <select name="status" class="border rounded px-3 py-2 text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100">
                    <option value="">Semua Status</option>
                    <option value="pending_wali_kelas" @selected(request('status')=='pending_wali_kelas')>Pending Wali Kelas</option>
                    <option value="disetujui_wali_kelas" @selected(request('status')=='disetujui_wali_kelas')>Disetujui Wali Kelas</option>
                    <option value="ditolak_wali_kelas" @selected(request('status')=='ditolak_wali_kelas')>Ditolak Wali Kelas</option>
                    <option value="disetujui_keamanan" @selected(request('status')=='disetujui_keamanan')>Disetujui Keamanan</option>
                    <option value="ditolak_keamanan" @selected(request('status')=='ditolak_keamanan')>Ditolak Keamanan</option>
                </select>
            </form>

            {{-- Tabel --}}
            <div class="table-container overflow-x-auto rounded-xl border p-4 bg-white dark:bg-gray-800 shadow-inner">
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                        <tr>
                            <th class="px-3 py-2">No</th>
                            <th class="px-3 py-2">Nama Santri</th>
                            <th class="px-3 py-2">Jenis Izin</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2">Durasi</th>
                            <th class="px-3 py-2">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($izinList as $index => $izin)
                            @php
                                $statusMap = [
                                    'pending_wali_kelas'   => ['bg-yellow-100','text-yellow-900','Pending walikelas'],
                                    'disetujui_wali_kelas' => ['bg-blue-100','text-blue-800','Disetujui wali kelas'],
                                    'ditolak_wali_kelas'   => ['bg-red-100','text-red-800','Ditolak wali kelas'],
                                    'disetujui_keamanan'   => ['bg-green-100','text-green-800','Disetujui Keamanan'],
                                    'ditolak_keamanan'     => ['bg-gray-700','text-white','Ditolak Keamanan'],
                                ];
                                $bg = $statusMap[$izin->status][0] ?? 'bg-gray-100';
                                $text = $statusMap[$izin->status][1] ?? 'text-gray-500';
                                $label = $statusMap[$izin->status][2] ?? ucfirst(str_replace('_',' ',$izin->status));
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-3 py-2">{{ $loop->iteration }}</td>
                                <td class="px-3 py-2 font-semibold">{{ $izin->santri->nama }}</td>
                                <td class="px-3 py-2">
                                    <button x-data @click.stop="$dispatch('open-izin-modal', {{ $izin->id }})"
                                            class="text-blue-600 hover:underline dark:text-blue-400 text-sm">
                                        {{ $izin->jenis_izin }}
                                    </button>
                                </td>
                                <td class="px-3 py-2">
                                    <span class="px-2 py-1 rounded text-xs font-semibold {{ $bg }} {{ $text }}">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="px-3 py-2">
                                    {{ $izin->tgl_mulai_disetujui?->format('d/m/Y') ?? '-' }}
                                    -
                                    {{ $izin->tgl_selesai_disetujui?->format('d/m/Y') ?? '-' }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ in_array($izin->status, ['ditolak_wali_kelas','ditolak_keamanan']) ? ($izin->catatan ?? '-') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-gray-500 dark:text-gray-400">
                                    Belum ada data izin
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4 w-full flex justify-center">
                {{ $izinList->appends(request()->query())->links('vendor.pagination.custom') }}
            </div>

            {{-- Modal Alasan --}}
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
                        <button @click="open=false" class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded transition">
                            Tutup
                        </button>
                    </div>
                </div>
            @endforeach

        </div>
    </div>
</x-app-layout>

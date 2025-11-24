<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-100 leading-tight flex items-center gap-2">
            🗑️ Data Izin Terhapus
        </h2>
    </x-slot>

    <div class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl mx-auto bg-gray-100 dark:bg-gray-800 shadow-xl rounded-xl p-5">

            {{-- Table --}}
            <div class="table-container overflow-x-auto rounded-xl border p-4">
                <table class="w-full text-sm border-collapse">
                    <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                        <tr>
                            <th class="px-3 py-2 text-left">NIS</th>
                            <th class="px-3 py-2 text-left">Nama Santri</th>
                            <th class="px-3 py-2 text-left">Jenis</th>
                            <th class="px-3 py-2 text-left">Durasi</th>
                            <th class="px-3 py-2 text-left">Status</th>
                            <th class="px-3 py-2 text-left">Deleted At</th>
                            <th class="px-3 py-2 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($izinList as $izin)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                            <td class="px-3 py-2">{{ $izin->santri->nis }}</td>
                            <td class="px-3 py-2 font-semibold">{{ $izin->santri->nama }}</td>
                            <td class="px-3 py-2">{{ $izin->jenis_izin }}</td>
                            <td class="px-3 py-2">{{ $izin->durasi ?? '-' }}</td>
                            <td class="px-3 py-2">
                                <span class="px-2 py-1 rounded text-xs bg-red-100 text-red-800">{{ ucfirst(str_replace('_',' ', $izin->status)) }}</span>
                            </td>
                            <td class="px-3 py-2">{{ $izin->deleted_at ? $izin->deleted_at->format('d/m/Y H:i') : '-' }}</td>
                            <td class="px-3 py-2 text-center">
                                <form action="{{ route('izin.keamanan.restore', $izin->id) }}" method="POST">
                                    @csrf
                                    <button class="px-3 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700 transition">
                                        Restore
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-gray-500 dark:text-gray-400">Tidak ada data.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4 w-full flex justify-center">
                {{ $izinList->links('vendor.pagination.custom') }}
            </div>

            {{-- Tombol Kembali --}}
            <div class="mt-4 flex justify-start">
                <a href="{{ route('izin.keamanan.index') }}" 
                   class="px-4 py-2 bg-gray-700 text-white rounded hover:bg-gray-800 transition">
                    ← Kembali ke Validasi Izin
                </a>
            </div>
        </div>
    </div>
</x-app-layout>

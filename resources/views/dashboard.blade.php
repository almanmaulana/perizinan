@php
    $user = $user ?? auth()->user();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-chart-pie text-xl text-gray-700 dark:text-gray-200"></i>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Dashboard
            </h2>
        </div>
    </x-slot>

    <div class="px-4 py-6 space-y-6">

        {{-- ====================== CARDS ====================== --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- Semua role: Total santri --}}
            <x-dashboard-card title="Total Santri" :value="$totalSantri" icon="fa-users" color="blue"/>

            {{-- Total kelas: Keamanan + Wali kelas --}}
            @if($user->role === 'keamanan' || $user->role === 'wali_kelas')
                <x-dashboard-card title="Total Kelas" :value="$totalKelas" icon="fa-graduation-cap" color="yellow"/>
            @endif

            {{-- Khusus keamanan --}}
            @if($user->role === 'keamanan')
                <x-dashboard-card title="Total Wali Kelas" :value="$totalWaliKelas" icon="fa-user-tie" color="green"/>
                <x-dashboard-card title="Total Wali Santri" :value="$totalWaliSantri" icon="fa-user" color="purple"/>
            @endif

        </div>


        {{-- ====================== CHARTS ====================== --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Grafik Izin Bulan Ini --}}
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-5 transition-colors">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-calendar-days text-blue-500 dark:text-blue-400"></i>
                    Izin Bulan Ini
                </h3>
                <canvas id="izinBarChart" height="200"></canvas>
            </div>

            {{-- Grafik Denda: hanya keamanan --}}
            @if($user->role === 'keamanan')
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl p-5 transition-colors">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-money-bill-wave text-green-500 dark:text-green-400"></i>
                        Denda
                    </h3>
                    <canvas id="dendaPieChart" height="200"></canvas>
                </div>
            @endif

        </div>



        {{-- ====================== TABLE IZIN TERBARU ====================== --}}
        <div class="mt-8 bg-white dark:bg-gray-800 shadow-lg rounded-xl p-5 transition-colors">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-clipboard-list text-yellow-500 dark:text-yellow-400"></i>
                Izin Terbaru
            </h3>

            <div class="overflow-x-auto rounded border bg-white dark:bg-gray-800">
                <table class="w-full text-sm border-collapse table-auto">
                    <thead class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200">
                        <tr>
                            <th class="px-3 py-2 text-left">No</th>
                            <th class="px-3 py-2 text-left">NIS</th>
                            <th class="px-3 py-2 text-left">Nama</th>
                            <th class="px-3 py-2 text-left">Kelas</th>
                            <th class="px-3 py-2 text-left">Alamat</th>
                            <th class="px-3 py-2 text-left">Jenis Izin</th>
                            <th class="px-3 py-2 text-left">Status Lapor</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($latestIzin as $izin)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-3 py-2">{{ $loop->iteration }}</td>
                                <td class="px-3 py-2">{{ $izin->santri->nis }}</td>
                                <td class="px-3 py-2">{{ $izin->santri->nama }}</td>

                                <td class="px-3 py-2">
                                    {{ $izin->santri->kelas
                                        ? $izin->santri->kelas->tingkat.' '.$izin->santri->kelas->nama_kelas.' '.$izin->santri->kelas->jurusan
                                        : '-' }}
                                </td>

                                <td class="px-3 py-2">{{ $izin->santri->alamat }}</td>
                                <td class="px-3 py-2">{{ $izin->jenis_izin }}</td>

                                <td class="px-3 py-2">
                                    <span class="{{ $izin->status_lapor === 'sudah_lapor'
                                        ? 'text-green-600 font-semibold'
                                        : 'text-red-600 font-semibold' }}">
                                        {{ ucfirst(str_replace('_', ' ', $izin->status_lapor)) }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>
        </div>

    </div>


    {{-- ====================== PASS DATA TO JS ====================== --}}
    <script>
        window.userRole = '{{ $user->role }}';
        window.izinLabels = {!! json_encode(array_keys($izinCounts)) !!};
        window.izinCounts = {!! json_encode(array_values($izinCounts)) !!};
        window.totalDendaDibayar = {{ $totalDendaDibayar }};
        window.totalDendaBelum = {{ $totalDendaBelum }};
    </script>

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- File JS untuk Chart --}}
    <script src="{{ asset('js/dashboard-charts.js') }}"></script>

</x-app-layout>

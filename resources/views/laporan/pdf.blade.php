<!DOCTYPE html>
<html>
<head>
    <title>Laporan Izin Santri</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #eee; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h3>Laporan Izin Santri</h3>
    <table>
        <thead>
            <tr>
                <th>Nama Santri</th>
                <th>Jenis Izin</th>
                <th>Kelas</th>
                <th>Denda</th>
            </tr>
        </thead>
        <tbody>
            @foreach($izinList as $izin)
                <tr>
                    <td>{{ $izin->santri->nama }}</td>
                    <td>{{ ucfirst($izin->jenis_izin) }}</td>
                    <td>{{ $izin->santri->kelas ? $izin->santri->kelas->tingkat.' '.$izin->santri->kelas->nama_kelas.' '.$izin->santri->kelas->jurusan : '-' }}</td>
                    <td class="text-right">
                        {{ $izin->status_lapor=='sudah_lapor' ? 'Rp '.number_format($izin->denda,0,',','.') : 'Rp '.number_format($izin->denda_berjalan,0,',','.') }}
                    </td>
                </tr>
            @endforeach

            <tr>
                <td colspan="3" class="text-right"><strong>Total Denda Dibayar:</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($totalDendaDibayar,0,',','.') }}</strong></td>
            </tr>
            <tr>
                <td colspan="3" class="text-right"><strong>Total Denda Belum Dibayar:</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($totalDendaBelum,0,',','.') }}</strong></td>
            </tr>
        </tbody>
    </table>
</body>
</html>

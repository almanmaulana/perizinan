<?php

namespace App\Imports;

use App\Models\Santri;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SantriImport implements ToModel, WithHeadingRow
{
    private array $nisSeen = [];
    public array $errors = []; // simpan error tiap baris

    public function model(array $row)
    {
        try {
            // Pastikan kolom wajib
            if (!isset($row['nis'], $row['nama'], $row['jenjang'], $row['tingkat'])) {
                throw ValidationException::withMessages([
                    'format' => 'Kolom wajib: nis, nama, jenjang, tingkat (jurusan & kode_keluarga optional).',
                ]);
            }

            $nis = trim($row['nis']);

            // Cek duplikat di file
            if (in_array($nis, $this->nisSeen)) {
                throw ValidationException::withMessages([
                    'nis' => "NIS {$nis} duplikat di file Excel.",
                ]);
            }
            $this->nisSeen[] = $nis;

            // Cek duplikat di DB
            if (Santri::where('nis', $nis)->exists()) {
                throw ValidationException::withMessages([
                    'nis' => "NIS {$nis} sudah ada di database.",
                ]);
            }

            // Cari kelas
            $kelas = Kelas::where('jenjang', $row['jenjang'])
                          ->where('tingkat', $row['tingkat'])
                          ->when(isset($row['jurusan']), fn($q) => $q->where('jurusan', $row['jurusan']))
                          ->first();

            if (!$kelas) {
                throw ValidationException::withMessages([
                    'kelas' => "Kelas untuk {$row['jenjang']} {$row['tingkat']} " . ($row['jurusan'] ?? '') . " tidak ditemukan.",
                ]);
            }

            // Cek kode keluarga
            $kodeKeluarga = $row['kode_keluarga'] ?? null;
            $waliId = null;
            if ($kodeKeluarga) {
                $wali = User::where('role', 'wali_santri')
                            ->where('kode_keluarga', $kodeKeluarga)
                            ->first();
                if (!$wali) {
                    throw ValidationException::withMessages([
                        'kode_keluarga' => "Kode keluarga {$kodeKeluarga} tidak ditemukan di user wali_santri.",
                    ]);
                }
                $waliId = $wali->id;
            }

            // Simpan Santri
            return new Santri([
                'nis' => $nis,
                'nama' => trim($row['nama']),
                'kelas_id' => $kelas->id,
                'kode_keluarga' => $kodeKeluarga,
                'wali_santri_id' => $waliId,
            ]);
        } catch (\Exception $e) {
            // Simpan error tapi jangan throw agar bisa lihat semua baris
            $this->errors[] = [
                'row' => $row['nis'] ?? 'tidak diketahui',
                'error' => $e->getMessage(),
            ];
            return null; // baris ini tidak dimasukkan
        }
    }
}

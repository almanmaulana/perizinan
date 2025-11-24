<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Izin extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'izin';

    protected $fillable = [
        'santri_id',
        'jenis_izin',
        'alasan',
        'status',
        'catatan',
        'tgl_mulai_disetujui',
        'tgl_selesai_disetujui',
        'status_lapor',
        'denda',
        'status_denda',
        'tanggal_dibayar',
    ];

    protected $casts = [
        'tgl_mulai_disetujui' => 'datetime:Y-m-d',
        'tgl_selesai_disetujui' => 'datetime:Y-m-d',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static $tanggalSimulasi = "2025-11-28";

    public static function today()
    {
        return self::$tanggalSimulasi
            ? Carbon::parse(self::$tanggalSimulasi)
            : now();
    }

    public function santri()
    {
        return $this->belongsTo(Santri::class);
    }

    /** 🔥 Durasi tampilan */
    public function getDurasiAttribute()
    {
        if (!$this->tgl_mulai_disetujui || !$this->tgl_selesai_disetujui) return '-';

        return Carbon::parse($this->tgl_mulai_disetujui)->format('d/m/Y')
             .' - '.
             Carbon::parse($this->tgl_selesai_disetujui)->format('d/m/Y');
    }

    /** 🔥 Denda berjalan */
    public function getDendaBerjalanAttribute()
    {
        if ($this->status !== 'disetujui_keamanan') return 0;
        if ($this->status_lapor === 'sudah_lapor') return $this->denda;
        if (!$this->tgl_selesai_disetujui) return 0;

        if (self::today()->greaterThan($this->tgl_selesai_disetujui)) {
            return Carbon::parse($this->tgl_selesai_disetujui)
                        ->diffInDays(self::today()) * 15000;
        }

        return 0;
    }

    /** 🔥 Kunci denda ke DB */
    public function kunciDenda()
    {
        $dendaBerjalan = $this->denda_berjalan;

        if ($dendaBerjalan > 0) {
            $this->denda = $dendaBerjalan;
        }

        $this->status_denda = 'belum_dibayar';
        $this->save();
    }
}

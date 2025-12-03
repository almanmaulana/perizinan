<?php

namespace App\Models;

use App\Http\Controllers\SantriController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use function Laravel\Prompts\table;

class Kelas extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kelas',
        'jenjang',
        'tingkat',
        'jurusan',
        'wali_kelas_id',
    ];

    protected $table = 'kelas';


        public function waliKelas()
    {
        return $this->belongsTo(User::class, 'wali_kelas_id');
    }

    public function santris()
    {
        return $this->hasMany(Santri::class, 'kelas_id');
    }

     public function getFormattedNameAttribute()
    {
        switch($this->jenjang) {
            case 'SMK':
                return "{$this->tingkat} {$this->nama_kelas} {$this->jurusan}";
            case 'SMA':
                return "{$this->tingkat} {$this->jurusan} {$this->nama_kelas}";
            case 'SMP':
                return "{$this->tingkat} {$this->nama_kelas}";
            default:
                return $this->nama_kelas;
        }
    }

}

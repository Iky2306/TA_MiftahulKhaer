<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalMengajar extends Model
{
    use HasFactory;

    protected $table = 'jurnal_mengajar';
    protected $guarded = [];

    // Relasi ke Master Kelas
    public function master_kelas()
    {
        return $this->belongsTo(MasterKelas::class, 'kode_kelas', 'kode_kelas');
    }

    // Relasi ke tabel Karyawan (bukan User)
    public function karyawan()
    {
        return $this->belongsTo(Karyawan::class, 'nik', 'nik');
    }
}

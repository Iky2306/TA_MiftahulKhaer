<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterSiswa extends Model
{
    use HasFactory;

    protected $table = 'master_siswa';
    protected $guarded = [];

    public function kelas()
    {
        return $this->belongsTo(MasterKelas::class, 'kode_kelas', 'kode_kelas');
    }
}

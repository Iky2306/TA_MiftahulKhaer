<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterKelas extends Model
{
    use HasFactory;

    protected $table = 'master_kelas';
    protected $primaryKey = 'kode_kelas';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    public function mapels()
    {
        return $this->hasMany(MasterMapelKelas::class, 'kode_kelas', 'kode_kelas');
    }

    public function waliKelas()
    {
        return $this->belongsTo(Karyawan::class, 'wali_kelas_nik', 'nik');
    }
}

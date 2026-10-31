<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterMapelKelas extends Model
{
    use HasFactory;

    protected $table = 'master_mapel_kelas';
    protected $guarded = [];

    public function kelas()
    {
        return $this->belongsTo(MasterKelas::class, 'kode_kelas', 'kode_kelas');
    }
}

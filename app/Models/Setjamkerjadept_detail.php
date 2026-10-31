<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setjamkerjadept_detail extends Model
{
    use HasFactory;
    
    protected $table = "konfigurasi_jk_dept_detail";
    protected $fillable = [
        'kode_jk_dept',
        'hari',
        'kode_jam_kerja'
    ];

    public function jamkerja()
    {
        return $this->belongsTo(Jamkerja::class, 'kode_jam_kerja', 'kode_jam_kerja');
    }
}
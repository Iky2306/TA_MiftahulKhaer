<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setjamkerjabydate extends Model
{
    use HasFactory;
    
    protected $table = "konfigurasi_jamkerja_by_date";
    protected $fillable = [
        'nik',
        'tanggal',
        'kode_jam_kerja'
    ];

    public function jamkerja()
    {
        return $this->belongsTo(Jamkerja::class, 'kode_jam_kerja', 'kode_jam_kerja');
    }
}
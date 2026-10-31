<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setjamkerja extends Model
{
    use HasFactory;
    protected $table = "konfigurasi_jamkerja";
     protected $fillable = [
        'nik',
        'hari',
        'kode_jam_kerja'
    ];

    public function jamkerja()
    {
        return $this->belongsTo(Jamkerja::class, 'kode_jam_kerja', 'kode_jam_kerja');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jamkerja extends Model
{
    use HasFactory;
    
    protected $table = "jam_kerja";
    protected $primaryKey = "kode_jam_kerja";
    public $incrementing = false;
    protected $fillable = [
        'kode_jam_kerja',
        'nama_jam_kerja',
        'awal_jam_masuk',
        'jam_masuk',
        'akhir_jam_masuk',
        'status_istirahat',
        'awal_jam_istirahat',
        'akhir_jam_istirahat',
        'jam_pulang',
        'total_jam',
        'lintashari'
    ];
}
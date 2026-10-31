<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Presensi extends Model
{
    use HasFactory;

    protected $table = 'presensi'; // pastikan ini sesuai dengan nama tabel di database
    protected $primaryKey = 'id'; // jika primary key menggunakan kolom 'id'
    public $timestamps = false;   // jika tabel tidak menggunakan kolom created_at & updated_at
}


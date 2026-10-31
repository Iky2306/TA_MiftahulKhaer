<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengabdian extends Model
{
    use HasFactory;

    protected $table = 'pengabdian';

    protected $fillable = [
        'nama',
        'lokasi',
        'tunjangan_jabatan',
        'tunjangan_pengabdian',
        'tunjangan_lainnya',
        'honorarium_wkt',
        'honorarium_stn',
        'kehadiran_wkt',
        'kehadiran_stn',
        'potongan_dansos',
        'potongan_fpmk',
        'potongan_lainnya',
    ];
}

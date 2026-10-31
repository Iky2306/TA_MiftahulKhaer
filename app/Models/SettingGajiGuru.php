<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingGajiGuru extends Model
{
    protected $table = 'setting_gaji_guru';

    protected $fillable = [
        'golongan',
        'transport',
        'simpanan_koperasi',
        'dana_sosial',
        'gaji_pokok',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setjamkerjadept extends Model
{
    use HasFactory;
    
    protected $table = "konfigurasi_jk_dept";
    protected $primaryKey = "kode_jk_dept";
    public $incrementing = false;
    protected $fillable = [
        'kode_jk_dept',
        'kode_cabang',
        'kode_dept'
    ];

    public function detail()
    {
        return $this->hasMany(Setjamkerjadept_detail::class, 'kode_jk_dept', 'kode_jk_dept');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Karyawan extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = "karyawan";
    protected $primaryKey = "nik";
    public $incrementing = false;
    protected $fillable = [
        'nik',
        'nama_lengkap',
        'jabatan',
        'tipe_jabatan',
        'no_hp',
        'password',
        'kode_dept',
        'kode_cabang',
        'foto',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Tambahkan relasi baru
    public function setjamkerja()
    {
        return $this->hasMany(Setjamkerja::class, 'nik', 'nik');
    }

    public function setjamkerjadept()
    {
        return $this->belongsTo(Setjamkerjadept::class, 'kode_dept', 'kode_dept')
                    ->where('kode_cabang', $this->kode_cabang);
    }

    public function setjamkerjabydate()
    {
        return $this->hasMany(Setjamkerjabydate::class, 'nik', 'nik');
    }

    /**
     * Cek apakah karyawan adalah Petugas Piket
     */
    public function isPetugasPiket(): bool
    {
        return $this->kode_dept === 'PPT';
    }

    /**
     * Cek apakah karyawan adalah Wali Kelas
     */
    public function isWaliKelas(): bool
    {
        return $this->kode_dept === '2';
    }

    /**
     * Cek apakah karyawan adalah Guru biasa
     */
    public function isGuru(): bool
    {
        return $this->kode_dept !== 'PPT' && $this->kode_dept !== '2';
    }
}
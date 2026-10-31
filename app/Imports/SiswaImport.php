<?php

namespace App\Imports;

use App\Models\MasterSiswa;
use App\Http\Controllers\MasterSiswaController;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImport implements ToModel, WithHeadingRow
{
    protected $kode_kelas;
    public $successCount = 0;

    public function __construct($kode_kelas)
    {
        $this->kode_kelas = $kode_kelas;
    }

    public function model(array $row)
    {
        $namaSiswa = trim($row['nama_siswa'] ?? '');

        if (empty($namaSiswa)) {
            return null;
        }

        $nisn = trim($row['nisn'] ?? '');
        if (empty($nisn)) {
            $nisn = MasterSiswaController::generateNisn();
        }

        $jk = strtoupper(trim($row['jenis_kelamin'] ?? 'L'));
        if (!in_array($jk, ['L', 'P'])) {
            $jk = 'L';
        }

        $this->successCount++;

        return new MasterSiswa([
            'nisn'          => $nisn,
            'nama_siswa'    => $namaSiswa,
            'jenis_kelamin' => $jk,
            'kode_kelas'    => $this->kode_kelas,
            'status'        => 'aktif',
        ]);
    }
}

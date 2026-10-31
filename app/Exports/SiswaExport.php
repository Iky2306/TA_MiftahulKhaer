<?php

namespace App\Exports;

use App\Models\MasterSiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SiswaExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    private $kode_kelas;
    private $kode_cabang;

    public function __construct($kode_kelas = null, $kode_cabang = null)
    {
        $this->kode_kelas = $kode_kelas;
        $this->kode_cabang = $kode_cabang;
    }

    public function collection()
    {
        $query = MasterSiswa::with('kelas')->where('status', 'aktif');

        if ($this->kode_kelas) {
            $query->where('kode_kelas', $this->kode_kelas);
        }

        if ($this->kode_cabang) {
            $query->where('kode_cabang', $this->kode_cabang);
        }

        return $query->orderBy('kode_kelas')->orderBy('nama_lengkap')->get();
    }

    public function headings(): array
    {
        return [
            'NIS',
            'Nama Lengkap',
            'Kode Kelas',
            'Nama Kelas',
            'Lembaga',
            'Cabang',
            'Jenis Kelamin',
            'No. HP Ortu',
            'Tahun Masuk',
            'Status',
        ];
    }

    public function map($siswa): array
    {
        return [
            $siswa->nis,
            $siswa->nama_lengkap,
            $siswa->kode_kelas,
            $siswa->kelas->nama_kelas ?? '-',
            $siswa->kelas->lembaga ?? '-',
            $siswa->kode_cabang,
            $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
            $siswa->no_hp_ortu,
            $siswa->tahun_masuk,
            ucfirst($siswa->status),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}

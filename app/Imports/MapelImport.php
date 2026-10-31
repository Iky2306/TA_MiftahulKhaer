<?php

namespace App\Imports;

use App\Models\MasterMapelKelas;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;

class MapelImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    private $kode_kelas;
    private $rowCount = 0;
    private $successCount = 0;
    private $duplicateCount = 0;

    public function __construct($kode_kelas)
    {
        $this->kode_kelas = $kode_kelas;
    }

    public function model(array $row)
    {
        $this->rowCount++;

        $namaMapel = $this->getCellValue($row, ['nama_mata_pelajaran', 'nama_mapel', 'mapel', 'nama', 'mata_pelajaran']);
        if (empty($namaMapel)) {
            return null;
        }

        $namaMapelTrimmed = trim($namaMapel);

        // Cek duplikasi di kelas yang sama
        $exists = MasterMapelKelas::where('kode_kelas', $this->kode_kelas)
            ->where('nama_mapel', $namaMapelTrimmed)
            ->exists();

        if ($exists) {
            $this->duplicateCount++;
            return null;
        }

        $this->successCount++;

        return new MasterMapelKelas([
            'kode_kelas' => $this->kode_kelas,
            'nama_mapel' => $namaMapelTrimmed,
        ]);
    }

    private function getCellValue(array $row, array $possibleKeys)
    {
        foreach ($possibleKeys as $key) {
            if (isset($row[$key]) && $row[$key] !== null && $row[$key] !== '') {
                return $row[$key];
            }
            $cleanKey = str_replace('_', '', $key);
            if (isset($row[$cleanKey]) && $row[$cleanKey] !== null && $row[$cleanKey] !== '') {
                return $row[$cleanKey];
            }
            $spacedKey = str_replace('_', ' ', $key);
            if (isset($row[$spacedKey]) && $row[$spacedKey] !== null && $row[$spacedKey] !== '') {
                return $row[$spacedKey];
            }
        }

        return null;
    }

    public function rules(): array
    {
        return [];
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    public function getSuccessCount(): int
    {
        return $this->successCount;
    }

    public function getDuplicateCount(): int
    {
        return $this->duplicateCount;
    }
}

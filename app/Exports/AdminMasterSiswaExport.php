<?php

namespace App\Exports;

use App\Models\MasterSiswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AdminMasterSiswaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithEvents
{
    protected $request;
    protected $rowNumber = 0;

    public function __construct(array $request = [])
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = MasterSiswa::with('kelas');

        if (!empty($this->request['kode_cabang']) || !empty($this->request['lembaga'])) {
            $query->whereHas('kelas', function ($q) {
                if (!empty($this->request['kode_cabang'])) {
                    $q->where('kode_cabang', $this->request['kode_cabang']);
                }
                if (!empty($this->request['lembaga'])) {
                    $q->where('lembaga', $this->request['lembaga']);
                }
            });
        }

        if (!empty($this->request['kode_kelas'])) {
            $query->where('kode_kelas', $this->request['kode_kelas']);
        }

        if (!empty($this->request['status'])) {
            $query->where('status', $this->request['status']);
        }

        if (!empty($this->request['search'])) {
            $search = $this->request['search'];
            $query->where(function($q) use ($search) {
                $q->where('nama_siswa', 'like', '%' . $search . '%')
                  ->orWhere('nisn', 'like', '%' . $search . '%');
            });
        }

        return $query->orderBy('kode_kelas')->orderBy('nama_siswa')->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'NISN',
            'NAMA SISWA',
            'L/P',
            'KELAS',
            'LEMBAGA',
            'LOKASI SEKOLAH',
            'STATUS',
        ];
    }

    public function map($siswa): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $siswa->nisn ?? '-',
            strtoupper($siswa->nama_siswa),
            $siswa->jenis_kelamin ?? 'L',
            $siswa->kelas->nama_kelas ?? $siswa->kode_kelas,
            $siswa->kelas->lembaga ?? '-',
            $siswa->kelas->kode_cabang ?? '-',
            strtoupper($siswa->status ?? 'AKTIF'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 18,
            'C' => 38,
            'D' => 8,
            'E' => 18,
            'F' => 14,
            'G' => 18,
            'H' => 14,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 11,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '206BC4'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                $sheet->getStyle("A1:H{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                ]);

                $sheet->getStyle("A2:A{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("B2:B{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("D2:D{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("E2:H{$highestRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->freezePane('A2');
                $sheet->setTitle('Data Master Siswa');
            },
        ];
    }
}

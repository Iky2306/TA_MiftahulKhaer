<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SiswaTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    public function array(): array
    {
        return [
            ['0102946477', 'AFIFAH AINI LUBIS', 'P'],
            ['0096692512', 'APIFAH ISWANDARI', 'P'],
            ['0087712345', 'AHMAD FAUZI', 'L'],
        ];
    }

    public function headings(): array
    {
        return [
            'nisn',
            'nama_siswa',
            'jenis_kelamin',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 40,
            'C' => 15,
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
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                ],
            ],
            'A2:C100' => [
                'alignment' => [
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

                $sheet->getStyle('A1:C1')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                $sheet->getStyle('A2:C4')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'CCCCCC'],
                        ],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFF9E6'],
                    ],
                ]);

                $sheet->freezePane('A2');
                $sheet->setTitle('Data Siswa');

                // Sheet Petunjuk
                $spreadsheet = $sheet->getParent();
                $petunjukSheet = $spreadsheet->createSheet();
                $petunjukSheet->setTitle('Petunjuk');

                $petunjukSheet->setCellValue('A1', '📌 PETUNJUK PENGISIAN TEMPLATE IMPORT DATA SISWA');
                $petunjukSheet->setCellValue('A3', '1. Kolom nisn (nomor nisn siswa/opsional).');
                $petunjukSheet->setCellValue('A4', '2. Kolom nama_siswa (nama lengkap siswa).');
                $petunjukSheet->setCellValue('A5', '3. Kolom jenis_kelamin (diisi "L" untuk Laki-laki atau "P" untuk Perempuan).');
                $petunjukSheet->setCellValue('A6', '4. Hapus baris contoh (baris warna kuning) sebelum mengunggah.');
                $petunjukSheet->setCellValue('A7', '5. JANGAN mengubah nama header pada baris 1 (nisn, nama_siswa, jenis_kelamin).');

                $petunjukSheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '206BC4']],
                ]);
                $petunjukSheet->getStyle('A3:A7')->applyFromArray([
                    'font' => ['size' => 11],
                ]);

                $petunjukSheet->getColumnDimension('A')->setWidth(75);
                $spreadsheet->setActiveSheetIndex(0);
            },
        ];
    }
}

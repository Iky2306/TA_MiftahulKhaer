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

class MapelTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    public function array(): array
    {
        return [
            ['Al - Quran Hadist'],
            ['Bahasa Indonesia'],
            ['Fikih'],
            ['Fisika'],
            ['PPKN'],
            ['Matematika'],
        ];
    }

    public function headings(): array
    {
        return [
            'nama_mata_pelajaran',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 35,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style header row
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
            // Style data rows
            'A2:A100' => [
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

                // Border untuk header
                $sheet->getStyle('A1')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Border untuk data contoh
                $sheet->getStyle('A2:A7')->applyFromArray([
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

                // Freeze pane pada header
                $sheet->freezePane('A2');

                // Set sheet title
                $sheet->setTitle('Mapel Kelas');

                // ===== SHEET PETUNJUK =====
                $spreadsheet = $sheet->getParent();
                $petunjukSheet = $spreadsheet->createSheet();
                $petunjukSheet->setTitle('Petunjuk');

                $petunjukSheet->setCellValue('A1', '📌 PETUNJUK PENGISIAN TEMPLATE IMPORT MAPEL');
                $petunjukSheet->setCellValue('A3', '1. Isi daftar nama mata pelajaran di sheet "Mapel Kelas".');
                $petunjukSheet->setCellValue('A4', '2. Hapus baris contoh (baris kuning) sebelum mengunggah.');
                $petunjukSheet->setCellValue('A5', '3. Kolom WAJIB: nama_mata_pelajaran.');
                $petunjukSheet->setCellValue('A6', '4. JANGAN mengubah nama header di baris 1.');
                $petunjukSheet->setCellValue('A7', '5. Sistem otomatis melewati mata pelajaran yang sudah ada (mencegah duplikat).');

                $petunjukSheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '206BC4']],
                ]);
                $petunjukSheet->getStyle('A3:A7')->applyFromArray([
                    'font' => ['size' => 11],
                ]);

                $petunjukSheet->getColumnDimension('A')->setWidth(75);

                // Kembali ke sheet utama
                $spreadsheet->setActiveSheetIndex(0);
            },
        ];
    }
}

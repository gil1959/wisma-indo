<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use App\Models\ListingCategory;

class ServicesTemplateExport implements WithHeadings, WithTitle, WithStyles, WithEvents
{
    public function headings(): array
    {
        return [
            'Judul Iklan',
            'Deskripsi Lengkap',
            'Harga',
            'Bisa Nego (Ya/Tidak)',
            'Kategori (Pilih Nama Kategori)',
            'Area Layanan',
            'Lokasi Singkat',
            'Alamat Lengkap',
            'Google Maps URL (Opsional)',
            'WhatsApp',
            'Telepon (Opsional)',
            'YouTube URL',
            'Cover Image URL',
            'Image 2 URL',
            'Image 3 URL',
            'Image 4 URL',
            'Image 5 URL',
            'Image 6 URL',
            'Image 7 URL',
            'Image 8 URL',
            'Image 9 URL',
            'Image 10 URL',
            'Image 11 URL',
            'Image 12 URL',
        ];
    }

    public function title(): string
    {
        return 'Data Jasa';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF0194F3']
                ]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = 1000;

                $categories = ListingCategory::where('type', 'services')->pluck('name')->toArray();
                $categoryList = '"' . implode(',', $categories) . '"';

                $this->addValidation($sheet, 'D2:D'.$highestRow, '"Ya,Tidak"');
                $this->addValidation($sheet, 'E2:E'.$highestRow, $categoryList);

                // Set column widths
                $colWidths = [
                    'A' => 35, // Judul Iklan
                    'B' => 50, // Deskripsi
                    'C' => 18, // Harga
                    'D' => 18, // Bisa Nego
                    'E' => 30, // Kategori
                    'F' => 35, // Area Layanan
                    'G' => 25, // Lokasi Singkat
                    'H' => 35, // Alamat Lengkap
                    'I' => 35, // Google Maps URL
                    'J' => 20, // WhatsApp
                    'K' => 20, // Telepon
                    'L' => 35, // YouTube URL
                    'M' => 30, // Cover Image URL
                    'N' => 25, 'O' => 25, 'P' => 25, 'Q' => 25, 'R' => 25,
                    'S' => 25, 'T' => 25, 'U' => 25, 'V' => 25, 'W' => 25,
                    'X' => 25,
                ];
                foreach ($colWidths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                // Wrap text for ALL data rows and headers
                $sheet->getStyle('A1:X1000')->getAlignment()->setWrapText(true);

                // Freeze first row
                $sheet->freezePane('A2');
            },
        ];
    }

    private function addValidation($sheet, $cellRange, $formula)
    {
        $validation = $sheet->getCell(explode(':', $cellRange)[0])->getDataValidation();
        $validation->setType(DataValidation::TYPE_LIST);
        $validation->setErrorStyle(DataValidation::STYLE_STOP);
        $validation->setAllowBlank(true);
        $validation->setShowInputMessage(true);
        $validation->setShowErrorMessage(true);
        $validation->setShowDropDown(true);
        $validation->setErrorTitle('Input tidak valid');
        $validation->setError('Silakan pilih opsi dari panah dropdown.');
        $validation->setFormula1($formula);
        
        $sheet->setDataValidation($cellRange, $validation);
    }
}

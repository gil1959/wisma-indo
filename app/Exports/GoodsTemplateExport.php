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

class GoodsTemplateExport implements WithHeadings, WithTitle, ShouldAutoSize, WithStyles, WithEvents
{
    public function headings(): array
    {
        return [
            'Judul Iklan',
            'Deskripsi Lengkap',
            'Harga',
            'Bisa Nego (Ya/Tidak)',
            'Kategori (Pilih Nama Kategori)',
            'Kondisi (Baru/Bekas)',
            'Merek / Brand (Opsional)',
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
        return 'Data Barang';
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

                $categories = ListingCategory::where('type', 'goods')->pluck('name')->toArray();
                $categoryList = '"' . implode(',', $categories) . '"';

                $this->addValidation($sheet, 'D2:D'.$highestRow, '"Ya,Tidak"');
                $this->addValidation($sheet, 'E2:E'.$highestRow, $categoryList);
                $this->addValidation($sheet, 'F2:F'.$highestRow, '"Baru,Bekas"');
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

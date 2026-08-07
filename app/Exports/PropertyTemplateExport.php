<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use App\Models\ListingCategory;

class PropertyTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new PropertyDataSheet(),
            new PropertyReferenceSheet(),
        ];
    }
}

// ─── Sheet 1: Data Properti ──────────────────────────────────────────────────
class PropertyDataSheet implements WithHeadings, WithTitle, WithStyles, WithEvents
{
    public function headings(): array
    {
        return [
            'Judul Iklan',
            'Tipe Transaksi (dijual/disewakan)',
            'Harga',
            'Bisa Nego (Ya/Tidak)',
            'Kategori (Pilih Nama Kategori)',
            'Lokasi Singkat',
            'Alamat Lengkap',
            'Google Maps URL (Opsional)',
            'Deskripsi',
            'Luas Tanah',
            'Luas Bangunan',
            'Kamar Tidur',
            'Kamar Mandi',
            'Jml Lantai',
            'Carport',
            'Garasi',
            'Tahun Bangun',
            'Sertifikat',
            'Kondisi Perabotan (Unfurnished/Semi/Fully)',
            'IMB (Ya/Tidak)',
            'PBB (Ya/Tidak)',
            'Fasilitas (pisahkan koma, lihat Sheet Referensi)',
            'Area Sekitar (pisahkan koma, lihat Sheet Referensi)',
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
        return 'Data Properti';
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

                $categories = ListingCategory::where('type', 'property')->pluck('name')->toArray();
                $categoryList = '"' . implode(',', $categories) . '"';

                // Dropdown validations
                $this->addValidation($sheet, 'B2:B'.$highestRow, '"dijual,disewakan"');
                $this->addValidation($sheet, 'D2:D'.$highestRow, '"Ya,Tidak"');
                $this->addValidation($sheet, 'E2:E'.$highestRow, $categoryList);
                $this->addValidation($sheet, 'R2:R'.$highestRow, '"SHM - Sertifikat Hak Milik,HGB - Hak Guna Bangunan,AJB - Akta Jual Beli"');
                $this->addValidation($sheet, 'S2:S'.$highestRow, '"Unfurnished,Semi Furnished,Fully Furnished"');
                $this->addValidation($sheet, 'T2:T'.$highestRow, '"Ya,Tidak"');
                $this->addValidation($sheet, 'U2:U'.$highestRow, '"Ya,Tidak"');

                // Set column widths manually (per-column control)
                $colWidths = [
                    'A' => 35, 'B' => 28, 'C' => 18, 'D' => 18, 'E' => 30,
                    'F' => 25, 'G' => 35, 'H' => 35, 'I' => 50, 'J' => 14,
                    'K' => 16, 'L' => 14, 'M' => 15, 'N' => 12, 'O' => 12,
                    'P' => 12, 'Q' => 14, 'R' => 30, 'S' => 38, 'T' => 14,
                    'U' => 14, 'V' => 50, 'W' => 50, 'X' => 20, 'Y' => 20,
                    'Z' => 35, 
                    'AA' => 25, 'AB' => 25, 'AC' => 25, 'AD' => 25, 'AE' => 25,
                    'AF' => 25, 'AG' => 25, 'AH' => 25, 'AI' => 25, 'AJ' => 25,
                    'AK' => 25, 'AL' => 35,
                ];
                foreach ($colWidths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width);
                }

                // Wrap text for ALL data rows and headers, so URLs don't overlap
                $sheet->getStyle('A1:AL1000')->getAlignment()->setWrapText(true);


                // Highlight Fasilitas & Area Sekitar header columns (amber/yellow)
                $sheet->getStyle('V1:W1')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFFBBF24');
                $sheet->getStyle('V1:W1')->getFont()
                    ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF1e293b'))
                    ->setBold(true);

                // Add cell comments to Fasilitas & Area Sekitar headers
                $fasilitasOptions = "PILIHAN FASILITAS (pisahkan dengan koma):\n"
                    . "Area Hiburan, Balkon, Gym, Halaman Terbuka,\n"
                    . "Jalan Raya, Karaoke, Keamanan 24 Jam, Kitchen Set,\n"
                    . "Kolam Renang, Lapangan Basket, Lapangan Tenis,\n"
                    . "One Gate System, Parkir, Pemanas Air,\n"
                    . "Pendingin Ruangan (AC), Pos Security, Rooftop,\n"
                    . "Ruang Rapat, Ruang Serbaguna, Spa dan Sauna,\n"
                    . "Taman, Taman Bermain Anak, Telepon, Televisi,\n"
                    . "Tempat BBQ, Teras, Transportasi Umum, Trek Lari, WiFi\n\n"
                    . "Contoh: Kolam Renang,Taman,Gym";
                $comment = $sheet->getComment('V1');
                $comment->getText()->createTextRun($fasilitasOptions);
                $comment->setVisible(false);
                $comment->setWidth('220pt');
                $comment->setHeight('280pt');

                $areaOptions = "PILIHAN AREA SEKITAR (pisahkan dengan koma):\n"
                    . "Apotek, Kolam Renang, Mall, Masjid, Pasar,\n"
                    . "Rumah Sakit, Sarana Pendidikan,\n"
                    . "Sarana Perbelanjaan, Tempat Olahraga\n\n"
                    . "Contoh: Mall,Rumah Sakit,Masjid";
                $commentArea = $sheet->getComment('W1');
                $commentArea->getText()->createTextRun($areaOptions);
                $commentArea->setVisible(false);
                $commentArea->setWidth('200pt');
                $commentArea->setHeight('180pt');

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

// ─── Sheet 2: Referensi Fasilitas & Area ─────────────────────────────────────
class PropertyReferenceSheet implements WithTitle, WithHeadings, WithStyles, WithEvents
{
    public function title(): string
    {
        return 'Referensi';
    }

    public function headings(): array
    {
        return ['Pilihan Fasilitas', 'Pilihan Area Sekitar'];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF0f766e']
                ]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $fasilitas = [
                    'Area Hiburan', 'Balkon', 'Gym', 'Halaman Terbuka',
                    'Jalan Raya', 'Karaoke', 'Keamanan 24 Jam', 'Kitchen Set',
                    'Kolam Renang', 'Lapangan Basket', 'Lapangan Tenis',
                    'One Gate System', 'Parkir', 'Pemanas Air',
                    'Pendingin Ruangan (AC)', 'Pos Security', 'Rooftop',
                    'Ruang Rapat', 'Ruang Serbaguna', 'Spa dan Sauna',
                    'Taman', 'Taman Bermain Anak', 'Telepon', 'Televisi',
                    'Tempat BBQ', 'Teras', 'Transportasi Umum', 'Trek Lari', 'WiFi',
                ];

                $area = [
                    'Apotek', 'Kolam Renang', 'Mall', 'Masjid', 'Pasar',
                    'Rumah Sakit', 'Sarana Pendidikan', 'Sarana Perbelanjaan',
                    'Tempat Olahraga',
                ];

                $maxRows = max(count($fasilitas), count($area));
                for ($i = 0; $i < $maxRows; $i++) {
                    $row = $i + 2;
                    $sheet->setCellValue('A' . $row, $fasilitas[$i] ?? '');
                    $sheet->setCellValue('B' . $row, $area[$i] ?? '');
                    // Alternating row colors
                    if ($i % 2 === 0) {
                        $sheet->getStyle('A'.$row.':B'.$row)->getFill()
                            ->setFillType(Fill::FILL_SOLID)
                            ->getStartColor()->setARGB('FFf0fdf4');
                    }
                }

                // Column widths
                $sheet->getColumnDimension('A')->setWidth(35);
                $sheet->getColumnDimension('B')->setWidth(32);
                $sheet->getColumnDimension('D')->setWidth(60);

                // Instruction note
                $sheet->setCellValue('D1', 'CARA PENGGUNAAN:');
                $sheet->setCellValue('D2', '1. Salin nama persis seperti yang tertulis di kolom A atau B.');
                $sheet->setCellValue('D3', '2. Pisahkan pilihan dengan koma (,) di kolom Fasilitas/Area Sekitar sheet "Data Properti".');
                $sheet->setCellValue('D4', '3. Contoh isian kolom Fasilitas: Kolam Renang,Taman,Gym');
                $sheet->setCellValue('D5', '4. Contoh isian kolom Area Sekitar: Mall,Rumah Sakit,Masjid');
                $sheet->getStyle('D1')->getFont()->setBold(true)->setSize(11);
                $sheet->getStyle('D1:D5')->getAlignment()->setWrapText(true);
                $sheet->getRowDimension(1)->setRowHeight(20);

                // Style instruction cells
                $sheet->getStyle('D1:D5')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFfefce8');

                $sheet->freezePane('A2');
            },
        ];
    }
}



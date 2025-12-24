<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PartsTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        // Contoh data untuk template
        return [
            [
                'PART-001',
                'Bearing 6203',
                100,
                10,
                500,
                'pcs',
                'A1-01',
                'PT Supplier ABC',
                'PART-001.jpg'  // Contoh nama file gambar
            ],
            [
                'PART-002',
                'Bolt M10x50',
                200,
                20,
                1000,
                'pcs',
                'A1-02',
                'PT Supplier XYZ',
                'bearing-6203.png'  // Contoh nama file gambar
            ],
            [
                'PART-003',
                'Gasket Seal',
                50,
                5,
                200,
                'pcs',
                'B2-05',
                'PT Supplier ABC',
                ''  // Kosongkan jika tidak ada gambar
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'kode_part',
            'nama',
            'stock',
            'min_stock',
            'max_stock',
            'satuan',
            'address',
            'supplier',
            'gambar'  // Tambahkan kolom gambar
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '000000'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Style untuk contoh data
        $sheet->getStyle('A2:I4')->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Border untuk semua cell yang berisi data
        $sheet->getStyle('A1:I4')->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Set row height
        $sheet->getRowDimension(1)->setRowHeight(25);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,  // kode_part
            'B' => 30,  // nama
            'C' => 12,  // stock
            'D' => 12,  // min_stock
            'E' => 12,  // max_stock
            'F' => 12,  // satuan
            'G' => 15,  // address
            'H' => 30,  // supplier
            'I' => 25,  // gambar (nama file)
        ];
    }
}
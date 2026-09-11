<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use RuntimeException;

class CheckIndicatorSheetParser
{
    /**
     * @return array{part_no: string, bagian: array<int, array{nama_bagian: string, standards: array<int, array{poin: string, standar: string}>}>}
     */
    public function parse(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);

        $partNo = null;
        $bagianList = [];

        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $sheetData = $this->parseSheet($sheet);

            if ($sheetData === null) {
                // sheet ini bukan format check indicator yang dikenali, skip
                continue;
            }

            if ($partNo === null) {
                $partNo = $sheetData['part_no'];
            } elseif ($partNo !== $sheetData['part_no']) {
                throw new RuntimeException(
                    "Part No tidak konsisten antar sheet: '{$partNo}' vs '{$sheetData['part_no']}' (sheet: {$sheet->getTitle()})"
                );
            }

            if (empty($sheetData['standards'])) {
                // sheet ketemu part no & bagian, tapi gak ada item pengecekan -> skip bagian ini
                continue;
            }

            $bagianList[] = [
                'nama_bagian' => $sheetData['nama_bagian'],
                'standards'   => $sheetData['standards'],
            ];
        }

        if ($partNo === null) {
            throw new RuntimeException('Tidak ada sheet dengan format Check Indicator (PART NO / PROSES NO) yang bisa dibaca dari file ini.');
        }

        if (empty($bagianList)) {
            throw new RuntimeException('Part No ditemukan, tapi tidak ada item pengecekan yang berhasil terbaca di sheet manapun.');
        }

        return [
            'part_no' => $partNo,
            'bagian'  => $bagianList,
        ];
    }

    /**
     * @return array{part_no: string, nama_bagian: string, standards: array}|null
     */
    protected function parseSheet(Worksheet $sheet): ?array
    {
        $highestRow = $sheet->getHighestRow();
        $highestCol = Coordinate::columnIndexFromString($sheet->getHighestColumn());

        $partNo = $this->findValueRightOfLabel($sheet, 'PART NO', $highestRow, $highestCol);
        $namaBagian = $this->findValueRightOfLabel($sheet, 'PROSES NO', $highestRow, $highestCol);

        if (!$partNo || !$namaBagian) {
            return null;
        }

        return [
            'part_no'     => trim($partNo),
            'nama_bagian' => trim($namaBagian),
            'standards'   => $this->extractStandards($sheet, $highestRow),
        ];
    }

    protected function findValueRightOfLabel(Worksheet $sheet, string $label, int $highestRow, int $highestCol): ?string
    {
        for ($row = 1; $row <= $highestRow; $row++) {
            for ($col = 1; $col <= $highestCol; $col++) {
                $cellValue = trim((string) $sheet->getCellByColumnAndRow($col, $row)->getValue());

                if (strcasecmp($cellValue, $label) !== 0) {
                    continue;
                }

                for ($nextCol = $col + 1; $nextCol <= $highestCol; $nextCol++) {
                    $nextValue = $sheet->getCellByColumnAndRow($nextCol, $row)->getValue();
                    $nextValue = trim((string) $nextValue);

                    if ($nextValue !== '') {
                        return $nextValue;
                    }
                }
            }
        }

        return null;
    }

    /**
     * @return array<int, array{poin: string, standar: string}>
     */
    protected function extractStandards(Worksheet $sheet, int $highestRow): array
    {
        $startRow = null;

        for ($row = 1; $row <= $highestRow; $row++) {
            $colA = trim((string) $sheet->getCellByColumnAndRow(1, $row)->getValue());
            $colB = trim((string) $sheet->getCellByColumnAndRow(2, $row)->getValue());

            if (strcasecmp($colA, 'NO') === 0 && stripos($colB, 'ITEM PENGECEKAN') !== false) {
                $startRow = $row + 1;
                break;
            }
        }

        if ($startRow === null) {
            return [];
        }

        $standards = [];
        $current = null;

        for ($row = $startRow; $row <= $highestRow; $row++) {
            $colAText = trim((string) $sheet->getCellByColumnAndRow(1, $row)->getValue());
            $colBText = trim((string) $sheet->getCellByColumnAndRow(2, $row)->getValue());

            // Ketemu section header baru (misal "TINDAKAN PERAWATAN & PERBAIKAN DIES") -> berhenti
            if ($colAText !== '' && !is_numeric($colAText) && $colBText === '') {
                break;
            }

            if ($colAText !== '' && is_numeric($colAText)) {
                if ($current !== null) {
                    $standards[] = $current;
                }
                $current = [
                    'poin'    => $colAText,
                    'standar' => $colBText,
                ];
                continue;
            }

            if ($colAText === '' && $colBText !== '' && $current !== null) {
                $current['standar'] .= "\n" . $colBText;
            }
        }

        if ($current !== null) {
            $standards[] = $current;
        }

        return $standards;
    }
}
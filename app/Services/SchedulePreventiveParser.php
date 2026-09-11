<?php

namespace App\Services;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use RuntimeException;

class SchedulePreventiveParser
{
    protected const WEEK_OFFSET_DAYS = [
        'I'   => 0,
        'II'  => 7,
        'III' => 14,
        'IV'  => 21,
    ];

    public function parse(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);

        if (!$spreadsheet->sheetNameExists('MASTER')) {
            throw new RuntimeException("Sheet 'MASTER' tidak ditemukan di file ini.");
        }

        $sheet = $spreadsheet->getSheetByName('MASTER');
        $highestRow = $sheet->getHighestRow();
        $highestCol = Coordinate::columnIndexFromString($sheet->getHighestColumn());

        [$headerRow, $partCodeCol] = $this->findHeader($sheet, $highestCol);
        $weekLabelRow = $headerRow + 1;

        $weekColumns = $this->mapWeekColumns($sheet, $headerRow, $weekLabelRow, $partCodeCol, $highestCol);

        if (empty($weekColumns)) {
            throw new RuntimeException('Tidak ditemukan kolom jadwal mingguan (I/II/III/IV) di sheet MASTER.');
        }

        $dataStartRow = $this->findDataStartRow($sheet, $partCodeCol, $weekLabelRow, $highestRow);

        return $this->extractRows($sheet, $partCodeCol, $weekColumns, $dataStartRow, $highestRow);
    }

    protected function findHeader(Worksheet $sheet, int $highestCol): array
    {
        for ($row = 1; $row <= 10; $row++) {
            for ($col = 1; $col <= $highestCol; $col++) {
                $val = trim((string) $sheet->getCellByColumnAndRow($col, $row)->getValue());
                if (strcasecmp($val, 'DELIVERY PART CODE') === 0) {
                    return [$row, $col];
                }
            }
        }

        throw new RuntimeException("Kolom 'DELIVERY PART CODE' tidak ditemukan di sheet MASTER.");
    }

    protected function mapWeekColumns(Worksheet $sheet, int $headerRow, int $weekLabelRow, int $partCodeCol, int $highestCol): array
    {
        $weekColumns = [];
        $lastMonthDate = null;

        for ($col = $partCodeCol + 1; $col <= $highestCol; $col++) {
            $monthCell = $sheet->getCellByColumnAndRow($col, $headerRow);
            $rawValue = $monthCell->getValue();

            if ($rawValue !== null && ExcelDate::isDateTime($monthCell)) {
                $lastMonthDate = Carbon::instance(ExcelDate::excelToDateTimeObject($rawValue))->startOfMonth();
            }

            $weekLabel = strtoupper(trim((string) $sheet->getCellByColumnAndRow($col, $weekLabelRow)->getValue()));

            if ($lastMonthDate !== null && isset(self::WEEK_OFFSET_DAYS[$weekLabel])) {
                $weekColumns[$col] = $lastMonthDate->copy()->addDays(self::WEEK_OFFSET_DAYS[$weekLabel]);
            }
        }

        return $weekColumns;
    }

    protected function findDataStartRow(Worksheet $sheet, int $partCodeCol, int $weekLabelRow, int $highestRow): int
    {
        for ($row = $weekLabelRow + 1; $row <= $highestRow; $row++) {
            $val = trim((string) $sheet->getCellByColumnAndRow($partCodeCol, $row)->getValue());
            if ($val !== '') {
                return $row;
            }
        }

        throw new RuntimeException('Tidak ada baris data ditemukan di sheet MASTER.');
    }

    protected function extractRows(Worksheet $sheet, int $partCodeCol, array $weekColumns, int $dataStartRow, int $highestRow): array
    {
        $results = [];
        $seenPartCodes = [];

        for ($row = $dataStartRow; $row <= $highestRow; $row++) {
            $partCode = trim((string) $sheet->getCellByColumnAndRow($partCodeCol, $row)->getValue());

            if ($partCode === '' || isset($seenPartCodes[$partCode])) {
                continue;
            }
            $seenPartCodes[$partCode] = true;

            $markedDates = [];
            foreach ($weekColumns as $col => $date) {
                $val = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                if ($val !== null && trim((string) $val) !== '') {
                    $markedDates[] = $date;
                }
            }

            if (empty($markedDates)) {
                $results[] = [
                    'part_no' => $partCode,
                    'status'  => 'no_marks',
                ];
                continue;
            }

            $uniqueDates = collect($markedDates)
                ->sortBy(fn (Carbon $d) => $d->timestamp)
                ->unique(fn (Carbon $d) => $d->toDateString())
                ->values();

            $mulaiService = $uniqueDates->first();

            if ($uniqueDates->count() === 1) {
                $days = 365;
            } else {
                $gaps = [];
                for ($i = 1; $i < $uniqueDates->count(); $i++) {
                    $gaps[] = $uniqueDates[$i]->diffInDays($uniqueDates[$i - 1]);
                }
                sort($gaps);
                $mid = intdiv(count($gaps), 2);
                $days = count($gaps) % 2 === 0
                    ? intdiv($gaps[$mid - 1] + $gaps[$mid], 2)
                    : $gaps[$mid];
                $days = max(1, $days);
            }

            [$periode, $intervalValue] = $this->classifyInterval($days);

            $results[] = [
                'part_no'        => $partCode,
                'status'         => 'ok',
                'mulai_service'  => $mulaiService->toDateString(),
                'periode'        => $periode,
                'interval_value' => $intervalValue,
            ];
        }

        return $results;
    }

    /**
     * Mapping jumlah hari -> periode + interval_value yang paling pas.
     * Custom cuma dipakai kalau jumlah harinya gak pas ke kelipatan
     * harian/mingguan/bulanan/tahunan (dengan toleransi kecil karena
     * panjang bulan di kalender gak seragam).
     *
     * @return array{0: string, 1: int}
     */
    protected function classifyInterval(int $days): array
    {
        // Tahunan: kelipatan ~365 hari (toleransi ±5 hari)
        $years = round($days / 365);
        if ($years >= 1 && abs($days - ($years * 365)) <= 5) {
            return ['tahunan', (int) $years];
        }

        // Bulanan: kelipatan ~30 hari (toleransi ±3 hari)
        $months = round($days / 30);
        if ($months >= 1 && abs($days - ($months * 30)) <= 3) {
            return ['bulanan', (int) $months];
        }

        // Mingguan: kelipatan 7 hari persis
        if ($days % 7 === 0) {
            return ['mingguan', (int) ($days / 7)];
        }

        // Harian: 1 hari persis
        if ($days === 1) {
            return ['harian', 1];
        }

        // Sisanya: custom, satuan hari
        return ['custom', $days];
    }
}
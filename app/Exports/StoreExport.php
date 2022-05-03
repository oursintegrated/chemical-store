<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use Carbon\Carbon;
use DB;

class StoreExport extends DefaultValueBinder implements ShouldAutoSize, FromQuery, WithCustomCsvSettings, WithMapping, WithHeadings, WithStrictNullComparison, WithCustomValueBinder
{
    use Exportable;

    public function __construct()
    {

    }

    public function query()
    {

        return DB::table('stores')
            ->select(
                'store_code',
                'initials_code',
                'store_desc',
                'created_at',
                'updated_at'
            )
            ->orderBy('store_code');
    }

    public function getCsvSettings(): array
    {
        return [
            'delimiter'              => ';',
            'enclosure'              => '',
            'line_ending'            => PHP_EOL,
            'use_bom'                => false,
            'include_separator_line' => false,
            'excel_compatibility'    => false,
        ];
    }

    public function headings(): array
    {
        return [
            'Store Code',
            'Initial',
            'Store Name',
            'Created At',
            'Updated At'
        ];
    }

    // **
    //  * @var PostpaidTransaction $report
    //  */
    public function map($report): array
    {
        return [
            $report->store_code,
            $report->initials_code,
            $report->store_desc,
            $report->created_at,
            $report->updated_at,
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        // Convert semua data numeric menjadi string supaya format number di csv tidak berubah menjadi notasi matematika & tidak dibulatkan
        if (is_numeric($value)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }
}

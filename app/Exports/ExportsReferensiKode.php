<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportsReferensiKode implements FromCollectionFromArray, WithHeadings, WithTitle, 
ShouldAutoSize, WithColumnFormatting, WithMapping
{
    protected $rows;
    /**
    * @return \Illuminate\Support\Collection
    */

    public function __construct(array $rows)
    {
        $this->rows = $rows;
    }

    public function map($row): array
    {
        return [
            $row['name'],
            $row['impressions'],
            $row['clicks'],
            $row['ctr']
        ];
    }

    public function headings(): array
    {
        return [
            'Name',
            'Impressions',
            'Clicks',
            'CTR'
        ];
    }

    public function array(): array
    {
        return $this->rows;
    }

    public function title(): string
    {
        return 'Refernsi Kode';
    }

    public function columnFormats(): array
    {
        return [
            'B' => '#,##0',
            'C' => '#,##0',
            'D' => NumberFormat::FORMAT_PERCENTAGE_00
        ];
    }
}

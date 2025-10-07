<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;

class SurveyInPerDayExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected $rows;
    protected $columnsLower;
    protected $sheetTitle;

    public function __construct(Collection $rows, array $columnsLower, string $sheetTitle = 'Survey In')
    {
        $this->rows = $rows;
        $this->columnsLower = $columnsLower;
        $this->sheetTitle = $sheetTitle;
    }

    public function collection()
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return array_map(function ($c) {
            return strtoupper(str_replace('_', ' ', $c));
        }, $this->columnsLower);
    }

    public function map($row): array
    {
        return array_map(function ($col) use ($row) {
            return $row->{$col} ?? null;
        }, $this->columnsLower);
    }

    public function title(): string
    {
        return $this->sheetTitle;
    }
}

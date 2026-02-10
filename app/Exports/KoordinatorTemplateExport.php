<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromArray;

class KoordinatorTemplateExport implements WithHeadings, ShouldAutoSize, FromArray
{
    public function headings(): array
    {
        return [
            'nama',
            'rt',
            'rw',
        ];
    }

    public function array(): array
    {
        return [
            ['Pak Budi', '001', '002'],
        ];
    }
}

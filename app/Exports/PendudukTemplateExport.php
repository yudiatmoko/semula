<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PendudukTemplateExport implements WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'nik',
            'nama',
            'jenis_kelamin',
            'alamat',
            'rt',
            'rw',
        ];
    }
}
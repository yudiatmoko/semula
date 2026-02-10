<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromArray;

class PendukungTemplateExport implements WithHeadings, ShouldAutoSize, FromArray
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
            'koordinator',
        ];
    }

    public function array(): array
    {
        return [
            ['3201234567890001', 'John Doe', 'L', 'Jl. Merdeka No. 10', '001', '002', 'Pak Budi'],
        ];
    }
}

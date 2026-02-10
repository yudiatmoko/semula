<?php

namespace App\Imports;

use App\Models\Koordinator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class KoordinatorImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading
{
    public function model(array $row)
    {
        return Koordinator::updateOrCreate(
            [
                'nama' => $row['nama'],
                'rt' => $row['rt'],
                'rw' => $row['rw'],
            ],
            [
                'nama' => $row['nama'],
                'rt' => $row['rt'],
                'rw' => $row['rw'],
            ]
        );
    }

    public function rules(): array
    {
        return [
            'nama' => 'required',
            'rt' => 'required',
            'rw' => 'required',
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}

<?php

namespace App\Imports;

use App\Models\Penduduk;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class PendudukImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading
{
    public function model(array $row)
    {
        return Penduduk::updateOrCreate(
            ['nik' => $row['nik']],
            [
                'nama' => $row['nama'],
                'jenis_kelamin' => $row['jenis_kelamin'],
                'alamat' => $row['alamat'],
                'rt' => $row['rt'],
                'rw' => $row['rw'],
            ]
        );
    }

    public function rules(): array
    {
        return [
            'nik' => 'required',
            'nama' => 'required',
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}
<?php

namespace App\Imports;

use App\Models\Pendukung;
use App\Models\Penduduk;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class PendukungImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading
{
    public function model(array $row)
    {
        $penduduk = Penduduk::where('nik', $row['nik'])->first();

        if (!$penduduk) {
            return null;
        }

        return Pendukung::updateOrCreate(
            ['nik' => $row['nik']],
            [
                'nama' => $penduduk->nama,
                'jenis_kelamin' => $penduduk->jenis_kelamin,
                'alamat' => $penduduk->alamat,
                'rt' => $penduduk->rt,
                'rw' => $penduduk->rw,
                'koordinator' => $row['koordinator'],
            ]
        );
    }

    public function rules(): array
    {
        return [
            'nik' => 'required',
            'koordinator' => 'required',
        ];
    }

    public function chunkSize(): int
    {
        return 500;
    }
}

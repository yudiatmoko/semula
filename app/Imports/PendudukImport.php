<?php

namespace App\Imports;

use App\Models\Penduduk;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithChunkReading; // 1. Import interface ini

class PendudukImport implements ToModel, WithHeadingRow, WithValidation, WithChunkReading // 2. Implement di sini
{
    public function model(array $row)
    {
        // updateOrCreate memang agak lambat dibanding insert biasa,
        // tapi aman untuk menghindari duplikasi data.
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
            // Tambahkan validasi lain jika perlu
        ];
    }

    // 3. Tentukan jumlah baris yang diproses per batch (Hemat RAM)
    public function chunkSize(): int
    {
        return 500; // Proses per 500 baris
    }
}
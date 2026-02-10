<?php

namespace App\Filament\Imports;

use App\Models\Penduduk;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class PendudukImporter extends Importer
{
    protected static ?string $model = Penduduk::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nik')->label('NIK')
                ->requiredMapping()
                ->rules(['required', 'max:16', 'unique:penduduks,nik']),

            ImportColumn::make('nama')
                ->requiredMapping()
                ->rules(['required']),

            ImportColumn::make('jenis_kelamin')
                ->rules(['required', 'in:L,P']),

            ImportColumn::make('alamat')
                ->requiredMapping(),

            ImportColumn::make('rt')->label('RT')->rules(['required']),
            ImportColumn::make('rw')->label('RW')->rules(['required']),
        ];
    }

    public function resolveRecord(): ?Penduduk
    {
        return Penduduk::firstOrNew([
            'nik' => $this->data['nik'],
        ]);
    }

    public static function getCompletedNotificationTitle(Import $import): string
    {
        if ($import->successful_rows === 0 && $import->getFailedRowsCount() > 0) {
            return 'Gagal mengimpor data';
        }

        return 'Impor data selesai';
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Impor selesai. ' . number_format($import->successful_rows) . ' data berhasil masuk.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' data gagal.';
        }

        return $body;
    }
}
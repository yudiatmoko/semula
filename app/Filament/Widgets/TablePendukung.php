<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget;
use Filament\Tables;
use App\Models\Pendukung;
use Illuminate\Database\Eloquent\Builder;

class TablePendukung extends TableWidget
{
    protected static ?string $heading = 'Total Pendukung per Wilayah';

    protected int|string|array $columnSpan = 'full';

    /**
     * 🔑 RECORD KEY (WAJIB & UNIQUE UNTUK GROUP BY)
     */
    public function getTableRecordKey($record): string
    {
        return md5(
            ($record->alamat ?? '') .
            ($record->rt ?? '') .
            ($record->rw ?? '')
        );
    }

    /**
     * 📊 QUERY DATA
     */
    protected function getTableQuery(): Builder
    {
        return Pendukung::query()
            ->selectRaw('
                alamat,
                rt,
                rw,
                COUNT(*) as total_pendukung
            ')
            ->groupBy('alamat', 'rt', 'rw')
            ->orderBy('alamat')
            ->orderBy('rw')
            ->orderBy('rt');
    }

    /**
     * 🧱 KOLOM TABEL
     */
    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('row_number')
                ->label('No')
                ->rowIndex(),

            Tables\Columns\TextColumn::make('alamat')
                ->label('Alamat')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('rt')
                ->label('RT')
                ->sortable(),

            Tables\Columns\TextColumn::make('rw')
                ->label('RW')
                ->sortable(),

            Tables\Columns\TextColumn::make('total_pendukung')
                ->label('Total Pendukung')
                ->badge()
                ->color('success')
                ->sortable(),
        ];
    }

    /**
     * 🔍 FILTER (ALAMAT + RT + RW)
     * KOSONG = TAMPIL SEMUA
     */
    protected function getTableFilters(): array
    {
        return [
            // FILTER ALAMAT
            Tables\Filters\SelectFilter::make('alamat')
                ->label('Alamat')
                ->options(
                    Pendukung::query()
                        ->select('alamat')
                        ->distinct()
                        ->orderBy('alamat')
                        ->pluck('alamat', 'alamat')
                        ->toArray()
                )
                ->query(function (Builder $query, array $data): Builder {
                    if (filled($data['value'])) {
                        $query->where('alamat', $data['value']);
                    }

                    return $query;
                }),

            // FILTER RT
            Tables\Filters\SelectFilter::make('rt')
                ->label('RT')
                ->options(
                    Pendukung::query()
                        ->select('rt')
                        ->distinct()
                        ->orderBy('rt')
                        ->pluck('rt', 'rt')
                        ->toArray()
                )
                ->query(function (Builder $query, array $data): Builder {
                    if (filled($data['value'])) {
                        $query->where('rt', $data['value']);
                    }

                    return $query;
                }),

            // FILTER RW
            Tables\Filters\SelectFilter::make('rw')
                ->label('RW')
                ->options(
                    Pendukung::query()
                        ->select('rw')
                        ->distinct()
                        ->orderBy('rw')
                        ->pluck('rw', 'rw')
                        ->toArray()
                )
                ->query(function (Builder $query, array $data): Builder {
                    if (filled($data['value'])) {
                        $query->where('rw', $data['value']);
                    }

                    return $query;
                }),
        ];
    }
}

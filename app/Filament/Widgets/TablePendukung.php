<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget;
use Filament\Tables;
use App\Models\Pendukung;
use App\Models\Penduduk;
use Illuminate\Database\Eloquent\Builder;

class TablePendukung extends TableWidget
{
    protected static ?string $heading = 'Total Pendukung per Wilayah';

    protected int|string|array $columnSpan = 'full';

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns($this->getTableColumns())
            ->filters($this->getTableFilters())
            ->paginated(false)
            ->defaultSort('persentase', 'desc');
    }

    public function getTableRecordKey($record): string
    {
        return md5(
            ($record->rt ?? '') .
            ($record->rw ?? '')
        );
    }

    protected function getTableQuery(): Builder
    {
        return Penduduk::query()
            ->selectRaw('
                    rt,
                    rw,
                    COUNT(*) as total_penduduk,
                    (
                        SELECT COUNT(*) 
                        FROM pendukungs 
                        WHERE pendukungs.rt = penduduks.rt 
                        AND pendukungs.rw = penduduks.rw
                    ) as total_pendukung
                ')
            ->groupBy('rt', 'rw');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('row_number')
                ->label('No')
                ->rowIndex(),

            Tables\Columns\TextColumn::make('rt')
                ->label('RT')
                ->sortable(),

            Tables\Columns\TextColumn::make('rw')
                ->label('RW')
                ->sortable(),

            Tables\Columns\TextColumn::make('total_penduduk')
                ->label('Jumlah Penduduk')
                ->sortable(),

            Tables\Columns\TextColumn::make('total_pendukung')
                ->label('Total Pendukung')
                ->sortable()
                ->badge(),

            Tables\Columns\TextColumn::make('persentase')
                ->label('Persentase')
                ->state(function ($record) {
                    $total = $record->total_penduduk;
                    $count = $record->total_pendukung;
                    return $total > 0 ? round(($count / $total) * 100, 1) . '%' : '0%';
                })
                ->badge()
                ->color(
                    fn($state) =>
                    (float) $state >= 75 ? 'success' :
                    ((float) $state >= 50 ? 'warning' :
                        'danger')
                )
                ->sortable(query: function (Builder $query, string $direction): Builder {
                    return $query->orderByRaw("
            (
                (SELECT COUNT(*) 
                 FROM pendukungs 
                 WHERE pendukungs.rt = penduduks.rt 
                 AND pendukungs.rw = penduduks.rw) 
                / 
                COUNT(*)
            ) $direction
        ");
                }),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\SelectFilter::make('alamat')
                ->label('Alamat')
                ->options(
                    Penduduk::query()
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

            Tables\Filters\SelectFilter::make('rt')
                ->label('RT')
                ->options(
                    Penduduk::query()
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

            Tables\Filters\SelectFilter::make('rw')
                ->label('RW')
                ->options(
                    Penduduk::query()
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

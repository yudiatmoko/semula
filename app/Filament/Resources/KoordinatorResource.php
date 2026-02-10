<?php

namespace App\Filament\Resources;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\KoordinatorResource\Pages;
use App\Models\Koordinator;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class KoordinatorResource extends Resource
{
    protected static ?string $model = Koordinator::class;
    protected static ?string $slug = 'koordinator';
    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $navigationLabel = 'Data Koordinator';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->label('Nama Koordinator'),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('rt')
                        ->label('RT')
                        ->required(),
                    Forms\Components\TextInput::make('rw')
                        ->label('RW')
                        ->required(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([50, 100, 500])
            ->defaultPaginationPageOption(50)
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Koordinator')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rt')
                    ->label('RT')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('rw')
                    ->label('RW')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pendukungs_count')
                    ->label('Jumlah Pendukung')
                    ->counts('pendukungs')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('rt')
                    ->label('RT')
                    ->options(fn() => Koordinator::select('rt')->distinct()->pluck('rt', 'rt')->toArray()),
                Tables\Filters\SelectFilter::make('rw')
                    ->label('RW')
                    ->options(fn() => Koordinator::select('rw')->distinct()->pluck('rw', 'rw')->toArray()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Data Terpilih'),
                    FilamentExportBulkAction::make('export')
                        ->label('Ekspor')
                        ->disableAdditionalColumns(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKoordinators::route('/'),
            'create' => Pages\CreateKoordinator::route('/create'),
            'edit' => Pages\EditKoordinator::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PendukungResource\Pages;
use App\Models\Pendukung;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PendukungResource extends Resource
{
    protected static ?string $model = Pendukung::class;
    protected static ?string $slug = 'pendukung';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Data Pendukung';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Warga (Read Only)')
                    ->schema([
                        Forms\Components\TextInput::make('nik')->disabled(),
                        Forms\Components\TextInput::make('nama')->disabled(),
                        Forms\Components\TextInput::make('jenis_kelamin')->disabled(),
                        Forms\Components\Textarea::make('alamat')->disabled()->columnSpanFull(),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('rt')->disabled(),
                            Forms\Components\TextInput::make('rw')->disabled(),
                        ]),
                    ]),

                Forms\Components\Section::make('Data Timses')
                    ->schema([
                        Forms\Components\TextInput::make('koordinator')
                            ->required()
                            ->label('Koordinator Lapangan'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('koordinator')
                    ->label('Koordinator')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama')->searchable(),
                Tables\Columns\TextColumn::make('alamat'),
                Tables\Columns\TextColumn::make('rt')->label('RT'),
                Tables\Columns\TextColumn::make('rw')->label('RW'),
                Tables\Columns\TextColumn::make('jenis_kelamin')->label('L/P'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('koordinator')
                    ->options(fn() => Pendukung::pluck('koordinator', 'koordinator')->unique()->toArray()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([

                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Data Terpilih'),

                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPendukungs::route('/'),
            'create' => Pages\CreatePendukung::route('/create'),
            'edit' => Pages\EditPendukung::route('/{record}/edit'),
        ];
    }
}
<?php

namespace App\Filament\Resources;

use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use App\Filament\Resources\PendukungResource\Pages;
use App\Models\Koordinator;
use App\Models\Penduduk;
use App\Models\Pendukung;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PendukungResource extends Resource
{
    protected static ?string $model = Pendukung::class;
    protected static ?string $slug = 'pendukung';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Data Pendukung';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pilih Penduduk')
                    ->schema([
                        Forms\Components\Select::make('penduduk_id')
                            ->label('Cari berdasarkan NIK / Nama')
                            ->searchable()
                            ->getSearchResultsUsing(function (string $search) {
                                return Penduduk::query()
                                    ->whereNotIn('nik', Pendukung::pluck('nik'))
                                    ->where(function ($q) use ($search) {
                                        $q->where('nik', 'like', "%{$search}%")
                                          ->orWhere('nama', 'like', "%{$search}%");
                                    })
                                    ->limit(20)
                                    ->get()
                                    ->mapWithKeys(fn($p) => [
                                        $p->nik => "{$p->nik} — {$p->nama}",
                                    ])
                                    ->toArray();
                            })
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                if (!$state) {
                                    $set('nik', null);
                                    $set('nama', null);
                                    $set('jenis_kelamin', null);
                                    $set('alamat', null);
                                    $set('rt', null);
                                    $set('rw', null);
                                    $set('koordinator_id', null);
                                    return;
                                }

                                $penduduk = Penduduk::where('nik', $state)->first();
                                if ($penduduk) {
                                    $set('nik', $penduduk->nik);
                                    $set('nama', $penduduk->nama);
                                    $set('jenis_kelamin', $penduduk->jenis_kelamin);
                                    $set('alamat', $penduduk->alamat);
                                    $set('rt', $penduduk->rt);
                                    $set('rw', $penduduk->rw);
                                    $set('koordinator_id', null);
                                }
                            })
                            ->dehydrated(false)
                            ->visibleOn('create')
                            ->required(),
                    ])
                    ->visibleOn('create'),

                Forms\Components\Section::make('Data Pendukung')
                    ->schema([
                        Forms\Components\TextInput::make('nik')->label('NIK')->disabled()->dehydrated(),
                        Forms\Components\TextInput::make('nama')->disabled()->dehydrated(),
                        Forms\Components\TextInput::make('jenis_kelamin')
                            ->label('Jenis Kelamin')
                            ->formatStateUsing(fn($state) => ucfirst($state))
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\Textarea::make('alamat')->disabled()->dehydrated()->columnSpanFull(),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('rt')->label('RT')->disabled()->dehydrated(),
                            Forms\Components\TextInput::make('rw')->label('RW')->disabled()->dehydrated(),
                        ]),
                    ]),

                Forms\Components\Section::make('Data Koordinator Lapangan')
                    ->schema([
                        Forms\Components\Select::make('koordinator_id')
                            ->label('Koordinator')
                            ->relationship('koordinator', 'nama')
                            ->options(function (Get $get) {
                                $rt = $get('rt');
                                $rw = $get('rw');

                                if (!$rt || !$rw) {
                                    return [];
                                }

                                return Koordinator::where('rt', $rt)
                                    ->where('rw', $rw)
                                    ->pluck('nama', 'id')
                                    ->toArray();
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->helperText('Koordinator otomatis difilter berdasarkan RT & RW penduduk.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([50, 100, 500])
            ->defaultPaginationPageOption(50)
            ->columns([
                Tables\Columns\TextColumn::make('koordinator.nama')
                    ->label('Koordinator')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nik')->label('NIK')->searchable(),
                Tables\Columns\TextColumn::make('nama')->searchable(),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->formatStateUsing(fn($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('alamat')->limit(30),
                Tables\Columns\TextColumn::make('rt')->label('RT'),
                Tables\Columns\TextColumn::make('rw')->label('RW')
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('koordinator_id')
                    ->label('Koordinator')
                    ->relationship('koordinator', 'nama'),
                Tables\Filters\SelectFilter::make('alamat')
                    ->options(fn() => Pendukung::select('alamat')->distinct()->pluck('alamat', 'alamat')->toArray())
                    ->searchable(),
                Tables\Filters\SelectFilter::make('rt')
                    ->label('RT')
                    ->options(fn() => Pendukung::select('rt')->distinct()->pluck('rt', 'rt')->toArray()),
                Tables\Filters\SelectFilter::make('rw')
                    ->label('RW')
                    ->options(fn() => Pendukung::select('rw')->distinct()->pluck('rw', 'rw')->toArray()),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([

                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Hapus Data Terpilih'),

                    FilamentExportBulkAction::make('export')->label("Ekspor")->disableAdditionalColumns(),

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
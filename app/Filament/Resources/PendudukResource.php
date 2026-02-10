<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PendudukResource\Pages;
use App\Models\Koordinator;
use App\Models\Penduduk;
use App\Models\Pendukung;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class PendudukResource extends Resource
{
    protected static ?string $model = Penduduk::class;
    protected static ?string $slug = 'penduduk';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Data Penduduk';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nik')->label('NIK')->required()->unique(ignoreRecord: true)->maxLength(16),
                Forms\Components\TextInput::make('nama')->required(),
                Forms\Components\Select::make('jenis_kelamin')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'L',
                        'P' => 'P',
                    ])->required(),
                Forms\Components\Textarea::make('alamat')->required()->columnSpanFull(),
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\TextInput::make('rt')->label('RT')->required(),
                    Forms\Components\TextInput::make('rw')->label('RW')->required(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([50, 100, 500])
            ->defaultPaginationPageOption(50)
            ->deselectAllRecordsWhenFiltered(false)
            ->columns([
                Tables\Columns\TextColumn::make('nik')->label('NIK')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('nama')->searchable(),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->formatStateUsing(fn($state) => ucfirst($state)),
                Tables\Columns\TextColumn::make('alamat')->searchable()->limit(30),
                Tables\Columns\TextColumn::make('rt')->label('RT')->searchable(),
                Tables\Columns\TextColumn::make('rw')->label(label: 'RW')->searchable(),

                Tables\Columns\IconColumn::make('is_recruited')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-s-check-circle')
                    ->falseIcon('heroicon-o-minus')
                    ->state(fn($record) => Pendukung::where('nik', $record->nik)->exists()),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('alamat')
                    ->options(fn() => Penduduk::select('alamat')->distinct()->pluck('alamat', 'alamat')->toArray())
                    ->searchable(),
                Tables\Filters\SelectFilter::make('rt')
                    ->label('RT')
                    ->options(fn() => Penduduk::select('rt')->distinct()->pluck('rt', 'rt')->toArray()),
                Tables\Filters\SelectFilter::make('rw')
                    ->label('RW')
                    ->options(fn() => Penduduk::select('rw')->distinct()->pluck('rw', 'rw')->toArray()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    BulkAction::make('salinKePendukung')
                        ->label('Tambah ke Pendukung')
                        ->icon('heroicon-o-user-plus')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Tentukan Koordinator')
                        ->modalDescription('Pilih koordinator untuk masing-masing RT/RW yang dipilih.')

                        ->form(function (Collection $records) {
                            $groups = $records->groupBy(fn($r) => $r->rt . '|' . $r->rw);

                            $fields = [];
                            foreach ($groups as $key => $group) {
                                [$rt, $rw] = explode('|', $key);
                                $count = $group->count();

                                $koordinators = Koordinator::where('rt', $rt)
                                    ->where('rw', $rw)
                                    ->pluck('nama', 'id')
                                    ->toArray();

                                $fields[] = Forms\Components\Select::make("koordinator_{$rt}_{$rw}")
                                    ->label("Koordinator RT {$rt} / RW {$rw} ({$count} orang)")
                                    ->options($koordinators)
                                    ->searchable()
                                    ->required()
                                    ->helperText(empty($koordinators) ? 'Belum ada koordinator untuk RT/RW ini.' : null);
                            }

                            return $fields;
                        })

                        ->action(function (Collection $records, array $data) {
                            $berhasil = 0;
                            $sudahAda = 0;
                            $tanpaKoordinator = 0;

                            foreach ($records as $warga) {
                                if (Pendukung::where('nik', $warga->nik)->exists()) {
                                    $sudahAda++;
                                    continue;
                                }

                                $koordinatorId = $data["koordinator_{$warga->rt}_{$warga->rw}"] ?? null;

                                if (!$koordinatorId) {
                                    $tanpaKoordinator++;
                                    continue;
                                }

                                Pendukung::create([
                                    'nik' => $warga->nik,
                                    'nama' => $warga->nama,
                                    'alamat' => $warga->alamat,
                                    'rt' => $warga->rt,
                                    'rw' => $warga->rw,
                                    'jenis_kelamin' => $warga->jenis_kelamin,
                                    'koordinator_id' => $koordinatorId,
                                ]);
                                $berhasil++;
                            }

                            $messages = [];
                            if ($berhasil > 0) {
                                $messages[] = "{$berhasil} pendukung berhasil ditambahkan.";
                            }
                            if ($sudahAda > 0) {
                                $messages[] = "{$sudahAda} data sudah terdaftar.";
                            }
                            if ($tanpaKoordinator > 0) {
                                $messages[] = "{$tanpaKoordinator} dilewati (koordinator tidak dipilih).";
                            }

                            if ($berhasil > 0) {
                                Notification::make()
                                    ->title('Proses selesai')
                                    ->body(implode(' ', $messages))
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Tidak ada data yang ditambahkan')
                                    ->body(implode(' ', $messages))
                                    ->warning()
                                    ->send();
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenduduks::route('/'),
            'create' => Pages\CreatePenduduk::route('/create'),
            'edit' => Pages\EditPenduduk::route('/{record}/edit'),
        ];
    }
}
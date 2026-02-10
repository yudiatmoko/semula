<?php

namespace App\Filament\Resources\PendukungResource\Pages;

use App\Exports\PendukungTemplateExport;
use App\Filament\Resources\PendukungResource;
use App\Imports\PendukungImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ListPendukungs extends ListRecords
{
    protected static string $resource = PendukungResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('downloadTemplate')
                ->label('Unduh template')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info')
                ->action(function () {
                    return Excel::download(
                        new PendukungTemplateExport(),
                        'template_pendukung.xlsx'
                    );
                }),

            Actions\Action::make('importPendukung')
                ->label('Impor data')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->closeModalByClickingAway(false)
                ->modalDescription('NIK harus sudah terdaftar di data penduduk. Data nama, alamat, RT, RW, dan jenis kelamin akan diambil dari data penduduk.')
                ->form([
                    FileUpload::make('attachment')
                        ->label('Upload File Excel')
                        ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'])
                        ->required()
                        ->directory('imports'),
                ])
                ->action(function (array $data) {
                    set_time_limit(0);
                    ini_set('memory_limit', '1024M');

                    $filePath = Storage::disk('public')->path($data['attachment']);

                    try {
                        Excel::import(new PendukungImport, $filePath);

                        if (Storage::disk('public')->exists($data['attachment'])) {
                            Storage::disk('public')->delete($data['attachment']);
                        }

                        Notification::make()
                            ->title('Sukses')
                            ->body('Data pendukung berhasil diimpor!')
                            ->success()
                            ->send();

                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('Gagal')
                            ->body('Error: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\PendudukResource\Pages;

use App\Exports\PendudukTemplateExport;
use App\Filament\Resources\PendudukResource;
use App\Imports\PendudukImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ListPenduduks extends ListRecords
{
    protected static string $resource = PendudukResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('downloadTemplate')
                ->label('Unduh template')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info')
                ->action(function () {
                    return Excel::download(
                        new PendudukTemplateExport(),
                        'template_penduduk.xlsx'
                    );
                }),
            Actions\Action::make('importPenduduk')
                ->label('Impor data')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->closeModalByClickingAway(false)

                ->modalDescription('Proses ini mungkin memerlukan waktu beberapa menit untuk data yang besar. Mohon tunggu dan jangan tutup halaman ini.')
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
                        Excel::import(new PendudukImport, $filePath);

                        if (Storage::disk('public')->exists($data['attachment'])) {
                            Storage::disk('public')->delete($data['attachment']);
                        }

                        Notification::make()
                            ->title('Sukses')
                            ->body('Data penduduk berhasil diimpor!')
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
<?php

namespace App\Filament\Resources\KoordinatorResource\Pages;

use App\Exports\KoordinatorTemplateExport;
use App\Filament\Resources\KoordinatorResource;
use App\Imports\KoordinatorImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ListKoordinators extends ListRecords
{
    protected static string $resource = KoordinatorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('downloadTemplate')
                ->label('Unduh  template')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info')
                ->action(function () {
                    return Excel::download(
                        new KoordinatorTemplateExport(),
                        'template_koordinator.xlsx'
                    );
                }),

            Actions\Action::make('importKoordinator')
                ->label('Impor data')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('success')
                ->closeModalByClickingAway(false)
                ->modalDescription('Upload file Excel dengan kolom: nama, rt, rw.')
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
                        Excel::import(new KoordinatorImport, $filePath);

                        if (Storage::disk('public')->exists($data['attachment'])) {
                            Storage::disk('public')->delete($data['attachment']);
                        }

                        Notification::make()
                            ->title('Sukses')
                            ->body('Data koordinator berhasil diimpor!')
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

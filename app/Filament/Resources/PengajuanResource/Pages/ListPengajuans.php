<?php

namespace App\Filament\Resources\PengajuanResource\Pages;

use App\Filament\Resources\PengajuanResource;
use Filament\Actions;
use Filament\Pages\Page;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use pxlrbt\FilamentExcel\Actions\Exports\ExportAction;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction as PagesExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;

class ListPengajuans extends ListRecords
{
    protected static string $resource = PengajuanResource::class;
    protected function getHeaderActions(): array
    {
        //** @var User $user */
        $user = auth('web')->user();

        return [
            Actions\CreateAction::make()
                ->visible(fn() => $user->role === 'mahasiswa'),

            PagesExportAction::make('export')
                ->label('Export Data')
                ->exports([
                    ExcelExport::make()
                        ->withColumns([
                            Column::make('user.nim')->heading('NIM'),
                            Column::make('user.name')->heading('Mahasiswa'),
                            Column::make('prodi')->heading('Program Studi'),
                            Column::make('judul_skripsi')->heading('Judul Skripsi'),
                            Column::make('pembimbing.nama')->heading('Dosen Pembimbing'),
                            Column::make('status')->heading('Status Pengajuan'),
                            Column::make('jenis_naskah')->heading('Jenis Naskah'),
                            Column::make('similarity_score')->heading('Similarity (%)'),
                            Column::make('created_at')->heading('Tanggal Pengajuan'),
                            Column::make('updated_at')->heading('Tanggal Selesai'),
                        ])

                ]),
        ];
    }
}

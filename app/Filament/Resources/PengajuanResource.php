<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengajuanResource\Pages;
use App\Filament\Resources\PengajuanResource\RelationManagers;
use App\Models\Pembimbing;
use App\Models\Pengajuan;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
// use pxlrbt\FilamentExcel\Actions\Exports\ExportAction;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class PengajuanResource extends Resource
{
    protected static ?string $model = Pengajuan::class;
    protected static ?string $modelLabel = 'Pengajuan';
    protected static ?string $pluralLabel = 'pengajuan';
    protected static ?string $navigationLabel = 'Pengajuan';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Form $form): Form
    {
        //** @var User $user */
        $iduser = auth('web')->id();
        $username = auth('web')->user()->name;
        $user = auth('web')->user();

        return $form->schema([
            // Nama mahasiswa otomatis dari user login
            Hidden::make('user_id')
                ->default($iduser),

            TextInput::make('nama_mahasiswa')
                ->label('Nama Mahasiswa')
                ->default($username)
                ->disabled(),

            TextInput::make('nim')
                ->label('NIM Mahasiswa')
                ->disabled(),

            // TextInput::make('prodi')
            //     ->label('Program Studi')
            //     ->default($prodi)
            //     ->disabled(),

            Select::make('prodi')
                ->label('Jenis Naskah')
                ->options([
                    'perbankan syariah' => 'Perbankan Syariah',
                    'ekonomi syariah' => 'Ekonomi Syariah',
                    'akuntansi syariah' => 'Akuntansi Syariah',
                    'informatika' => 'Informatika',
                    'bisnis digital' => 'Bisnis Digital',
                    'sains data' => 'Sains Data',
                ])
                // ->disabled()
                ->required(),

            TextInput::make('no_hp')
                ->label('Nomor HP/WA')
                ->required()
                ->tel(),

            TextInput::make('judul_skripsi')
                ->required()
                ->maxLength(255),

            Select::make('jenis_naskah')
                ->label('Jenis Naskah')
                ->options([
                    'proposal' => 'Proposal',
                    'skripsi' => 'Skripsi',
                ])
                ->default('skripsi')
                ->required(),

            Select::make('pembimbing_id')
                ->relationship('pembimbing', 'nama')
                ->searchable()
                ->preload()
                ->required(),

            TextInput::make('file_skripsi')
                ->label('URL File Proposal/Skripsi di Google Drive')
                ->required(),

            Checkbox::make('pernyataan_keaslian')
                ->label('Naskah proposal/skripsi yang diunggah adalah naskah yang akan diajukan untuk seminar proposal dan atau ujian skripsi yang sesuai dengan dokumen aslinya')
                ->required(),

            // Hanya untuk Admin
            FileUpload::make('laporan_turnitin')
                ->disk('public')
                ->directory('laporan')
                ->nullable()
                ->visible(fn() => $user->role === 'admin'),

            Textarea::make('catatan_admin')
                ->rows(3)
                ->nullable()
                ->visible(fn() => $user->role === 'admin'),
        ]);
    }

    public static function table(Table $table): Table
    {
        //** @var User $user */
        $user = auth('web')->user();

        return $table
            ->modifyQueryUsing(function ($query) {
                //** @var User $user */
                $user = auth('web')->user();
                if ($user->role === 'mahasiswa') {
                    $query->where('user_id', $user->id);
                }
            })
            ->columns([
                // TextColumn::make('id')->sortable(),
                TextColumn::make('user.custom_fields.nim')
                    ->label('nim')
                    ->formatStateUsing(fn($state, $record) => $record->user->custom_fields['nim'] ?? '-')
                    ->searchable(),
                TextColumn::make('user.name')->label('Mahasiswa')->searchable(),
                TextColumn::make('prodi')->label('Program Studi')->sortable(),
                TextColumn::make('pembimbing.nama')->label('Dosen Pembimbing'),
                TextColumn::make('judul_skripsi')->limit(50)->wrap()->searchable(),
                //TextColumn::make('file_skripsi')->label('Link File')->sortable(),
                TextColumn::make('file_skripsi')
                    ->label('Link File')
                    ->formatStateUsing(fn() => 'Lihat File') // Ganti teks yang ditampilkan
                    ->url(fn($record) => $record->file_skripsi)
                    ->openUrlInNewTab()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status Pengajuan')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'info'    => 'diproses',
                        'success' => 'selesai',
                        'danger'  => 'ditolak',
                    ])
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'diproses' => 'Diproses',
                        'selesai' => 'Selesai',
                        'ditolak' => 'Ditolak',
                        default => '-',
                    })
                    ->sortable(),
                TextColumn::make('similarity_score')->label('Similarity (%)')->sortable(),
                TextColumn::make('created_at')->label('Tgl pengajuan')->dateTime('d-M-Y H:i'),
                TextColumn::make('updated_at')->label('Tgl selesai')->dateTime('d-M-Y H:i'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'pending' => 'Pending',
                    'diproses' => 'Diproses',
                    'selesai' => 'Selesai',
                    'ditolak' => 'Ditolak',
                ]),
            ])
            ->actions([
                // Aksi untuk mahasiswa
                Tables\Actions\ViewAction::make()
                    ->visible(fn() => $user->role === 'mahasiswa'),
                Tables\Actions\EditAction::make()
                    ->visible(function ($record) {
                        //** @var User $user */
                        $user = auth('web')->user();
                        return $user->role === 'mahasiswa' && $record->status === 'pending';
                    }),


                // Aksi Proses untuk admin
                Action::make('proses')
                    ->label('Proses')
                    ->icon('heroicon-o-cog')
                    ->visible(fn() => $user->role === 'admin')
                    ->form([
                        Forms\Components\TextInput::make('similarity_score')
                            ->label('Similarity (%)')
                            ->numeric()
                            ->required(),

                        Forms\Components\TextInput::make('surat_keterangan')
                            ->label('Link Surat Keterangan')
                            ->required(),

                        Forms\Components\TextInput::make('hasil_turnitin')
                            ->label('Link Hasil Cek Turnitin')
                            ->required(),

                        Forms\Components\Textarea::make('catatan_admin')
                            ->label('Catatan')
                            ->rows(3),

                        Forms\Components\Select::make('status')
                            ->label('Status Pengajuan')
                            ->options([
                                'pending'  => 'Pending',
                                'diproses'  => 'Diproses',
                                'selesai'  => 'Selesai',
                                'ditolak'  => 'Ditolak',
                            ])
                            ->required(),
                    ])
                    // Di sini kita isi nilai awal form dari record
                    ->fillForm(function ($record) {
                        return [
                            'similarity_score' => $record->similarity_score,
                            'surat_keterangan' => $record->surat_keterangan,
                            'hasil_turnitin'   => $record->hasil_turnitin,
                            'catatan_admin'    => $record->catatan_admin,
                            'status'           => $record->status,
                        ];
                    })
                    ->action(function ($record, array $data) {
                        $record->update([
                            'similarity_score' => $data['similarity_score'],
                            'surat_keterangan' => $data['surat_keterangan'],
                            'hasil_turnitin'   => $data['hasil_turnitin'],
                            'catatan_admin'    => $data['catatan_admin'],
                            'status'           => $data['status'],
                        ]);
                    })
                    ->modalHeading('Proses Pengajuan Turnitin')
                    ->modalSubmitActionLabel('Simpan'),

                // Aksi Lihat Hasil hanya mahasiswa & status selesai
                Action::make('lihat_hasil')
                    ->label('Lihat Hasil')
                    ->icon('heroicon-o-eye')
                    ->visible(fn($record) => $user->role === 'mahasiswa' && $record->status === 'selesai')
                    ->modalHeading('Hasil Pengecekan Turnitin')
                    ->modalDescription('Berikut adalah hasil pengecekan Turnitin untuk pengajuan ini.')
                    ->modalContent(function ($record) {
                        return new \Illuminate\Support\HtmlString("
                            <div class='space-y-2'>
                                <p><strong>Link Surat Keterangan:</strong> <a href='{$record->surat_keterangan}' class='text-primary-600 underline' target='_blank'>Buka</a></p>
                                <p><strong>Link Hasil Cek Turnitin:</strong> <a href='{$record->hasil_turnitin}' class='text-primary-600 underline' target='_blank'>Buka</a></p>
                                <p><strong>Catatan:</strong><br>{$record->catatan_admin}</p>
                            </div>
                        ");
                    })
                    ->modalSubmitAction(false), // Tidak perlu tombol submit
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Mahasiswa')
                    ->schema([
                        TextEntry::make('user.custom_fields.nim')
                            ->label('NIM')
                            ->formatStateUsing(fn($state, $record) => $record->user->custom_fields['nim'] ?? '-'),
                        TextEntry::make('user.name')->label('Nama'),
                        TextEntry::make('prodi')->label('Program Studi'),
                        TextEntry::make('no_hp')->label('No. HP / WA'),
                    ])->columns(4),

                Section::make('Detail Naskah')
                    ->schema([
                        TextEntry::make('judul_skripsi')->label('Judul Skripsi / Proposal'),
                        TextEntry::make('jenis_naskah')->label('Jenis Naskah'),
                        TextEntry::make('pembimbing.nama')->label('Pembimbing'),
                        TextEntry::make('status')->label('Status')
                            ->label('Status Pengajuan')
                            ->badge()
                            ->colors([
                                'warning' => 'pending',
                                'info'    => 'diproses',
                                'success' => 'selesai',
                                'danger'  => 'ditolak',
                            ])
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                'pending' => 'Pending',
                                'diproses' => 'Diproses',
                                'selesai' => 'Selesai',
                                'ditolak' => 'Ditolak',
                                default   => '-',
                            }),
                        TextEntry::make('file_skripsi')->label('Link File'),
                    ])->columns(2),

                Section::make('Hasil Pemeriksaan')
                    ->schema([
                        TextEntry::make('similarity_score')->label('Similarity (%)'),
                        TextEntry::make('surat_keterangan')
                            ->label('Link Surat Keterangan')
                            ->url(fn($record) => $record->surat_keterangan, true),
                        TextEntry::make('hasil_turnitin')
                            ->label('Link Hasil Cek Turnitin')
                            ->url(fn($record) => $record->hasil_turnitin, true),
                        TextEntry::make('catatan_admin')->label('Catatan Admin'),
                    ])->columns(2),
            ]);
    }



    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPengajuans::route('/'),
            'create' => Pages\CreatePengajuan::route('/create'),
            'view' => Pages\ViewPengajuan::route('/{record}'),
            'edit' => Pages\EditPengajuan::route('/{record}/edit'),
        ];
    }
}

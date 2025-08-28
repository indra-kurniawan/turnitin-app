<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $modelLabel = 'User';
    protected static ?string $pluralLabel = 'User';
    protected static ?string $navigationLabel = 'User';
    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function canAccess(): bool
    {
        //** @var User $user */
        $user = auth('web')->user();
        return $user->role === 'admin';
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required()->maxLength(100),
                TextInput::make('nim')->maxLength(100),

                Select::make('prodi')
                    ->options([
                        'perbankan syariah' => 'Perbankan Syariah',
                        'ekonomi syariah' => 'Ekonomi Syariah',
                        'akuntansi syariah' => 'Akuntansi Syariah',
                        'informatika' => 'Informatika',
                        'bisnis digital' => 'Bisnis Digital',
                        'sains data' => 'Sains Data',
                    ])->required(),

                TextInput::make('email')->email()->required()->unique(User::class, 'email', ignoreRecord: true),
                TextInput::make('password')

                    ->password()
                    ->dehydrated(fn($state) => filled($state))
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->label('Password')
                    ->helperText('Kosongkan jika tidak ingin mengubah password pada edit.'),
                Select::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'mahasiswa' => 'Mahasiswa',
                    ])->required(),
                // TextInput::make('nim')->maxLength(20)->nullable()
                //     ->visible(fn($get) => $get('role') === 'mahasiswa'),
                // TextInput::make('prodi')->maxLength(100)->nullable()
                //     ->visible(fn($get) => $get('role') === 'mahasiswa'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('role')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'admin' => 'Admin',
                        'mahasiswa' => 'Mahasiswa',
                    })
                    ->sortable(),

                TextColumn::make('nim')->label('NIM')->toggleable(),
                TextColumn::make('prodi')
                    ->label('Prodi')
                    ->formatStateUsing(fn($state) => match (strtolower(trim($state))) {
                        'perbankan syariah' => 'Perbankan Syariah',
                        'ekonomi syariah' => 'Ekonomi Syariah',
                        'akuntansi syariah' => 'Akuntansi Syariah',
                        'informatika' => 'Informatika',
                        'bisnis digital' => 'Bisnis Digital',
                        'sains data' => 'Sains Data',
                        default => $state ?? '-', // biar aman kalau null atau tidak cocok
                    })
                    ->sortable(),
                TextColumn::make('created_at')->dateTime()->label('Dibuat'),
            ])->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

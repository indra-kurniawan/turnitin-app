<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Pengajuan;
use Illuminate\Support\Facades\Auth;

class StatsPengajuan extends BaseWidget
{
    protected function getStats(): array
    {
        $userId = Auth::id();
        $role = Auth::user()->role;

        if ($role == 'admin') {
            return [
                Card::make('Total Diajukan', Pengajuan::count())
                    ->icon('heroicon-o-clipboard-document-list')
                    ->color('primary'),

                Card::make('Sedang Diproses', Pengajuan::where('status', 'diproses')->count())
                    ->icon('heroicon-o-arrow-path')
                    ->color('info'),

                Card::make('Pending', Pengajuan::where('status', 'pending')->count())
                    ->icon('heroicon-o-clock')
                    ->color('warning'),

                Card::make('Selesai', Pengajuan::where('status', 'selesai')->count())
                    ->icon('heroicon-o-check-circle')
                    ->color('success'),

                Card::make('Ditolak', Pengajuan::where('status', 'ditolak')->count())
                    ->icon('heroicon-o-x-circle')
                    ->color('danger'),
            ];
        }

        return [
            Card::make('Total Pengajuan', Pengajuan::where('user_id', $userId)->count())
                ->icon('heroicon-o-clipboard-document-list')
                ->color('primary'),
            Card::make('Sedang Diproses', Pengajuan::where('user_id', $userId)->where('status', 'diproses')->count())
                ->icon('heroicon-o-arrow-path')
                ->color('info'),
            Card::make('Pending', Pengajuan::where('user_id', $userId)->where('status', 'pending')->count())
                ->icon('heroicon-o-clock')
                ->color('warning'),
            Card::make('Selesai', Pengajuan::where('user_id', $userId)->where('status', 'selesai')->count())
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Card::make('Ditolak', Pengajuan::where('user_id', $userId)->where('status', 'ditolak')->count())
                ->icon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}

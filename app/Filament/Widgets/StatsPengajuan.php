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
                Card::make('Total Diajukan', Pengajuan::count()),
                Card::make('Sedang Diproses', Pengajuan::count()),
                Card::make('Selesai', Pengajuan::count()),
                Card::make('Ditolak', Pengajuan::count()),
            ];
        }

        return [
            Card::make('Total Pengajuan', Pengajuan::where('user_id', $userId)->count()),
            Card::make('Sedang Diproses', Pengajuan::where('user_id', $userId)->where('status', 'diproses')->count()),
            Card::make('Selesai', Pengajuan::where('user_id', $userId)->where('status', 'selesai')->count()),
            Card::make('Ditolak', Pengajuan::where('user_id', $userId)->where('status', 'ditolak')->count()),
        ];
    }
}
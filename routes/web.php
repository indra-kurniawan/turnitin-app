<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterMahasiswaController;
use App\Models\Pengajuan;
use Illuminate\Support\Facades\Redirect;

Route::get('/', function () {
    return Redirect('/siplagi');
})->name('login');

Route::get('/export/pengajuan', function () {
    return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PengajuanExport, 'pengajuan.xlsx');
})->name('pengajuan.export');

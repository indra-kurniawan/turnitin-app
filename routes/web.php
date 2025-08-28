<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterMahasiswaController;
use Illuminate\Support\Facades\Redirect;

Route::get('/', function () {
    return Redirect('/siplagi');
})->name('login');

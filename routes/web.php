<?php

use App\Http\Controllers\BmiController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BmiController::class, 'form'])->name('bmi.form');
Route::post('/hitung', [BmiController::class, 'hitung'])->name('bmi.hitung');
Route::get('/hasil', [BmiController::class, 'hasil'])->name('bmi.hasil');

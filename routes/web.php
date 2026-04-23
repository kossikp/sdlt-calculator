<?php

use App\Http\Controllers\SdltCalculatorController;
use Illuminate\Support\Facades\Route;

Route::get('/', [SdltCalculatorController::class, 'index'])->name('sdlt.index');
Route::post('/calculate', [SdltCalculatorController::class, 'calculate'])->name('sdlt.calculate');

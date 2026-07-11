<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PjokRecordController;
use App\Support\PjokMasterData;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check() ? redirect('/dashboard') : redirect('/login'))->name('home');
Route::get('/dashboard', fn () => view('app', ['initialData' => PjokMasterData::load()]))->middleware('auth')->name('dashboard');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/records', [PjokRecordController::class, 'index'])->name('records.index');
    Route::post('/records', [PjokRecordController::class, 'store'])->name('records.store');
});


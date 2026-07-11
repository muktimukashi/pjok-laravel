<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PjokRecordController;
use App\Http\Controllers\StudentImportController;
use App\Http\Controllers\UserManagementController;
use App\Support\PjokMasterData;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check() ? redirect('/dashboard') : redirect('/login'))->name('home');
Route::get('/dashboard', fn () => view('app', ['initialData' => array_merge(PjokMasterData::load(), ['userRecords' => UserManagementController::userRecords()])]))->middleware('auth')->name('dashboard');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/records', [PjokRecordController::class, 'index'])->name('records.index');
    Route::post('/records', [PjokRecordController::class, 'store'])->name('records.store');
    Route::post('/records/sync', [PjokRecordController::class, 'sync'])->name('records.sync');
    Route::post('/students/import-csv', [StudentImportController::class, 'store'])->name('students.importCsv');
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
});


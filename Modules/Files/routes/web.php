<?php

use Illuminate\Support\Facades\Route;
use Modules\Files\Http\Controllers\FileController;
use Modules\Files\Livewire\FileManager;

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::middleware('permission:view,files')->group(function () {
        Route::livewire('files', FileManager::class)->name('files.index');
        Route::get('files/dashboard', fn () => redirect()->route('files.index'))->name('files.dashboard');
        Route::get('files/{id}/download', [FileController::class, 'download'])
            ->middleware('file.access')
            ->name('files.download');
        Route::get('files/{id}/preview', [FileController::class, 'preview'])
            ->middleware('file.access')
            ->name('files.preview');
    });

    Route::middleware('permission:edit,files')->group(function () {
        Route::patch('files/{id}/rename', [FileController::class, 'rename'])
            ->middleware('file.access')
            ->name('files.rename');
    });

    Route::middleware('permission:delete,files')->group(function () {
        Route::delete('files/{id}', [FileController::class, 'destroy'])
            ->middleware('file.access')
            ->name('files.destroy');
    });
});

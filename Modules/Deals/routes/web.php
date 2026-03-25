<?php

use Illuminate\Support\Facades\Route;
use Modules\Deals\Http\Controllers\DealsController;
use Modules\Deals\Livewire\DealDetail;
use Modules\Deals\Livewire\DealForm;
use Modules\Deals\Livewire\DealKanban;
use Modules\Deals\Livewire\DealList;
use Modules\Deals\Livewire\PipelineManager;

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::middleware('permission:view,deals')->group(function () {
        Route::livewire('deals', DealKanban::class)->name('deals.index');
        Route::get('deals/dashboard', fn () => redirect()->route('deals.index'))->name('deals.dashboard');
        Route::livewire('deals/list', DealList::class)->name('deals.list');
        Route::livewire('deals/{id}', DealDetail::class)->whereUuid('id')->name('deals.show');
        Route::livewire('pipelines', PipelineManager::class)->name('pipelines.index');
    });

    Route::middleware('permission:create,deals')->group(function () {
        Route::livewire('deals/create', DealForm::class)->name('deals.create');
    });

    Route::middleware('permission:edit,deals')->group(function () {
        Route::livewire('deals/{id}/edit', DealForm::class)->whereUuid('id')->name('deals.edit');
        Route::patch('deals/{id}/stage', [DealsController::class, 'stage'])->whereUuid('id')->name('deals.stage');
        Route::patch('deals/{id}/won', [DealsController::class, 'won'])->whereUuid('id')->name('deals.won');
        Route::patch('deals/{id}/lost', [DealsController::class, 'lost'])->whereUuid('id')->name('deals.lost');
    });

    Route::middleware('permission:delete,deals')->group(function () {
        Route::delete('deals/{id}', [DealsController::class, 'destroy'])->whereUuid('id')->name('deals.destroy');
    });
});

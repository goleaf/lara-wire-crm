<?php

use Illuminate\Support\Facades\Route;
use Modules\Cases\Http\Controllers\CasesController;
use Modules\Cases\Livewire\CaseDetail;
use Modules\Cases\Livewire\CaseForm;
use Modules\Cases\Livewire\CaseIndex;
use Modules\Cases\Livewire\SlaManager;
use Modules\Cases\Livewire\SupportDashboard;

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::middleware('permission:view,cases')->group(function () {
        Route::livewire('cases', CaseIndex::class)->name('cases.index');
        Route::livewire('cases/dashboard', SupportDashboard::class)->name('cases.dashboard');
        Route::livewire('cases/sla', SlaManager::class)->name('cases.sla');
        Route::livewire('cases/{id}', CaseDetail::class)->whereUuid('id')->name('cases.show');
    });

    Route::middleware('permission:create,cases')->group(function () {
        Route::livewire('cases/create', CaseForm::class)->name('cases.create');
        Route::post('cases/{id}/comments', [CasesController::class, 'addComment'])->whereUuid('id')->name('cases.comments.store');
    });

    Route::middleware('permission:edit,cases')->group(function () {
        Route::livewire('cases/{id}/edit', CaseForm::class)->whereUuid('id')->name('cases.edit');
        Route::patch('cases/{id}/status', [CasesController::class, 'updateStatus'])->whereUuid('id')->name('cases.status');
        Route::patch('cases/{id}/satisfaction', [CasesController::class, 'updateSatisfaction'])->whereUuid('id')->name('cases.satisfaction');
    });

    Route::middleware('permission:delete,cases')->group(function () {
        Route::delete('cases/{id}', [CasesController::class, 'destroy'])->whereUuid('id')->name('cases.destroy');
    });
});

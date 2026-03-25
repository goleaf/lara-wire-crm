<?php

use Illuminate\Support\Facades\Route;
use Modules\Leads\Http\Controllers\LeadsController;
use Modules\Leads\Livewire\LeadDetail;
use Modules\Leads\Livewire\LeadForm;
use Modules\Leads\Livewire\LeadIndex;
use Modules\Leads\Livewire\LeadKanban;

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::middleware('permission:view,leads')->group(function () {
        Route::livewire('leads', LeadIndex::class)->name('leads.index');
        Route::get('leads/dashboard', fn () => redirect()->route('leads.index'))->name('leads.dashboard');
        Route::livewire('leads/kanban', LeadKanban::class)->name('leads.kanban');
        Route::livewire('leads/{id}', LeadDetail::class)->whereUuid('id')->name('leads.show');
    });

    Route::middleware('permission:create,leads')->group(function () {
        Route::livewire('leads/create', LeadForm::class)->name('leads.create');
    });

    Route::middleware('permission:edit,leads')->group(function () {
        Route::livewire('leads/{id}/edit', LeadForm::class)->whereUuid('id')->name('leads.edit');
        Route::post('leads/{id}/convert', [LeadsController::class, 'convert'])->whereUuid('id')->name('leads.convert');
    });

    Route::middleware('permission:delete,leads')->group(function () {
        Route::delete('leads/{id}', [LeadsController::class, 'destroy'])->whereUuid('id')->name('leads.destroy');
    });
});

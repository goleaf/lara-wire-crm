<?php

use Illuminate\Support\Facades\Route;
use Modules\Quotes\Http\Controllers\QuotesController;
use Modules\Quotes\Livewire\QuoteDetail;
use Modules\Quotes\Livewire\QuoteForm;
use Modules\Quotes\Livewire\QuoteIndex;
use Modules\Quotes\Livewire\QuotePdfPreview;

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::middleware('permission:view,quotes')->group(function () {
        Route::livewire('quotes', QuoteIndex::class)->name('quotes.index');
        Route::get('quotes/dashboard', fn () => redirect()->route('quotes.index'))->name('quotes.dashboard');
        Route::livewire('quotes/{id}', QuoteDetail::class)->whereUuid('id')->name('quotes.show');
        Route::livewire('quotes/{id}/preview', QuotePdfPreview::class)->whereUuid('id')->name('quotes.preview');
        Route::get('quotes/{id}/pdf', [QuotesController::class, 'downloadPdf'])->whereUuid('id')->name('quotes.pdf');
    });

    Route::middleware('permission:create,quotes')->group(function () {
        Route::livewire('quotes/create', QuoteForm::class)->name('quotes.create');
        Route::post('quotes/{id}/duplicate', [QuotesController::class, 'duplicate'])->whereUuid('id')->name('quotes.duplicate');
        Route::post('quotes/{id}/convert', [QuotesController::class, 'convertToInvoice'])->whereUuid('id')->name('quotes.convert');
    });

    Route::middleware('permission:edit,quotes')->group(function () {
        Route::livewire('quotes/{id}/edit', QuoteForm::class)->whereUuid('id')->name('quotes.edit');
        Route::patch('quotes/{id}/status', [QuotesController::class, 'updateStatus'])->whereUuid('id')->name('quotes.status');
    });

    Route::middleware('permission:delete,quotes')->group(function () {
        Route::delete('quotes/{id}', [QuotesController::class, 'destroy'])->whereUuid('id')->name('quotes.destroy');
    });
});

<?php

use Illuminate\Support\Facades\Route;
use Modules\Invoices\Http\Controllers\InvoicesController;
use Modules\Invoices\Livewire\InvoiceAgingReport;
use Modules\Invoices\Livewire\InvoiceDetail;
use Modules\Invoices\Livewire\InvoiceForm;
use Modules\Invoices\Livewire\InvoiceIndex;

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::middleware('permission:view,invoices')->group(function () {
        Route::livewire('invoices', InvoiceIndex::class)->name('invoices.index');
        Route::get('invoices/dashboard', fn () => redirect()->route('invoices.index'))->name('invoices.dashboard');
        Route::livewire('invoices/aging', InvoiceAgingReport::class)->name('invoices.aging');
        Route::livewire('invoices/{id}', InvoiceDetail::class)->whereUuid('id')->name('invoices.show');
        Route::get('invoices/{id}/pdf', [InvoicesController::class, 'downloadPdf'])->whereUuid('id')->name('invoices.pdf');
    });

    Route::middleware('permission:create,invoices')->group(function () {
        Route::livewire('invoices/create', InvoiceForm::class)->name('invoices.create');
    });

    Route::middleware('permission:edit,invoices')->group(function () {
        Route::livewire('invoices/{id}/edit', InvoiceForm::class)->whereUuid('id')->name('invoices.edit');
        Route::post('invoices/{id}/payment', [InvoicesController::class, 'recordPayment'])->whereUuid('id')->name('invoices.payment');
        Route::patch('invoices/{id}/cancel', [InvoicesController::class, 'cancel'])->whereUuid('id')->name('invoices.cancel');
    });

    Route::middleware('permission:delete,invoices')->group(function () {
        Route::delete('invoices/{id}', [InvoicesController::class, 'destroy'])->whereUuid('id')->name('invoices.destroy');
    });
});

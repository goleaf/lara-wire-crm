<?php

use Illuminate\Support\Facades\Route;
use Modules\Contacts\Http\Controllers\ContactsController;
use Modules\Contacts\Livewire\AccountDetail;
use Modules\Contacts\Livewire\AccountForm;
use Modules\Contacts\Livewire\AccountIndex;
use Modules\Contacts\Livewire\ContactDetail;
use Modules\Contacts\Livewire\ContactForm;
use Modules\Contacts\Livewire\ContactIndex;

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::middleware('permission:view,contacts')->group(function () {
        Route::livewire('accounts', AccountIndex::class)->name('accounts.index');
        Route::livewire('accounts/{id}', AccountDetail::class)->whereUuid('id')->name('accounts.show');
        Route::livewire('contacts', ContactIndex::class)->name('contacts.index');
        Route::get('contacts/dashboard', fn () => redirect()->route('contacts.index'))->name('contacts.dashboard');
        Route::livewire('contacts/{id}', ContactDetail::class)->whereUuid('id')->name('contacts.show');
    });

    Route::middleware('permission:create,contacts')->group(function () {
        Route::livewire('accounts/create', AccountForm::class)->name('accounts.create');
        Route::livewire('contacts/create', ContactForm::class)->name('contacts.create');
    });

    Route::middleware('permission:edit,contacts')->group(function () {
        Route::livewire('accounts/{id}/edit', AccountForm::class)->whereUuid('id')->name('accounts.edit');
        Route::livewire('contacts/{id}/edit', ContactForm::class)->whereUuid('id')->name('contacts.edit');
    });

    Route::middleware('permission:delete,contacts')->group(function () {
        Route::delete('accounts/{id}', [ContactsController::class, 'destroyAccount'])->whereUuid('id')->name('accounts.destroy');
        Route::delete('contacts/{id}', [ContactsController::class, 'destroyContact'])->whereUuid('id')->name('contacts.destroy');
    });
});

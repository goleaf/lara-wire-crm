<?php

use Illuminate\Support\Facades\Route;
use Modules\Products\Http\Controllers\ProductsController;
use Modules\Products\Livewire\CategoryManager;
use Modules\Products\Livewire\ProductDetail;
use Modules\Products\Livewire\ProductForm;
use Modules\Products\Livewire\ProductIndex;
use Modules\Products\Livewire\ProductPricebook;

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::middleware('permission:view,products')->group(function () {
        Route::livewire('products', ProductIndex::class)->name('products.index');
        Route::get('products/dashboard', fn () => redirect()->route('products.index'))->name('products.dashboard');
        Route::livewire('products/categories', CategoryManager::class)->name('products.categories');
        Route::livewire('products/pricebook', ProductPricebook::class)->name('products.pricebook');
        Route::livewire('products/{id}', ProductDetail::class)->whereUuid('id')->name('products.show');
    });

    Route::middleware('permission:create,products')->group(function () {
        Route::livewire('products/create', ProductForm::class)->name('products.create');
    });

    Route::middleware('permission:edit,products')->group(function () {
        Route::livewire('products/{id}/edit', ProductForm::class)->whereUuid('id')->name('products.edit');
    });

    Route::middleware('permission:delete,products')->group(function () {
        Route::delete('products/{id}', [ProductsController::class, 'destroy'])->whereUuid('id')->name('products.destroy');
    });
});

<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Livewire\Dashboard;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', Dashboard::class)->name('dashboard');
});

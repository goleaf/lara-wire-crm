<?php

use Illuminate\Support\Facades\Route;
use Modules\Messaging\Livewire\MessagingLayout;

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::middleware('permission:view,messaging')->group(function () {
        Route::livewire('messages', MessagingLayout::class)->name('messages.index');
        Route::livewire('messages/{channelId}', MessagingLayout::class)->whereUuid('channelId')->name('messages.show');
    });
});

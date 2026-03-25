<?php

use Illuminate\Support\Facades\Route;
use Modules\Notifications\Http\Controllers\NotificationsController;
use Modules\Notifications\Livewire\NotificationCenter;
use Modules\Notifications\Livewire\NotificationPreferences;

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::middleware('permission:view,notifications')->group(function () {
        Route::livewire('notifications', NotificationCenter::class)->name('notifications.index');
        Route::get('notifications/dashboard', fn () => redirect()->route('notifications.index'))->name('notifications.dashboard');
        Route::livewire('notifications/preferences', NotificationPreferences::class)->name('notifications.preferences');
    });

    Route::middleware('permission:edit,notifications')->group(function () {
        Route::patch('notifications/{id}/read', [NotificationsController::class, 'markRead'])->whereUuid('id')->name('notifications.read');
        Route::patch('notifications/read-all', [NotificationsController::class, 'markAllRead'])->name('notifications.read-all');
    });

    Route::middleware('permission:delete,notifications')->group(function () {
        Route::delete('notifications/{id}', [NotificationsController::class, 'destroy'])->whereUuid('id')->name('notifications.destroy');
    });
});

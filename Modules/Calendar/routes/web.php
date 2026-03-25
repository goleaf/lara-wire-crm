<?php

use Illuminate\Support\Facades\Route;
use Modules\Calendar\Livewire\CalendarView;

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::middleware('permission:view,calendar')->group(function () {
        Route::livewire('calendar', CalendarView::class)->name('calendar.index');
        Route::livewire('calendar/week', CalendarView::class)->defaults('view', 'week')->name('calendar.week');
        Route::livewire('calendar/day/{date?}', CalendarView::class)->defaults('view', 'day')->name('calendar.day');
    });
});

<?php

use Illuminate\Support\Facades\Route;
use Modules\Activities\Livewire\ActivityDetail;
use Modules\Activities\Livewire\ActivityFeed;
use Modules\Activities\Livewire\ActivityForm;
use Modules\Activities\Livewire\MyActivities;

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::middleware('permission:view,activities')->group(function () {
        Route::livewire('activities', ActivityFeed::class)->name('activities.index');
        Route::livewire('activities/mine', MyActivities::class)->name('activities.mine');
        Route::livewire('activities/{id}', ActivityDetail::class)->whereUuid('id')->name('activities.show');
    });

    Route::middleware('permission:create,activities')->group(function () {
        Route::livewire('activities/create', ActivityForm::class)->name('activities.create');
    });

    Route::middleware('permission:edit,activities')->group(function () {
        Route::livewire('activities/{id}/edit', ActivityForm::class)->whereUuid('id')->name('activities.edit');
    });
});

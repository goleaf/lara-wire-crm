<?php

use Illuminate\Support\Facades\Route;
use Modules\Activities\Http\Controllers\ActivitiesController;
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
        Route::patch('activities/{id}/complete', [ActivitiesController::class, 'complete'])->whereUuid('id')->name('activities.complete');
        Route::patch('activities/{id}/cancel', [ActivitiesController::class, 'cancel'])->whereUuid('id')->name('activities.cancel');
    });

    Route::middleware('permission:delete,activities')->group(function () {
        Route::delete('activities/{id}', [ActivitiesController::class, 'destroy'])->whereUuid('id')->name('activities.destroy');
    });
});

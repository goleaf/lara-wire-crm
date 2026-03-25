<?php

use Illuminate\Support\Facades\Route;
use Modules\Campaigns\Http\Controllers\CampaignsController;
use Modules\Campaigns\Livewire\CampaignDetail;
use Modules\Campaigns\Livewire\CampaignForm;
use Modules\Campaigns\Livewire\CampaignIndex;

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::middleware('permission:view,campaigns')->group(function () {
        Route::livewire('campaigns', CampaignIndex::class)->name('campaigns.index');
        Route::get('campaigns/dashboard', fn () => redirect()->route('campaigns.index'))->name('campaigns.dashboard');
        Route::livewire('campaigns/{id}', CampaignDetail::class)->whereUuid('id')->name('campaigns.show');
    });

    Route::middleware('permission:create,campaigns')->group(function () {
        Route::livewire('campaigns/create', CampaignForm::class)->name('campaigns.create');
        Route::post('campaigns/{id}/contacts', [CampaignsController::class, 'addContacts'])->whereUuid('id')->name('campaigns.contacts.store');
    });

    Route::middleware('permission:edit,campaigns')->group(function () {
        Route::livewire('campaigns/{id}/edit', CampaignForm::class)->whereUuid('id')->name('campaigns.edit');
    });

    Route::middleware('permission:delete,campaigns')->group(function () {
        Route::delete('campaigns/{id}/contacts/{contactId}', [CampaignsController::class, 'removeContact'])->whereUuid('id')->whereUuid('contactId')->name('campaigns.contacts.destroy');
        Route::delete('campaigns/{id}', [CampaignsController::class, 'destroy'])->whereUuid('id')->name('campaigns.destroy');
    });
});

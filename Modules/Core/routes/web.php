<?php

use Illuminate\Support\Facades\Route;
use Modules\Core\Livewire\AuditLogIndex;
use Modules\Core\Livewire\Dashboard;
use Modules\Core\Livewire\Settings;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware('active')->group(function () {
        Route::livewire('core/dashboard', Dashboard::class)->name('core.dashboard');
        Route::livewire('crm/settings', Settings::class)->name('core.settings');
        Route::livewire('audit-logs', AuditLogIndex::class)->name('core.audit-logs');
    });
});

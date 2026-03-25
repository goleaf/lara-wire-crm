<?php

use Illuminate\Support\Facades\Route;
use Modules\Reports\Http\Controllers\ReportsController;
use Modules\Reports\Livewire\Dashboard;
use Modules\Reports\Livewire\DashboardEditor;
use Modules\Reports\Livewire\DashboardIndex;
use Modules\Reports\Livewire\ReportBuilder;
use Modules\Reports\Livewire\ReportIndex;
use Modules\Reports\Livewire\ReportView;

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::livewire('dashboard', Dashboard::class)->name('dashboard');

    Route::middleware('permission:view,reports')->group(function () {
        Route::livewire('reports', ReportIndex::class)->name('reports.index');
        Route::livewire('reports/{id}', ReportView::class)->whereUuid('id')->name('reports.show');
        Route::livewire('dashboards', DashboardIndex::class)->name('dashboards.index');
    });

    Route::middleware('permission:create,reports')->group(function () {
        Route::livewire('reports/create', ReportBuilder::class)->name('reports.create');
    });

    Route::middleware('permission:edit,reports')->group(function () {
        Route::livewire('reports/{id}/edit', ReportBuilder::class)->whereUuid('id')->name('reports.edit');
        Route::livewire('dashboards/{id}/edit', DashboardEditor::class)->whereUuid('id')->name('dashboards.edit');
    });

    Route::middleware('permission:export,reports')->group(function () {
        Route::get('reports/{id}/export', [ReportsController::class, 'export'])->whereUuid('id')->name('reports.export');
    });

    Route::middleware('permission:delete,reports')->group(function () {
        Route::delete('reports/{id}', [ReportsController::class, 'destroy'])->whereUuid('id')->name('reports.destroy');
    });
});

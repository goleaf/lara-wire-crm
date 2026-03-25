<?php

use Illuminate\Support\Facades\Route;
use Modules\Reports\Http\Controllers\DashboardsController;
use Modules\Reports\Http\Controllers\ReportsController;
use Modules\Reports\Livewire\Dashboard;
use Modules\Reports\Livewire\ReportBuilder;
use Modules\Reports\Livewire\ReportIndex;
use Modules\Reports\Livewire\ReportView;

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::livewire('dashboard', Dashboard::class)->name('dashboard');

    Route::middleware('permission:view,reports')->group(function () {
        Route::livewire('reports', ReportIndex::class)->name('reports.index');
        Route::livewire('reports/{id}', ReportView::class)->whereUuid('id')->name('reports.show');
    });

    Route::middleware('permission:create,reports')->group(function () {
        Route::livewire('reports/create', ReportBuilder::class)->name('reports.create');
        Route::post('dashboards', [DashboardsController::class, 'store'])->name('dashboards.store');
    });

    Route::middleware('permission:edit,reports')->group(function () {
        Route::livewire('reports/{id}/edit', ReportBuilder::class)->whereUuid('id')->name('reports.edit');
        Route::get('dashboards/{id}/edit', [DashboardsController::class, 'edit'])->whereUuid('id')->name('dashboards.edit');
        Route::patch('dashboards/{id}', [DashboardsController::class, 'update'])->whereUuid('id')->name('dashboards.update');
    });

    Route::middleware('permission:export,reports')->group(function () {
        Route::get('reports/{id}/export', [ReportsController::class, 'export'])->whereUuid('id')->name('reports.export');
    });

    Route::middleware('permission:delete,reports')->group(function () {
        Route::delete('reports/{id}', [ReportsController::class, 'destroy'])->whereUuid('id')->name('reports.destroy');
    });

    Route::middleware('permission:view,reports')->group(function () {
        Route::get('dashboards', [DashboardsController::class, 'index'])->name('dashboards.index');
    });
});

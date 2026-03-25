<?php

use Illuminate\Support\Facades\Route;
use Modules\Users\Livewire\Profile;
use Modules\Users\Livewire\RoleForm;
use Modules\Users\Livewire\RoleIndex;
use Modules\Users\Livewire\TeamForm;
use Modules\Users\Livewire\TeamIndex;
use Modules\Users\Livewire\UserForm;
use Modules\Users\Livewire\UserIndex;

Route::middleware(['auth', 'verified', 'active'])->group(function () {
    Route::livewire('profile', Profile::class)->name('users.profile');

    Route::middleware('permission:view,users')->group(function () {
        Route::livewire('users', UserIndex::class)->name('users.index');
        Route::get('users/dashboard', fn () => redirect()->route('users.index'))->name('users.dashboard');
        Route::livewire('roles', RoleIndex::class)->name('roles.index');
        Route::livewire('teams', TeamIndex::class)->name('teams.index');
    });

    Route::middleware('permission:create,users')->group(function () {
        Route::livewire('users/create', UserForm::class)->name('users.create');
        Route::livewire('roles/create', RoleForm::class)->name('roles.create');
        Route::livewire('teams/create', TeamForm::class)->name('teams.create');
    });

    Route::middleware('permission:edit,users')->group(function () {
        Route::livewire('users/{id}/edit', UserForm::class)->name('users.edit');
        Route::livewire('roles/{id}/edit', RoleForm::class)->name('roles.edit');
        Route::livewire('teams/{id}/edit', TeamForm::class)->name('teams.edit');
    });
});

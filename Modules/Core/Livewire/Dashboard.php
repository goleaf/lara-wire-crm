<?php

namespace Modules\Core\Livewire;

use Livewire\Component;
use Illuminate\Contracts\View\View;

class Dashboard extends Component
{
    public function render(): View
    {
        return view('core::livewire.dashboard')
            ->extends('core::layouts.module', ['title' => 'Dashboard']);
    }
}

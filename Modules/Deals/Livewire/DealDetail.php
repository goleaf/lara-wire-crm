<?php

namespace Modules\Deals\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Modules\Deals\Models\Deal;
use Modules\Deals\Models\PipelineStage;
use Modules\Deals\Services\DealService;

class DealDetail extends Component
{
    public Deal $deal;

    public string $tab = 'overview';

    public bool $showLostModal = false;

    public string $lostReason = 'Other';

    public string $lostNotes = '';

    public function mount(string $id): void
    {
        abort_unless(auth()->user()?->can('deals.view'), 403);

        $this->deal = Deal::query()
            ->with([
                'account:id,name',
                'contact:id,first_name,last_name',
                'owner:id,full_name',
                'pipeline:id,name',
                'stage:id,pipeline_id,name,color,probability,order',
                'products:id,name',
            ])
            ->findOrFail($id);
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    public function moveToStage(string $stageId): void
    {
        abort_unless(auth()->user()?->can('deals.edit'), 403);

        $stage = PipelineStage::query()->findOrFail($stageId);
        app(DealService::class)->moveToStage($this->deal, $stage);

        $this->deal->refresh();
    }

    public function markWon(): void
    {
        abort_unless(auth()->user()?->can('deals.edit'), 403);

        app(DealService::class)->markWon($this->deal);
        $this->deal->refresh();

        session()->flash('status', 'Deal marked as won.');
    }

    public function openLostModal(): void
    {
        abort_unless(auth()->user()?->can('deals.edit'), 403);
        $this->showLostModal = true;
    }

    public function closeLostModal(): void
    {
        $this->showLostModal = false;
    }

    public function markLost(): void
    {
        abort_unless(auth()->user()?->can('deals.edit'), 403);

        $this->validate([
            'lostReason' => ['required', 'in:Price,Competitor,No Budget,No Decision,Other'],
            'lostNotes' => ['nullable', 'string'],
        ]);

        app(DealService::class)->markLost($this->deal, $this->lostReason, $this->lostNotes);
        $this->deal->refresh();
        $this->showLostModal = false;

        session()->flash('status', 'Deal marked as lost.');
    }

    public function render(): View
    {
        return view('deals::livewire.deal-detail', [
            'pipelineStages' => PipelineStage::query()
                ->select(['id', 'pipeline_id', 'name', 'order', 'color'])
                ->where('pipeline_id', $this->deal->pipeline_id)
                ->orderBy('order')
                ->get(),
            'tabs' => ['overview', 'products', 'activities', 'quotes', 'invoices', 'files'],
        ])->extends('core::layouts.module', ['title' => $this->deal->name]);
    }
}

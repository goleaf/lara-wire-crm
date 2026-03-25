<?php

namespace Modules\Deals\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Modules\Deals\Models\Deal;
use Modules\Deals\Models\Pipeline;
use Modules\Deals\Models\PipelineStage;
use Modules\Deals\Services\DealService;

class DealKanban extends Component
{
    public string $pipelineFilter = '';

    public string $ownerFilter = '';

    public string $dateFrom = '';

    public string $dateTo = '';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('deals.view'), 403);

        $defaultPipeline = Pipeline::query()
            ->select(['id'])
            ->where('is_default', true)
            ->first();

        if ($defaultPipeline) {
            $this->pipelineFilter = (string) $defaultPipeline->id;
        }
    }

    public function moveDeal(string $dealId, string $stageId): void
    {
        abort_unless(auth()->user()?->can('deals.edit'), 403);

        $deal = Deal::query()->findOrFail($dealId);
        $stage = PipelineStage::query()->findOrFail($stageId);

        app(DealService::class)->moveToStage($deal, $stage);

        session()->flash('status', 'Deal moved to '.$stage->name.'.');
    }

    public function render(): View
    {
        $pipelines = Pipeline::query()
            ->select(['id', 'name', 'is_default'])
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        $owners = User::query()
            ->select(['id', 'full_name'])
            ->orderBy('full_name')
            ->get();

        $activePipelineId = $this->pipelineFilter !== ''
            ? $this->pipelineFilter
            : (string) $pipelines->first()?->id;

        $stages = PipelineStage::query()
            ->select(['id', 'pipeline_id', 'name', 'order', 'probability', 'color'])
            ->where('pipeline_id', $activePipelineId)
            ->orderBy('order')
            ->get();

        $deals = Deal::query()
            ->select([
                'id',
                'name',
                'account_id',
                'owner_id',
                'stage_id',
                'amount',
                'probability',
                'close_date',
            ])
            ->with([
                'account:id,name',
                'owner:id,full_name,avatar_path',
                'stage:id,name,color',
            ])
            ->when($activePipelineId !== '', fn ($query) => $query->where('pipeline_id', $activePipelineId))
            ->when($this->ownerFilter !== '', fn ($query) => $query->where('owner_id', $this->ownerFilter))
            ->when($this->dateFrom !== '', fn ($query) => $query->whereDate('close_date', '>=', $this->dateFrom))
            ->when($this->dateTo !== '', fn ($query) => $query->whereDate('close_date', '<=', $this->dateTo))
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('stage_id');

        return view('deals::livewire.deal-kanban', [
            'deals' => $deals,
            'owners' => $owners,
            'pipelines' => $pipelines,
            'stages' => $stages,
        ])->extends('core::layouts.module', ['title' => 'Deals Pipeline']);
    }
}

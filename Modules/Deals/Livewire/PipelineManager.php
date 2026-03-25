<?php

namespace Modules\Deals\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Modules\Deals\Models\Pipeline;
use Modules\Deals\Models\PipelineStage;

class PipelineManager extends Component
{
    public string $name = '';

    public string $selectedPipelineId = '';

    public string $newStageName = '';

    public int $newStageProbability = 0;

    public string $newStageColor = '#94a3b8';

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('deals.view'), 403);

        $first = Pipeline::query()->select(['id'])->orderByDesc('is_default')->orderBy('name')->first();
        $this->selectedPipelineId = (string) ($first?->id ?? '');
    }

    public function createPipeline(): void
    {
        abort_unless(auth()->user()?->can('deals.create'), 403);

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $pipeline = Pipeline::query()->create([
            'name' => $validated['name'],
            'is_default' => Pipeline::query()->count() === 0,
            'owner_id' => auth()->id(),
        ]);

        $this->name = '';
        $this->selectedPipelineId = $pipeline->id;
    }

    public function deletePipeline(string $id): void
    {
        abort_unless(auth()->user()?->can('deals.delete'), 403);

        if (Pipeline::query()->count() <= 1) {
            return;
        }

        Pipeline::query()->whereKey($id)->delete();

        $next = Pipeline::query()->select(['id'])->orderByDesc('is_default')->first();
        $this->selectedPipelineId = (string) ($next?->id ?? '');
    }

    public function setDefault(string $id): void
    {
        abort_unless(auth()->user()?->can('deals.edit'), 403);

        Pipeline::query()->update(['is_default' => false]);
        Pipeline::query()->whereKey($id)->update(['is_default' => true]);
    }

    public function createStage(): void
    {
        abort_unless(auth()->user()?->can('deals.edit'), 403);

        if ($this->selectedPipelineId === '') {
            return;
        }

        $validated = $this->validate([
            'newStageName' => ['required', 'string', 'max:255'],
            'newStageProbability' => ['required', 'integer', 'min:0', 'max:100'],
            'newStageColor' => ['required', 'string', 'max:20'],
        ]);

        $order = (int) PipelineStage::query()
            ->where('pipeline_id', $this->selectedPipelineId)
            ->max('order') + 1;

        PipelineStage::query()->create([
            'pipeline_id' => $this->selectedPipelineId,
            'name' => $validated['newStageName'],
            'order' => $order,
            'probability' => $validated['newStageProbability'],
            'color' => $validated['newStageColor'],
        ]);

        $this->newStageName = '';
        $this->newStageProbability = 0;
        $this->newStageColor = '#94a3b8';
    }

    public function updateStageOrder(string $stageId, int $position): void
    {
        abort_unless(auth()->user()?->can('deals.edit'), 403);

        $stage = PipelineStage::query()->findOrFail($stageId);
        $stage->order = $position + 1;
        $stage->save();
    }

    public function deleteStage(string $id): void
    {
        abort_unless(auth()->user()?->can('deals.edit'), 403);

        PipelineStage::query()->whereKey($id)->delete();
    }

    public function render(): View
    {
        $pipelines = Pipeline::query()
            ->select(['id', 'name', 'is_default'])
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        $stages = PipelineStage::query()
            ->select(['id', 'pipeline_id', 'name', 'order', 'probability', 'color'])
            ->where('pipeline_id', $this->selectedPipelineId)
            ->orderBy('order')
            ->get();

        return view('deals::livewire.pipeline-manager', [
            'pipelines' => $pipelines,
            'stages' => $stages,
        ])->extends('core::layouts.module', ['title' => 'Pipeline Manager']);
    }
}

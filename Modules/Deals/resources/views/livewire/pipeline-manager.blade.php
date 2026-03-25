<section class="space-y-6">
    <x-crm.status />

    <article class="crm-card p-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Pipelines</h3>
        <div class="mt-4 flex flex-wrap items-center gap-2">
            <input wire:model.blur="name" type="text" placeholder="New pipeline name" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
            <button wire:click="createPipeline" class="crm-btn crm-btn-primary">Create</button>
        </div>

        <div class="mt-4 grid gap-3 md:grid-cols-2">
            @foreach ($pipelines as $pipeline)
                <article class="rounded-2xl border border-slate-200 p-4 dark:border-slate-700">
                    <div class="flex items-center justify-between gap-2">
                        <button wire:click="$set('selectedPipelineId', '{{ $pipeline->id }}')" class="text-left text-sm font-semibold {{ $selectedPipelineId === $pipeline->id ? 'text-sky-700 dark:text-sky-300' : 'text-slate-800 dark:text-slate-100' }}">
                            {{ $pipeline->name }}
                        </button>
                        <div class="flex items-center gap-2">
                            @if ($pipeline->is_default)
                                <span class="rounded-full bg-emerald-100 px-2 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">Default</span>
                            @else
                                <button wire:click="setDefault('{{ $pipeline->id }}')" class="rounded-lg border border-slate-300 px-2 py-1 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">Set Default</button>
                            @endif
                            <button wire:click="deletePipeline('{{ $pipeline->id }}')" onclick="return confirm('Delete this pipeline?')" class="rounded-lg border border-rose-300 px-2 py-1 text-xs font-medium text-rose-700 dark:border-rose-500/40 dark:text-rose-300">Delete</button>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </article>

    <article class="crm-card p-6">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Stages</h3>
        <div class="mt-4 grid gap-3 md:grid-cols-4">
            <input wire:model.blur="newStageName" type="text" placeholder="Stage name" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
            <input wire:model.blur="newStageProbability" type="number" min="0" max="100" placeholder="Probability" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
            <input wire:model.live="newStageColor" type="color" class="h-10 rounded-xl border border-slate-200 bg-white px-2 py-1 dark:border-slate-700 dark:bg-slate-900" />
            <button wire:click="createStage" class="crm-btn crm-btn-primary">Add Stage</button>
        </div>

        <div class="mt-4 space-y-2" wire:sort="updateStageOrder">
            @forelse ($stages as $stage)
                <div wire:key="stage-{{ $stage->id }}" wire:sort:item="{{ $stage->id }}" class="flex items-center justify-between rounded-2xl border border-slate-200 px-4 py-3 text-sm dark:border-slate-700">
                    <div class="flex items-center gap-3">
                        <span class="size-3 rounded-full" style="background-color: {{ $stage->color }}"></span>
                        <span class="font-medium text-slate-900 dark:text-slate-100">{{ $stage->name }}</span>
                        <span class="text-xs text-slate-500 dark:text-slate-400">{{ $stage->probability }}%</span>
                    </div>
                    <button wire:click="deleteStage('{{ $stage->id }}')" class="rounded-lg border border-rose-300 px-2 py-1 text-xs font-medium text-rose-700 dark:border-rose-500/40 dark:text-rose-300">
                        Delete
                    </button>
                </div>
            @empty
                <p class="text-sm text-slate-500 dark:text-slate-400">No stages for selected pipeline.</p>
            @endforelse
        </div>
    </article>
</section>

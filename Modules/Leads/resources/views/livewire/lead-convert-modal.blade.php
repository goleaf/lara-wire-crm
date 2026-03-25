<section class="space-y-4">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Convert Lead</h3>
        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-800 dark:text-slate-300">Step {{ $step }} / 3</span>
    </div>

    @if ($step === 1)
        <article class="rounded-2xl border border-slate-200 p-4 dark:border-slate-700">
            <h4 class="mb-3 text-sm font-semibold text-slate-900 dark:text-white">Contact Details</h4>
            <div class="grid gap-3 md:grid-cols-2">
                <input wire:model.live="firstName" type="text" placeholder="First name" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                <input wire:model.live="lastName" type="text" placeholder="Last name" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                <input wire:model.live="email" type="email" placeholder="Email" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                <input wire:model.live="phone" type="text" placeholder="Phone" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
            </div>
        </article>
    @endif

    @if ($step === 2)
        <article class="rounded-2xl border border-slate-200 p-4 dark:border-slate-700">
            <h4 class="mb-3 text-sm font-semibold text-slate-900 dark:text-white">Account Details</h4>
            <input wire:model.live="accountName" type="text" placeholder="Account name" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
        </article>
    @endif

    @if ($step === 3)
        <article class="rounded-2xl border border-slate-200 p-4 dark:border-slate-700">
            <h4 class="mb-3 text-sm font-semibold text-slate-900 dark:text-white">Deal Preview</h4>
            <input wire:model.live="dealName" type="text" placeholder="Deal name" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">Deal creation runs when the Deals module is available and a default pipeline exists.</p>
        </article>
    @endif

    <div class="flex items-center justify-between">
        <button type="button" wire:click="previousStep" class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">
            Back
        </button>

        @if ($step < 3)
            <button type="button" wire:click="nextStep" class="rounded-xl bg-sky-600 px-3 py-2 text-xs font-semibold text-white">
                Next
            </button>
        @else
            <button type="button" wire:click="confirm" class="rounded-xl bg-emerald-600 px-3 py-2 text-xs font-semibold text-white">
                Confirm Conversion
            </button>
        @endif
    </div>
</section>

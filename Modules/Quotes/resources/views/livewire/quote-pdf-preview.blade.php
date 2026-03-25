<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-semibold text-slate-900 dark:text-slate-100">Quote PDF Preview</h1>
        <button wire:click="download" class="rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white hover:bg-sky-500">
            Download PDF
        </button>
    </div>

    <div class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
        @include('quotes::pdf.quote', ['quote' => $quote, 'company' => $company, 'preview' => true])
    </div>
</div>

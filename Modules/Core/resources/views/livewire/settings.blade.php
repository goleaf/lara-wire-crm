<section class="space-y-6">
    <x-crm.card class="p-6">
        <h3 class="text-xl font-semibold text-slate-900 dark:text-white">Settings</h3>
        <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Application defaults, company profile, and PDF header information.</p>
    </x-crm.card>

    <form wire:submit="save" class="space-y-6">
        <x-crm.card class="p-6">
            <div class="grid gap-4 md:grid-cols-2">
                <label class="space-y-1">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">App Name</span>
                    <input wire:model.live.debounce.300ms="app_name" type="text" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                </label>
                <label class="space-y-1">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Timezone</span>
                    <select wire:model.live="timezone" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        @foreach ($timezones as $timezoneOption)
                            <option value="{{ $timezoneOption }}">{{ $timezoneOption }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="space-y-1">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Currency Symbol</span>
                    <input wire:model.live.debounce.300ms="currency_symbol" type="text" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                </label>
                <label class="space-y-1">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Currency Code</span>
                    <input wire:model.live.debounce.300ms="currency_code" type="text" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                </label>
                <label class="space-y-1">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Date Format</span>
                    <select wire:model.live="date_format" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        @foreach ($dateFormats as $formatValue => $formatLabel)
                            <option value="{{ $formatValue }}">{{ $formatLabel }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="space-y-1">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Pagination Size</span>
                    <select wire:model.live="pagination_size" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        @foreach ($paginationSizes as $size)
                            <option value="{{ $size }}">{{ $size }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="space-y-1 md:col-span-2">
                    <span class="text-xs font-medium uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Logo</span>
                    <input wire:model="logo" type="file" accept="image/*" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                    @if ($logo_path)
                        <p class="text-xs text-slate-500 dark:text-slate-400">Current: {{ $logo_path }}</p>
                    @endif
                </label>
            </div>
        </x-crm.card>

        <x-crm.card class="p-6">
            <h4 class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-slate-400">Company Info</h4>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <label class="space-y-1">
                    <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Name</span>
                    <input wire:model.live.debounce.300ms="company_name" type="text" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                </label>
                <label class="space-y-1">
                    <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Phone</span>
                    <input wire:model.live.debounce.300ms="company_phone" type="text" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                </label>
                <label class="space-y-1">
                    <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Email</span>
                    <input wire:model.live.debounce.300ms="company_email" type="email" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                </label>
                <label class="space-y-1">
                    <span class="text-xs font-medium text-slate-600 dark:text-slate-300">VAT Number</span>
                    <input wire:model.live.debounce.300ms="company_vat" type="text" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                </label>
                <label class="space-y-1 md:col-span-2">
                    <span class="text-xs font-medium text-slate-600 dark:text-slate-300">Address</span>
                    <textarea wire:model.live.debounce.300ms="company_address" rows="3" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900"></textarea>
                </label>
            </div>
        </x-crm.card>

        <div class="flex justify-end">
            <button type="submit" class="crm-btn crm-btn-primary">
                Save Settings
            </button>
        </div>
    </form>
</section>

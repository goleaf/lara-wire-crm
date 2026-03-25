<section class="space-y-6">
    @if (session('status'))
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
            {{ session('status') }}
        </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
            <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Account Profile</h3>

            <div class="mt-5 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Name</label>
                    <input wire:model.blur="name" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Industry</label>
                    <select wire:model.live="industry" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        @foreach ($industries as $industryOption)
                            <option value="{{ $industryOption }}">{{ $industryOption }}</option>
                        @endforeach
                    </select>
                    @error('industry') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Type</label>
                    <select wire:model.live="type" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        @foreach ($types as $typeOption)
                            <option value="{{ $typeOption }}">{{ $typeOption }}</option>
                        @endforeach
                    </select>
                    @error('type') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Owner</label>
                    <select wire:model.live="ownerId" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        @foreach ($owners as $owner)
                            <option value="{{ $owner->id }}">{{ $owner->full_name }}</option>
                        @endforeach
                    </select>
                    @error('ownerId') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Website</label>
                    <input wire:model.blur="website" type="url" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('website') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Email</label>
                    <input wire:model.blur="email" type="email" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Phone</label>
                    <input wire:model.blur="phone" type="text" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('phone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Parent Account</label>
                    <select wire:model.live="parentAccountId" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900">
                        <option value="">No Parent</option>
                        @foreach ($parentAccounts as $parentAccount)
                            <option value="{{ $parentAccount->id }}">{{ $parentAccount->name }}</option>
                        @endforeach
                    </select>
                    @error('parentAccountId') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Annual Revenue</label>
                    <input wire:model.blur="annualRevenue" type="number" min="0" step="0.01" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('annualRevenue') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Employee Count</label>
                    <input wire:model.blur="employeeCount" type="number" min="0" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    @error('employeeCount') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Tags</label>
                    <input wire:model.blur="tagsInput" type="text" placeholder="Enterprise, North America, Priority" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Comma-separated. Chips will be generated on save.</p>
                </div>
            </div>
        </article>

        <article class="rounded-3xl border border-white/70 bg-white/80 p-6 shadow-sm dark:border-white/10 dark:bg-slate-950/40">
            <div class="flex items-center justify-between gap-3">
                <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Addresses</h3>
                <label class="inline-flex items-center gap-2 text-sm text-slate-600 dark:text-slate-300">
                    <input type="checkbox" wire:model.live="sameAsBilling" />
                    Same as billing for shipping
                </label>
            </div>

            <div class="mt-5 grid gap-6 md:grid-cols-2">
                <div class="space-y-3 rounded-2xl border border-slate-200 p-4 dark:border-slate-700">
                    <h4 class="text-sm font-semibold text-slate-900 dark:text-white">Billing Address</h4>
                    <input wire:model.blur="billingAddress.street" type="text" placeholder="Street" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    <input wire:model.blur="billingAddress.city" type="text" placeholder="City" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    <input wire:model.blur="billingAddress.state" type="text" placeholder="State" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    <input wire:model.blur="billingAddress.zip" type="text" placeholder="ZIP" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    <input wire:model.blur="billingAddress.country" type="text" placeholder="Country" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                </div>

                <div class="space-y-3 rounded-2xl border border-slate-200 p-4 dark:border-slate-700">
                    <h4 class="text-sm font-semibold text-slate-900 dark:text-white">Shipping Address</h4>
                    <input wire:model.blur="shippingAddress.street" type="text" placeholder="Street" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    <input wire:model.blur="shippingAddress.city" type="text" placeholder="City" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    <input wire:model.blur="shippingAddress.state" type="text" placeholder="State" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    <input wire:model.blur="shippingAddress.zip" type="text" placeholder="ZIP" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                    <input wire:model.blur="shippingAddress.country" type="text" placeholder="Country" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900" />
                </div>
            </div>
        </article>

        <div class="flex flex-wrap items-center justify-end gap-2">
            <a href="{{ route('accounts.index') }}" wire:navigate class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">
                Cancel
            </a>
            <button type="button" wire:click="save('new')" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 dark:border-slate-700 dark:text-slate-300">
                Save & New
            </button>
            <button type="button" wire:click="save('continue')" class="rounded-xl bg-sky-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-500">
                Save & Continue
            </button>
        </div>
    </form>
</section>

<?php

namespace Modules\Cases\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Modules\Cases\Models\SlaPolicy;

class SlaManager extends Component
{
    /**
     * @var array<string, int>
     */
    protected array $priorityOrder = [
        'Low' => 1,
        'Medium' => 2,
        'High' => 3,
        'Critical' => 4,
    ];

    /**
     * @var array<string, array{
     *     first_response_hours: int|string,
     *     resolution_hours: int|string,
     *     is_active: bool
     * }>
     */
    public array $policies = [];

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('cases.view'), 403);

        $this->policies = SlaPolicy::query()
            ->select(['id', 'first_response_hours', 'resolution_hours', 'is_active'])
            ->get()
            ->mapWithKeys(fn (SlaPolicy $policy) => [
                (string) $policy->id => [
                    'first_response_hours' => (int) $policy->first_response_hours,
                    'resolution_hours' => (int) $policy->resolution_hours,
                    'is_active' => (bool) $policy->is_active,
                ],
            ])
            ->toArray();
    }

    public function savePolicy(string $policyId): void
    {
        abort_unless(auth()->user()?->can('cases.edit'), 403);

        $policyData = $this->policies[$policyId] ?? null;

        if (! is_array($policyData)) {
            return;
        }

        validator($policyData, [
            'first_response_hours' => ['required', 'integer', 'min:1', 'max:240'],
            'resolution_hours' => ['required', 'integer', 'min:1', 'max:1000'],
            'is_active' => ['required', Rule::in([true, false, 0, 1, '0', '1'])],
        ])->validate();

        SlaPolicy::query()
            ->whereKey($policyId)
            ->update([
                'first_response_hours' => (int) $policyData['first_response_hours'],
                'resolution_hours' => (int) $policyData['resolution_hours'],
                'is_active' => (bool) $policyData['is_active'],
            ]);

        session()->flash('status', 'SLA policy updated.');
    }

    public function render(): View
    {
        $policies = SlaPolicy::query()
            ->select(['id', 'name', 'priority', 'first_response_hours', 'resolution_hours', 'is_active'])
            ->get()
            ->sortBy(fn (SlaPolicy $policy): int => $this->priorityOrder[$policy->priority] ?? 99)
            ->values();

        return view('cases::livewire.sla-manager', [
            'policiesList' => $policies,
        ])->extends('core::layouts.module', ['title' => 'SLA Manager']);
    }
}

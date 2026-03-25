<?php

namespace Modules\Activities\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Route;
use Livewire\Component;
use Modules\Activities\Models\Activity;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Deals\Models\Deal;
use Modules\Leads\Models\Lead;

class ActivityDetail extends Component
{
    public Activity $activity;

    public function mount(string $id): void
    {
        abort_unless(auth()->user()?->can('activities.view'), 403);

        $this->activity = Activity::query()
            ->with([
                'owner:id,full_name,email,avatar_path',
                'attendees:id,full_name,avatar_path',
            ])
            ->findOrFail($id);
    }

    public function markComplete(): void
    {
        abort_unless(auth()->user()?->can('activities.edit'), 403);

        $this->activity->update([
            'status' => 'Completed',
            'completed_at' => now(),
        ]);

        $this->activity->refresh();
        session()->flash('status', 'Activity marked as completed.');
    }

    public function cancel(): void
    {
        abort_unless(auth()->user()?->can('activities.edit'), 403);

        $this->activity->update([
            'status' => 'Cancelled',
        ]);

        $this->activity->refresh();
        session()->flash('status', 'Activity cancelled.');
    }

    public function render(): View
    {
        return view('activities::livewire.activity-detail', [
            'relatedLink' => $this->relatedLink(),
        ])->extends('core::layouts.module', ['title' => $this->activity->subject]);
    }

    /**
     * @return array{label:string,url:?string}
     */
    protected function relatedLink(): array
    {
        if (! $this->activity->related_to_type || ! $this->activity->related_to_id) {
            return ['label' => 'Not linked', 'url' => null];
        }

        if ($this->activity->related_to_type === Deal::class && Route::has('deals.show')) {
            return [
                'label' => 'Deal',
                'url' => route('deals.show', $this->activity->related_to_id),
            ];
        }

        if ($this->activity->related_to_type === Contact::class && Route::has('contacts.show')) {
            return [
                'label' => 'Contact',
                'url' => route('contacts.show', $this->activity->related_to_id),
            ];
        }

        if ($this->activity->related_to_type === Account::class && Route::has('accounts.show')) {
            return [
                'label' => 'Account',
                'url' => route('accounts.show', $this->activity->related_to_id),
            ];
        }

        if ($this->activity->related_to_type === Lead::class && Route::has('leads.show')) {
            return [
                'label' => 'Lead',
                'url' => route('leads.show', $this->activity->related_to_id),
            ];
        }

        return ['label' => class_basename($this->activity->related_to_type), 'url' => null];
    }
}

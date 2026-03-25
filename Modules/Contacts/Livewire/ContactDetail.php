<?php

namespace Modules\Contacts\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Modules\Activities\Models\Activity;
use Modules\Cases\Models\SupportCase;
use Modules\Contacts\Models\Contact;
use Modules\Deals\Models\Deal;

class ContactDetail extends Component
{
    public Contact $contact;

    public string $tab = 'overview';

    public function mount(string $id): void
    {
        abort_unless(auth()->user()?->can('contacts.view'), 403);

        $this->contact = Contact::query()
            ->select([
                'id',
                'first_name',
                'last_name',
                'email',
                'phone',
                'mobile',
                'job_title',
                'department',
                'account_id',
                'owner_id',
                'lead_source',
                'do_not_contact',
                'birthday',
                'preferred_channel',
                'notes',
            ])
            ->with([
                'account:id,name,type',
                'owner:id,full_name',
            ])
            ->findOrFail($id);
    }

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    public function render(): View
    {
        $deals = collect();
        $activities = collect();
        $cases = collect();

        if (class_exists(Deal::class)) {
            /** @var class-string<Model> $dealModel */
            $dealModel = Deal::class;

            $deals = $dealModel::query()
                ->select(['id', 'name', 'amount', 'contact_id'])
                ->where('contact_id', $this->contact->id)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        }

        if (class_exists(Activity::class)) {
            /** @var class-string<Model> $activityModel */
            $activityModel = Activity::class;

            $activities = $activityModel::query()
                ->select(['id', 'type', 'subject', 'status', 'due_date', 'related_to_type', 'related_to_id'])
                ->where('related_to_type', Contact::class)
                ->where('related_to_id', $this->contact->id)
                ->orderByDesc('due_date')
                ->limit(10)
                ->get();
        }

        if (class_exists(SupportCase::class)) {
            /** @var class-string<Model> $caseModel */
            $caseModel = SupportCase::class;

            $cases = $caseModel::query()
                ->select(['id', 'number', 'title', 'status', 'priority', 'contact_id'])
                ->where('contact_id', $this->contact->id)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        }

        return view('contacts::livewire.contact-detail', [
            'activities' => $activities,
            'cases' => $cases,
            'deals' => $deals,
            'tabs' => ['overview', 'deals', 'activities', 'cases', 'files'],
        ])->extends('core::layouts.module', ['title' => $this->contact->full_name]);
    }
}

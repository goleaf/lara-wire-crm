<?php

namespace Modules\Contacts\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Modules\Activities\Models\Activity;
use Modules\Contacts\Models\Account;
use Modules\Deals\Models\Deal;

class AccountDetail extends Component
{
    public Account $account;

    public string $tab = 'overview';

    public function mount(string $id): void
    {
        abort_unless(auth()->user()?->can('contacts.view'), 403);

        $this->account = Account::query()
            ->select([
                'id',
                'name',
                'industry',
                'type',
                'website',
                'phone',
                'email',
                'billing_address',
                'shipping_address',
                'annual_revenue',
                'employee_count',
                'owner_id',
                'parent_account_id',
                'tags',
                'created_at',
            ])
            ->with([
                'owner:id,full_name',
                'parent:id,name',
                'contacts:id,first_name,last_name,email,phone,account_id,owner_id,job_title,do_not_contact',
                'contacts.owner:id,full_name',
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

        if (class_exists(Deal::class)) {
            /** @var class-string<Model> $dealModel */
            $dealModel = Deal::class;

            $deals = $dealModel::query()
                ->select(['id', 'name', 'amount', 'stage_id', 'account_id'])
                ->where('account_id', $this->account->id)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        }

        if (class_exists(Activity::class)) {
            /** @var class-string<Model> $activityModel */
            $activityModel = Activity::class;

            $activities = $activityModel::query()
                ->select(['id', 'type', 'subject', 'status', 'due_date', 'related_to_type', 'related_to_id'])
                ->where('related_to_type', Account::class)
                ->where('related_to_id', $this->account->id)
                ->orderByDesc('due_date')
                ->limit(10)
                ->get();
        }

        return view('contacts::livewire.account-detail', [
            'activities' => $activities,
            'deals' => $deals,
            'tabs' => ['overview', 'contacts', 'deals', 'activities', 'files', 'notes'],
        ])->extends('core::layouts.module', ['title' => $this->account->name]);
    }
}

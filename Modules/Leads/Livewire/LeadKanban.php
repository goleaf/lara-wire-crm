<?php

namespace Modules\Leads\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Modules\Leads\Models\Lead;

class LeadKanban extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->user()?->can('leads.view'), 403);
    }

    public function moveLead(string $leadId, string $status): void
    {
        abort_unless(auth()->user()?->can('leads.edit'), 403);

        if (! in_array($status, ['New', 'Contacted', 'Qualified', 'Unqualified', 'Converted'], true)) {
            return;
        }

        Lead::query()
            ->whereKey($leadId)
            ->update([
                'status' => $status,
                'converted' => $status === 'Converted',
                'converted_at' => $status === 'Converted' ? now() : null,
            ]);
    }

    public function render(): View
    {
        $statuses = ['New', 'Contacted', 'Qualified', 'Unqualified', 'Converted'];

        $leadsByStatus = Lead::query()
            ->select([
                'id',
                'first_name',
                'last_name',
                'company',
                'status',
                'score',
                'rating',
                'owner_id',
            ])
            ->with('owner:id,full_name,avatar_path')
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('status');

        return view('leads::livewire.lead-kanban', [
            'leadsByStatus' => $leadsByStatus,
            'statuses' => $statuses,
        ])->extends('core::layouts.module', ['title' => 'Leads Kanban']);
    }
}

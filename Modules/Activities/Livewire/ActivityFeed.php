<?php

namespace Modules\Activities\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Activities\Models\Activity;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Deals\Models\Deal;
use Modules\Leads\Models\Lead;

class ActivityFeed extends Component
{
    use WithPagination;

    public string $search = '';

    public string $typeFilter = '';

    public string $statusFilter = '';

    public string $ownerFilter = '';

    public string $relatedFilter = '';

    /**
     * @var array<int, string>
     */
    public array $expanded = [];

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('activities.view'), 403);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingOwnerFilter(): void
    {
        $this->resetPage();
    }

    public function updatingRelatedFilter(): void
    {
        $this->resetPage();
    }

    public function toggleExpand(string $id): void
    {
        if (in_array($id, $this->expanded, true)) {
            $this->expanded = array_values(array_filter(
                $this->expanded,
                fn (string $expandedId): bool => $expandedId !== $id,
            ));

            return;
        }

        $this->expanded[] = $id;
    }

    public function markComplete(string $id): void
    {
        abort_unless(auth()->user()?->can('activities.edit'), 403);

        Activity::query()
            ->whereKey($id)
            ->update([
                'status' => 'Completed',
                'completed_at' => now(),
            ]);

        session()->flash('status', 'Activity marked complete.');
    }

    public function render(): View
    {
        $activities = $this->query()
            ->select([
                'id',
                'type',
                'subject',
                'description',
                'status',
                'priority',
                'due_date',
                'outcome',
                'related_to_type',
                'related_to_id',
                'owner_id',
                'created_at',
            ])
            ->with([
                'owner:id,full_name,avatar_path',
                'attendees:id,full_name',
            ])
            ->orderByDesc('due_date')
            ->orderByDesc('created_at')
            ->paginate(20);

        $groupedActivities = $activities->getCollection()
            ->groupBy(fn (Activity $activity): string => $this->groupForDate($activity->due_date));

        $owners = User::query()
            ->select(['id', 'full_name'])
            ->orderBy('full_name')
            ->get();

        return view('activities::livewire.activity-feed', [
            'activities' => $activities,
            'groupedActivities' => $groupedActivities,
            'owners' => $owners,
            'relatedOptions' => ['deal' => 'Deal', 'contact' => 'Contact', 'account' => 'Account', 'lead' => 'Lead'],
            'statuses' => ['Planned', 'Completed', 'Cancelled'],
            'types' => ['Meeting', 'Task', 'Note', 'SMS'],
        ])->extends('core::layouts.module', ['title' => 'Activities']);
    }

    protected function query(): Builder
    {
        return Activity::query()
            ->when($this->search !== '', function (Builder $query): void {
                $query->where(function (Builder $subQuery): void {
                    $subQuery
                        ->where('subject', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->typeFilter !== '', fn (Builder $query) => $query->where('type', $this->typeFilter))
            ->when($this->statusFilter !== '', fn (Builder $query) => $query->where('status', $this->statusFilter))
            ->when($this->ownerFilter !== '', fn (Builder $query) => $query->where('owner_id', $this->ownerFilter))
            ->when($this->relatedFilter !== '', function (Builder $query): void {
                $className = $this->relatedTypeMap()[$this->relatedFilter] ?? null;

                if ($className) {
                    $query->where('related_to_type', $className);
                }
            });
    }

    protected function groupForDate(?Carbon $date): string
    {
        if (! $date) {
            return 'No Due Date';
        }

        if ($date->isToday()) {
            return 'Today';
        }

        if ($date->isYesterday()) {
            return 'Yesterday';
        }

        if ($date->isCurrentWeek()) {
            return 'This Week';
        }

        return 'Earlier';
    }

    /**
     * @return array<string, string>
     */
    protected function relatedTypeMap(): array
    {
        return array_filter([
            'deal' => class_exists(Deal::class) ? Deal::class : null,
            'contact' => class_exists(Contact::class) ? Contact::class : null,
            'account' => class_exists(Account::class) ? Account::class : null,
            'lead' => class_exists(Lead::class) ? Lead::class : null,
        ]);
    }
}

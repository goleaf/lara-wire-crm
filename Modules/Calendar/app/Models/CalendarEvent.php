<?php

namespace Modules\Calendar\Models;

use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Modules\Calendar\Database\Factories\CalendarEventFactory;
use Modules\Contacts\Models\Contact;
use Modules\Core\Models\BaseModel;
use Modules\Deals\Models\Deal;

class CalendarEvent extends BaseModel
{
    use HasFactory;

    protected $table = 'calendar_events';

    protected $fillable = [
        'title',
        'type',
        'start_at',
        'end_at',
        'all_day',
        'location',
        'description',
        'organizer_id',
        'contact_id',
        'deal_id',
        'reminder_minutes',
        'recurrence',
        'recurrence_end_date',
        'status',
        'color',
    ];

    protected function casts(): array
    {
        return [
            'start_at' => 'datetime',
            'end_at' => 'datetime',
            'all_day' => 'boolean',
            'reminder_minutes' => 'integer',
            'recurrence_end_date' => 'date',
        ];
    }

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class, 'deal_id');
    }

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'calendar_event_attendees', 'event_id', 'user_id');
    }

    public function scopeInRange(Builder $query, CarbonInterface $from, CarbonInterface $to): Builder
    {
        $fromDate = Carbon::instance($from);
        $toDate = Carbon::instance($to);

        return $query
            ->where('start_at', '<=', $toDate)
            ->where(function (Builder $subQuery) use ($fromDate): void {
                $subQuery
                    ->whereNull('end_at')
                    ->orWhere('end_at', '>=', $fromDate);
            });
    }

    public function scopeForUser(Builder $query, string $userId): Builder
    {
        return $query->where(function (Builder $subQuery) use ($userId): void {
            $subQuery
                ->where('organizer_id', $userId)
                ->orWhereHas('attendees', fn (Builder $attendeeQuery) => $attendeeQuery->where('users.id', $userId));
        });
    }

    public function getDurationMinutesAttribute(): ?int
    {
        if (! $this->start_at || ! $this->end_at) {
            return null;
        }

        return max(0, $this->end_at->diffInMinutes($this->start_at));
    }

    public function generateRecurrences(): Collection
    {
        if ($this->recurrence === 'None') {
            return collect([$this]);
        }

        if (! $this->start_at) {
            return collect();
        }

        $endDate = Carbon::instance(
            $this->recurrence_end_date?->endOfDay()
                ?? Carbon::instance($this->start_at)->addMonths(3)
        );
        $cursor = Carbon::instance($this->start_at);
        $occurrences = collect();
        $index = 0;

        while ($cursor <= $endDate && $index < 366) {
            $occurrences->push($this->replicateWithDates($cursor));
            $cursor = match ($this->recurrence) {
                'Daily' => $cursor->copy()->addDay(),
                'Weekly' => $cursor->copy()->addWeek(),
                default => $cursor->copy()->addMonth(),
            };
            $index++;
        }

        return $occurrences;
    }

    protected static function newFactory(): CalendarEventFactory
    {
        return CalendarEventFactory::new();
    }

    protected function replicateWithDates(CarbonInterface $newStart): self
    {
        $startDate = Carbon::instance($newStart);
        $copy = $this->replicate();
        $copy->start_at = $startDate;

        if ($this->end_at && $this->start_at) {
            $duration = Carbon::instance($this->end_at)->diffInSeconds(Carbon::instance($this->start_at));
            $copy->end_at = $startDate->copy()->addSeconds($duration);
        }

        return $copy;
    }
}

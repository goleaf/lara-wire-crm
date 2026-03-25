<?php

namespace Modules\Activities\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Activities\Database\Factories\ActivityFactory;
use Modules\Core\Models\BaseModel;

class Activity extends BaseModel
{
    use HasFactory;

    protected $table = 'activities';

    protected $fillable = [
        'type',
        'subject',
        'description',
        'status',
        'priority',
        'due_date',
        'duration_minutes',
        'outcome',
        'related_to_type',
        'related_to_id',
        'owner_id',
        'reminder_at',
        'reminder_sent',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'datetime',
            'reminder_at' => 'datetime',
            'completed_at' => 'datetime',
            'duration_minutes' => 'integer',
            'reminder_sent' => 'boolean',
        ];
    }

    public function relatedTo(): MorphTo
    {
        return $this->morphTo();
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'activity_attendees');
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query
            ->where('status', 'Planned')
            ->where('due_date', '>', now());
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query
            ->where('status', 'Planned')
            ->where('due_date', '<', now());
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('due_date', today());
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeByOwner(Builder $query, string $userId): Builder
    {
        return $query->where('owner_id', $userId);
    }

    protected static function newFactory(): ActivityFactory
    {
        return ActivityFactory::new();
    }
}

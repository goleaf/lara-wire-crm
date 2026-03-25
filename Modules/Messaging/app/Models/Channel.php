<?php

namespace Modules\Messaging\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Core\Models\BaseModel;
use Modules\Messaging\Database\Factories\ChannelFactory;

class Channel extends BaseModel
{
    use HasFactory;

    protected $table = 'channels';

    protected $fillable = [
        'name',
        'type',
        'related_to_type',
        'related_to_id',
        'created_by',
    ];

    public function relatedTo(): MorphTo
    {
        return $this->morphTo();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'channel_user')
            ->withPivot(['last_read_at', 'is_muted']);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'channel_id');
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->type !== 'Direct') {
            return (string) ($this->name ?: 'Untitled Channel');
        }

        $currentUserId = auth()->id();

        $otherUser = $this->relationLoaded('members')
            ? $this->members->first(fn (User $member): bool => (string) $member->id !== (string) $currentUserId)
            : $this->members()
                ->select(['users.id', 'users.full_name', 'users.email'])
                ->when($currentUserId, fn (Builder $query) => $query->where('users.id', '!=', $currentUserId))
                ->first();

        if (! $otherUser) {
            return 'Direct Message';
        }

        return (string) ($otherUser->full_name ?: $otherUser->email);
    }

    public function getUnreadCount(User $user): int
    {
        $pivot = $this->members()
            ->where('users.id', $user->getKey())
            ->first()?->pivot;

        $lastReadAt = $pivot?->last_read_at;

        return $this->messages()
            ->notDeleted()
            ->where('sender_id', '!=', $user->getKey())
            ->when($lastReadAt, fn (Builder $query) => $query->where('sent_at', '>', $lastReadAt))
            ->count();
    }

    public function markAsRead(User $user): void
    {
        $this->members()->syncWithoutDetaching([
            (string) $user->getKey() => [
                'last_read_at' => now(),
            ],
        ]);
    }

    public function scopeForUser(Builder $query, string $userId): Builder
    {
        return $query->whereHas('members', fn (Builder $memberQuery) => $memberQuery->where('users.id', $userId));
    }

    public static function findOrCreateDm(User $a, User $b): self
    {
        $participantIds = collect([(string) $a->getKey(), (string) $b->getKey()])
            ->unique()
            ->values();

        $existing = self::query()
            ->select(['id', 'name', 'type', 'related_to_type', 'related_to_id', 'created_by', 'created_at', 'updated_at'])
            ->where('type', 'Direct')
            ->whereHas('members', fn (Builder $query) => $query->where('users.id', (string) $a->getKey()))
            ->whereHas('members', fn (Builder $query) => $query->where('users.id', (string) $b->getKey()))
            ->withCount('members')
            ->orderByDesc('updated_at')
            ->get()
            ->first(fn (self $channel): bool => $channel->members_count === $participantIds->count());

        if ($existing) {
            return $existing;
        }

        $channel = self::query()->create([
            'name' => null,
            'type' => 'Direct',
            'created_by' => (string) $a->getKey(),
        ]);

        $channel->members()->sync($participantIds->all());
        $channel->markAsRead($a);
        $channel->markAsRead($b);

        return $channel->fresh();
    }

    protected static function newFactory(): ChannelFactory
    {
        return ChannelFactory::new();
    }
}

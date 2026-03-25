<?php

namespace Modules\Notifications\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Core\Models\BaseModel;
use Modules\Notifications\Database\Factories\CrmNotificationFactory;

class CrmNotification extends BaseModel
{
    use HasFactory;

    protected $table = 'crm_notifications';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'body',
        'is_read',
        'read_at',
        'related_to_type',
        'related_to_id',
        'action_url',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function relatedTo(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser(Builder $query, string $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function markRead(): void
    {
        if ($this->is_read) {
            return;
        }

        $this->forceFill([
            'is_read' => true,
            'read_at' => now(),
        ])->save();
    }

    /**
     * @param  array{
     *     body?: string|null,
     *     related_to_type?: string|null,
     *     related_to_id?: string|null,
     *     action_url?: string|null
     * }  $data
     */
    public static function notify(User $user, string $type, string $title, array $data = []): self
    {
        return self::query()->create([
            'user_id' => $user->getKey(),
            'type' => $type,
            'title' => $title,
            'body' => $data['body'] ?? null,
            'is_read' => false,
            'read_at' => null,
            'related_to_type' => $data['related_to_type'] ?? null,
            'related_to_id' => $data['related_to_id'] ?? null,
            'action_url' => $data['action_url'] ?? null,
        ]);
    }

    protected static function newFactory(): CrmNotificationFactory
    {
        return CrmNotificationFactory::new();
    }
}

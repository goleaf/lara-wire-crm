<?php

namespace Modules\Messaging\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Core\Models\BaseModel;
use Modules\Files\Models\CrmFile;
use Modules\Messaging\Database\Factories\MessageFactory;

class Message extends BaseModel
{
    use HasFactory;

    protected $table = 'messages';

    protected $fillable = [
        'channel_id',
        'sender_id',
        'body',
        'sent_at',
        'edited_at',
        'is_deleted',
        'parent_message_id',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'edited_at' => 'datetime',
            'is_deleted' => 'boolean',
        ];
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class, 'channel_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_message_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(self::class, 'parent_message_id')->orderBy('sent_at');
    }

    public function mentions(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'message_mentions');
    }

    public function attachments(): BelongsToMany
    {
        return $this->belongsToMany(CrmFile::class, 'message_files', 'message_id', 'file_id');
    }

    public function scopeNotDeleted(Builder $query): Builder
    {
        return $query->where('is_deleted', false);
    }

    protected static function newFactory(): MessageFactory
    {
        return MessageFactory::new();
    }
}

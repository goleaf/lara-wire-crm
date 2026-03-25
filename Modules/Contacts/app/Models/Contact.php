<?php

namespace Modules\Contacts\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Contacts\Database\Factories\ContactFactory;
use Modules\Core\Models\BaseModel;

class Contact extends BaseModel
{
    use HasFactory;

    protected $table = 'contacts';

    protected $fillable = [
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
    ];

    protected function casts(): array
    {
        return [
            'do_not_contact' => 'boolean',
            'birthday' => 'date',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function scopeByOwner(Builder $query, string $userId): Builder
    {
        return $query->where('owner_id', $userId);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function (Builder $inner) use ($term): void {
            $inner
                ->where('first_name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%");
        });
    }

    protected static function newFactory(): ContactFactory
    {
        return ContactFactory::new();
    }
}

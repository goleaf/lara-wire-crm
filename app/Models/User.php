<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Modules\Activities\Models\Activity;
use Modules\Core\Models\Concerns\HasAuditLog;
use Modules\Core\Models\Concerns\HasUuid;
use Modules\Deals\Models\Deal;
use Modules\Users\Models\Role;
use Modules\Users\Models\Team;

#[Fillable([
    'name',
    'full_name',
    'email',
    'password',
    'role_id',
    'team_id',
    'is_active',
    'last_login',
    'quota',
    'avatar_path',
    'user_notification_preferences',
])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    use HasAuditLog;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasUuid;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $keyType = 'string';

    public $incrementing = false;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login' => 'datetime',
            'is_active' => 'boolean',
            'quota' => 'decimal:2',
            'user_notification_preferences' => 'array',
            'password' => 'hashed',
        ];
    }

    public function getNameAttribute(): string
    {
        return (string) ($this->attributes['full_name'] ?? '');
    }

    public function setNameAttribute(string $value): void
    {
        $this->attributes['full_name'] = $value;
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_user');
    }

    public function deals(): HasMany
    {
        return $this->hasMany(Deal::class, 'owner_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class, 'owner_id');
    }

    public function hasPermission(string $action): bool
    {
        $permissionKey = 'can_'.Str::snake($action);
        $role = $this->role;

        if (! $role) {
            return false;
        }

        return (bool) $role->getAttribute($permissionKey);
    }

    public function canAccessModule(string $module): bool
    {
        $role = $this->role;

        if (! $role) {
            return false;
        }

        if (Str::lower($role->name) === 'admin') {
            return true;
        }

        $accessMap = Arr::wrap($role->module_access);
        $moduleKey = Str::lower($module);

        if ($accessMap === []) {
            return true;
        }

        return (bool) ($accessMap[$moduleKey] ?? true);
    }

    public function hasRecordAccess(Model $record): bool
    {
        $role = $this->role;

        if (! $role) {
            return false;
        }

        if (Str::lower($role->name) === 'admin') {
            return true;
        }

        return match ($role->record_visibility) {
            'all' => true,
            'team' => $this->hasTeamAccess($record),
            default => $this->ownsRecord($record),
        };
    }

    protected function ownsRecord(Model $record): bool
    {
        $keys = ['owner_id', 'user_id', 'created_by'];

        foreach ($keys as $key) {
            if ($record->getAttribute($key) === $this->getKey()) {
                return true;
            }
        }

        return false;
    }

    protected function hasTeamAccess(Model $record): bool
    {
        if ($this->team_id && $record->getAttribute('team_id') === $this->team_id) {
            return true;
        }

        $ownerId = $record->getAttribute('owner_id') ?? $record->getAttribute('user_id');

        if (! $ownerId) {
            return false;
        }

        $owner = static::query()->select(['id', 'team_id'])->find($ownerId);

        return $owner && $owner->team_id === $this->team_id;
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->full_name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}

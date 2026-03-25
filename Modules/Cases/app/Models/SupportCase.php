<?php

namespace Modules\Cases\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Cases\Database\Factories\SupportCaseFactory;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Core\Models\BaseModel;
use Modules\Core\Models\Concerns\HasAuditLog;
use Modules\Deals\Models\Deal;

class SupportCase extends BaseModel
{
    use HasAuditLog;
    use HasFactory;

    protected $table = 'cases';

    protected $fillable = [
        'number',
        'title',
        'description',
        'status',
        'priority',
        'type',
        'contact_id',
        'account_id',
        'deal_id',
        'owner_id',
        'sla_deadline',
        'first_response_at',
        'resolved_at',
        'closed_at',
        'satisfaction_score',
        'channel',
        'resolution_notes',
    ];

    protected function casts(): array
    {
        return [
            'sla_deadline' => 'datetime',
            'first_response_at' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
            'satisfaction_score' => 'integer',
        ];
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class, 'deal_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(CaseComment::class, 'case_id')->orderBy('created_at');
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->sla_deadline !== null
            && $this->sla_deadline->isPast()
            && ! in_array($this->status, ['Resolved', 'Closed'], true);
    }

    public function getResponseTimeMinutesAttribute(): ?int
    {
        if ($this->first_response_at === null || $this->created_at === null) {
            return null;
        }

        return (int) $this->created_at->diffInMinutes($this->first_response_at);
    }

    public function generateNumber(): string
    {
        $sequence = self::query()->count() + 1;

        return sprintf('CASE-%04d', $sequence);
    }

    public function assignSla(): void
    {
        $policy = SlaPolicy::forPriority((string) $this->priority);

        if (! $policy) {
            return;
        }

        $base = $this->created_at ?? now();
        $this->sla_deadline = $base->copy()->addHours((int) $policy->resolution_hours);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['Resolved', 'Closed']);
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query
            ->whereNotIn('status', ['Resolved', 'Closed'])
            ->whereNotNull('sla_deadline')
            ->where('sla_deadline', '<', now());
    }

    public function scopeByOwner(Builder $query, string $ownerId): Builder
    {
        return $query->where('owner_id', $ownerId);
    }

    public function scopeByPriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    protected static function newFactory(): SupportCaseFactory
    {
        return SupportCaseFactory::new();
    }
}

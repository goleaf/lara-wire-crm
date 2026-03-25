<?php

namespace Modules\Campaigns\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Campaigns\Database\Factories\CampaignFactory;
use Modules\Contacts\Models\Contact;
use Modules\Core\Models\BaseModel;
use Modules\Deals\Models\Deal;
use Modules\Leads\Models\Lead;

class Campaign extends BaseModel
{
    use HasFactory;

    protected $table = 'campaigns';

    protected $fillable = [
        'name',
        'type',
        'status',
        'start_date',
        'end_date',
        'budget',
        'actual_cost',
        'target_audience',
        'expected_leads',
        'description',
        'owner_id',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'budget' => 'decimal:2',
            'actual_cost' => 'decimal:2',
            'expected_leads' => 'integer',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'campaign_id');
    }

    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(Contact::class, 'campaign_contacts', 'campaign_id', 'contact_id')
            ->withPivot(['added_at', 'status']);
    }

    public function linkedLeads(): BelongsToMany
    {
        return $this->belongsToMany(Lead::class, 'campaign_leads', 'campaign_id', 'lead_id')
            ->withPivot(['added_at']);
    }

    public function getActualLeadsAttribute(): int
    {
        return (int) $this->leads()->count();
    }

    public function getRevenueGeneratedAttribute(): float
    {
        $dealIds = $this->leads()
            ->whereNotNull('converted_to_deal_id')
            ->pluck('converted_to_deal_id');

        if ($dealIds->isEmpty()) {
            return 0.0;
        }

        return round((float) Deal::query()
            ->whereIn('id', $dealIds)
            ->whereHas('stage', fn ($query) => $query->where('name', 'Closed Won'))
            ->sum('amount'), 2);
    }

    public function getRoiAttribute(): float
    {
        $actualCost = (float) $this->actual_cost;

        if ($actualCost <= 0) {
            return 0;
        }

        return round((($this->revenue_generated - $actualCost) / $actualCost) * 100, 2);
    }

    public function getLeadConversionRateAttribute(): float
    {
        $totalLeads = (int) $this->leads()->count();

        if ($totalLeads === 0) {
            return 0;
        }

        $convertedLeads = (int) $this->leads()->where('converted', true)->count();

        return round(($convertedLeads / $totalLeads) * 100, 2);
    }

    public function getBudgetVarianceAttribute(): float
    {
        return round((float) $this->budget - (float) $this->actual_cost, 2);
    }

    public function getIsActiveAttribute(): bool
    {
        if ($this->status !== 'Active') {
            return false;
        }

        $today = now()->toDateString();

        if ($this->start_date !== null && $this->start_date->toDateString() > $today) {
            return false;
        }

        if ($this->end_date !== null && $this->end_date->toDateString() < $today) {
            return false;
        }

        return true;
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where('status', 'Active')
            ->where(function (Builder $inner): void {
                $inner->whereNull('start_date')->orWhereDate('start_date', '<=', now()->toDateString());
            })
            ->where(function (Builder $inner): void {
                $inner->whereNull('end_date')->orWhereDate('end_date', '>=', now()->toDateString());
            });
    }

    public function scopeByOwner(Builder $query, string $ownerId): Builder
    {
        return $query->where('owner_id', $ownerId);
    }

    protected static function newFactory(): CampaignFactory
    {
        return CampaignFactory::new();
    }
}

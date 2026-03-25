<?php

namespace Modules\Leads\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Modules\Campaigns\Models\Campaign;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Core\Models\BaseModel;
use Modules\Deals\Models\Deal;
use Modules\Deals\Models\Pipeline;
use Modules\Deals\Models\PipelineStage;
use Modules\Leads\Database\Factories\LeadFactory;

class Lead extends BaseModel
{
    use HasFactory;

    protected $table = 'leads';

    protected $fillable = [
        'first_name',
        'last_name',
        'company',
        'email',
        'phone',
        'lead_source',
        'status',
        'score',
        'rating',
        'campaign_id',
        'owner_id',
        'converted',
        'converted_to_contact_id',
        'converted_to_deal_id',
        'converted_at',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'converted' => 'boolean',
            'converted_at' => 'datetime',
            'score' => 'integer',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function convertedContact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'converted_to_contact_id');
    }

    public function convertedDeal(): BelongsTo
    {
        return $this->belongsTo(Deal::class, 'converted_to_deal_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function getIsConvertedAttribute(): bool
    {
        return (bool) $this->converted;
    }

    /**
     * @return array{contact:Contact, deal:mixed}
     */
    public function convert(User $convertedBy): array
    {
        return DB::transaction(function () use ($convertedBy): array {
            $account = Account::query()->firstOrCreate(
                [
                    'name' => $this->company ?: $this->full_name.' Account',
                ],
                [
                    'industry' => 'Other',
                    'type' => 'Prospect',
                    'billing_address' => [
                        'street' => '',
                        'city' => '',
                        'state' => '',
                        'zip' => '',
                        'country' => '',
                    ],
                    'owner_id' => $convertedBy->id,
                ],
            );

            $contact = Contact::query()->create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'account_id' => $account->id,
                'owner_id' => $convertedBy->id,
                'lead_source' => $this->lead_source,
                'preferred_channel' => 'Phone',
            ]);

            $deal = null;

            if (class_exists(Deal::class) && class_exists(Pipeline::class)) {
                $pipeline = Pipeline::query()
                    ->select(['id'])
                    ->where('is_default', true)
                    ->first();
                $stage = PipelineStage::query()
                    ->select(['id'])
                    ->where('pipeline_id', $pipeline?->id)
                    ->orderBy('order')
                    ->first();

                if ($pipeline && $stage) {
                    $deal = Deal::query()->create([
                        'name' => $this->full_name.' Opportunity',
                        'account_id' => $account->id,
                        'contact_id' => $contact->id,
                        'owner_id' => $convertedBy->id,
                        'pipeline_id' => $pipeline->id,
                        'stage_id' => $stage->id,
                        'amount' => 0,
                        'currency' => config('crm.default_currency.code', 'USD'),
                        'probability' => 10,
                        'expected_revenue' => 0,
                        'deal_type' => 'New Business',
                        'source' => $this->lead_source,
                    ]);
                }
            }

            $this->forceFill([
                'converted' => true,
                'status' => 'Converted',
                'converted_to_contact_id' => $contact->id,
                'converted_to_deal_id' => $deal?->id,
                'converted_at' => now(),
            ])->save();

            return ['contact' => $contact, 'deal' => $deal];
        });
    }

    public function scopeNotConverted(Builder $query): Builder
    {
        return $query->where('converted', false);
    }

    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeByRating(Builder $query, string $rating): Builder
    {
        return $query->where('rating', $rating);
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function (Builder $inner) use ($term): void {
            $inner
                ->where('first_name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('company', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%");
        });
    }

    protected static function newFactory(): LeadFactory
    {
        return LeadFactory::new();
    }
}

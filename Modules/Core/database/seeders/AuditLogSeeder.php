<?php

namespace Modules\Core\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Activities\Models\Activity;
use Modules\Campaigns\Models\Campaign;
use Modules\Cases\Models\SupportCase;
use Modules\Contacts\Models\Account;
use Modules\Core\Models\AuditLog;
use Modules\Deals\Models\Deal;
use Modules\Invoices\Models\Invoice;
use Modules\Leads\Models\Lead;
use Modules\Quotes\Models\Quote;

class AuditLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AuditLog::query()->delete();

        $userIds = User::query()->select(['id'])->limit(10)->pluck('id')->all();

        if ($userIds === []) {
            return;
        }

        $targets = collect([
            ['model' => User::class, 'id' => User::query()->value('id')],
            ['model' => Account::class, 'id' => Account::query()->value('id')],
            ['model' => Lead::class, 'id' => Lead::query()->value('id')],
            ['model' => Deal::class, 'id' => Deal::query()->value('id')],
            ['model' => Activity::class, 'id' => Activity::query()->value('id')],
            ['model' => Quote::class, 'id' => Quote::query()->value('id')],
            ['model' => Invoice::class, 'id' => Invoice::query()->value('id')],
            ['model' => Campaign::class, 'id' => Campaign::query()->value('id')],
            ['model' => SupportCase::class, 'id' => SupportCase::query()->value('id')],
        ])->filter(fn (array $target): bool => filled($target['id']))->values();

        if ($targets->isEmpty()) {
            return;
        }

        $actions = ['created', 'updated', 'deleted'];

        foreach ($targets as $target) {
            foreach ($actions as $action) {
                AuditLog::query()->create([
                    'user_id' => (string) $userIds[array_rand($userIds)],
                    'action' => $action,
                    'model_type' => (string) $target['model'],
                    'model_id' => (string) $target['id'],
                    'old_values' => $action === 'created' ? [] : ['status' => 'old-value'],
                    'new_values' => $action === 'deleted' ? [] : ['status' => 'new-value'],
                    'ip_address' => fake()->ipv4(),
                    'created_at' => now()->subMinutes(random_int(1, 720)),
                ]);
            }
        }
    }
}

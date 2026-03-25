<?php

namespace Modules\Notifications\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Notifications\Models\CrmNotification;

class CrmNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::query()->select(['id'])->limit(10)->pluck('id')->all();

        if ($users === []) {
            return;
        }

        $types = [
            'Reminder',
            'Mention',
            'Assignment',
            'SLA Breach',
            'Deal Update',
            'Task Due',
            'Case Update',
            'Payment Recorded',
            'Quote Accepted',
            'Other',
        ];

        foreach ($users as $index => $userId) {
            $type = $types[$index % count($types)];
            $isRead = $index % 3 === 0;

            CrmNotification::query()->create([
                'user_id' => (string) $userId,
                'type' => $type,
                'title' => $type.' notification',
                'body' => 'Seeded '.$type.' notification for demo workflows.',
                'is_read' => $isRead,
                'read_at' => $isRead ? now()->subMinutes(random_int(5, 240)) : null,
                'related_to_type' => User::class,
                'related_to_id' => (string) $userId,
                'action_url' => '/dashboard',
            ]);
        }
    }
}

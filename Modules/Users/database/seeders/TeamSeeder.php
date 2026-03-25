<?php

namespace Modules\Users\Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Users\Models\Team;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::query()
            ->select(['id', 'full_name', 'email', 'avatar_path'])
            ->orderBy('email')
            ->get()
            ->values();

        if ($users->isEmpty()) {
            return;
        }

        $regions = ['North', 'South', 'West'];
        $chunkSize = max(1, (int) ceil($users->count() / count($regions)));

        $users
            ->chunk($chunkSize)
            ->values()
            ->each(function ($teamUsers, int $index) use ($regions): void {
                $manager = $teamUsers->first();

                if (! $manager) {
                    return;
                }

                $team = Team::query()->updateOrCreate(
                    ['name' => 'Team '.($index + 1)],
                    [
                        'manager_id' => (string) $manager->id,
                        'region' => $regions[$index] ?? $regions[array_key_last($regions)],
                    ]
                );

                $memberIds = $teamUsers->pluck('id')->map(fn (mixed $id): string => (string) $id)->all();

                $team->members()->sync($memberIds);

                User::query()
                    ->whereIn('id', $memberIds)
                    ->update(['team_id' => $team->getKey()]);
            });

        User::query()
            ->select(['id', 'full_name', 'email', 'avatar_path', 'last_login'])
            ->get()
            ->each(function (User $user): void {
                if (blank($user->avatar_path)) {
                    $fallback = Str::slug((string) ($user->full_name ?: Str::before((string) $user->email, '@')));
                    $user->forceFill(['avatar_path' => 'avatars/'.$fallback.'.png']);
                }

                if ($user->last_login === null) {
                    $user->forceFill(['last_login' => now()->subMinutes(random_int(30, 1200))]);
                }

                $user->saveQuietly();
            });
    }
}

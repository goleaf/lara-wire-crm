<?php

namespace Modules\Users\Livewire;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Modules\Deals\Models\Deal;

class Profile extends Component
{
    use WithFileUploads;

    public string $full_name = '';

    public string $email = '';

    public string $current_password = '';

    public string $password = '';

    public string $password_confirmation = '';

    public ?TemporaryUploadedFile $avatar = null;

    public ?string $avatar_path = null;

    /**
     * @var array<string, bool>
     */
    public array $notification_types = [];

    public ?string $quiet_hours_start = null;

    public ?string $quiet_hours_end = null;

    public float $quota_used = 0;

    public function mount(): void
    {
        abort_unless(auth()->check(), 403);

        /** @var User $user */
        $user = auth()->user();

        $this->full_name = (string) $user->full_name;
        $this->email = (string) $user->email;
        $this->avatar_path = $user->avatar_path;

        $defaults = $this->defaultNotificationTypes();
        $preferences = $user->user_notification_preferences ?? [];

        if (is_string($preferences)) {
            $preferences = json_decode($preferences, true);
        }

        $storedTypes = is_array($preferences) ? ($preferences['types'] ?? []) : [];
        $this->notification_types = array_merge($defaults, is_array($storedTypes) ? $storedTypes : []);
        $this->quiet_hours_start = is_array($preferences) ? ($preferences['quiet_hours_start'] ?? null) : null;
        $this->quiet_hours_end = is_array($preferences) ? ($preferences['quiet_hours_end'] ?? null) : null;

        $this->quota_used = $this->calculateQuotaUsed($user);
    }

    public function save(): void
    {
        /** @var User $user */
        $user = auth()->user();

        $validated = $this->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique(User::class, 'email')->ignore($user->id)],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'quiet_hours_start' => ['nullable', 'date_format:H:i'],
            'quiet_hours_end' => ['nullable', 'date_format:H:i'],
            'current_password' => ['nullable', 'required_with:password', 'string'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        if (filled($validated['password'] ?? null)) {
            if (! Hash::check((string) $validated['current_password'], (string) $user->password)) {
                $this->addError('current_password', 'Current password is incorrect.');

                return;
            }

            $user->password = Hash::make((string) $validated['password']);
        }

        if ($this->avatar) {
            $this->avatar_path = $this->avatar->store('avatars', 'public');
            $user->avatar_path = $this->avatar_path;
        }

        $user->full_name = $validated['full_name'];
        $user->email = $validated['email'];
        $user->user_notification_preferences = [
            'types' => $this->notification_types,
            'quiet_hours_start' => $this->quiet_hours_start,
            'quiet_hours_end' => $this->quiet_hours_end,
        ];
        $user->save();

        $this->current_password = '';
        $this->password = '';
        $this->password_confirmation = '';

        session()->flash('status', 'Profile updated.');
    }

    public function render(): View
    {
        /** @var User $user */
        $user = auth()->user();
        $quota = (float) $user->quota;

        return view('users::livewire.profile', [
            'quota' => $quota,
            'quotaPercent' => $quota > 0 ? min(100, round(($this->quota_used / $quota) * 100, 2)) : 0,
        ])->extends('core::layouts.module', ['title' => 'My Profile']);
    }

    /**
     * @return array<string, bool>
     */
    protected function defaultNotificationTypes(): array
    {
        return [
            'Reminder' => true,
            'Mention' => true,
            'Assignment' => true,
            'SLA Breach' => true,
            'Deal Update' => true,
            'Task Due' => true,
            'Case Update' => true,
            'Payment Recorded' => true,
            'Quote Accepted' => true,
            'Other' => true,
        ];
    }

    protected function calculateQuotaUsed(User $user): float
    {
        if (! class_exists(Deal::class)) {
            return 0;
        }

        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        return (float) Deal::query()
            ->select(['id', 'owner_id', 'amount', 'created_at'])
            ->where('owner_id', $user->id)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('amount');
    }
}

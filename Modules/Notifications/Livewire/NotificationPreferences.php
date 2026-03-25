<?php

namespace Modules\Notifications\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class NotificationPreferences extends Component
{
    /**
     * @var array<string, bool>
     */
    public array $types = [];

    public ?string $quiet_hours_start = null;

    public ?string $quiet_hours_end = null;

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('notifications.view'), 403);

        $defaults = $this->defaultTypes();
        $preferences = auth()->user()->user_notification_preferences ?? [];

        if (is_string($preferences)) {
            $preferences = json_decode($preferences, true);
        }

        $storedTypes = is_array($preferences) ? ($preferences['types'] ?? []) : [];
        $this->types = array_merge($defaults, is_array($storedTypes) ? $storedTypes : []);
        $this->quiet_hours_start = is_array($preferences) ? ($preferences['quiet_hours_start'] ?? null) : null;
        $this->quiet_hours_end = is_array($preferences) ? ($preferences['quiet_hours_end'] ?? null) : null;
    }

    public function save(): void
    {
        abort_unless(auth()->user()?->can('notifications.edit'), 403);

        $this->validate([
            'quiet_hours_start' => ['nullable', 'date_format:H:i'],
            'quiet_hours_end' => ['nullable', 'date_format:H:i'],
        ]);

        auth()->user()->forceFill([
            'user_notification_preferences' => [
                'types' => $this->types,
                'quiet_hours_start' => $this->quiet_hours_start,
                'quiet_hours_end' => $this->quiet_hours_end,
            ],
        ])->save();

        session()->flash('status', 'Notification preferences saved.');
    }

    protected function defaultTypes(): array
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

    public function render(): View
    {
        return view('notifications::livewire.notification-preferences')
            ->extends('core::layouts.module', ['title' => 'Notification Preferences']);
    }
}

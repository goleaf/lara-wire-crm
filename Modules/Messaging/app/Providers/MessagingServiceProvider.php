<?php

namespace Modules\Messaging\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Modules\Messaging\Models\Message;
use Modules\Messaging\Observers\MessageObserver;
use Nwidart\Modules\Support\ModuleServiceProvider;

class MessagingServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Messaging';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'messaging';

    /**
     * Provider classes to register.
     *
     * @var string[]
     */
    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];

    public function boot(): void
    {
        parent::boot();

        Message::observe(MessageObserver::class);

        foreach (['view', 'create', 'edit', 'delete', 'export'] as $action) {
            Gate::define("messaging.{$action}", fn (User $user): bool => $user->hasPermission($action) && $user->canAccessModule('messaging'));
        }
    }
}

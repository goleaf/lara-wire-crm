<?php

namespace Modules\Campaigns\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Nwidart\Modules\Support\ModuleServiceProvider;

class CampaignsServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Campaigns';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'campaigns';

    /**
     * Command classes to register.
     *
     * @var string[]
     */
    // protected array $commands = [];

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

        foreach (['view', 'create', 'edit', 'delete', 'export'] as $action) {
            Gate::define("campaigns.{$action}", fn (User $user): bool => $user->hasPermission($action) && $user->canAccessModule('campaigns'));
        }
    }
}

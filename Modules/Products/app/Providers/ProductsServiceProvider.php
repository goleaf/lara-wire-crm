<?php

namespace Modules\Products\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Nwidart\Modules\Support\ModuleServiceProvider;

class ProductsServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Products';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'products';

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
            Gate::define("products.{$action}", fn (User $user): bool => $user->hasPermission($action) && $user->canAccessModule('products'));
        }
    }
}

<?php

namespace Modules\Core\Providers;

use Nwidart\Modules\Support\ModuleServiceProvider;

class CoreServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Core';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'core';

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

        config([
            'app.name' => config('crm.app_name', config('app.name')),
            'app.timezone' => config('crm.timezone', config('app.timezone')),
        ]);

        date_default_timezone_set((string) config('app.timezone'));
    }

    protected function registerConfig(): void
    {
        $crmConfigPath = module_path($this->name, 'config/crm.php');

        if (! is_file($crmConfigPath)) {
            return;
        }

        $this->publishes([$crmConfigPath => config_path('crm.php')], 'config');
        $this->mergeConfigFrom($crmConfigPath, 'crm');
    }
}

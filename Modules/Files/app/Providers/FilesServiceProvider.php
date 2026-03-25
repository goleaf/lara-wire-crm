<?php

namespace Modules\Files\Providers;

use Modules\Files\Http\Middleware\FileAccessMiddleware;
use Nwidart\Modules\Support\ModuleServiceProvider;

class FilesServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Files';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'files';

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

        $this->app['router']->aliasMiddleware('file.access', FileAccessMiddleware::class);
    }
}

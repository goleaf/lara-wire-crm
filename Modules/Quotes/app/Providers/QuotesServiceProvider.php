<?php

namespace Modules\Quotes\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Modules\Quotes\Models\Quote;
use Modules\Quotes\Models\QuoteLineItem;
use Modules\Quotes\Observers\QuoteLineItemObserver;
use Modules\Quotes\Observers\QuoteObserver;
use Nwidart\Modules\Support\ModuleServiceProvider;

class QuotesServiceProvider extends ModuleServiceProvider
{
    /**
     * The name of the module.
     */
    protected string $name = 'Quotes';

    /**
     * The lowercase version of the module name.
     */
    protected string $nameLower = 'quotes';

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

        Quote::observe(QuoteObserver::class);
        QuoteLineItem::observe(QuoteLineItemObserver::class);

        foreach (['view', 'create', 'edit', 'delete', 'export'] as $action) {
            Gate::define("quotes.{$action}", fn (User $user): bool => $user->hasPermission($action) && $user->canAccessModule('quotes'));
        }
    }
}

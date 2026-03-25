<?php

namespace App\View\Components;

use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\View\Component;
use Modules\Campaigns\Models\Campaign;
use Modules\Cases\Models\SupportCase;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Deals\Models\Deal;
use Modules\Invoices\Models\Invoice;
use Modules\Leads\Models\Lead;
use Modules\Products\Models\Product;
use Modules\Quotes\Models\Quote;
use Modules\Users\Models\Role;
use Modules\Users\Models\Team;

class Breadcrumbs extends Component
{
    /**
     * @var array<int, array{label: string, href: string|null, current: bool}>
     */
    public array $items = [];

    public function __construct()
    {
        $this->items = $this->resolveItems();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.breadcrumbs');
    }

    /**
     * @return array<int, array{label: string, href: string|null, current: bool}>
     */
    protected function resolveItems(): array
    {
        $items = [
            [
                'label' => 'CRM',
                'href' => Route::has('dashboard') ? route('dashboard') : null,
                'current' => false,
            ],
        ];

        $routeName = (string) request()->route()?->getName();

        if ($routeName === '') {
            $items[0]['current'] = true;

            return $items;
        }

        if ($routeName === 'dashboard') {
            $items[] = ['label' => 'Dashboard', 'href' => null, 'current' => true];

            return $items;
        }

        $module = Str::before($routeName, '.');
        $moduleLabel = Str::headline(str_replace('-', ' ', $module));
        $moduleIndexRoute = "{$module}.index";
        $hasModuleIndexRoute = Route::has($moduleIndexRoute);

        $items[] = [
            'label' => $moduleLabel,
            'href' => $hasModuleIndexRoute ? route($moduleIndexRoute) : null,
            'current' => false,
        ];

        $modelLabel = $this->resolveModelLabel($routeName, (string) request()->route('id'));

        if ($modelLabel !== null) {
            $items[] = ['label' => $modelLabel, 'href' => null, 'current' => false];
        }

        $actionLabel = Str::afterLast($routeName, '.');

        if (! in_array($actionLabel, ['index', 'show'], true)) {
            $items[] = [
                'label' => match ($actionLabel) {
                    'create' => 'Create',
                    'edit' => 'Edit',
                    default => Str::headline($actionLabel),
                },
                'href' => null,
                'current' => true,
            ];
        } else {
            $lastIndex = array_key_last($items);

            if ($lastIndex !== null) {
                $items[$lastIndex]['current'] = true;
                $items[$lastIndex]['href'] = null;
            }
        }

        return $items;
    }

    protected function resolveModelLabel(string $routeName, string $id): ?string
    {
        if ($id === '') {
            return null;
        }

        /**
         * @var array<string, array{class: class-string<Model>, column: string}>
         */
        $map = [
            'accounts' => ['class' => Account::class, 'column' => 'name'],
            'contacts' => ['class' => Contact::class, 'column' => 'full_name'],
            'deals' => ['class' => Deal::class, 'column' => 'name'],
            'leads' => ['class' => Lead::class, 'column' => 'full_name'],
            'campaigns' => ['class' => Campaign::class, 'column' => 'name'],
            'cases' => ['class' => SupportCase::class, 'column' => 'number'],
            'quotes' => ['class' => Quote::class, 'column' => 'number'],
            'invoices' => ['class' => Invoice::class, 'column' => 'number'],
            'products' => ['class' => Product::class, 'column' => 'name'],
            'users' => ['class' => User::class, 'column' => 'full_name'],
            'roles' => ['class' => Role::class, 'column' => 'name'],
            'teams' => ['class' => Team::class, 'column' => 'name'],
        ];

        $prefix = Str::before($routeName, '.');
        $config = $map[$prefix] ?? null;

        if ($config === null || ! class_exists($config['class'])) {
            return null;
        }

        /** @var Model|null $model */
        $model = $config['class']::query()
            ->select(['id', $config['column']])
            ->find($id);

        if (! $model) {
            return null;
        }

        return (string) data_get($model, $config['column']);
    }
}

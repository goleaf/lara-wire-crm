<?php

namespace Modules\Core\Providers;

use Illuminate\Support\Facades\Schema;
use Modules\Core\Models\Setting;
use Nwidart\Modules\Support\ModuleServiceProvider;
use Throwable;

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

        $this->applyRuntimeSettings();

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

    private function applyRuntimeSettings(): void
    {
        if (! Schema::hasTable('settings')) {
            config([
                'app.name' => config('crm.app_name', config('app.name')),
                'app.timezone' => config('crm.timezone', config('app.timezone')),
            ]);

            return;
        }

        try {
            $settings = Setting::allValues();
        } catch (Throwable) {
            return;
        }

        $appName = (string) ($settings['crm.app_name'] ?? config('crm.app_name', config('app.name')));
        $timezone = (string) ($settings['crm.timezone'] ?? config('crm.timezone', config('app.timezone')));
        $currencyCode = (string) ($settings['crm.currency.code'] ?? config('crm.default_currency.code', config('crm.default_currency_code', 'USD')));
        $currencySymbol = (string) ($settings['crm.currency.symbol'] ?? config('crm.default_currency.symbol', '$'));
        $dateFormat = (string) ($settings['crm.date_format'] ?? config('crm.date_format', 'Y-m-d'));
        $paginationSize = (int) ($settings['crm.pagination_size'] ?? config('crm.pagination_size', 15));

        $companyName = (string) ($settings['crm.company.name'] ?? data_get(config('crm.company', []), 'name', ''));
        $companyAddress = (string) ($settings['crm.company.address'] ?? data_get(config('crm.company', []), 'address', ''));
        $companyPhone = (string) ($settings['crm.company.phone'] ?? data_get(config('crm.company', []), 'phone', ''));
        $companyEmail = (string) ($settings['crm.company.email'] ?? data_get(config('crm.company', []), 'email', ''));
        $companyVat = (string) ($settings['crm.company.vat_number'] ?? $settings['crm.company.vat'] ?? data_get(config('crm.company', []), 'vat_number', ''));
        $companyLogo = (string) ($settings['crm.company.logo'] ?? data_get(config('crm.company', []), 'logo', ''));
        $bankAccountName = (string) ($settings['crm.company.bank_details.account_name'] ?? data_get(config('crm.company', []), 'bank_details.account_name', ''));
        $bankIban = (string) ($settings['crm.company.bank_details.iban'] ?? data_get(config('crm.company', []), 'bank_details.iban', ''));
        $bankSwift = (string) ($settings['crm.company.bank_details.swift'] ?? data_get(config('crm.company', []), 'bank_details.swift', ''));

        config([
            'app.name' => $appName,
            'app.timezone' => $timezone,
            'crm.app_name' => $appName,
            'crm.timezone' => $timezone,
            'crm.date_format' => $dateFormat,
            'crm.pagination_size' => $paginationSize,
            'crm.default_currency.code' => $currencyCode,
            'crm.default_currency.symbol' => $currencySymbol,
            'crm.default_currency_code' => $currencyCode,
            'crm.company' => [
                'name' => $companyName,
                'address' => $companyAddress,
                'phone' => $companyPhone,
                'email' => $companyEmail,
                'logo' => $companyLogo,
                'vat' => $companyVat,
                'vat_number' => $companyVat,
                'bank_details' => [
                    'account_name' => $bankAccountName,
                    'iban' => $bankIban,
                    'swift' => $bankSwift,
                ],
            ],
        ]);
    }
}

<?php

namespace Modules\Core\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Modules\Core\Models\Setting;

class Settings extends Component
{
    use WithFileUploads;

    public string $app_name = '';

    public string $currency_symbol = '$';

    public string $currency_code = 'USD';

    public string $timezone = 'UTC';

    public string $date_format = 'Y-m-d';

    public int $pagination_size = 15;

    public ?TemporaryUploadedFile $logo = null;

    public bool $remove_logo = false;

    public string $company_name = '';

    public string $company_address = '';

    public string $company_phone = '';

    public string $company_email = '';

    public string $company_vat = '';

    public string $bank_account_name = '';

    public string $bank_iban = '';

    public string $bank_swift = '';

    public ?string $logo_path = null;

    public function mount(): void
    {
        abort_unless(auth()->check(), 403);
        abort_unless(auth()->user()?->hasPermission('view'), 403);

        $this->app_name = (string) Setting::getValue('crm.app_name', config('crm.app_name', config('app.name')));
        $this->currency_symbol = (string) Setting::getValue('crm.currency.symbol', config('crm.default_currency.symbol', '$'));
        $this->currency_code = (string) Setting::getValue('crm.currency.code', config('crm.default_currency.code', 'USD'));
        $this->timezone = (string) Setting::getValue('crm.timezone', config('crm.timezone', config('app.timezone')));
        $this->date_format = (string) Setting::getValue('crm.date_format', config('crm.date_format', 'Y-m-d'));
        $this->pagination_size = (int) Setting::getValue('crm.pagination_size', config('crm.pagination_size', 15));
        $this->logo_path = (string) Setting::getValue('crm.company.logo', data_get(config('crm.company', []), 'logo'));
        $this->company_name = (string) Setting::getValue('crm.company.name', data_get(config('crm.company', []), 'name', ''));
        $this->company_address = (string) Setting::getValue('crm.company.address', data_get(config('crm.company', []), 'address', ''));
        $this->company_phone = (string) Setting::getValue('crm.company.phone', data_get(config('crm.company', []), 'phone', ''));
        $this->company_email = (string) Setting::getValue('crm.company.email', data_get(config('crm.company', []), 'email', ''));
        $this->company_vat = (string) Setting::getValue('crm.company.vat_number', Setting::getValue('crm.company.vat', data_get(config('crm.company', []), 'vat_number', '')));
        $this->bank_account_name = (string) Setting::getValue('crm.company.bank_details.account_name', data_get(config('crm.company', []), 'bank_details.account_name', ''));
        $this->bank_iban = (string) Setting::getValue('crm.company.bank_details.iban', data_get(config('crm.company', []), 'bank_details.iban', ''));
        $this->bank_swift = (string) Setting::getValue('crm.company.bank_details.swift', data_get(config('crm.company', []), 'bank_details.swift', ''));
    }

    public function save(): void
    {
        abort_unless(auth()->user()?->hasPermission('edit'), 403);

        $validated = $this->validate([
            'app_name' => ['required', 'string', 'max:255'],
            'currency_symbol' => ['required', 'string', 'max:5'],
            'currency_code' => ['required', 'string', 'max:5'],
            'timezone' => ['required', 'string', 'max:120'],
            'date_format' => ['required', 'in:d/m/Y,m/d/Y,Y-m-d'],
            'pagination_size' => ['required', 'integer', 'in:10,15,25,50'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'remove_logo' => ['boolean'],
            'company_name' => ['nullable', 'string', 'max:255'],
            'company_address' => ['nullable', 'string', 'max:500'],
            'company_phone' => ['nullable', 'string', 'max:50'],
            'company_email' => ['nullable', 'email', 'max:255'],
            'company_vat' => ['nullable', 'string', 'max:100'],
            'bank_account_name' => ['nullable', 'string', 'max:255'],
            'bank_iban' => ['nullable', 'string', 'max:120'],
            'bank_swift' => ['nullable', 'string', 'max:120'],
        ]);

        if ($validated['remove_logo'] && $this->logo_path) {
            Storage::disk('public')->delete($this->logo_path);
            $this->logo_path = null;
            $this->logo = null;
        }

        if (! $validated['remove_logo'] && $this->logo) {
            $this->logo_path = $this->logo->store('crm/logo', 'public');
        }

        $currencyCode = strtoupper((string) $validated['currency_code']);
        $currencySymbol = trim((string) $validated['currency_symbol']);

        Setting::setValue('crm.app_name', $validated['app_name']);
        Setting::setValue('crm.currency.symbol', $currencySymbol);
        Setting::setValue('crm.currency.code', $currencyCode);
        Setting::setValue('crm.timezone', $validated['timezone']);
        Setting::setValue('crm.date_format', $validated['date_format']);
        Setting::setValue('crm.pagination_size', $validated['pagination_size'], 'integer');
        Setting::setValue('crm.company.logo', $this->logo_path ?? '');
        Setting::setValue('crm.company.name', $validated['company_name'] ?? '');
        Setting::setValue('crm.company.address', $validated['company_address'] ?? '');
        Setting::setValue('crm.company.phone', $validated['company_phone'] ?? '');
        Setting::setValue('crm.company.email', $validated['company_email'] ?? '');
        Setting::setValue('crm.company.vat', $validated['company_vat'] ?? '');
        Setting::setValue('crm.company.vat_number', $validated['company_vat'] ?? '');
        Setting::setValue('crm.company.bank_details.account_name', $validated['bank_account_name'] ?? '');
        Setting::setValue('crm.company.bank_details.iban', $validated['bank_iban'] ?? '');
        Setting::setValue('crm.company.bank_details.swift', $validated['bank_swift'] ?? '');

        $this->remove_logo = false;
        $this->currency_code = $currencyCode;
        $this->currency_symbol = $currencySymbol;

        $this->dispatch('flash', type: 'success', message: 'Settings updated.');
    }

    public function render(): View
    {
        return view('core::livewire.settings', [
            'dateFormats' => [
                'd/m/Y' => 'DD/MM/YYYY',
                'm/d/Y' => 'MM/DD/YYYY',
                'Y-m-d' => 'YYYY-MM-DD',
            ],
            'paginationSizes' => [10, 15, 25, 50],
            'timezones' => timezone_identifiers_list(),
        ])->extends('core::layouts.module', ['title' => 'Settings']);
    }
}

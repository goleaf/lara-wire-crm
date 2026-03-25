<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Modules\Activities\Models\Activity;
use Modules\Calendar\Models\CalendarEvent;
use Modules\Campaigns\Models\Campaign;
use Modules\Cases\Models\CaseComment;
use Modules\Cases\Models\SlaPolicy;
use Modules\Cases\Models\SupportCase;
use Modules\Contacts\Models\Account;
use Modules\Contacts\Models\Contact;
use Modules\Core\Models\AuditLog;
use Modules\Core\Models\Setting;
use Modules\Deals\Models\Deal;
use Modules\Deals\Models\Pipeline;
use Modules\Deals\Models\PipelineStage;
use Modules\Files\Models\CrmFile;
use Modules\Invoices\Models\Invoice;
use Modules\Invoices\Models\InvoiceLineItem;
use Modules\Invoices\Models\Payment;
use Modules\Leads\Models\Lead;
use Modules\Messaging\Models\Channel;
use Modules\Messaging\Models\Message;
use Modules\Notifications\Models\CrmNotification;
use Modules\Products\Models\Product;
use Modules\Products\Models\ProductCategory;
use Modules\Quotes\Models\Quote;
use Modules\Quotes\Models\QuoteLineItem;
use Modules\Reports\Models\Dashboard;
use Modules\Reports\Models\DashboardWidget;
use Modules\Reports\Models\Report;
use Modules\Users\Models\Role;
use Modules\Users\Models\Team;

test('database seeder populates all crm models with complete demo data', function () {
    $this->seed(DatabaseSeeder::class);

    $models = [
        Role::class,
        Team::class,
        User::class,
        CrmFile::class,
        ProductCategory::class,
        Product::class,
        CrmNotification::class,
        Account::class,
        Contact::class,
        Lead::class,
        Pipeline::class,
        PipelineStage::class,
        Deal::class,
        Activity::class,
        CalendarEvent::class,
        Channel::class,
        Message::class,
        Quote::class,
        QuoteLineItem::class,
        Invoice::class,
        InvoiceLineItem::class,
        Payment::class,
        Campaign::class,
        SlaPolicy::class,
        SupportCase::class,
        CaseComment::class,
        Report::class,
        Dashboard::class,
        DashboardWidget::class,
        Setting::class,
        AuditLog::class,
    ];

    foreach ($models as $modelClass) {
        expect($modelClass::query()->count())->toBeGreaterThan(0, $modelClass.' is not seeded');
    }

    expect(User::query()->whereNull('team_id')->count())->toBe(0);
    expect(User::query()->whereNull('last_login')->count())->toBe(0);
    expect(User::query()->whereNull('avatar_path')->count())->toBe(0);

    expect(Account::query()->whereNull('website')->count())->toBe(0);
    expect(Account::query()->whereNull('phone')->count())->toBe(0);
    expect(Account::query()->whereNull('email')->count())->toBe(0);
    expect(Account::query()->whereNull('shipping_address')->count())->toBe(0);
    expect(Account::query()->whereNull('annual_revenue')->count())->toBe(0);
    expect(Account::query()->whereNull('employee_count')->count())->toBe(0);

    expect(Contact::query()->whereNull('email')->count())->toBe(0);
    expect(Contact::query()->whereNull('phone')->count())->toBe(0);
    expect(Contact::query()->whereNull('mobile')->count())->toBe(0);
    expect(Contact::query()->whereNull('job_title')->count())->toBe(0);
    expect(Contact::query()->whereNull('department')->count())->toBe(0);
    expect(Contact::query()->whereNull('birthday')->count())->toBe(0);
    expect(Contact::query()->whereNull('notes')->count())->toBe(0);

    expect(Lead::query()->whereNull('company')->count())->toBe(0);
    expect(Lead::query()->whereNull('email')->count())->toBe(0);
    expect(Lead::query()->whereNull('phone')->count())->toBe(0);
    expect(Lead::query()->whereNull('campaign_id')->count())->toBe(0);
    expect(Lead::query()->whereNull('description')->count())->toBe(0);

    expect(Quote::query()->whereNull('pdf_path')->count())->toBe(0);
    expect(Invoice::query()->whereNull('pdf_path')->count())->toBe(0);
    expect(CrmNotification::query()->whereNull('body')->count())->toBe(0);
    expect(CrmNotification::query()->whereNull('action_url')->count())->toBe(0);
    expect(CrmFile::query()->whereNull('description')->count())->toBe(0);
    expect(CrmFile::query()->whereNull('related_to_type')->count())->toBe(0);
    expect(CrmFile::query()->whereNull('related_to_id')->count())->toBe(0);

    expect(Setting::query()->whereNull('value')->count())->toBe(0);
    expect(AuditLog::query()->whereNull('user_id')->count())->toBe(0);
    expect(AuditLog::query()->whereNull('ip_address')->count())->toBe(0);
});

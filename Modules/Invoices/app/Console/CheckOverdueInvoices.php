<?php

namespace Modules\Invoices\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Modules\Invoices\Models\Invoice;
use Modules\Notifications\Services\NotificationService;

class CheckOverdueInvoices extends Command
{
    protected $signature = 'invoices:check-overdue';

    protected $description = 'Mark unpaid invoices as overdue and notify owners.';

    public function handle(): int
    {
        $notificationsEnabled = class_exists(NotificationService::class);

        $invoices = Invoice::query()
            ->select(['id', 'owner_id', 'number', 'status', 'due_date'])
            ->with('owner:id,full_name,email,user_notification_preferences')
            ->whereIn('status', ['Issued', 'Partially Paid'])
            ->whereDate('due_date', '<', now()->toDateString())
            ->get();

        $updated = 0;

        foreach ($invoices as $invoice) {
            $invoice->forceFill(['status' => 'Overdue'])->save();
            $updated++;

            if (! $notificationsEnabled || ! $invoice->owner) {
                continue;
            }

            app(NotificationService::class)->send(
                $invoice->owner,
                'Payment Recorded',
                'Invoice overdue',
                "Invoice {$invoice->number} is overdue.",
                Invoice::class,
                (string) $invoice->id,
                Route::has('invoices.show') ? route('invoices.show', $invoice->id) : null
            );
        }

        $this->info("Invoices marked overdue: {$updated}");

        return self::SUCCESS;
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('number')->unique();
            $table->foreignUuid('quote_id')->nullable()->constrained('quotes')->nullOnDelete();
            $table->foreignUuid('deal_id')->nullable()->constrained('deals')->nullOnDelete();
            $table->foreignUuid('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignUuid('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignUuid('owner_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['Draft', 'Issued', 'Partially Paid', 'Paid', 'Overdue', 'Cancelled'])->default('Draft');
            $table->date('issue_date');
            $table->date('due_date');
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->enum('discount_type', ['Percentage', 'Fixed'])->default('Percentage');
            $table->decimal('discount_value', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->string('currency')->default(config('crm.default_currency.code', 'USD'));
            $table->string('pdf_path')->nullable();
            $table->timestamps();

            $table->index(['owner_id', 'status', 'due_date'], 'invoices_owner_status_due_idx');
            $table->index(['account_id', 'issue_date'], 'invoices_account_issue_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

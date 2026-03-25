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
        Schema::create('quotes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('number')->unique();
            $table->string('name');
            $table->foreignUuid('deal_id')->nullable()->constrained('deals')->nullOnDelete();
            $table->foreignUuid('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignUuid('account_id')->nullable()->constrained('accounts')->nullOnDelete();
            $table->foreignUuid('owner_id')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['Draft', 'Sent', 'Accepted', 'Rejected', 'Expired'])->default('Draft');
            $table->date('valid_until')->nullable();
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->enum('discount_type', ['Percentage', 'Fixed'])->default('Percentage');
            $table->decimal('discount_value', 15, 2)->default(0);
            $table->decimal('discount_amount', 15, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('total', 15, 2)->default(0);
            $table->string('currency')->default(config('crm.default_currency.code', 'USD'));
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('pdf_path')->nullable();
            $table->timestamps();

            $table->index(['owner_id', 'status', 'valid_until'], 'quotes_owner_status_valid_idx');
            $table->index(['deal_id', 'account_id'], 'quotes_deal_account_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotes');
    }
};

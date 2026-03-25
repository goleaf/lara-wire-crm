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
        Schema::create('deals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->foreignUuid('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->foreignUuid('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignUuid('owner_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('pipeline_id')->constrained('pipelines')->cascadeOnDelete();
            $table->uuid('stage_id')->index();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('currency')->default(config('crm.default_currency.code', 'USD'));
            $table->unsignedTinyInteger('probability')->default(0);
            $table->decimal('expected_revenue', 15, 2)->default(0);
            $table->date('close_date')->nullable();
            $table->enum('deal_type', ['New Business', 'Renewal', 'Upsell', 'Cross-sell'])->default('New Business');
            $table->enum('lost_reason', ['Price', 'Competitor', 'No Budget', 'No Decision', 'Other'])->nullable();
            $table->text('lost_notes')->nullable();
            $table->string('source')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['owner_id', 'close_date']);
            $table->index(['pipeline_id', 'stage_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};

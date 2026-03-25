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
        Schema::create('products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->decimal('unit_price', 15, 2);
            $table->decimal('cost_price', 15, 2)->default(0);
            $table->string('currency')->default(config('crm.default_currency_code', config('app.currency', 'USD')));
            $table->uuid('category_id')->nullable();
            $table->decimal('tax_rate', 5, 2)->default(0);
            $table->boolean('active')->default(true);
            $table->boolean('recurring')->default(false);
            $table->enum('billing_frequency', ['One-time', 'Monthly', 'Annual'])->nullable();
            $table->string('unit')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('category_id')->references('id')->on('product_categories')->nullOnDelete();
            $table->index(['active', 'recurring']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

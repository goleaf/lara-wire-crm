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
        Schema::create('accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->enum('industry', ['Technology', 'Finance', 'Retail', 'Healthcare', 'Manufacturing', 'Education', 'Real Estate', 'Other']);
            $table->enum('type', ['Customer', 'Partner', 'Prospect', 'Vendor']);
            $table->string('website')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->json('billing_address');
            $table->json('shipping_address')->nullable();
            $table->decimal('annual_revenue', 15, 2)->nullable();
            $table->integer('employee_count')->nullable();
            $table->uuid('owner_id');
            $table->uuid('parent_account_id')->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('parent_account_id')->references('id')->on('accounts')->nullOnDelete();

            $table->index(['type', 'industry']);
            $table->index('owner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};

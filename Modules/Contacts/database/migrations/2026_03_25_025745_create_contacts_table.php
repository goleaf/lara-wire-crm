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
        Schema::create('contacts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('job_title')->nullable();
            $table->string('department')->nullable();
            $table->uuid('account_id')->nullable();
            $table->uuid('owner_id');
            $table->enum('lead_source', ['Walk-in', 'Cold Call', 'Referral', 'Internal Form', 'Event', 'Other'])->default('Other');
            $table->boolean('do_not_contact')->default(false);
            $table->date('birthday')->nullable();
            $table->enum('preferred_channel', ['Phone', 'SMS', 'In-person'])->default('Phone');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('account_id')->references('id')->on('accounts')->nullOnDelete();
            $table->foreign('owner_id')->references('id')->on('users')->cascadeOnDelete();

            $table->index(['owner_id', 'lead_source']);
            $table->index('do_not_contact');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};

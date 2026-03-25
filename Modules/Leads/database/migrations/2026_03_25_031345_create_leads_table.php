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
        Schema::create('leads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('company')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->enum('lead_source', ['Walk-in', 'Cold Call', 'Referral', 'Internal Form', 'Event', 'Other'])->default('Other');
            $table->enum('status', ['New', 'Contacted', 'Qualified', 'Unqualified', 'Converted'])->default('New');
            $table->unsignedTinyInteger('score')->default(0);
            $table->enum('rating', ['Hot', 'Warm', 'Cold'])->default('Cold');
            $table->uuid('campaign_id')->nullable()->index();
            $table->foreignUuid('owner_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('converted')->default(false);
            $table->foreignUuid('converted_to_contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->uuid('converted_to_deal_id')->nullable()->index();
            $table->timestamp('converted_at')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['status', 'converted']);
            $table->index(['rating', 'score']);
            $table->index(['owner_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};

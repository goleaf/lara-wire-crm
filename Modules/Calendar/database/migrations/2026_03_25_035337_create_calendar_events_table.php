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
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->enum('type', ['Meeting', 'Demo', 'Follow-up', 'Reminder', 'Other']);
            $table->dateTime('start_at');
            $table->dateTime('end_at')->nullable();
            $table->boolean('all_day')->default(false);
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->foreignUuid('organizer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignUuid('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignUuid('deal_id')->nullable()->constrained('deals')->nullOnDelete();
            $table->unsignedSmallInteger('reminder_minutes')->nullable();
            $table->enum('recurrence', ['None', 'Daily', 'Weekly', 'Monthly'])->default('None');
            $table->date('recurrence_end_date')->nullable();
            $table->enum('status', ['Scheduled', 'Completed', 'Cancelled'])->default('Scheduled');
            $table->string('color')->nullable();
            $table->timestamps();

            $table->index(['start_at', 'end_at']);
            $table->index(['organizer_id', 'start_at']);
            $table->index(['contact_id', 'start_at']);
            $table->index(['deal_id', 'start_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};

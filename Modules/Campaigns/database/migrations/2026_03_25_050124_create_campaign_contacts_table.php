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
        Schema::create('campaign_contacts', function (Blueprint $table) {
            $table->foreignUuid('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->foreignUuid('contact_id')->constrained('contacts')->cascadeOnDelete();
            $table->timestamp('added_at')->nullable();
            $table->enum('status', ['Targeted', 'Contacted', 'Responded', 'Converted', 'Opted Out'])->default('Targeted');

            $table->primary(['campaign_id', 'contact_id']);
            $table->index(['campaign_id', 'status'], 'campaign_contacts_campaign_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_contacts');
    }
};

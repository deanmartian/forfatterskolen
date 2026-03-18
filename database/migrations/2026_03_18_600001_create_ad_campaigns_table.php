<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('platform'); // facebook, google
            $table->string('type'); // lead, retargeting, search, display
            $table->string('name');
            $table->unsignedBigInteger('free_webinar_id')->nullable();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->string('status')->default('draft'); // draft, active, paused, completed
            $table->decimal('daily_budget', 10, 2)->nullable();
            $table->string('external_campaign_id')->nullable();
            $table->string('external_adset_id')->nullable();
            $table->string('external_ad_id')->nullable();
            $table->string('external_form_id')->nullable();
            $table->json('config')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('stopped_at')->nullable();
            $table->timestamps();

            $table->index('platform');
            $table->index('status');
            $table->index('free_webinar_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_campaigns');
    }
};

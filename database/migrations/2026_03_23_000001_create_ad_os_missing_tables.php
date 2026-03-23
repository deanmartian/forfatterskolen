<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ad_action_log')) {
            Schema::create('ad_action_log', function (Blueprint $table) {
                $table->increments('id');
                $table->string('action_type', 50);
                $table->string('entity_type', 50)->nullable();
                $table->unsignedInteger('entity_id')->nullable();
                $table->text('description')->nullable();
                $table->json('metadata')->nullable();
                $table->string('initiated_by', 50)->default('system');
                $table->string('status', 20)->default('completed');
                $table->timestamps();
                $table->index(['action_type', 'created_at']);
            });
        }

        if (!Schema::hasTable('ad_daily_metrics')) {
            Schema::create('ad_daily_metrics', function (Blueprint $table) {
                $table->increments('id');
                $table->date('date');
                $table->string('entity_type', 50);
                $table->unsignedInteger('entity_id');
                $table->decimal('spend', 10, 2)->default(0);
                $table->integer('impressions')->default(0);
                $table->integer('clicks')->default(0);
                $table->integer('conversions')->default(0);
                $table->decimal('revenue', 10, 2)->default(0);
                $table->decimal('cpa', 10, 2)->nullable();
                $table->decimal('roas', 8, 2)->nullable();
                $table->decimal('ctr', 8, 4)->nullable();
                $table->decimal('cpc', 10, 2)->nullable();
                $table->timestamps();
                $table->unique(['date', 'entity_type', 'entity_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_daily_metrics');
        Schema::dropIfExists('ad_action_log');
    }
};

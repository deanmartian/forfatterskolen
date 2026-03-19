<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ad_strategy_profiles')) {
            Schema::create('ad_strategy_profiles', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->enum('automation_level', ['manual', 'assisted', 'supervised', 'full_operator'])->default('assisted');
                $table->enum('primary_goal', ['leads', 'purchases', 'webinar_signups', 'applications', 'traffic', 'awareness'])->default('leads');
                $table->decimal('monthly_budget', 12, 2)->nullable();
                $table->decimal('daily_budget_ceiling', 10, 2)->nullable();
                $table->decimal('target_cpa', 10, 2)->nullable();
                $table->decimal('target_roas', 8, 2)->nullable();
                $table->integer('min_conversion_volume')->nullable();
                $table->enum('risk_tolerance', ['low', 'medium', 'high'])->default('medium');
                $table->json('preferred_campaign_types')->nullable();
                $table->json('priority_products')->nullable();
                $table->boolean('is_active')->default(true);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ad_budget_policies')) {
            Schema::create('ad_budget_policies', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('strategy_profile_id')->index();
                $table->decimal('monthly_max', 12, 2);
                $table->decimal('daily_max', 10, 2);
                $table->decimal('max_increase_per_day_percent', 5, 2)->default(15.00);
                $table->decimal('max_increase_per_week_percent', 5, 2)->default(30.00);
                $table->decimal('max_single_campaign_budget', 10, 2)->nullable();
                $table->decimal('min_campaign_budget', 10, 2)->default(50.00);
                $table->boolean('allow_auto_rebalance')->default(false);
                $table->timestamps();
                $table->foreign('strategy_profile_id')->references('id')->on('ad_strategy_profiles')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('ad_risk_policies')) {
            Schema::create('ad_risk_policies', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('strategy_profile_id')->index();
                $table->decimal('stop_loss_daily', 10, 2)->nullable();
                $table->decimal('stop_loss_weekly', 10, 2)->nullable();
                $table->decimal('max_cpa_threshold', 10, 2)->nullable();
                $table->decimal('min_roas_threshold', 8, 2)->nullable();
                $table->decimal('max_spend_without_conversion', 10, 2)->nullable();
                $table->integer('min_impressions_before_eval')->default(1000);
                $table->integer('min_clicks_before_eval')->default(50);
                $table->integer('min_days_before_eval')->default(3);
                $table->boolean('auto_pause_losers')->default(false);
                $table->boolean('auto_scale_winners')->default(false);
                $table->timestamps();
                $table->foreign('strategy_profile_id')->references('id')->on('ad_strategy_profiles')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('ad_approval_policies')) {
            Schema::create('ad_approval_policies', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('strategy_profile_id')->index();
                $table->boolean('approve_new_campaigns')->default(true);
                $table->boolean('approve_budget_increase')->default(true);
                $table->decimal('budget_increase_approval_threshold_percent', 5, 2)->default(20.00);
                $table->boolean('approve_pause_campaigns')->default(false);
                $table->boolean('approve_new_creatives')->default(true);
                $table->boolean('approve_targeting_changes')->default(true);
                $table->boolean('approve_major_reallocation')->default(true);
                $table->decimal('major_reallocation_threshold_percent', 5, 2)->default(25.00);
                $table->json('auto_approved_action_types')->nullable();
                $table->boolean('emergency_kill_switch')->default(false);
                $table->timestamps();
                $table->foreign('strategy_profile_id')->references('id')->on('ad_strategy_profiles')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('ad_accounts')) {
            Schema::create('ad_accounts', function (Blueprint $table) {
                $table->increments('id');
                $table->enum('platform', ['facebook', 'google', 'tiktok', 'linkedin']);
                $table->string('account_id');
                $table->string('account_name')->nullable();
                $table->json('credentials')->nullable();
                $table->enum('status', ['active', 'paused', 'disconnected', 'error'])->default('active');
                $table->json('sync_state')->nullable();
                $table->timestamp('last_synced_at')->nullable();
                $table->unsignedInteger('strategy_profile_id')->nullable()->index();
                $table->timestamps();
                $table->foreign('strategy_profile_id')->references('id')->on('ad_strategy_profiles')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_approval_policies');
        Schema::dropIfExists('ad_risk_policies');
        Schema::dropIfExists('ad_budget_policies');
        Schema::dropIfExists('ad_accounts');
        Schema::dropIfExists('ad_strategy_profiles');
    }
};

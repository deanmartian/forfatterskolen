<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ad_metric_snapshots')) {
            Schema::create('ad_metric_snapshots', function (Blueprint $table) {
                $table->increments('id');
                $table->enum('level', ['campaign', 'ad_set', 'ad']);
                $table->unsignedInteger('reference_id');
                $table->unsignedInteger('campaign_id')->nullable()->index();
                $table->date('date');
                $table->integer('impressions')->default(0);
                $table->integer('clicks')->default(0);
                $table->decimal('spend', 10, 2)->default(0);
                $table->integer('conversions')->default(0);
                $table->decimal('cpa', 10, 2)->nullable();
                $table->decimal('roas', 8, 2)->nullable();
                $table->decimal('ctr', 6, 4)->nullable();
                $table->decimal('cpc', 8, 2)->nullable();
                $table->decimal('cpm', 8, 2)->nullable();
                $table->integer('leads')->default(0);
                $table->decimal('revenue', 12, 2)->default(0);
                $table->json('platform_data')->nullable();
                $table->timestamps();
                $table->index(['level', 'reference_id', 'date']);
                $table->foreign('campaign_id')->references('id')->on('ad_campaigns')->onDelete('set null');
            });
        }

        if (!Schema::hasTable('ad_rules')) {
            Schema::create('ad_rules', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('metric');
                $table->enum('operator', ['<', '<=', '>', '>=', '==', '!=']);
                $table->decimal('threshold', 12, 4);
                $table->integer('min_data_points')->default(0);
                $table->decimal('min_spend_threshold', 10, 2)->nullable();
                $table->integer('evaluation_window_days')->default(7);
                $table->string('action');
                $table->enum('risk_level', ['low', 'medium', 'high', 'critical'])->default('medium');
                $table->boolean('auto_apply')->default(false);
                $table->enum('scope', ['campaign', 'ad_set', 'ad', 'account'])->default('campaign');
                $table->unsignedInteger('campaign_id')->nullable();
                $table->integer('priority')->default(50);
                $table->boolean('is_active')->default(true);
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ad_rule_runs')) {
            Schema::create('ad_rule_runs', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('rule_id')->index();
                $table->boolean('triggered')->default(false);
                $table->string('target_type')->nullable();
                $table->unsignedInteger('target_id')->nullable();
                $table->decimal('actual_value', 12, 4)->nullable();
                $table->string('result')->nullable();
                $table->json('details')->nullable();
                $table->timestamps();
                $table->foreign('rule_id')->references('id')->on('ad_rules')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('ad_ai_decisions')) {
            Schema::create('ad_ai_decisions', function (Blueprint $table) {
                $table->increments('id');
                $table->string('decision_type');
                $table->decimal('confidence', 3, 2)->nullable();
                $table->text('reasoning_summary');
                $table->enum('risk_level', ['low', 'medium', 'high', 'critical'])->default('medium');
                $table->boolean('requires_approval')->default(true);
                $table->json('proposed_action');
                $table->json('context_data')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected', 'executed', 'failed', 'expired'])->default('pending');
                $table->unsignedInteger('campaign_id')->nullable()->index();
                $table->unsignedInteger('rule_id')->nullable();
                $table->timestamp('executed_at')->nullable();
                $table->json('execution_result')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ad_approval_requests')) {
            Schema::create('ad_approval_requests', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('decision_id')->index();
                $table->json('action_payload');
                $table->text('ai_summary');
                $table->enum('status', ['pending', 'approved', 'rejected', 'expired'])->default('pending');
                $table->unsignedInteger('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->text('reviewer_notes')->nullable();
                $table->json('execution_result')->nullable();
                $table->timestamps();
                $table->foreign('decision_id')->references('id')->on('ad_ai_decisions')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('ad_action_logs')) {
            Schema::create('ad_action_logs', function (Blueprint $table) {
                $table->increments('id');
                $table->string('action_type');
                $table->string('target_type')->nullable();
                $table->unsignedInteger('target_id')->nullable();
                $table->json('payload')->nullable();
                $table->json('result')->nullable();
                $table->enum('triggered_by', ['human', 'ai', 'rule', 'system'])->default('system');
                $table->unsignedInteger('user_id')->nullable();
                $table->unsignedInteger('rule_id')->nullable();
                $table->unsignedInteger('decision_id')->nullable();
                $table->enum('status', ['success', 'failed', 'pending'])->default('success');
                $table->text('error_message')->nullable();
                $table->timestamps();
                $table->index(['target_type', 'target_id']);
                $table->index('action_type');
            });
        }

        if (!Schema::hasTable('ad_experiments')) {
            Schema::create('ad_experiments', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->text('hypothesis')->nullable();
                $table->unsignedInteger('campaign_id')->nullable()->index();
                $table->enum('status', ['draft', 'running', 'completed', 'cancelled'])->default('draft');
                $table->unsignedInteger('winner_variant_id')->nullable();
                $table->date('started_at')->nullable();
                $table->date('ended_at')->nullable();
                $table->json('results_summary')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ad_experiment_variants')) {
            Schema::create('ad_experiment_variants', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('experiment_id')->index();
                $table->unsignedInteger('creative_id')->nullable()->index();
                $table->unsignedInteger('ad_id')->nullable()->index();
                $table->string('label')->nullable();
                $table->json('metrics')->nullable();
                $table->boolean('is_winner')->default(false);
                $table->boolean('is_control')->default(false);
                $table->timestamps();
                $table->foreign('experiment_id')->references('id')->on('ad_experiments')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('ad_asset_links')) {
            Schema::create('ad_asset_links', function (Blueprint $table) {
                $table->increments('id');
                $table->string('asset_path');
                $table->enum('asset_type', ['image', 'video', 'document', 'other'])->default('image');
                $table->json('tags')->nullable();
                $table->string('product_reference')->nullable();
                $table->unsignedInteger('campaign_id')->nullable();
                $table->unsignedInteger('creative_id')->nullable();
                $table->decimal('performance_score', 5, 2)->nullable();
                $table->decimal('relevance_score', 5, 2)->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('ad_sync_runs')) {
            Schema::create('ad_sync_runs', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('account_id')->nullable()->index();
                $table->enum('platform', ['facebook', 'google', 'tiktok', 'linkedin']);
                $table->enum('type', ['campaigns', 'metrics', 'creatives', 'full']);
                $table->enum('status', ['running', 'completed', 'failed'])->default('running');
                $table->integer('records_synced')->default(0);
                $table->json('details')->nullable();
                $table->text('error_message')->nullable();
                $table->timestamp('started_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ad_sync_runs');
        Schema::dropIfExists('ad_asset_links');
        Schema::dropIfExists('ad_experiment_variants');
        Schema::dropIfExists('ad_experiments');
        Schema::dropIfExists('ad_action_logs');
        Schema::dropIfExists('ad_approval_requests');
        Schema::dropIfExists('ad_ai_decisions');
        Schema::dropIfExists('ad_rule_runs');
        Schema::dropIfExists('ad_rules');
        Schema::dropIfExists('ad_metric_snapshots');
    }
};

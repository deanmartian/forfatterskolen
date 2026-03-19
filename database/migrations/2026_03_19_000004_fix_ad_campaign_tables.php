<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Drop any partially created tables from failed migration
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Schema::dropIfExists('ad_creative_variants');
        Schema::dropIfExists('ad_ads');
        Schema::dropIfExists('ad_creatives');
        Schema::dropIfExists('ad_ad_sets');
        Schema::dropIfExists('ad_campaigns');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Recreate without inline foreign keys to avoid charset/collation mismatches
        Schema::create('ad_campaigns', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('account_id')->index();
            $table->enum('platform', ['facebook', 'google', 'tiktok', 'linkedin']);
            $table->string('external_id')->nullable()->index();
            $table->string('name');
            $table->enum('objective', ['leads', 'conversions', 'traffic', 'awareness', 'engagement', 'sales', 'app_installs'])->default('leads');
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'active', 'paused', 'completed', 'archived', 'error'])->default('draft');
            $table->decimal('daily_budget', 10, 2)->nullable();
            $table->decimal('total_budget', 12, 2)->nullable();
            $table->decimal('spent_total', 12, 2)->default(0);
            $table->enum('automation_level', ['manual', 'assisted', 'supervised', 'full_operator'])->nullable();
            $table->string('landing_page')->nullable();
            $table->string('product_reference')->nullable();
            $table->json('targeting')->nullable();
            $table->json('tracking')->nullable();
            $table->json('platform_meta')->nullable();
            $table->text('ai_brief')->nullable();
            $table->text('ai_notes')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ad_ad_sets', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('campaign_id')->index();
            $table->string('external_id')->nullable()->index();
            $table->string('name');
            $table->enum('status', ['draft', 'active', 'paused', 'completed', 'archived', 'error'])->default('draft');
            $table->decimal('daily_budget', 10, 2)->nullable();
            $table->json('targeting')->nullable();
            $table->json('platform_meta')->nullable();
            $table->timestamps();
        });

        Schema::create('ad_creatives', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->enum('platform', ['facebook', 'google', 'tiktok', 'linkedin', 'universal']);
            $table->json('headlines')->nullable();
            $table->json('descriptions')->nullable();
            $table->text('primary_text')->nullable();
            $table->string('cta')->nullable();
            $table->string('display_url')->nullable();
            $table->string('final_url')->nullable();
            $table->json('asset_ids')->nullable();
            $table->unsignedInteger('variant_of')->nullable()->index();
            $table->integer('generation')->default(1);
            $table->decimal('performance_score', 5, 2)->nullable();
            $table->enum('status', ['draft', 'active', 'paused', 'archived'])->default('draft');
            $table->json('ai_metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('ad_ads', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ad_set_id')->index();
            $table->unsignedInteger('creative_id')->nullable()->index();
            $table->string('external_id')->nullable()->index();
            $table->string('name');
            $table->enum('status', ['draft', 'active', 'paused', 'completed', 'archived', 'error'])->default('draft');
            $table->json('platform_meta')->nullable();
            $table->timestamps();
        });

        Schema::create('ad_creative_variants', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('creative_id')->index();
            $table->unsignedInteger('parent_id')->nullable()->index();
            $table->unsignedInteger('experiment_id')->nullable()->index();
            $table->integer('generation')->default(1);
            $table->string('variant_label')->nullable();
            $table->decimal('performance_score', 5, 2)->nullable();
            $table->json('metrics_snapshot')->nullable();
            $table->enum('status', ['testing', 'winner', 'loser', 'archived'])->default('testing');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Schema::dropIfExists('ad_creative_variants');
        Schema::dropIfExists('ad_ads');
        Schema::dropIfExists('ad_creatives');
        Schema::dropIfExists('ad_ad_sets');
        Schema::dropIfExists('ad_campaigns');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ad_campaigns', function (Blueprint $table) {
            if (!Schema::hasColumn('ad_campaigns', 'external_campaign_id')) {
                $table->string('external_campaign_id')->nullable()->after('daily_budget');
            }
            if (!Schema::hasColumn('ad_campaigns', 'external_adset_id')) {
                $table->string('external_adset_id')->nullable()->after('external_campaign_id');
            }
            if (!Schema::hasColumn('ad_campaigns', 'external_ad_id')) {
                $table->string('external_ad_id')->nullable()->after('external_adset_id');
            }
            if (!Schema::hasColumn('ad_campaigns', 'external_form_id')) {
                $table->string('external_form_id')->nullable()->after('external_ad_id');
            }
        });
    }

    public function down(): void
    {
        // Don't drop columns in down
    }
};

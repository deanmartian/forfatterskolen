<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('free_webinars', function (Blueprint $table) {
            // BigMarker auto-oppretting
            $table->string('bigmarker_conference_id')->nullable()->after('gtwebinar_id');
            $table->string('bigmarker_status')->nullable()->after('bigmarker_conference_id');

            // Facebook Lead Ads
            $table->string('facebook_campaign_id')->nullable();
            $table->string('facebook_adset_id')->nullable();
            $table->string('facebook_ad_id')->nullable();
            $table->string('facebook_lead_form_id')->nullable();
            $table->string('facebook_ad_status')->nullable(); // paused, active, completed
            $table->integer('facebook_daily_budget')->nullable();
            $table->integer('facebook_impressions')->default(0);
            $table->integer('facebook_clicks')->default(0);
            $table->decimal('facebook_spend', 10, 2)->default(0);
            $table->integer('facebook_leads_count')->default(0);

            // Google Ads
            $table->string('google_search_campaign_id')->nullable();
            $table->string('google_display_campaign_id')->nullable();
            $table->string('google_ad_status')->nullable();
            $table->integer('google_daily_budget')->nullable();
            $table->integer('google_impressions')->default(0);
            $table->integer('google_clicks')->default(0);
            $table->decimal('google_spend', 10, 2)->default(0);
            $table->integer('google_conversions')->default(0);

            // Felles
            $table->timestamp('ad_stats_updated_at')->nullable();

            // Annonsemateriale
            $table->string('ad_headline')->nullable();
            $table->text('ad_text')->nullable();
            $table->string('ad_image')->nullable();
            $table->text('google_keywords')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('free_webinars', function (Blueprint $table) {
            $table->dropColumn([
                'bigmarker_conference_id', 'bigmarker_status',
                'facebook_campaign_id', 'facebook_adset_id', 'facebook_ad_id',
                'facebook_lead_form_id', 'facebook_ad_status', 'facebook_daily_budget',
                'facebook_impressions', 'facebook_clicks', 'facebook_spend', 'facebook_leads_count',
                'google_search_campaign_id', 'google_display_campaign_id',
                'google_ad_status', 'google_daily_budget',
                'google_impressions', 'google_clicks', 'google_spend', 'google_conversions',
                'ad_stats_updated_at', 'ad_headline', 'ad_text', 'ad_image', 'google_keywords',
            ]);
        });
    }
};

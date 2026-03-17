<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FreeWebinar extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'free_webinars';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description', 'learning_points', 'target_audience', 'replay_url',
        'start_date', 'image', 'gtwebinar_id',
        // BigMarker
        'bigmarker_conference_id', 'bigmarker_status',
        // Facebook Ads
        'facebook_campaign_id', 'facebook_adset_id', 'facebook_ad_id',
        'facebook_lead_form_id', 'facebook_ad_status', 'facebook_daily_budget',
        'facebook_impressions', 'facebook_clicks', 'facebook_spend', 'facebook_leads_count',
        // Google Ads
        'google_search_campaign_id', 'google_display_campaign_id',
        'google_ad_status', 'google_daily_budget',
        'google_impressions', 'google_clicks', 'google_spend', 'google_conversions',
        // Felles
        'ad_stats_updated_at', 'ad_headline', 'ad_text', 'ad_image', 'google_keywords',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'ad_stats_updated_at' => 'datetime',
        'facebook_spend' => 'decimal:2',
        'google_spend' => 'decimal:2',
    ];

    /**
     * Get the webinar presenters
     */
    public function webinar_presenters(): HasMany
    {
        return $this->hasMany(\App\FreeWebinarPresenter::class);
    }

    /**
     * On delete, remove also the files
     */
    public static function boot()
    {
        parent::boot();

        // if the row is deleted, delete also the document for that row
        FreeWebinar::deleted(function ($record) {
            $file = public_path($record->image);
            if (\File::isFile($file)) {
                \File::delete($file);
            }
        });
    }
}

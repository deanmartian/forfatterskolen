<?php

namespace App;

use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ShopManuscriptsTaken extends Model
{
    use Loggable;

    protected $table = 'shop_manuscripts_taken';

    protected $fillable = ['user_id', 'shop_manuscript_id', 'file', 'is_active', 'words', 'feedback_user_id',
        'expected_finish', 'manuscript_uploaded_date', 'genre', 'description', 'is_manuscript_locked', 'synopsis',
        'coaching_time_later', 'is_welcome_email_sent', 'gift_purchase_id'];

    /* protected $with = ['shop_manuscript', 'user', 'receivedWelcomeEmail', 'receivedExpectedFinishEmail',
        'receivedAdminFeedbackEmail', 'receivedFollowUpEmail']; */

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(\App\ShopManuscriptTakenFeedback::class, 'shop_manuscript_taken_id')->orderBy('created_at', 'desc');
    }

    public function shop_manuscript(): BelongsTo
    {
        return $this->belongsTo(\App\ShopManuscript::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(\App\ShopManuscriptComment::class, 'shop_manuscript_taken_id')->orderBy('created_at', 'desc');
    }

    public function getCreatedAtAttribute($value)
    {
        return date_format(date_create($value), 'M d, Y h:i a');
    }

    public function getStatusAttribute()
    {
        if (! $this->attributes['is_active']) {
            return 'Not started';
        }
        $file = $this->attributes['file'];
        $feedbacks = $this->feedbacks->count();
        $approved = 0;
        if ($feedbacks > 0) {
            $approved = $this->feedbacks->first()->approved;
        }
        // $this->feedbacks->each(function($feedback) {
        //     $approved = $feedback->approved;
        // });

        if ($file && $feedbacks > 0 && $approved == 1) {
            return 'Finished';
        } elseif ($file && $feedbacks > 0 && $approved == 0) {
            return 'Pending';
        } elseif ($file && $feedbacks == 0) {
            return 'Started';
        } elseif (! $file) {
            return 'Not started';
        }
    }

    public function getExpectedFinishAttribute($value)
    {
        return $value ? date_format(date_create($value), 'd.m.Y') : null;
    }

    public function getEditorExpectedFinishAttribute($value)
    {
        return $value ? date_format(date_create($value), 'd.m.Y') : null;
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(\App\User::class, 'feedback_user_id');
    }

    public function receivedWelcomeEmail(): HasOne
    {
        return $this->hasOne(\App\EmailHistory::class, 'parent_id', 'id')
            ->where('parent', 'shop-manuscripts-taken-welcome')->latest();
    }

    public function receivedExpectedFinishEmail(): HasOne
    {
        return $this->hasOne(\App\EmailHistory::class, 'parent_id', 'id')
            ->where('parent', 'shop-manuscripts-taken-expected-finish')->latest();
    }

    public function receivedAdminFeedbackEmail(): HasOne
    {
        return $this->hasOne(\App\EmailHistory::class, 'parent_id', 'id')
            ->where('parent', 'shop-manuscripts-taken-admin-feedback')->latest();
    }

    public function receivedFollowUpEmail(): HasOne
    {
        return $this->hasOne(\App\EmailHistory::class, 'parent_id', 'id')
            ->where('parent', 'shop-manuscripts-taken-follow-up')->latest();
    }

    public function requests(): HasMany
    {
        return $this->hasMany(\App\RequestToEditor::class, 'manuscript_id', 'id')
            ->whereHas('editor')
            ->where('from_type', 'shop-manuscript');
    }
}

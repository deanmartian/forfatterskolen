<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopManuscriptsTaken extends Model
{
    protected $table = 'shop_manuscripts_taken';
    protected $fillable = ['user_id', 'shop_manuscript_id', 'file', 'is_active', 'words', 'feedback_user_id',
        'expected_finish', 'manuscript_uploaded_date', 'genre', 'description', 'is_manuscript_locked','synopsis',
        'coaching_time_later', 'is_welcome_email_sent'];

    protected $with = ['shop_manuscript', 'user', 'receivedWelcomeEmail', 'receivedExpectedFinishEmail',
        'receivedAdminFeedbackEmail', 'receivedFollowUpEmail'];

    
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function feedbacks()
    {
        return $this->hasMany('App\ShopManuscriptTakenFeedback', 'shop_manuscript_taken_id')->orderBy('created_at', 'desc');
    }


    public function shop_manuscript()
    {
        return $this->belongsTo('App\ShopManuscript');
    }

    public function comments()
    {
        return $this->hasMany('App\ShopManuscriptComment', 'shop_manuscript_taken_id')->orderBy('created_at', 'desc');
    }
    
    public function getCreatedAtAttribute($value)
    {
        return date_format(date_create($value), 'M d, Y h:i a');
    }



    public function getStatusAttribute()
    {
        if( !$this->attributes['is_active'] ) return "Not started";
        $file = $this->attributes['file'];
        $feedbacks = $this->feedbacks->count();
        $approved = 0;
        if($feedbacks > 0){
            $approved = $this->feedbacks->first()->approved;
        }
        // $this->feedbacks->each(function($feedback) {
        //     $approved = $feedback->approved;
        // });

        if( $file && $feedbacks > 0 && $approved == 1) :
            return "Finished";
        elseif( $file && $feedbacks > 0 && $approved == 0 ) :
            return "Pending";
        elseif( $file && $feedbacks == 0 ) :
            return "Started";
        elseif( !$file ) :
            return "Not started";
        endif;
    }

    public function getExpectedFinishAttribute($value) {
        return $value ? date_format(date_create($value), 'd.m.Y') : NULL;
    }

    
    public function admin(){
        return $this->belongsTo('App\User', 'feedback_user_id');
    }

    public function receivedWelcomeEmail()
    {
        return $this->hasOne('App\EmailHistory', 'parent_id', 'id')
            ->where('parent', 'shop-manuscripts-taken-welcome')->latest();
    }

    public function receivedExpectedFinishEmail()
    {
        return $this->hasOne('App\EmailHistory', 'parent_id', 'id')
            ->where('parent', 'shop-manuscripts-taken-expected-finish')->latest();
    }

    public function receivedAdminFeedbackEmail()
    {
        return $this->hasOne('App\EmailHistory', 'parent_id', 'id')
            ->where('parent', 'shop-manuscripts-taken-admin-feedback')->latest();
    }

    public function receivedFollowUpEmail()
    {
        return $this->hasOne('App\EmailHistory', 'parent_id', 'id')
            ->where('parent', 'shop-manuscripts-taken-follow-up')->latest();
    }
}
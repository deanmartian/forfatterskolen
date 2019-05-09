<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ShopManuscriptsTaken extends Model
{
    protected $table = 'shop_manuscripts_taken';
    protected $fillable = ['user_id', 'shop_manuscript_id', 'file', 'is_active', 'words', 'feedback_user_id',
        'expected_finish', 'manuscript_uploaded_date', 'genre', 'description', 'is_manuscript_locked','synopsis',
        'coaching_time_later'];

    
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
        if( $file && $feedbacks > 0 ) :
            return "Finished";
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
}
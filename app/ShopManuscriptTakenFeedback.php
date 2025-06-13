<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopManuscriptTakenFeedback extends Model
{
    protected $table = 'shop_manuscript_taken_feedbacks';

    protected $fillable = ['shop_manuscript_taken_id', 'filename', 'grade', 'notes', 'hours_worked', 'notes_to_head_editor'];

    public function shop_manuscript_taken(): BelongsTo
    {
        return $this->belongsTo(\App\ShopManuscriptsTaken::class, 'shop_manuscript_taken_id');
    }

    public function getFilenameAttribute($value)
    {
        return json_decode($value);
    }

    public function getCreatedAtAttribute($value)
    {
        return date_format(date_create($value), 'M d, Y h:i a');
    }
}

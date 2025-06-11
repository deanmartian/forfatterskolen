<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PilotReaderBookInvitationLink extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pilot_reader_book_invitation_links';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['book_id', 'link_token', 'enabled'];

    public function books()
    {
        return $this->belongsToMany('App\PilotReaderBook', 'pilot_reader_book_invitation_links', 'id', 'book_id');
    }
}

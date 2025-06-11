<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SosChildren extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sos_children';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'description', 'bottom_description', 'video_url', 'is_main_description', 'is_primary'];

    /**
     * Get the record that is marked as main description
     *
     * @return Model|null|static
     */
    public function getMainDescription()
    {
        return $this->where('is_main_description', 1)->first();
    }

    /**
     * Get the record that is marked as primary video
     *
     * @return Model|null|static
     */
    public function getPrimaryVideo()
    {
        return $this->where('is_primary', 1)->first();
    }

    public function getVideoRecords()
    {
        return $this->where('is_main_description', 0)->get();
    }
}

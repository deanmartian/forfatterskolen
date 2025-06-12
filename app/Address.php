<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use Loggable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'addresses';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'phone', 'street', 'city', 'country', 'zip', 'vipps_phone_number'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\User::class);
    }
}

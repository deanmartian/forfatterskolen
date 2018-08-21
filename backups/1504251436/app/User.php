<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use File;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'role', 'gender', 'birthday', 'profile_image',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getAddressAttribute()
    {
        $address = \App\Address::where('user_id', $this->attributes['id'])->first();

        if( !$address ) :
            $empty_address = new \App\Address();
            return $empty_address;
        endif;

        return $address;
    }

    public function getManuscriptsAttribute()
    {
        $coursesTaken = $this->coursesTaken->pluck('id')->toArray();
        $manuscripts = \App\Manuscript::whereIn('coursetaken_id', $coursesTaken)->orderBy('created_at', 'desc')->get();
        return $manuscripts;
    }

    public function coursesTaken()
    {
        return $this->hasMany('App\CoursesTaken')->orderBy('created_at', 'desc');
    }

    public function shopManuscriptsTaken()
    {
        return $this->hasMany('App\ShopManuscriptsTaken')->orderBy('created_at', 'desc');
    }


    public function workshopsTaken()
    {
        return $this->hasMany('App\WorkshopsTaken')->orderBy('created_at', 'desc');
    }

    public function invoices()
    {
        return $this->hasMany('App\Invoice')->orderBy('created_at', 'desc');
    }

    public function getProfileImageAttribute($value)
    {
        $image = substr($this->attributes['profile_image'], 1);
        if(File::exists($image)) return $value;

        return asset('images/user.png');
        
    }


    public function getFullNameAttribute()
    {
        return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
    }


    public function getHasProfileImageAttribute()
    {
        $image = substr($this->attributes['profile_image'], 1);
        return File::exists($image);
    }
}

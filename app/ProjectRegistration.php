<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProjectRegistration extends Model
{

    protected $fillable = ['project_id', 'field', 'value', 'type', 'book_price', 'in_storage'];

    protected $appends = ['isbn_type'];

    protected $isbnTypes = [
        1 => 'Trykt, innbundet (hard perm/hardcover)',
        2 => 'Trykt, heftet (myk perm/softcover)',
        3 => 'E-bok (ePub)',
        4 => 'E-bok (PDF)',
        5 => 'Lydbok (digital)',
        6 => 'Lydbok (CD)',
    ];

    public function scopeIsbns( $query )
    {
        $query->where('field', 'isbn');
    }

    public function scopeCentralDistributions( $query )
    {
        $query->where('field', 'central-distribution');
    }

    public function scopeMentorBookBase( $query )
    {
        $query->where('field', 'mentor-book-base');
    }

    public function scopeUploadFilesToMentorBookBase( $query )
    {
        $query->where('field', 'upload-files-to-mentor-book-base');
    }

    public function isbnTypes()
    {
        return $this->isbnTypes;
    }

    public function detail()
    {
        return $this->hasOne('\App\StorageDetail', 'project_book_id', 'id');
    }

    public function various()
    {
        return $this->hasOne('\App\StorageVarious', 'project_book_id', 'id');
    }

    public function distributionCosts()
    {
        return $this->hasMany('\App\StorageDistributionCost', 'project_book_id', 'id');
    }

    public function getIsbnTypeAttribute()
    {
        return $this->isbnTypes()[$this->attributes['type']] ?? NULL;
    }

    public function totalDistributionCost() {
        return $this->distributionCosts()->sum('amount');
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProjectRegistration extends Model
{
    protected $fillable = ['project_id', 'parent_id', 'field', 'value', 'type', 'book_price', 'in_storage'];

    protected $appends = ['isbn_type'];

    protected $isbnTypes = [
        1 => 'Trykt, innbundet (hard perm/hardcover)',
        2 => 'Trykt, heftet (myk perm/softcover)',
        3 => 'E-bok (ePub)',
        4 => 'E-bok (PDF)',
        5 => 'Lydbok (digital)',
        6 => 'Lydbok (CD)',
    ];

    #[Scope]
    protected function isbns($query)
    {
        $query->where('field', 'isbn');
    }

    #[Scope]
    protected function centralDistributions($query)
    {
        $query->where('field', 'central-distribution');
    }

    #[Scope]
    protected function mentorBookBase($query)
    {
        $query->where('field', 'mentor-book-base');
    }

    #[Scope]
    protected function uploadFilesToMentorBookBase($query)
    {
        $query->where('field', 'upload-files-to-mentor-book-base');
    }

    public function isbnTypes()
    {
        return $this->isbnTypes;
    }

    public function detail(): HasOne
    {
        return $this->hasOne(\App\StorageDetail::class, 'project_book_id', 'id');
    }

    public function various(): HasOne
    {
        return $this->hasOne(\App\StorageVarious::class, 'project_book_id', 'id');
    }

    public function distributionCosts(): HasMany
    {
        return $this->hasMany(\App\StorageDistributionCost::class, 'project_book_id', 'id');
    }

    public function getIsbnTypeAttribute()
    {
        return $this->isbnTypes()[$this->attributes['type']] ?? null;
    }

    public function totalDistributionCost()
    {
        return $this->distributionCosts()->sum('amount');
    }

    public function childMentorBookBase()
    {
        return $this->hasOne(ProjectRegistration::class, 'parent_id')->where('field', 'mentor-book-base');
    }

    public function childUploadMentorBookBase()
    {
        return $this->hasOne(ProjectRegistration::class, 'parent_id')->where('field', 'upload-files-to-mentor-book-base');
    }
}

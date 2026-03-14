<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseGroup extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'description',
        'icon',
    ];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_group_members')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}

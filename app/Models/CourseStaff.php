<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseStaff extends Model
{
    protected $table = 'course_staff';

    protected $fillable = [
        'course_id', 'user_id', 'role', 'student_user_id',
        'webinar_id', 'start_date', 'end_date', 'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function course()
    {
        return $this->belongsTo(\App\Course::class);
    }

    public function staff()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    public function student()
    {
        return $this->belongsTo(\App\User::class, 'student_user_id');
    }

    public function webinar()
    {
        return $this->belongsTo(\App\Webinar::class);
    }

    public function scopeEditors($query)
    {
        return $query->where('role', 'editor');
    }

    public function scopeCourseLeaders($query)
    {
        return $query->where('role', 'course_leader');
    }

    public static function roleLabel(string $role): string
    {
        return match ($role) {
            'editor' => 'Redaktør',
            'mentor' => 'Mentor',
            'guest_editor' => 'Gjesteredaktør',
            'course_leader' => 'Kursholder',
            'webinar_host' => 'Webinar-vert',
            default => ucfirst($role),
        };
    }
}

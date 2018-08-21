<?php

namespace App\Policies;

use App\User;
use App\CoursesTaken;
use Illuminate\Auth\Access\HandlesAuthorization;

class LearnerPolicy
{
    use HandlesAuthorization;

    public function participateCourse(User $user, CoursesTaken $courseTaken)
    {
        return CoursesTaken::where('id', $courseTaken->id)->where('is_active', true)->where('user_id', $user->id)->first();
    }
}



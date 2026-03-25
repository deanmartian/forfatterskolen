<?php

namespace App\Http\Controllers\Frontend;

use App\CoursesTaken;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PabyggTreffController extends Controller
{
    private const COURSE_ID = 120;

    /**
     * Show the Påbyggingstreff signup/info page.
     */
    public function index()
    {
        $user = Auth::user();
        $courseTaken = $this->getCourseTaken($user);

        if (!$courseTaken) {
            abort(403, 'Du har ikke tilgang til denne siden.');
        }

        return view('frontend.learner.pabygg-treff', [
            'courseTaken' => $courseTaken,
        ]);
    }

    /**
     * Handle signup/change for Påbyggingstreff.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pabygg_treff_day' => 'required|in:friday,saturday',
        ]);

        $user = Auth::user();
        $courseTaken = $this->getCourseTaken($user);

        if (!$courseTaken) {
            abort(403, 'Du har ikke tilgang til denne siden.');
        }

        $courseTaken->pabygg_treff_day = $request->input('pabygg_treff_day');
        $courseTaken->save();

        return redirect()->route('learner.pabygg-treff')->with('success', 'Påmelding registrert!');
    }

    /**
     * Admin view: show all signups for Påbyggingstreff.
     */
    public function adminIndex()
    {
        $signups = CoursesTaken::whereHas('package', function ($q) {
            $q->where('course_id', self::COURSE_ID);
        })
            ->whereNotNull('pabygg_treff_day')
            ->with(['user', 'package'])
            ->orderBy('pabygg_treff_day')
            ->get();

        $allEnrolled = CoursesTaken::whereHas('package', function ($q) {
            $q->where('course_id', self::COURSE_ID);
        })
            ->with(['user', 'package'])
            ->where('is_active', 1)
            ->get();

        return view('backend.pabygg-treff', [
            'signups' => $signups,
            'allEnrolled' => $allEnrolled,
        ]);
    }

    /**
     * Get the active course_taken record for course 120 for a user.
     */
    private function getCourseTaken($user)
    {
        return CoursesTaken::where('user_id', $user->id)
            ->where('is_active', 1)
            ->whereHas('package', function ($q) {
                $q->where('course_id', self::COURSE_ID);
            })
            ->first();
    }
}

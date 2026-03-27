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
    private const MAX_PER_DAY = 9;

    public function index()
    {
        $user = Auth::user();
        $courseTaken = $this->getCourseTaken($user);

        if (!$courseTaken) {
            abort(403, 'Du har ikke tilgang til denne siden.');
        }

        return view('frontend.learner.pabygg-treff', [
            'courseTaken' => $courseTaken,
            'fridayCount' => $this->countForDay('friday'),
            'saturdayCount' => $this->countForDay('saturday'),
            'maxPerDay' => self::MAX_PER_DAY,
        ]);
    }

    /**
     * Handle signup/change for Påbyggingstreff.
     */
    public function store(Request $request)
    {
        $request->validate([
            'pabygg_treff_day' => 'required|in:friday,saturday,digital',
        ]);

        $user = Auth::user();
        $courseTaken = $this->getCourseTaken($user);

        if (!$courseTaken) {
            abort(403, 'Du har ikke tilgang til denne siden.');
        }

        $chosenDay = $request->input('pabygg_treff_day');

        // Don't count current user if they're switching days
        // Digital has no limit
        $currentCount = $this->countForDay($chosenDay);
        if ($chosenDay === 'digital') {
            // No limit for digital
        } elseif ($courseTaken->pabygg_treff_day === $chosenDay) {
            // Already on this day, no change needed
        } elseif ($currentCount >= self::MAX_PER_DAY) {
            return redirect()->route('learner.pabygg-treff')
                ->withErrors(['pabygg_treff_day' => ucfirst($chosenDay === 'friday' ? 'Fredag' : 'Lørdag') . ' er fullt (maks ' . self::MAX_PER_DAY . ' deltakere). Velg en annen dag.']);
        }

        $courseTaken->pabygg_treff_day = $chosenDay;
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
    private function countForDay(string $day): int
    {
        return CoursesTaken::whereHas('package', function ($q) {
            $q->where('course_id', self::COURSE_ID);
        })
            ->where('pabygg_treff_day', $day)
            ->where('is_active', 1)
            ->count();
    }

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

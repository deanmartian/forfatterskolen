<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\PilotReaderReaderProfile;
use App\UserPreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PilotReaderAccountController extends Controller
{
    public function index(): View
    {
        return view('frontend.learner.pilot-reader.account.index');
    }

    /**
     * Get the user preference
     *
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public function viewUserPreferences()
    {
        return UserPreference::where('user_id', Auth::user()->id)->first();
    }

    /**
     * Set preference for a user
     */
    public function setUserPreferences(Request $request): JsonResponse
    {
        $data = $request->all();
        $user = Auth::user();
        $user_preferences = UserPreference::where('user_id', $user->id)->first();
        if ($user_preferences && $request->has(['role', 'joined_reader_community'])) {
            if (! $user_preferences->update($data)) {
                return response()->json(['error' => 'Opss. Something went wrong'], 500);
            }
        } else {
            $data['user_id'] = $user->id;
            if (! UserPreference::create($data)) {
                return response()->json(['error' => 'Opss. Something went wrong'], 500);
            }
        }

        return response()->json(['success' => 'User preferences saved.'], 200);
    }

    /**
     * Display the reader profile page
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function readerProfile(): View
    {
        return view('frontend.learner.pilot-reader.account.reader-profile');
    }

    /**
     * Get the reader profile
     *
     * @return \Illuminate\Database\Eloquent\Model|null|static
     */
    public function viewReaderProfile()
    {
        return PilotReaderReaderProfile::where('user_id', Auth::user()->id)->first();
    }

    public function setReaderProfile(Request $request): JsonResponse
    {
        $data = $request->all();
        $author = Auth::user();
        $reader_profile = PilotReaderReaderProfile::where('user_id', $author->id)->first();
        if ($reader_profile) {
            $data['availability'] = (int) $request->exists('availability');
            if (! $reader_profile->update($data)) {
                return response()->json(['error' => 'Opss. Something went wrong'], 500);
            }
        } else {
            $data['user_id'] = $author->id;
            if (! PilotReaderReaderProfile::create($data)) {
                return response()->json(['error' => 'Opss. Something went wrong'], 500);
            }
        }

        return response()->json(['success' => 'Reader Profile saved.'], 200);
    }
}

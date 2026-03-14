<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasApiUser;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    use HasApiUser;

    public function index()
    {
        $profiles = Profile::with('user')->get();

        return response()->json($profiles);
    }

    public function show(Request $request)
    {
        $userId = $this->getApiUserId($request);

        $profile = Profile::with('user')
            ->where('user_id', $userId)
            ->first();

        if (!$profile) {
            // Auto-create profile if it doesn't exist
            $user = $this->getApiUser($request);
            $profile = Profile::create([
                'id' => Str::uuid(),
                'user_id' => $userId,
                'name' => $user->first_name . ' ' . $user->last_name,
            ]);
            $profile->load('user');
        }

        return response()->json($profile);
    }

    public function showById($id)
    {
        $profile = Profile::with('user')
            ->where('user_id', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        return response()->json($profile);
    }

    public function update(Request $request)
    {
        $userId = $this->getApiUserId($request);

        $profile = Profile::where('user_id', $userId)->first();

        if (!$profile) {
            $user = $this->getApiUser($request);
            $profile = Profile::create([
                'id' => Str::uuid(),
                'user_id' => $userId,
                'name' => $user->first_name . ' ' . $user->last_name,
            ]);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string',
            'author_name' => 'nullable|string',
            'avatar_url' => 'nullable|string',
            'bio' => 'nullable|string',
            'genres' => 'nullable|array',
            'writing_interests' => 'nullable|array',
            'current_project' => 'nullable|string',
        ]);

        $profile->update($validated);

        return response()->json($profile);
    }
}

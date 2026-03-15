<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasApiUser;
use App\Models\CourseGroup;
use App\Models\Post;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseGroupController extends Controller
{
    use HasApiUser;

    public function index(Request $request)
    {
        $groups = CourseGroup::with('members')->get();
        $userId = $this->getApiUserId($request);

        // Add membership status for current user
        foreach ($groups as $group) {
            $group->is_member = $group->members->contains('id', $userId);
        }

        return response()->json($groups);
    }

    public function store(Request $request)
    {
        $userId = $this->getApiUserId($request);

        if (!$this->isAdmin($userId)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        $group = CourseGroup::create([
            'id' => Str::uuid(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? '📚',
        ]);

        return response()->json($group, 201);
    }

    public function show(Request $request, $id)
    {
        $group = CourseGroup::with('members.profile')->findOrFail($id);
        $userId = $this->getApiUserId($request);

        $group->is_member = $group->members->contains('id', $userId);

        return response()->json($group);
    }

    public function update(Request $request, $id)
    {
        $userId = $this->getApiUserId($request);

        if (!$this->isAdmin($userId)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $group = CourseGroup::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        $group->update($validated);

        return response()->json($group);
    }

    public function destroy(Request $request, $id)
    {
        $userId = $this->getApiUserId($request);

        if (!$this->isAdmin($userId)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $group = CourseGroup::findOrFail($id);
        $group->delete();

        return response()->json(['message' => 'Course group deleted'], 200);
    }

    public function join(Request $request, $id)
    {
        $group = CourseGroup::findOrFail($id);
        $userId = $this->getApiUserId($request);

        if (!$group->members->contains('id', $userId)) {
            $group->members()->attach($userId, ['role' => 'member']);
        }

        return response()->json(['message' => 'Joined group successfully']);
    }

    public function leave(Request $request, $id)
    {
        $group = CourseGroup::findOrFail($id);
        $userId = $this->getApiUserId($request);

        $group->members()->detach($userId);

        return response()->json(['message' => 'Left group successfully']);
    }

    public function getPosts($id)
    {
        $posts = Post::with(['user.profile', 'reactions', 'comments'])
            ->where('course_group_id', $id)
            ->orderByDesc('pinned')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($posts);
    }

    public function getMembers($id)
    {
        $group = CourseGroup::with('members.profile')->findOrFail($id);

        return response()->json($group->members);
    }

    private function isAdmin(int $userId): bool
    {
        $profile = Profile::where('user_id', $userId)->first();
        return $profile && $profile->badge === 'admin';
    }
}

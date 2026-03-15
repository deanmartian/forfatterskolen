<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasApiUser;
use App\Models\ManuscriptExcerpt;
use App\Models\ManuscriptFeedback;
use App\Models\ManuscriptProject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ManuscriptController extends Controller
{
    use HasApiUser;

    // Projects
    public function index()
    {
        $projects = ManuscriptProject::with(['user.profile', 'excerpts', 'followers'])
            ->orderByDesc('created_at')
            ->get();

        return response()->json($projects);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'genre' => 'required|string',
            'description' => 'nullable|string',
            'word_count' => 'sometimes|integer',
            'status' => 'sometimes|string',
        ]);

        $userId = $this->getApiUserId($request);

        $project = ManuscriptProject::create([
            'id' => Str::uuid(),
            'user_id' => $userId,
            'title' => $validated['title'],
            'genre' => $validated['genre'],
            'description' => $validated['description'] ?? null,
            'word_count' => $validated['word_count'] ?? 0,
            'status' => $validated['status'] ?? 'pågår',
        ]);

        $project->load(['user.profile', 'excerpts', 'followers']);

        return response()->json($project, 201);
    }

    public function show($id)
    {
        $project = ManuscriptProject::with(['user.profile', 'excerpts.user.profile', 'followers'])
            ->findOrFail($id);

        return response()->json($project);
    }

    public function update(Request $request, $id)
    {
        $project = ManuscriptProject::findOrFail($id);
        $userId = $this->getApiUserId($request);

        if ($project->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string',
            'genre' => 'sometimes|string',
            'description' => 'nullable|string',
            'word_count' => 'sometimes|integer',
            'status' => 'sometimes|string',
        ]);

        $project->update($validated);
        $project->load(['user.profile', 'excerpts', 'followers']);

        return response()->json($project);
    }

    public function destroy(Request $request, $id)
    {
        $project = ManuscriptProject::findOrFail($id);
        $userId = $this->getApiUserId($request);

        if ($project->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $project->delete();

        return response()->json(['message' => 'Project deleted'], 200);
    }

    // Excerpts
    public function getExcerpts($projectId)
    {
        $excerpts = ManuscriptExcerpt::with(['user.profile', 'feedback'])
            ->where('project_id', $projectId)
            ->orderByDesc('created_at')
            ->get();

        return response()->json($excerpts);
    }

    public function storeExcerpt(Request $request, $projectId)
    {
        $project = ManuscriptProject::findOrFail($projectId);
        $userId = $this->getApiUserId($request);

        if ($project->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'word_count' => 'sometimes|integer',
        ]);

        $excerpt = ManuscriptExcerpt::create([
            'id' => Str::uuid(),
            'project_id' => $projectId,
            'user_id' => $userId,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'word_count' => $validated['word_count'] ?? str_word_count($validated['content']),
        ]);

        $excerpt->load(['user.profile', 'feedback']);

        return response()->json($excerpt, 201);
    }

    public function updateExcerpt(Request $request, $id)
    {
        $excerpt = ManuscriptExcerpt::findOrFail($id);
        $userId = $this->getApiUserId($request);

        if ($excerpt->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string',
            'content' => 'sometimes|string',
            'word_count' => 'sometimes|integer',
        ]);

        $excerpt->update($validated);
        $excerpt->load(['user.profile', 'feedback']);

        return response()->json($excerpt);
    }

    public function destroyExcerpt(Request $request, $id)
    {
        $excerpt = ManuscriptExcerpt::findOrFail($id);
        $userId = $this->getApiUserId($request);

        if ($excerpt->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $excerpt->delete();

        return response()->json(['message' => 'Excerpt deleted'], 200);
    }

    // Feedback
    public function getFeedback($excerptId)
    {
        $feedback = ManuscriptFeedback::with('user.profile')
            ->where('excerpt_id', $excerptId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($feedback);
    }

    public function storeFeedback(Request $request, $excerptId)
    {
        $excerpt = ManuscriptExcerpt::findOrFail($excerptId);
        $userId = $this->getApiUserId($request);

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $feedback = ManuscriptFeedback::create([
            'id' => Str::uuid(),
            'excerpt_id' => $excerptId,
            'user_id' => $userId,
            'content' => $validated['content'],
        ]);

        $feedback->load('user.profile');

        return response()->json($feedback, 201);
    }

    public function updateFeedback(Request $request, $id)
    {
        $feedback = ManuscriptFeedback::findOrFail($id);
        $userId = $this->getApiUserId($request);

        if ($feedback->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $feedback->update($validated);
        $feedback->load('user.profile');

        return response()->json($feedback);
    }

    public function destroyFeedback(Request $request, $id)
    {
        $feedback = ManuscriptFeedback::findOrFail($id);
        $userId = $this->getApiUserId($request);

        if ($feedback->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $feedback->delete();

        return response()->json(['message' => 'Feedback deleted'], 200);
    }

    // Followers
    public function follow(Request $request, $id)
    {
        $project = ManuscriptProject::findOrFail($id);
        $userId = $this->getApiUserId($request);

        if (!$project->followers->contains('id', $userId)) {
            $project->followers()->attach($userId);
        }

        return response()->json(['message' => 'Following project']);
    }

    public function unfollow(Request $request, $id)
    {
        $project = ManuscriptProject::findOrFail($id);
        $userId = $this->getApiUserId($request);

        $project->followers()->detach($userId);

        return response()->json(['message' => 'Unfollowed project']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasApiUser;
use App\Models\Notification;
use App\Models\Post;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    use HasApiUser;

    public function index(Request $request)
    {
        $query = Post::with(['user.profile', 'reactions', 'comments'])
            ->whereNull('course_group_id');

        if ($request->has('course_group_id')) {
            $query->where('course_group_id', $request->course_group_id);
        }

        $posts = $query->orderByDesc('pinned')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'image_url' => 'nullable|string',
            'course_group_id' => 'nullable|uuid',
        ]);

        $userId = $this->getApiUserId($request);

        $post = Post::create([
            'id' => Str::uuid(),
            'user_id' => $userId,
            'content' => $validated['content'],
            'image_url' => $validated['image_url'] ?? null,
            'course_group_id' => $validated['course_group_id'] ?? null,
        ]);

        // Check for @mentions and create notifications
        $this->handleMentions($post, $userId);

        $post->load(['user.profile', 'reactions', 'comments']);

        return response()->json($post, 201);
    }

    public function show($id)
    {
        $post = Post::with(['user.profile', 'reactions', 'comments.user.profile'])
            ->findOrFail($id);

        return response()->json($post);
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $userId = $this->getApiUserId($request);

        // Check authorization (owner or admin)
        if ($post->user_id !== $userId && !$this->isAdmin($userId)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'content' => 'sometimes|string',
            'image_url' => 'nullable|string',
            'pinned' => 'sometimes|boolean',
        ]);

        $post->update($validated);
        $post->load(['user.profile', 'reactions', 'comments']);

        return response()->json($post);
    }

    public function destroy(Request $request, $id)
    {
        $post = Post::findOrFail($id);
        $userId = $this->getApiUserId($request);

        // Check authorization (owner or admin)
        if ($post->user_id !== $userId && !$this->isAdmin($userId)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted'], 200);
    }

    public function pin(Request $request, $id)
    {
        $userId = $this->getApiUserId($request);

        if (!$this->isAdmin($userId)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $post = Post::findOrFail($id);
        $post->pinned = !$post->pinned;
        $post->save();

        return response()->json($post);
    }

    private function isAdmin(int $userId): bool
    {
        $profile = Profile::where('user_id', $userId)->first();
        return $profile && $profile->badge === 'admin';
    }

    private function handleMentions(Post $post, int $currentUserId): void
    {
        // Extract @mentions from content
        preg_match_all('/@(\w+)/', $post->content, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $mentionedName) {
                // Find user by name
                $profile = Profile::where('name', 'like', $mentionedName)->first();

                if ($profile && $profile->user_id !== $currentUserId) {
                    Notification::create([
                        'id' => Str::uuid(),
                        'user_id' => $profile->user_id,
                        'type' => 'mention',
                        'content' => $this->getApiUser(request())->first_name . ' mentioned you in a post',
                        'from_user_id' => $currentUserId,
                        'link' => '/community/' . $post->id,
                    ]);
                }
            }
        }
    }
}

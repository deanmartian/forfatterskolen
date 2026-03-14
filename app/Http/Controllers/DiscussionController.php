<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasApiUser;
use App\Models\Discussion;
use App\Models\DiscussionReply;
use App\Models\Notification;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DiscussionController extends Controller
{
    use HasApiUser;

    public function index(Request $request)
    {
        $query = Discussion::with(['user.profile', 'replies']);

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $discussions = $query->orderByDesc('pinned')
            ->orderByDesc('created_at')
            ->get();

        return response()->json($discussions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'content' => 'required|string',
            'category' => 'required|string',
        ]);

        $userId = $this->getApiUserId($request);

        $discussion = Discussion::create([
            'id' => Str::uuid(),
            'user_id' => $userId,
            'title' => $validated['title'],
            'content' => $validated['content'],
            'category' => $validated['category'],
        ]);

        $discussion->load(['user.profile', 'replies']);

        return response()->json($discussion, 201);
    }

    public function show($id)
    {
        $discussion = Discussion::with(['user.profile', 'replies.user.profile'])
            ->findOrFail($id);

        return response()->json($discussion);
    }

    public function update(Request $request, $id)
    {
        $discussion = Discussion::findOrFail($id);
        $userId = $this->getApiUserId($request);

        if ($discussion->user_id !== $userId && !$this->isAdmin($userId)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string',
            'content' => 'sometimes|string',
            'category' => 'sometimes|string',
            'pinned' => 'sometimes|boolean',
        ]);

        $discussion->update($validated);
        $discussion->load(['user.profile', 'replies']);

        return response()->json($discussion);
    }

    public function destroy(Request $request, $id)
    {
        $discussion = Discussion::findOrFail($id);
        $userId = $this->getApiUserId($request);

        if ($discussion->user_id !== $userId && !$this->isAdmin($userId)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $discussion->delete();

        return response()->json(['message' => 'Discussion deleted'], 200);
    }

    public function pin(Request $request, $id)
    {
        $userId = $this->getApiUserId($request);

        if (!$this->isAdmin($userId)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $discussion = Discussion::findOrFail($id);
        $discussion->pinned = !$discussion->pinned;
        $discussion->save();

        return response()->json($discussion);
    }

    public function getReplies($discussionId)
    {
        $replies = DiscussionReply::with('user.profile')
            ->where('discussion_id', $discussionId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($replies);
    }

    public function storeReply(Request $request, $discussionId)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $discussion = Discussion::findOrFail($discussionId);
        $userId = $this->getApiUserId($request);
        $user = $this->getApiUser($request);

        $reply = DiscussionReply::create([
            'id' => Str::uuid(),
            'discussion_id' => $discussionId,
            'user_id' => $userId,
            'content' => $validated['content'],
        ]);

        // Notify discussion owner about reply
        if ($discussion->user_id !== $userId) {
            Notification::create([
                'id' => Str::uuid(),
                'user_id' => $discussion->user_id,
                'type' => 'reply',
                'content' => ($user->first_name ?? 'Someone') . ' replied to your discussion',
                'from_user_id' => $userId,
                'link' => '/community/' . $discussionId,
            ]);
        }

        $reply->load('user.profile');

        return response()->json($reply, 201);
    }

    public function updateReply(Request $request, $id)
    {
        $reply = DiscussionReply::findOrFail($id);
        $userId = $this->getApiUserId($request);

        if ($reply->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $reply->update($validated);
        $reply->load('user.profile');

        return response()->json($reply);
    }

    public function destroyReply(Request $request, $id)
    {
        $reply = DiscussionReply::findOrFail($id);
        $userId = $this->getApiUserId($request);

        if ($reply->user_id !== $userId && !$this->isAdmin($userId)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $reply->delete();

        return response()->json(['message' => 'Reply deleted'], 200);
    }

    private function isAdmin(int $userId): bool
    {
        $profile = Profile::where('user_id', $userId)->first();
        return $profile && $profile->badge === 'admin';
    }
}

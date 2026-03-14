<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasApiUser;
use App\Models\Notification;
use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostCommentController extends Controller
{
    use HasApiUser;

    public function index($postId)
    {
        $comments = PostComment::with('user.profile')
            ->where('post_id', $postId)
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($comments);
    }

    public function store(Request $request, $postId)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $post = Post::findOrFail($postId);
        $userId = $this->getApiUserId($request);
        $user = $this->getApiUser($request);

        $comment = PostComment::create([
            'id' => Str::uuid(),
            'post_id' => $postId,
            'user_id' => $userId,
            'content' => $validated['content'],
        ]);

        // Notify post owner about reply (if not self-reply)
        if ($post->user_id !== $userId) {
            Notification::create([
                'id' => Str::uuid(),
                'user_id' => $post->user_id,
                'type' => 'reply',
                'content' => ($user->first_name ?? 'Someone') . ' replied to your post',
                'from_user_id' => $userId,
                'link' => '/community/' . $postId,
            ]);
        }

        $comment->load('user.profile');

        return response()->json($comment, 201);
    }

    public function update(Request $request, $id)
    {
        $comment = PostComment::findOrFail($id);
        $userId = $this->getApiUserId($request);

        if ($comment->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $comment->update($validated);
        $comment->load('user.profile');

        return response()->json($comment);
    }

    public function destroy(Request $request, $id)
    {
        $comment = PostComment::findOrFail($id);
        $userId = $this->getApiUserId($request);

        if ($comment->user_id !== $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(['message' => 'Comment deleted'], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\HasApiUser;
use App\Models\Notification;
use App\Models\Post;
use App\Models\PostReaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostReactionController extends Controller
{
    use HasApiUser;

    public function index($postId)
    {
        $reactions = PostReaction::with('user.profile')
            ->where('post_id', $postId)
            ->get();

        return response()->json($reactions);
    }

    public function toggle(Request $request, $postId)
    {
        $validated = $request->validate([
            'reaction' => 'sometimes|string',
        ]);

        $reaction = $validated['reaction'] ?? 'like';
        $post = Post::findOrFail($postId);
        $userId = $this->getApiUserId($request);
        $user = $this->getApiUser($request);

        $existingReaction = PostReaction::where('post_id', $postId)
            ->where('user_id', $userId)
            ->where('reaction', $reaction)
            ->first();

        if ($existingReaction) {
            // Remove reaction
            $existingReaction->delete();
            return response()->json(['message' => 'Reaction removed', 'removed' => true]);
        } else {
            // Add reaction
            $newReaction = PostReaction::create([
                'id' => Str::uuid(),
                'post_id' => $postId,
                'user_id' => $userId,
                'reaction' => $reaction,
            ]);

            // Notify post owner about like (if not self-like)
            if ($reaction === 'like' && $post->user_id !== $userId) {
                Notification::create([
                    'id' => Str::uuid(),
                    'user_id' => $post->user_id,
                    'type' => 'like',
                    'content' => ($user->first_name ?? 'Someone') . ' liked your post',
                    'from_user_id' => $userId,
                    'link' => '/community/' . $postId,
                ]);
            }

            $newReaction->load('user.profile');
            return response()->json(['message' => 'Reaction added', 'reaction' => $newReaction], 201);
        }
    }
}

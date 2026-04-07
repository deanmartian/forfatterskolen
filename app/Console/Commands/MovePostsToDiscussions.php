<?php

namespace App\Console\Commands;

use App\Models\Discussion;
use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MovePostsToDiscussions extends Command
{
    protected $signature = 'community:move-posts-to-discussions';
    protected $description = 'Move non-pinned, non-bot community posts to discussions';

    public function handle(): int
    {
        $posts = Post::where('pinned', false)
            ->where(function ($q) {
                $q->where('is_bot_post', false)->orWhereNull('is_bot_post');
            })
            ->whereNull('course_group_id') // Only general feed posts, not course group posts
            ->with('user')
            ->get();

        $this->info("Fant {$posts->count()} innlegg å flytte.");

        $moved = 0;
        foreach ($posts as $post) {
            // Generate a title from content (first line or first 60 chars)
            $content = $post->content;
            $firstLine = strtok($content, "\n");
            $title = Str::limit(strip_tags($firstLine), 80, '...');
            if (strlen($title) < 5) {
                $title = Str::limit(strip_tags($content), 80, '...');
            }

            Discussion::create([
                'id' => Str::uuid(),
                'user_id' => $post->user_id,
                'title' => $title,
                'content' => $content,
                'image_url' => $post->image_url,
                'category' => 'Generelt',
                'pinned' => false,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
            ]);

            $post->delete();
            $moved++;
        }

        $this->info("Ferdig! Flyttet {$moved} innlegg til diskusjoner.");
        return self::SUCCESS;
    }
}

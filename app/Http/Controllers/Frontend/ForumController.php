<?php

namespace App\Http\Controllers\Frontend;

use App\ForumCategory;
use App\ForumPost;
use App\ForumThread;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    /**
     * Display the forum index with categories and recent threads.
     */
    public function index()
    {
        $categories = ForumCategory::orderBy('sort_order')->get();

        // If no categories exist yet, show the general view with all threads
        $threads = ForumThread::with(['user', 'category', 'latestPost.user'])
            ->withCount('posts')
            ->orderByDesc('is_pinned')
            ->latest()
            ->paginate(20);

        return view('frontend.learner.forum.index', compact('categories', 'threads'));
    }

    /**
     * Display threads for a specific category.
     */
    public function category($id)
    {
        $category = ForumCategory::findOrFail($id);
        $categories = ForumCategory::orderBy('sort_order')->get();

        $threads = ForumThread::with(['user', 'latestPost.user'])
            ->withCount('posts')
            ->where('forum_category_id', $id)
            ->orderByDesc('is_pinned')
            ->latest()
            ->paginate(20);

        return view('frontend.learner.forum.index', compact('categories', 'threads', 'category'));
    }

    /**
     * Show the create thread form.
     */
    public function createThread()
    {
        $categories = ForumCategory::orderBy('sort_order')->get();

        return view('frontend.learner.forum.create-thread', compact('categories'));
    }

    /**
     * Store a new thread.
     */
    public function storeThread(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'forum_category_id' => 'required|exists:forum_categories,id',
        ]);

        $thread = ForumThread::create([
            'forum_category_id' => $request->forum_category_id,
            'user_id' => Auth::id(),
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return redirect()->route('learner.forum.thread', $thread->id)
            ->withErrors(['Tråden ble opprettet.'], 'success');
    }

    /**
     * Display a thread with its posts.
     */
    public function showThread($id)
    {
        $thread = ForumThread::with(['user', 'category'])->findOrFail($id);
        $posts = ForumPost::with('user')
            ->where('forum_thread_id', $id)
            ->oldest()
            ->paginate(20);

        return view('frontend.learner.forum.thread', compact('thread', 'posts'));
    }

    /**
     * Store a new reply/post to a thread.
     */
    public function storePost(Request $request, $threadId)
    {
        $thread = ForumThread::findOrFail($threadId);

        if ($thread->is_locked) {
            return redirect()->route('learner.forum.thread', $thread->id)
                ->withErrors(['Denne tråden er låst.']);
        }

        $request->validate([
            'body' => 'required|string',
        ]);

        ForumPost::create([
            'forum_thread_id' => $thread->id,
            'user_id' => Auth::id(),
            'body' => $request->body,
        ]);

        return redirect()->route('learner.forum.thread', $thread->id)
            ->withErrors(['Svaret ditt ble publisert.'], 'success');
    }

    /**
     * Edit a thread (only by the author).
     */
    public function editThread($id)
    {
        $thread = ForumThread::where('user_id', Auth::id())->findOrFail($id);
        $categories = ForumCategory::orderBy('sort_order')->get();

        return view('frontend.learner.forum.edit-thread', compact('thread', 'categories'));
    }

    /**
     * Update a thread (only by the author).
     */
    public function updateThread(Request $request, $id)
    {
        $thread = ForumThread::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'forum_category_id' => 'required|exists:forum_categories,id',
        ]);

        $thread->update($request->only('title', 'body', 'forum_category_id'));

        return redirect()->route('learner.forum.thread', $thread->id)
            ->withErrors(['Tråden ble oppdatert.'], 'success');
    }

    /**
     * Delete a thread (only by the author).
     */
    public function deleteThread($id)
    {
        $thread = ForumThread::where('user_id', Auth::id())->findOrFail($id);
        $thread->delete();

        return redirect()->route('learner.forum.index')
            ->withErrors(['Tråden ble slettet.'], 'success');
    }

    /**
     * Edit a post (only by the author).
     */
    public function editPost(Request $request, $id)
    {
        $post = ForumPost::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'body' => 'required|string',
        ]);

        $post->update(['body' => $request->body]);

        return redirect()->route('learner.forum.thread', $post->forum_thread_id)
            ->withErrors(['Innlegget ble oppdatert.'], 'success');
    }

    /**
     * Delete a post (only by the author).
     */
    public function deletePost($id)
    {
        $post = ForumPost::where('user_id', Auth::id())->findOrFail($id);
        $threadId = $post->forum_thread_id;
        $post->delete();

        return redirect()->route('learner.forum.thread', $threadId)
            ->withErrors(['Innlegget ble slettet.'], 'success');
    }
}

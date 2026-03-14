<?php

namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Models\CourseGroup;
use App\Models\Discussion;
use App\Models\Post;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\View\View;

class CommunityController extends Controller
{
    public function __construct()
    {
        $this->middleware('checkPageAccess:15');
    }

    /**
     * Overview tab — stats + recent posts
     */
    public function index(): View
    {
        $stats = [
            'members'      => Profile::count(),
            'posts'        => Post::count(),
            'discussions'  => Discussion::count(),
            'courseGroups'  => CourseGroup::count(),
        ];

        $recentPosts = Post::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('backend.community.index', compact('stats', 'recentPosts'));
    }

    /**
     * Members tab — search, filter, manage
     */
    public function members(Request $request): View
    {
        $query = Profile::with('user');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('author_name', 'like', "%{$search}%");
            });
        }

        if ($badge = $request->input('badge')) {
            $query->where('badge', $badge);
        }

        $members = $query->latest()->paginate(20);

        return view('backend.community.members', compact('members'));
    }

    /**
     * Update a member's badge
     */
    public function updateMemberBadge(string $id, Request $request): RedirectResponse
    {
        $request->validate([
            'badge' => 'required|in:aktiv_elev,tidligere_elev,mentor,moderator,admin',
        ]);

        $profile = Profile::findOrFail($id);
        $profile->update(['badge' => $request->badge]);

        return redirect()->back()->with([
            'errors' => new MessageBag(['Badge oppdatert.']),
            'alert_type' => 'success',
        ]);
    }

    /**
     * Toggle member suspension
     */
    public function toggleSuspend(string $id): RedirectResponse
    {
        $profile = Profile::findOrFail($id);
        $profile->update(['is_suspended' => !$profile->is_suspended]);

        $msg = $profile->is_suspended ? 'Medlem suspendert.' : 'Medlem gjenopprettet.';
        return redirect()->back()->with([
            'errors' => new MessageBag([$msg]),
            'alert_type' => 'success',
        ]);
    }

    /**
     * Posts tab
     */
    public function posts(): View
    {
        $posts = Post::with('user', 'comments')
            ->latest()
            ->paginate(20);

        return view('backend.community.posts', compact('posts'));
    }

    /**
     * Toggle post pin status
     */
    public function togglePinPost(string $id): RedirectResponse
    {
        $post = Post::findOrFail($id);
        $post->update(['pinned' => !$post->pinned]);

        return redirect()->back()->with([
            'errors' => new MessageBag(['Innlegg ' . ($post->pinned ? 'festet' : 'løsnet') . '.']),
            'alert_type' => 'success',
        ]);
    }

    /**
     * Delete a post
     */
    public function destroyPost(string $id): RedirectResponse
    {
        Post::findOrFail($id)->delete();

        return redirect()->back()->with([
            'errors' => new MessageBag(['Innlegg slettet.']),
            'alert_type' => 'success',
        ]);
    }

    /**
     * Discussions tab
     */
    public function discussions(): View
    {
        $discussions = Discussion::with('user')
            ->withCount('replies')
            ->latest()
            ->paginate(20);

        return view('backend.community.discussions', compact('discussions'));
    }

    /**
     * Toggle discussion pin status
     */
    public function togglePinDiscussion(string $id): RedirectResponse
    {
        $discussion = Discussion::findOrFail($id);
        $discussion->update(['pinned' => !$discussion->pinned]);

        return redirect()->back()->with([
            'errors' => new MessageBag(['Diskusjon ' . ($discussion->pinned ? 'festet' : 'løsnet') . '.']),
            'alert_type' => 'success',
        ]);
    }

    /**
     * Delete a discussion
     */
    public function destroyDiscussion(string $id): RedirectResponse
    {
        Discussion::findOrFail($id)->delete();

        return redirect()->back()->with([
            'errors' => new MessageBag(['Diskusjon slettet.']),
            'alert_type' => 'success',
        ]);
    }

    /**
     * Course groups tab
     */
    public function courseGroups(): View
    {
        $courseGroups = CourseGroup::withCount('members')->get();

        return view('backend.community.course-groups', compact('courseGroups'));
    }

    /**
     * Store a new course group
     */
    public function storeCourseGroup(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:10',
        ]);

        CourseGroup::create([
            'name'        => $request->name,
            'description' => $request->description,
            'icon'        => $request->icon ?: '📚',
        ]);

        return redirect()->back()->with([
            'errors' => new MessageBag(['Kursgruppe opprettet.']),
            'alert_type' => 'success',
        ]);
    }

    /**
     * Update a course group
     */
    public function updateCourseGroup(string $id, Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon'        => 'nullable|string|max:10',
        ]);

        CourseGroup::findOrFail($id)->update($request->only('name', 'description', 'icon'));

        return redirect()->back()->with([
            'errors' => new MessageBag(['Kursgruppe oppdatert.']),
            'alert_type' => 'success',
        ]);
    }

    /**
     * Delete a course group
     */
    public function destroyCourseGroup(string $id): RedirectResponse
    {
        CourseGroup::findOrFail($id)->delete();

        return redirect()->back()->with([
            'errors' => new MessageBag(['Kursgruppe slettet.']),
            'alert_type' => 'success',
        ]);
    }
}

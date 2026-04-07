<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostReaction;
use App\Models\Discussion;
use App\Models\DiscussionReply;
use App\Models\DirectMessage;
use App\Models\Profile;
use App\Models\Notification;
use App\Models\CourseGroup;
use App\Models\ManuscriptProject;
use App\Models\ManuscriptExcerpt;
use App\Models\ManuscriptFeedback;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Dropbox\Client as DropboxClient;

class CommunityForumController extends Controller
{
    /* ===================== HELPERS ===================== */

    private static $requiredTables = [
        'profiles', 'posts', 'post_reactions', 'post_comments',
        'discussions', 'discussion_replies', 'community_notifications',
        'direct_messages', 'manuscript_projects', 'manuscript_excerpts',
        'manuscript_feedback', 'manuscript_followers',
    ];

    public function __construct()
    {
        foreach (self::$requiredTables as $table) {
            if (!\Schema::hasTable($table)) {
                abort(503, "Community-modulen krever at database-migrasjoner kjøres. Tabell '$table' mangler. Kjør: php artisan migrate");
            }
        }
    }

    private function ensureProfile()
    {
        $user = Auth::user();
        $profile = Profile::where('user_id', $user->id)->first();

        if (!$profile) {
            $columns = \Schema::getColumnListing('profiles');
            $data = [
                'id'      => Str::uuid(),
                'user_id' => $user->id,
            ];
            $optional = [
                'name'         => trim($user->first_name . ' ' . $user->last_name),
                'badge'        => 'aktiv_elev',
                'access_level' => 'community_member',
            ];
            foreach ($optional as $col => $val) {
                if (in_array($col, $columns)) {
                    $data[$col] = $val;
                }
            }
            $profile = Profile::create($data);
        }

        return $profile;
    }

    private function isAdmin()
    {
        $user = Auth::user();
        if ($user && $user->role == 1) {
            return true;
        }
        $profile = Profile::where('user_id', Auth::id())->first();
        return $profile && $profile->badge === 'admin';
    }

    private function rejectIfSuspended()
    {
        $profile = Profile::where('user_id', Auth::id())->first();
        if ($profile && \Schema::hasColumn('profiles', 'is_suspended') && $profile->is_suspended) {
            abort(403, 'Kontoen din er suspendert.');
        }
    }

    private function formatName($name)
    {
        return collect(explode(' ', $name))
            ->map(fn($w) => ucfirst($w))
            ->join(' ');
    }

    private function safeOrderByPinned($query, $table)
    {
        if (\Schema::hasColumn($table, 'pinned')) {
            $query->orderByDesc('pinned');
        }
        return $query;
    }

    private function unreadNotificationCount()
    {
        if (!\Schema::hasTable('community_notifications')) return 0;
        return Notification::where('user_id', Auth::id())->where('read', false)->count();
    }

    private function unreadMessageCount()
    {
        if (!\Schema::hasTable('direct_messages')) return 0;
        return DirectMessage::where('recipient_id', Auth::id())->where('read', false)->count();
    }

    private function uploadPostImage(Request $request): ?string
    {
        if (!$request->hasFile('image')) {
            return null;
        }

        $file = $request->file('image');
        $fileName = 'community_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
        $destinationPath = 'Forfatterskolen_app/community-images/';

        $file->storeAs($destinationPath, $fileName, 'dropbox');

        try {
            $dropbox = new DropboxClient(config('filesystems.disks.dropbox.authorization_token'));
            $dropboxPath = $destinationPath . $fileName;

            $response = $dropbox->listSharedLinks($dropboxPath);
            if (isset($response[0]['url'])) {
                return str_replace('?dl=0', '?raw=1', $response[0]['url']);
            }

            $response = $dropbox->createSharedLinkWithSettings($dropboxPath, [
                'requested_visibility' => 'public',
            ]);
            return str_replace('?dl=0', '?raw=1', $response['url']);
        } catch (\Exception $e) {
            \Log::error('Community image upload failed: ' . $e->getMessage());
            return null;
        }
    }

    /* ===================== HOME / FEED ===================== */

    public function home()
    {
        $profile = $this->ensureProfile();

        // Pinned posts only (official announcements)
        $posts = Post::with(['user.profile', 'reactions', 'comments.user.profile'])
            ->whereNull('course_group_id')
            ->where('pinned', true)
            ->orderByDesc('created_at')
            ->get();

        // All discussions (user-generated content)
        $discussions = Discussion::with(['user.profile', 'replies.user.profile'])
            ->orderByDesc('pinned')
            ->orderByDesc('created_at')
            ->get();

        return view('frontend.learner.community.home', [
            'posts'   => $posts,
            'discussions' => $discussions,
            'profile' => $profile,
            'unreadNotifications' => $this->unreadNotificationCount(),
            'unreadMessages'      => $this->unreadMessageCount(),
            'activePage'          => 'home',
        ]);
    }

    public function storePost(Request $request)
    {
        $this->rejectIfSuspended();
        $request->validate([
            'content' => 'required|string|max:2000',
            'image'   => 'nullable|image|mimes:jpeg,png,gif,webp|max:2048',
        ]);

        $imageUrl = $this->uploadPostImage($request);

        Post::create([
            'id'        => Str::uuid(),
            'user_id'   => Auth::id(),
            'content'   => $request->content,
            'image_url' => $imageUrl,
        ]);

        return redirect()->route('learner.community.home')->with('success', 'Innlegg publisert!');
    }

    public function toggleLike($postId)
    {
        $this->rejectIfSuspended();
        $userId = Auth::id();
        $existing = PostReaction::where('post_id', $postId)->where('user_id', $userId)->first();

        if ($existing) {
            $existing->delete();
        } else {
            PostReaction::create([
                'id'       => Str::uuid(),
                'post_id'  => $postId,
                'user_id'  => $userId,
                'reaction' => 'like',
            ]);
        }

        return redirect()->back();
    }

    public function storeComment(Request $request, $postId)
    {
        $this->rejectIfSuspended();
        $request->validate(['content' => 'required|string|max:1000']);

        PostComment::create([
            'id'      => Str::uuid(),
            'post_id' => $postId,
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        return redirect()->back();
    }

    public function deletePost($id)
    {
        $post = Post::findOrFail($id);
        if ($post->user_id !== Auth::id() && !$this->isAdmin()) {
            abort(403);
        }
        $post->delete();

        return redirect()->route('learner.community.home')->with('success', 'Innlegg slettet.');
    }

    /* ===================== DISCUSSIONS ===================== */

    public function discussions()
    {
        $profile = $this->ensureProfile();
        $discussionsQuery = Discussion::with(['user.profile', 'replies']);
        $this->safeOrderByPinned($discussionsQuery, 'discussions');
        $discussions = $discussionsQuery->orderByDesc('created_at')->get();

        return view('frontend.learner.community.discussions', [
            'discussions' => $discussions,
            'profile'     => $profile,
            'unreadNotifications' => $this->unreadNotificationCount(),
            'unreadMessages'      => $this->unreadMessageCount(),
            'activePage'          => 'discussions',
        ]);
    }

    public function storeDiscussion(Request $request)
    {
        $this->rejectIfSuspended();
        $request->validate([
            'title'    => 'required|string|max:255',
            'content'  => 'required|string|max:5000',
            'category' => 'required|string|max:100',
            'image'    => 'nullable|image|mimes:jpeg,png,gif,webp|max:2048',
        ]);

        $imageUrl = $this->uploadPostImage($request);

        $discussion = Discussion::create([
            'id'        => Str::uuid(),
            'user_id'   => Auth::id(),
            'title'     => $request->title,
            'content'   => $request->content,
            'image_url' => $imageUrl,
            'category'  => $request->category,
        ]);

        return redirect()->route('learner.community.discussion', $discussion->id)
            ->with('success', 'Diskusjon opprettet!');
    }

    public function showDiscussion($id)
    {
        $profile = $this->ensureProfile();
        $discussion = Discussion::with(['user.profile', 'replies.user.profile'])->findOrFail($id);

        return view('frontend.learner.community.discussion-thread', [
            'discussion' => $discussion,
            'profile'    => $profile,
            'unreadNotifications' => $this->unreadNotificationCount(),
            'unreadMessages'      => $this->unreadMessageCount(),
            'activePage'          => 'discussions',
        ]);
    }

    public function storeReply(Request $request, $discussionId)
    {
        $this->rejectIfSuspended();
        $request->validate(['content' => 'required|string|max:2000']);

        $discussion = Discussion::findOrFail($discussionId);

        DiscussionReply::create([
            'id'            => Str::uuid(),
            'discussion_id' => $discussionId,
            'user_id'       => Auth::id(),
            'content'       => $request->content,
        ]);

        // Notify discussion owner
        if ($discussion->user_id !== Auth::id()) {
            $profile = $this->ensureProfile();
            Notification::create([
                'id'           => Str::uuid(),
                'user_id'      => $discussion->user_id,
                'type'         => 'discussion_reply',
                'content'      => $this->formatName($profile->name) . ' svarte på diskusjonen din',
                'from_user_id' => Auth::id(),
                'link'         => '/community/discussions/' . $discussionId,
            ]);
        }

        return redirect()->back()->with('success', 'Svar publisert!');
    }

    /* ===================== MESSAGES ===================== */

    public function messagesIndex()
    {
        $profile = $this->ensureProfile();
        $userId = Auth::id();

        // Get unique conversation partners with latest message
        $conversations = DirectMessage::where('sender_id', $userId)
            ->orWhere('recipient_id', $userId)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($msg) use ($userId) {
                $partnerId = $msg->sender_id === $userId ? $msg->recipient_id : $msg->sender_id;
                return [
                    'partner_id' => $partnerId,
                    'message'    => $msg,
                ];
            })
            ->unique('partner_id')
            ->values();

        // Enrich with partner profile and unread count
        $enriched = $conversations->map(function ($conv) use ($userId) {
            $partner = \App\User::with('profile')->find($conv['partner_id']);
            $unread = DirectMessage::where('sender_id', $conv['partner_id'])
                ->where('recipient_id', $userId)
                ->where('read', false)
                ->count();

            return [
                'partner'       => $partner,
                'last_message'  => $conv['message'],
                'unread_count'  => $unread,
            ];
        });

        return view('frontend.learner.community.messages', [
            'conversations' => $enriched,
            'profile'       => $profile,
            'activeChat'    => null,
            'chatMessages'  => collect(),
            'chatPartner'   => null,
            'members'       => Profile::with('user')->get(),
            'unreadNotifications' => $this->unreadNotificationCount(),
            'unreadMessages'      => $this->unreadMessageCount(),
            'activePage'          => 'messages',
        ]);
    }

    public function conversation($partnerId)
    {
        $profile = $this->ensureProfile();
        $userId = Auth::id();

        // Mark messages as read
        DirectMessage::where('sender_id', $partnerId)
            ->where('recipient_id', $userId)
            ->where('read', false)
            ->update(['read' => true]);

        // Get messages
        $messages = DirectMessage::with(['sender.profile', 'recipient.profile'])
            ->where(function ($q) use ($userId, $partnerId) {
                $q->where('sender_id', $userId)->where('recipient_id', $partnerId);
            })
            ->orWhere(function ($q) use ($userId, $partnerId) {
                $q->where('sender_id', $partnerId)->where('recipient_id', $userId);
            })
            ->orderBy('created_at')
            ->get();

        $partner = \App\User::with('profile')->find($partnerId);

        // Also get conversation list
        $allConvs = DirectMessage::where('sender_id', $userId)
            ->orWhere('recipient_id', $userId)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($msg) use ($userId) {
                $pid = $msg->sender_id === $userId ? $msg->recipient_id : $msg->sender_id;
                return ['partner_id' => $pid, 'message' => $msg];
            })
            ->unique('partner_id')
            ->values()
            ->map(function ($conv) use ($userId) {
                $p = \App\User::with('profile')->find($conv['partner_id']);
                $unread = DirectMessage::where('sender_id', $conv['partner_id'])
                    ->where('recipient_id', $userId)
                    ->where('read', false)
                    ->count();
                return [
                    'partner'      => $p,
                    'last_message' => $conv['message'],
                    'unread_count' => $unread,
                ];
            });

        return view('frontend.learner.community.messages', [
            'conversations' => $allConvs,
            'profile'       => $profile,
            'activeChat'    => $partnerId,
            'chatMessages'  => $messages,
            'chatPartner'   => $partner,
            'members'       => Profile::with('user')->get(),
            'unreadNotifications' => $this->unreadNotificationCount(),
            'unreadMessages'      => $this->unreadMessageCount(),
            'activePage'          => 'messages',
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|integer|exists:users,id',
            'content'      => 'required|string|max:2000',
        ]);

        DirectMessage::create([
            'id'           => Str::uuid(),
            'sender_id'    => Auth::id(),
            'recipient_id' => $request->recipient_id,
            'content'      => $request->content,
        ]);

        // Notify recipient
        $profile = $this->ensureProfile();
        Notification::create([
            'id'           => Str::uuid(),
            'user_id'      => $request->recipient_id,
            'type'         => 'new_message',
            'content'      => $this->formatName($profile->name) . ' sendte deg en melding',
            'from_user_id' => Auth::id(),
            'link'         => '/community/messages/' . Auth::id(),
        ]);

        return redirect()->route('learner.community.conversation', $request->recipient_id);
    }

    /* ===================== MEMBERS ===================== */

    public function members()
    {
        $profile = $this->ensureProfile();
        $members = Profile::with('user')->get();

        return view('frontend.learner.community.members', [
            'members' => $members,
            'profile' => $profile,
            'unreadNotifications' => $this->unreadNotificationCount(),
            'unreadMessages'      => $this->unreadMessageCount(),
            'activePage'          => 'members',
        ]);
    }

    /* ===================== NOTIFICATIONS ===================== */

    public function notifications()
    {
        $profile = $this->ensureProfile();
        $notifications = Notification::with('fromUser.profile')
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('frontend.learner.community.notifications', [
            'notifications' => $notifications,
            'profile'       => $profile,
            'unreadNotifications' => $this->unreadNotificationCount(),
            'unreadMessages'      => $this->unreadMessageCount(),
            'activePage'          => 'notifications',
        ]);
    }

    public function markNotificationRead($id)
    {
        $notification = Notification::findOrFail($id);
        if ($notification->user_id !== Auth::id()) abort(403);
        $notification->update(['read' => true]);

        return redirect()->back();
    }

    public function markAllNotificationsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('read', false)
            ->update(['read' => true]);

        return redirect()->back()->with('success', 'Alle varsler markert som lest.');
    }

    /* ===================== MANUSCRIPTS ===================== */

    public function manuscripts()
    {
        $profile = $this->ensureProfile();
        $projects = ManuscriptProject::with(['user.profile', 'excerpts', 'followers'])
            ->orderByDesc('created_at')
            ->get();

        return view('frontend.learner.community.manuscripts', [
            'projects' => $projects,
            'profile'  => $profile,
            'unreadNotifications' => $this->unreadNotificationCount(),
            'unreadMessages'      => $this->unreadMessageCount(),
            'activePage'          => 'manuscripts',
        ]);
    }

    public function storeManuscript(Request $request)
    {
        $this->rejectIfSuspended();
        $request->validate([
            'title'       => 'required|string|max:255',
            'genre'       => 'required|string|max:100',
            'description' => 'nullable|string|max:2000',
        ]);

        ManuscriptProject::create([
            'id'          => Str::uuid(),
            'user_id'     => Auth::id(),
            'title'       => $request->title,
            'genre'       => $request->genre,
            'description' => $request->description,
            'word_count'  => $request->word_count ?? 0,
            'status'      => $request->status ?? 'Pågår',
        ]);

        return redirect()->route('learner.community.manuscripts')->with('success', 'Prosjekt opprettet!');
    }

    public function showManuscript($id)
    {
        $profile = $this->ensureProfile();
        $project = ManuscriptProject::with(['user.profile', 'excerpts.user.profile', 'excerpts.feedback.user.profile', 'followers'])
            ->findOrFail($id);

        return view('frontend.learner.community.manuscript-detail', [
            'project' => $project,
            'profile' => $profile,
            'unreadNotifications' => $this->unreadNotificationCount(),
            'unreadMessages'      => $this->unreadMessageCount(),
            'activePage'          => 'manuscripts',
        ]);
    }

    public function storeExcerpt(Request $request, $projectId)
    {
        $this->rejectIfSuspended();
        $project = ManuscriptProject::findOrFail($projectId);
        if ($project->user_id !== Auth::id()) abort(403);

        $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string|max:50000',
        ]);

        $wordCount = str_word_count($request->content);

        ManuscriptExcerpt::create([
            'id'         => Str::uuid(),
            'project_id' => $projectId,
            'user_id'    => Auth::id(),
            'title'      => $request->title,
            'content'    => $request->content,
            'word_count' => $wordCount,
        ]);

        return redirect()->route('learner.community.manuscript', $projectId)->with('success', 'Utdrag publisert!');
    }

    public function showExcerpt($id)
    {
        $profile = $this->ensureProfile();
        $excerpt = ManuscriptExcerpt::with(['user.profile', 'feedback.user.profile', 'project'])
            ->findOrFail($id);

        return view('frontend.learner.community.excerpt-detail', [
            'excerpt' => $excerpt,
            'profile' => $profile,
            'unreadNotifications' => $this->unreadNotificationCount(),
            'unreadMessages'      => $this->unreadMessageCount(),
            'activePage'          => 'manuscripts',
        ]);
    }

    public function storeFeedback(Request $request, $excerptId)
    {
        $this->rejectIfSuspended();
        $request->validate(['content' => 'required|string|max:5000']);

        $excerpt = ManuscriptExcerpt::findOrFail($excerptId);

        ManuscriptFeedback::create([
            'id'         => Str::uuid(),
            'excerpt_id' => $excerptId,
            'user_id'    => Auth::id(),
            'content'    => $request->content,
        ]);

        // Notify excerpt owner
        if ($excerpt->user_id !== Auth::id()) {
            $profile = $this->ensureProfile();
            Notification::create([
                'id'           => Str::uuid(),
                'user_id'      => $excerpt->user_id,
                'type'         => 'manuscript_feedback',
                'content'      => $this->formatName($profile->name) . ' ga tilbakemelding på utdraget ditt',
                'from_user_id' => Auth::id(),
            ]);
        }

        return redirect()->back()->with('success', 'Tilbakemelding sendt!');
    }

    public function toggleFollow($projectId)
    {
        $this->rejectIfSuspended();
        $project = ManuscriptProject::findOrFail($projectId);
        $userId = Auth::id();

        if ($project->followers()->where('user_id', $userId)->exists()) {
            $project->followers()->detach($userId);
        } else {
            $project->followers()->attach($userId, ['id' => Str::uuid()]);
        }

        return redirect()->back();
    }

    /* ===================== COURSE GROUPS ===================== */

    public function courseGroups()
    {
        $this->ensureProfile();
        $user = Auth::user();

        if ($this->isAdmin()) {
            // Admin ser alle aktive kurs som har elever og er markert for kursgrupper
            $courseIds = \App\CoursesTaken::join('packages', 'courses_taken.package_id', '=', 'packages.id')
                ->distinct()->pluck('packages.course_id');
            $courses = \App\Course::whereIn('id', $courseIds)
                ->where('status', 1)
                ->where('show_in_course_groups', 1)
                ->get();
        } else {
            // Get user's active courses via CoursesTaken → Package → Course
            $coursesTaken = $user->coursesTaken()
                ->with('package.course')
                ->get();

            // Group by course, collecting unique courses
            $courses = collect();
            foreach ($coursesTaken as $ct) {
                if ($ct->package && $ct->package->course) {
                    $course = $ct->package->course;
                    if (!$courses->contains('id', $course->id) && ($course->show_in_course_groups ?? true)) {
                        $courses->push($course);
                    }
                }
            }
        }

        // For each course, count how many learners are enrolled
        foreach ($courses as $course) {
            $course->learner_count = \App\CoursesTaken::whereHas('package', function ($q) use ($course) {
                $q->where('course_id', $course->id);
            })->distinct('user_id')->count('user_id');
        }

        return view('frontend.learner.community.course-groups', [
            'courses'              => $courses,
            'unreadNotifications'  => $this->unreadNotificationCount(),
            'unreadMessages'       => $this->unreadMessageCount(),
            'activePage'           => 'courseGroups',
        ]);
    }

    public function showCourseGroup($courseId)
    {
        $this->ensureProfile();
        $user = Auth::user();

        $course = \App\Course::findOrFail($courseId);

        // Verify user is enrolled in this course
        $enrolled = $user->coursesTaken()->whereHas('package', function ($q) use ($courseId) {
            $q->where('course_id', $courseId);
        })->exists();

        if (!$enrolled && !$this->isAdmin()) {
            abort(403);
        }

        // Get all learner IDs enrolled in this course
        $learnerIds = \App\CoursesTaken::whereHas('package', function ($q) use ($courseId) {
            $q->where('course_id', $courseId);
        })->pluck('user_id')->unique();

        // Get posts for this course group (course_group_id references courses.id)
        $cgPostsQuery = Post::where('course_group_id', $courseId)
            ->with('user', 'comments.user');
        $this->safeOrderByPinned($cgPostsQuery, 'posts');
        $posts = $cgPostsQuery->orderByDesc('created_at')->get();

        // Get member profiles
        $members = Profile::whereIn('user_id', $learnerIds)->get();

        return view('frontend.learner.community.course-group-detail', [
            'course'               => $course,
            'posts'                => $posts,
            'members'              => $members,
            'learnerCount'         => $learnerIds->count(),
            'unreadNotifications'  => $this->unreadNotificationCount(),
            'unreadMessages'       => $this->unreadMessageCount(),
            'activePage'           => 'courseGroups',
        ]);
    }

    public function storeCourseGroupPost(Request $request, $courseId)
    {
        $this->rejectIfSuspended();
        $request->validate([
            'content' => 'required|string|max:2000',
            'image'   => 'nullable|image|mimes:jpeg,png,gif,webp|max:2048',
        ]);

        $user = Auth::user();
        $this->ensureProfile();

        // Verify enrollment
        $enrolled = $user->coursesTaken()->whereHas('package', function ($q) use ($courseId) {
            $q->where('course_id', $courseId);
        })->exists();

        if (!$enrolled && !$this->isAdmin()) {
            abort(403);
        }

        $imageUrl = $this->uploadPostImage($request);

        Post::create([
            'id'              => Str::uuid(),
            'user_id'         => $user->id,
            'content'         => $request->content,
            'image_url'       => $imageUrl,
            'course_group_id' => $courseId,
        ]);

        return redirect()->route('learner.community.courseGroup', $courseId);
    }

    /* ===================== PROFILE ===================== */

    public function profile()
    {
        $profile = $this->ensureProfile();

        return view('frontend.learner.community.profile', [
            'profile'             => $profile,
            'user'                => Auth::user(),
            'allGenres'           => \App\Genre::orderBy('name')->pluck('name')->toArray(),
            'unreadNotifications' => $this->unreadNotificationCount(),
            'unreadMessages'      => $this->unreadMessageCount(),
            'activePage'          => 'profile',
        ]);
    }

    public function updateProfile(Request $request)
    {
        $profile = $this->ensureProfile();

        $data = $request->only(['author_name', 'use_author_name', 'bio', 'current_project']);
        $data['use_author_name'] = $request->has('use_author_name');

        $data['genres'] = $request->input('genres', []);

        $profile->update($data);

        return redirect()->route('learner.community.profile')->with('success', 'Profil oppdatert!');
    }

    public function updatePushPreferences(Request $request)
    {
        $types = [
            'push_community_posts', 'push_community_comments', 'push_community_discussions',
            'push_community_groups', 'push_community_mentions', 'push_community_likes',
        ];

        foreach ($types as $type) {
            \Auth::user()->notificationPreferences()->updateOrCreate(
                ['type' => $type],
                ['enabled' => $request->boolean($type)]
            );
        }

        return redirect()->route('learner.community.profile')->with('success', 'Push-innstillinger lagret!');
    }
}

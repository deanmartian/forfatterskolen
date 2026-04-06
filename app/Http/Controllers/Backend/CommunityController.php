<?php

namespace App\Http\Controllers\Backend;

use App\Http\AdminHelpers;
use App\Http\Controllers\Controller;
use App\Models\CourseGroup;
use App\Models\Discussion;
use App\Models\Post;
use App\Models\Profile;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Spatie\Dropbox\Client as DropboxClient;

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
     * Generate AI discussion content
     */
    public function generateAiDiscussion(Request $request): JsonResponse
    {
        $topic = $request->input('topic', 'skriveteknikk');

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'x-api-key' => config('services.anthropic.key'),
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->timeout(60)->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-sonnet-4-20250514',
                'max_tokens' => 1024,
                'system' => 'Du er Forfatterskolen sin community-manager. Lag en diskusjonstråd for et skrivefellesskap på norsk. '
                    . 'Svaret SKAL være gyldig JSON med denne strukturen: {"title": "Diskusjons-tittel", "content": "Innholdet i diskusjonen (2-4 avsnitt, engasjerende, med spørsmål til leserne)", "category": "Kategori"} '
                    . 'Kategorier kan være: Skriveteknikk, Inspirasjon, Forfatterlivet, Bokanbefaling, Skriveøvelse, Tilbakemelding, Sjanger, Publisering. '
                    . 'Diskusjonen skal invitere til samtale og meningsutveksling. Avslutt med et åpent spørsmål. Ikke bruk markdown, skriv ren tekst. Svar KUN med JSON.',
                'messages' => [
                    ['role' => 'user', 'content' => 'Lag en diskusjon om: ' . $topic],
                ],
            ]);

            $data = $response->json();
            $text = $data['content'][0]['text'] ?? '';

            // Parse JSON
            $text = trim($text);
            if (str_starts_with($text, '```')) {
                $text = preg_replace('/^```(?:json)?\s*/', '', $text);
                $text = preg_replace('/\s*```$/', '', $text);
            }

            $parsed = json_decode($text, true);
            if (!$parsed || !isset($parsed['title'])) {
                return response()->json(['error' => 'Kunne ikke parse AI-svaret.'], 500);
            }

            return response()->json($parsed);
        } catch (\Exception $e) {
            return response()->json(['error' => 'AI-feil: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Store an AI-generated discussion
     */
    public function storeAiDiscussion(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|string|max:100',
        ]);

        Discussion::create([
            'id' => \Str::uuid(),
            'user_id' => \Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category,
            'pinned' => $request->has('pinned'),
        ]);

        return redirect()->back()->with([
            'errors' => new MessageBag(['Diskusjon opprettet!']),
            'alert_type' => 'success',
        ]);
    }

    /**
     * Store a new bot post from admin
     */
    public function storeBotPost(Request $request): RedirectResponse
    {
        $request->validate([
            'content' => 'required|string|max:5000',
            'image'   => 'nullable|image|mimes:jpeg,png,gif,webp|max:2048',
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = 'community_bot_' . Str::uuid() . '.' . $file->getClientOriginalExtension();
            $destinationPath = 'Forfatterskolen_app/community-images/';
            $file->storeAs($destinationPath, $fileName, 'dropbox');

            try {
                $dropbox = new DropboxClient(config('filesystems.disks.dropbox.authorization_token'));
                $response = $dropbox->createSharedLinkWithSettings($destinationPath . $fileName, [
                    'requested_visibility' => 'public',
                ]);
                $imageUrl = str_replace('?dl=0', '?raw=1', $response['url']);
            } catch (\Exception $e) {
                \Log::error('Bot post image upload failed: ' . $e->getMessage());
            }
        }

        // Use image URL if no file uploaded
        if (!$imageUrl && $request->input('image_url')) {
            $imageUrl = $request->input('image_url');
        }

        Post::create([
            'id'             => Str::uuid(),
            'user_id'        => \Auth::id(),
            'content'        => $request->content,
            'image_url'      => $imageUrl,
            'is_bot_post'    => $request->input('post_as', 'school') === 'school',
            'pinned'         => $request->has('pinned'),
            'course_group_id' => $request->input('course_group_id') ?: null,
        ]);

        return redirect()->back()->with([
            'errors'     => new MessageBag(['Innlegg fra Forfatterskolen publisert!']),
            'alert_type' => 'success',
        ]);
    }

    /**
     * Generate AI content for bot post
     */
    public function publishPost($id): RedirectResponse
    {
        $post = Post::findOrFail($id);
        $post->update(['status' => 'published']);

        return redirect()->back()->with([
            'errors' => new MessageBag(['Innlegg publisert!']),
            'alert_type' => 'success',
        ]);
    }

    public function generateAiContent(Request $request): JsonResponse
    {
        $request->validate([
            'prompt' => 'nullable|string|max:500',
        ]);

        $userPrompt = $request->input('prompt', 'Gi meg et inspirerende skrivetips for forfattere.');

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'x-api-key' => config('services.anthropic.key'),
                'anthropic-version' => '2023-06-01',
                'Content-Type' => 'application/json',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-sonnet-4-20250514',
                'max_tokens' => 500,
                'system' => 'Du er Forfatterskolen sin assistent. Skriv innlegg på norsk for et skrivefellesskap. Innleggene skal være inspirerende, lærerike og engasjerende for forfattere og skriveglade. Hold det kort og engasjerende (maks 3-4 avsnitt). Bruk emojier naturlig og ofte (📝✍️📚💡🎉❤️🔥✨💬🌟) for å gjøre innlegget levende og engasjerende. Ikke bruk markdown-formatering som ** eller ##, bruk vanlig tekst med emojier.',
                'messages' => [
                    ['role' => 'user', 'content' => $userPrompt],
                ],
            ]);

            $data = $response->json();
            $content = $data['content'][0]['text'] ?? '';

            return response()->json(['content' => $content]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Kunne ikke generere innhold: ' . $e->getMessage()], 500);
        }
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

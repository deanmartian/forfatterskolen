<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\InboxService;
use Illuminate\Http\Request;

class InboxController extends Controller
{
    public function __construct(
        private readonly InboxService $inboxService,
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['status', 'assigned_to', 'inbox', 'category', 'search', 'starred', 'sent', 'follow_up', 'mentions', 'awaiting']);
        $conversations = $this->inboxService->getConversations($filters);
        $stats = $this->inboxService->getStats();
        $teamMembers = $this->inboxService->getTeamMembers();
        $inboxes = $this->inboxService->getInboxes();

        return view('backend.inbox.index', compact('conversations', 'filters', 'stats', 'teamMembers', 'inboxes'));
    }

    public function show(int $id)
    {
        $conversation = $this->inboxService->getConversation($id);
        $timeline = $conversation->timeline();
        $teamMembers = $this->inboxService->getTeamMembers();
        $cannedResponses = $this->inboxService->getCannedResponses();
        $studentContext = $this->getStudentContext($conversation);

        return view('backend.inbox.show', compact('conversation', 'timeline', 'teamMembers', 'cannedResponses', 'studentContext'));
    }

    public function reply(Request $request, int $id)
    {
        $request->validate(['body' => 'required|string']);
        $isDraft = $request->boolean('save_as_draft', false);
        $sendAndClose = $request->boolean('send_and_close', false);

        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('inbox-attachments', 'public');
                $attachments[] = [
                    'path' => storage_path('app/public/' . $path),
                    'name' => $file->getClientOriginalName(),
                ];
            }
        }

        $this->inboxService->sendReply($id, $request->input('body'), auth()->id(), $isDraft, $attachments);

        if ($sendAndClose && !$isDraft) {
            $this->inboxService->updateStatus($id, 'closed');
            // Gå tilbake til MIN arbeidsliste etter lukk, ikke default "Åpne"-
            // visning som viser alle åpne samtaler (inkludert andre admins
            // sine). Dette unngår at admin ved en feiltakelse svarer på
            // samtaler som tilhører Annina/Kristine/Taran.
            return redirect()->route('admin.inbox.index', ['assigned_to' => auth()->id()])
                ->with('alert_type', 'success')
                ->with('message', 'Svar sendt og samtale lukket!');
        }

        return redirect()->route('admin.inbox.show', $id)
            ->with('alert_type', $isDraft ? 'info' : 'success')
            ->with('message', $isDraft ? 'Utkast lagret' : 'Svar sendt!');
    }

    public function comment(Request $request, int $id)
    {
        $request->validate(['body' => 'required|string']);

        $mentionedIds = $request->input('mentioned_user_ids', []);
        $this->inboxService->addComment($id, auth()->id(), $request->input('body'), $mentionedIds);

        return redirect()->route('admin.inbox.show', $id)
            ->with('alert_type', 'success')
            ->with('message', 'Kommentar lagt til');
    }

    public function assign(Request $request, int $id)
    {
        $request->validate(['assigned_to' => 'required|integer']);

        $this->inboxService->assignConversation($id, $request->input('assigned_to'), auth()->id(), $request->input('note'));

        return redirect()->route('admin.inbox.show', $id)
            ->with('alert_type', 'success')
            ->with('message', 'Samtale tildelt');
    }

    public function updateStatus(Request $request, int $id)
    {
        $request->validate(['status' => 'required|in:open,pending,closed,snoozed']);

        $this->inboxService->updateStatus($id, $request->input('status'));

        return redirect()->route('admin.inbox.show', $id)
            ->with('alert_type', 'success')
            ->with('message', 'Status oppdatert');
    }

    public function toggleStar(int $id)
    {
        $this->inboxService->toggleStar($id);
        return redirect()->back();
    }

    public function markSpam(int $id)
    {
        $this->inboxService->markAsSpam($id);
        return redirect()->route('admin.inbox.index')
            ->with('alert_type', 'success')
            ->with('message', 'Markert som spam');
    }

    public function compose(Request $request)
    {
        $request->validate(['to' => 'required|email', 'subject' => 'required', 'body' => 'required']);
        $isDraft = $request->boolean('save_draft', false);

        // Handle attachments
        $attachmentPaths = null;
        if ($request->hasFile('attachments')) {
            $attachmentPaths = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('inbox-attachments', 'public');
                $attachmentPaths[] = storage_path('app/public/' . $path);
            }
        }

        // Create conversation
        $conversation = \App\Models\Inbox\InboxConversation::create([
            'subject' => $request->input('subject'),
            'customer_email' => $request->input('to'),
            'customer_name' => $request->input('to'),
            'status' => $isDraft ? 'pending' : 'closed',
            'source' => 'compose',
            'inbox' => 'post@forfatterskolen.no',
        ]);

        // Try to link user
        $user = \App\User::where('email', $request->input('to'))->first();
        if ($user) {
            $conversation->update(['user_id' => $user->id, 'customer_name' => $user->full_name]);
        }

        // Add signature
        $bodyWithSig = rtrim($request->input('body')) . "\n\nSkrivevarm hilsen,\n" . auth()->user()->full_name . "\nForfatterskolen / Easywrite / Indiemoon Publishing";

        // Create message
        $message = \App\Models\Inbox\InboxMessage::create([
            'conversation_id' => $conversation->id,
            'type' => 'reply',
            'direction' => 'outbound',
            'from_email' => 'post@forfatterskolen.no',
            'from_name' => auth()->user()->full_name . ' — Forfatterskolen',
            'to_email' => $request->input('to'),
            'subject' => $request->input('subject'),
            'body' => $bodyWithSig,
            'body_plain' => $bodyWithSig,
            'body_html' => collect(preg_split('/\r?\n\r?\n/', e($bodyWithSig)))->map(fn($p) => '<p style="margin:0 0 4px;">' . str_replace("\n", '<br>', trim($p)) . '</p>')->implode(''),
            'sent_by_user_id' => auth()->id(),
            'is_draft' => $isDraft,
            'sent_at' => $isDraft ? null : now(),
        ]);

        if (!$isDraft) {
            $htmlBody = collect(preg_split('/\r?\n\r?\n/', e($bodyWithSig)))->map(fn($p) => '<p style="margin:0 0 4px;">' . str_replace("\n", '<br>', trim($p)) . '</p>')->implode('');
            dispatch(new \App\Jobs\AddMailToQueueJob(
                $request->input('to'),
                $request->input('subject'),
                $htmlBody,
                'post@forfatterskolen.no',
                auth()->user()->full_name . ' — Forfatterskolen',
                $attachmentPaths, 'inbox-compose', $conversation->id
            ));
        }

        return redirect()->route('admin.inbox.show', $conversation->id)
            ->with('alert_type', 'success')
            ->with('message', $isDraft ? 'Utkast lagret' : 'E-post sendt!');
    }

    public function bulk(Request $request)
    {
        $ids = json_decode($request->input('ids', '[]'));
        $action = $request->input('action');

        if (empty($ids)) {
            return redirect()->back();
        }

        switch ($action) {
            case 'close':
                \App\Models\Inbox\InboxConversation::whereIn('id', $ids)->update(['status' => 'closed']);
                $msg = count($ids) . ' samtaler lukket';
                break;
            case 'reopen':
                \App\Models\Inbox\InboxConversation::whereIn('id', $ids)->update(['status' => 'open']);
                $msg = count($ids) . ' samtaler gjenåpnet';
                break;
            case 'assign':
                $assignTo = (int) $request->input('assign_to');
                $assignee = \App\User::find($assignTo);
                \App\Models\Inbox\InboxConversation::whereIn('id', $ids)->update(['assigned_to' => $assignTo]);
                $msg = count($ids) . ' samtaler tildelt ' . ($assignee->first_name ?? '');
                break;
            case 'delete':
                \App\Models\Inbox\InboxMessage::whereIn('conversation_id', $ids)->delete();
                \App\Models\Inbox\InboxConversation::whereIn('id', $ids)->delete();
                $msg = count($ids) . ' samtaler slettet';
                break;
            default:
                $msg = 'Ukjent handling';
        }

        return redirect()->route('admin.inbox.index')
            ->with('alert_type', 'success')
            ->with('message', $msg);
    }

    public function setFollowUp(Request $request, int $id)
    {
        $conversation = \App\Models\Inbox\InboxConversation::findOrFail($id);
        $conversation->follow_up_at = $request->input('follow_up_at') ?: null;
        if ($conversation->follow_up_at && $conversation->status === 'closed') {
            $conversation->status = 'pending';
        }
        $conversation->save();

        return redirect()->route('admin.inbox.show', $id)
            ->with('alert_type', 'success')
            ->with('message', $conversation->follow_up_at ? 'Oppfølging satt!' : 'Oppfølging fjernet');
    }

    public function generateAiDraft(int $id)
    {
        $draft = $this->inboxService->generateAiDraft($id);

        return redirect()->route('admin.inbox.show', $id)
            ->with('alert_type', $draft ? 'success' : 'error')
            ->with('message', $draft ? 'AI-utkast generert!' : 'Kunne ikke generere utkast');
    }

    /**
     * "Forbedr svar" — tar Svens raske/bryske utkast og polerer det
     * til Forfatterskolens varme, profesjonelle tone via Anthropic API.
     *
     * Aksepterer JSON { body: "Svens utkast-tekst" }
     * Returnerer JSON { polished: "Polert tekst" }
     *
     * AI-en bruker eksisterende svar-eksempler som stilreferanse, og
     * beholder alltid Svens budskap — bare tonen endres.
     */
    public function polishReply(Request $request, int $id)
    {
        $request->validate(['body' => 'required|string|min:5']);

        $conversation = $this->inboxService->getConversation($id);
        $draft = $request->input('body');
        $senderName = auth()->user()->first_name ?? 'Sven Inge';

        // Hent svar-eksempler for stilreferanse
        $examples = \App\HelpwiseReplyExample::orderBy('created_at', 'desc')
            ->limit(5)
            ->pluck('reply_body')
            ->map(fn($r) => \Illuminate\Support\Str::limit(strip_tags($r), 300))
            ->implode("\n---\n");

        $examplesSection = $examples
            ? "\n\nSTILREFERANSE — slik skriver Forfatterskolen vanligvis:\n{$examples}\n"
            : '';

        $prompt = <<<PROMPT
Du er en tekstforbedrer for Forfatterskolen. Din oppgave er å ta et raskt, uformelt svarutkast fra {$senderName} og polere det til Forfatterskolens tone:

REGLER:
- Behold ALLTID det opprinnelige budskapet og alle fakta — du endrer KUN tonen
- Bruk varm, inkluderende og profesjonell norsk (aldri arrogant eller overdrevet)
- Bruk gjerne emojis der det passer naturlig (📖 ✍️ 😊 osv.)
- Start med å hilse på kunden ved fornavn hvis du ser det i utkastet
- Avslutt med "Ha en fin dag!\nMvh {$senderName}\nForfatterskolen"
- Bruk [tekst](url) for lenker — ALDRI lim inn rå URL-er
- ALDRI bruk markdown-formatering som **fet**, *kursiv*, # overskrifter
- ALDRI legg til informasjon som ikke er i det opprinnelige utkastet
- Hvis utkastet er kort og greit, hold svaret kort og greit — ikke blås det opp
{$examplesSection}
KUNDENS EMNE: {$conversation->subject}
KUNDENS NAVN: {$conversation->customer_name}

SVENS UTKAST:
{$draft}

Skriv det polerte svaret. Kun selve svaret, ingen kommentarer eller forklaringer.
PROMPT;

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'x-api-key' => config('services.anthropic.api_key'),
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-sonnet-4-20250514',
                'max_tokens' => 1024,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            if (!$response->successful()) {
                \Log::error('Polish reply API feilet', ['status' => $response->status(), 'body' => $response->body()]);
                return response()->json(['error' => 'AI-tjenesten er midlertidig utilgjengelig'], 500);
            }

            $polished = $response->json('content.0.text', '');

            if (empty($polished)) {
                return response()->json(['error' => 'AI returnerte tomt svar'], 500);
            }

            return response()->json(['polished' => $polished]);
        } catch (\Throwable $e) {
            \Log::error('Polish reply exception: ' . $e->getMessage());
            return response()->json(['error' => 'Feil ved AI-polering: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Gjør en privat samtale offentlig. Kun eieren kan utføre dette.
     */
    public function makePublic(int $id)
    {
        $success = $this->inboxService->makePublic($id, auth()->id());

        return redirect()->route('admin.inbox.show', $id)
            ->with('alert_type', $success ? 'success' : 'error')
            ->with('message', $success
                ? 'Samtalen er nå offentlig og synlig for alle admins'
                : 'Du kan ikke gjøre en annens private samtale offentlig');
    }

    /**
     * Gjør en offentlig samtale privat for innlogget admin.
     */
    public function makePrivate(int $id)
    {
        $success = $this->inboxService->makePrivate($id, auth()->id());

        return redirect()->route('admin.inbox.show', $id)
            ->with('alert_type', $success ? 'success' : 'error')
            ->with('message', $success
                ? 'Samtalen er nå privat — kun du ser den'
                : 'Samtalen er allerede privat for en annen admin');
    }

    /**
     * Utfør en AI-foreslått handling som er lagret i ai_tool_actions.
     * Validerer at handlingen tilhører samtalen før den utføres.
     */
    public function executeTool(int $id, int $actionId, \App\Services\AiTools\AiToolExecutor $executor)
    {
        $action = \App\Models\AiToolAction::findOrFail($actionId);

        // Sikkerhet: handlingen MÅ tilhøre denne samtalen
        if ($action->conversation_id !== $id) {
            abort(403, 'Handlingen tilhører ikke denne samtalen');
        }

        $result = $executor->execute($actionId, auth()->user());

        return redirect()->route('admin.inbox.show', $id)
            ->with('alert_type', $result->success ? 'success' : 'error')
            ->with('message', $result->success
                ? '✓ ' . $result->message
                : '✗ Kunne ikke utføre: ' . $result->message);
    }

    public function importFromHelpwise()
    {
        $imported = $this->inboxService->importFromHelpwise();

        return redirect()->route('admin.inbox.index')
            ->with('alert_type', 'success')
            ->with('message', "{$imported} samtaler importert fra Helpwise");
    }

    public function cannedResponses()
    {
        $responses = $this->inboxService->getCannedResponses();
        return view('backend.inbox.canned-responses', compact('responses'));
    }

    public function storeCannedResponse(Request $request)
    {
        $request->validate(['title' => 'required|string', 'body' => 'required|string']);

        \App\Models\Inbox\InboxCannedResponse::create(array_merge($request->all(), ['created_by' => auth()->id()]));

        return redirect()->route('admin.inbox.canned-responses')
            ->with('alert_type', 'success')
            ->with('message', 'Hurtigsvar opprettet');
    }

    private function getStudentContext($conversation): array
    {
        if (!$conversation->user_id) return [];

        $user = $conversation->customer;
        if (!$user) return [];

        $context = [
            'Navn' => $user->first_name . ' ' . $user->last_name,
            'E-post' => $user->email,
            'Rolle' => match ($user->role) { 1 => 'Admin', 2 => 'Elev', 3 => 'Redaktør', default => 'Ukjent' },
        ];

        try {
            $courses = $user->coursesTaken()->where('is_active', 1)->with('package.course')->get();
            if ($courses->isNotEmpty()) {
                $context['Aktive kurs'] = $courses->map(fn($ct) => $ct->package?->course?->title ?? 'Ukjent')->implode(', ');
            }
        } catch (\Exception $e) {}

        return $context;
    }

    public function downloadAttachment($filename)
    {
        $path = storage_path('app/inbox-attachments/' . basename($filename));
        if (!file_exists($path)) {
            abort(404, 'Vedlegg ikke funnet.');
        }
        return response()->download($path);
    }

    /**
     * Ta imot et bilde limt inn eller dratt på reply-feltet, lagre det i
     * public/inbox-images/ med et unikt filnavn, og returner en offentlig
     * URL som kan embed-es inline i e-posten. Gmail/Outlook/Apple Mail
     * laster bildet direkte fra denne URL-en når kunden åpner meldingen.
     */
    public function pasteImage(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'image' => 'required|file|mimes:jpeg,jpg,png,gif,webp|max:10240', // 10 MB
        ]);

        $file = $request->file('image');
        $ext = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'png');

        $dir = public_path('inbox-images');
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $filename = 'inbox-' . time() . '-' . \Illuminate\Support\Str::random(8) . '.' . $ext;
        $file->move($dir, $filename);

        // Bruk den offentlige www-URL-en så e-postmottakere får en ren
        // lenke som ikke avslører admin-subdomenet. Alle tre subdomener
        // deler samme filesystem på cPanel, så www serverer samme fil.
        $publicUrl = 'https://www.forfatterskolen.no/inbox-images/' . $filename;

        return response()->json([
            'url' => $publicUrl,
            'filename' => $filename,
        ]);
    }

    /**
     * Vis innstillinger-siden for innlogget admin (per-bruker signatur osv.)
     */
    public function settings()
    {
        $user = auth()->user();
        // Vi sender med både den lagrede signaturen (kan være null) og
        // den fallback-baserte standarden, slik at view-en kan vise
        // "Bruker for tiden default-signatur" hvis tomt.
        $defaultSignature = "Ha en fin dag!\nMvh {$user->full_name}\nForfatterskolen / Easywrite / Indiemoon Publishing";
        return view('backend.inbox.settings', [
            'user' => $user,
            'savedSignature' => $user->inbox_signature,
            'defaultSignature' => $defaultSignature,
            'effectiveSignature' => $user->getInboxSignature(),
        ]);
    }

    /**
     * Lagre per-bruker inbox-innstillinger.
     */
    public function storeSettings(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'inbox_signature' => 'nullable|string|max:2000',
        ]);

        $user = auth()->user();
        $sig = trim($request->input('inbox_signature', ''));
        $user->inbox_signature = $sig === '' ? null : $sig;
        $user->save();

        return redirect()->route('admin.inbox.settings')
            ->with('success', 'Innstillinger lagret.');
    }
}

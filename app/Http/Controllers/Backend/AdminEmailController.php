<?php

namespace App\Http\Controllers\Backend;

use App\EmailLog;
use App\EmailTemplate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AdminEmailController extends Controller
{
    /**
     * Register over alle Mail-klasser i systemet med metadata.
     */
    protected function emailRegistry(): array
    {
        return [
            'course_order' => [
                'name' => 'Kursbestilling',
                'category' => 'Salg',
                'mailable' => \App\Mail\CourseOrderMail::class,
                'description' => 'Sendes når en elev kjøper et kurs',
                'variables' => ['email_subject', 'email_content', 'actionText', 'actionUrl', 'user', 'package_id'],
            ],
            'registration' => [
                'name' => 'Registrering / Velkommen',
                'category' => 'Bruker',
                'mailable' => \App\Mail\RegistrationEmail::class,
                'description' => 'Sendes ved ny brukerregistrering',
                'variables' => ['user', 'actionText', 'actionUrl'],
            ],
            'password_reset' => [
                'name' => 'Passordtilbakestilling',
                'category' => 'Bruker',
                'mailable' => \App\Mail\PasswordResetEmail::class,
                'description' => 'Sendes når bruker ber om nytt passord',
                'variables' => ['actionText', 'actionUrl'],
            ],
            'email_confirmation' => [
                'name' => 'E-postbekreftelse',
                'category' => 'Bruker',
                'mailable' => \App\Mail\MultipleEmailConfirmation::class,
                'description' => 'Bekreftelses-e-post for ny e-postadresse',
                'variables' => ['name', 'email', 'token'],
            ],
            'subject_body' => [
                'name' => 'Generell e-post (Subject/Body)',
                'category' => 'System',
                'mailable' => \App\Mail\SubjectBodyEmail::class,
                'description' => 'Fleksibel mal brukt av mange funksjoner',
                'variables' => ['email_subject', 'email_message', 'from_name', 'from_email', 'view', 'view_data'],
            ],
            'add_mail_to_queue' => [
                'name' => 'Kø-e-post',
                'category' => 'System',
                'mailable' => \App\Mail\AddMailToQueueMail::class,
                'description' => 'E-post sendt via kø-systemet',
                'variables' => ['subject', 'message', 'from_email', 'from_name', 'track_code'],
            ],
            'free_course_new_user' => [
                'name' => 'Gratiskurs — ny bruker',
                'category' => 'Gratiskurs',
                'mailable' => \App\Mail\FreeCourseNewUserEmail::class,
                'description' => 'Sendes til nye brukere som melder seg på gratiskurs',
                'variables' => ['email_message', 'email_subject'],
            ],
            'discussion' => [
                'name' => 'Ny diskusjon',
                'category' => 'Fellesskap',
                'mailable' => \App\Mail\DiscussionEmail::class,
                'description' => 'Varsel om ny diskusjon i gruppe',
                'variables' => ['receiver', 'sender', 'type', 'discussion_url', 'discussion_title', 'group_url', 'group_title'],
            ],
            'discussion_replies' => [
                'name' => 'Svar på diskusjon',
                'category' => 'Fellesskap',
                'mailable' => \App\Mail\DiscussionRepliesEmail::class,
                'description' => 'Varsel om nytt svar i diskusjon',
                'variables' => ['receiver', 'sender', 'type', 'discussion_url', 'discussion_title', 'email_message'],
            ],
            'assignment_submitted' => [
                'name' => 'Oppgave levert',
                'category' => 'Oppgaver',
                'mailable' => \App\Mail\AssignmentSubmittedEmail::class,
                'description' => 'Sendes til redaktør når oppgave er levert',
                'variables' => ['email_message'],
            ],
            'assignment_manuscript_to_list' => [
                'name' => 'Manus til oppgavegruppe',
                'category' => 'Oppgaver',
                'mailable' => \App\Mail\AssignmentManuscriptEmailToList::class,
                'description' => 'Sendes til oppgavegruppe med manuskript',
                'variables' => ['data'],
            ],
            'send_email_message_only' => [
                'name' => 'Oppgave levert — bekreftelse',
                'category' => 'Oppgaver',
                'mailable' => \App\Mail\SendEmailMessageOnly::class,
                'description' => 'Bekreftelse til elev etter innlevering',
                'variables' => ['email_message'],
            ],
            'coaching_suggestion_date' => [
                'name' => 'Coaching — foreslå dato',
                'category' => 'Coaching',
                'mailable' => \App\Mail\CoachingSuggestionDateEmail::class,
                'description' => 'Forslag til coachingtidspunkt',
                'variables' => ['sender', 'suggested_dates'],
            ],
            'new_conversation_message' => [
                'name' => 'Ny melding i samtale',
                'category' => 'Meldinger',
                'mailable' => \App\Mail\NewConversationMessageMail::class,
                'description' => 'Varsel om ny melding i meldingssystemet',
                'variables' => ['conversation', 'message', 'senderName', 'recipientName', 'messagePreview', 'conversationUrl'],
            ],
        ];
    }

    /**
     * Oversiktstabell med alle Mail-klasser.
     */
    public function index(): View
    {
        $registry = $this->emailRegistry();

        // Hent logg-statistikk per mailable_class
        $stats = EmailLog::selectRaw('mailable_class, COUNT(*) as total, MAX(created_at) as last_sent')
            ->groupBy('mailable_class')
            ->get()
            ->keyBy('mailable_class');

        // Grupper etter kategori
        $categories = [];
        foreach ($registry as $type => $info) {
            $mailableClass = $info['mailable'];
            $stat = $stats->get($mailableClass);
            $info['type'] = $type;
            $info['total_sent'] = $stat ? $stat->total : 0;
            $info['last_sent'] = $stat ? $stat->last_sent : null;
            $categories[$info['category']][] = $info;
        }

        return view('backend.admin-emails.index', compact('categories', 'registry'));
    }

    /**
     * E-post-logg (filtrerbar).
     */
    public function log(Request $request): View
    {
        $query = EmailLog::latest();

        if ($request->filled('type')) {
            $registry = $this->emailRegistry();
            if (isset($registry[$request->type])) {
                $query->where('mailable_class', $registry[$request->type]['mailable']);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('to_email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('to_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $logs = $query->paginate(50)->appends($request->all());
        $registry = $this->emailRegistry();

        // Lag lookup fra mailable-klasse til norsk navn
        $mailableNames = [];
        foreach ($registry as $type => $info) {
            $mailableNames[$info['mailable']] = $info['name'];
        }

        return view('backend.admin-emails.log', compact('logs', 'registry', 'mailableNames'));
    }

    /**
     * Forhåndsvis e-post med testdata.
     */
    public function preview(string $type)
    {
        $registry = $this->emailRegistry();
        if (!isset($registry[$type])) {
            abort(404, 'Ukjent e-posttype');
        }

        $info = $registry[$type];

        // Hent tilhørende EmailTemplate om den finnes
        $template = EmailTemplate::where('page_name', $type)->first();

        return view('backend.admin-emails.preview', compact('type', 'info', 'template'));
    }

    /**
     * Send test-e-post.
     */
    public function sendTest(Request $request, string $type)
    {
        $request->validate([
            'test_email' => 'required|email',
        ]);

        $registry = $this->emailRegistry();
        if (!isset($registry[$type])) {
            return back()->with('error', 'Ukjent e-posttype.');
        }

        $info = $registry[$type];

        try {
            // Send en enkel test-e-post via SubjectBodyEmail
            $testData = [
                'email_subject' => '[TEST] ' . $info['name'],
                'email_message' => '<h2>Test-e-post</h2><p>Dette er en test av e-posttypen: <strong>' . $info['name'] . '</strong></p><p>Kategori: ' . $info['category'] . '</p><p>Beskrivelse: ' . $info['description'] . '</p><p style="color:#888; font-size:12px;">Sendt fra admin e-postoversikt</p>',
            ];

            Mail::to($request->test_email)->send(new \App\Mail\SubjectBodyEmail($testData));

            return back()->with('success', 'Test-e-post sendt til ' . $request->test_email);
        } catch (\Throwable $e) {
            return back()->with('error', 'Kunne ikke sende: ' . $e->getMessage());
        }
    }

    /**
     * Rediger e-postmal.
     */
    public function edit(string $type): View
    {
        $registry = $this->emailRegistry();
        if (!isset($registry[$type])) {
            abort(404, 'Ukjent e-posttype');
        }

        $info = $registry[$type];

        // Hent eller opprett EmailTemplate
        $template = EmailTemplate::firstOrNew(['page_name' => $type]);
        if (!$template->exists) {
            $template->subject = '';
            $template->email_content = '';
        }

        return view('backend.admin-emails.edit', compact('type', 'info', 'template'));
    }

    /**
     * Lagre endringer i e-postmal.
     */
    public function update(Request $request, string $type)
    {
        $registry = $this->emailRegistry();
        if (!isset($registry[$type])) {
            return back()->with('error', 'Ukjent e-posttype.');
        }

        $request->validate([
            'subject' => 'required|string|max:255',
            'email_content' => 'required|string',
        ]);

        EmailTemplate::updateOrCreate(
            ['page_name' => $type],
            [
                'subject' => $request->subject,
                'email_content' => $request->email_content,
            ]
        );

        return redirect()->route('admin.emails.index')->with('success', 'E-postmal oppdatert.');
    }
}

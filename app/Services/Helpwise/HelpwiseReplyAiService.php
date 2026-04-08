<?php

namespace App\Services\Helpwise;

use App\User;
use App\HelpwiseConversation;
use App\HelpwiseMessage;
use App\Models\AiKnownIssue;
use App\Models\HelpwiseReplyExample;
use App\Models\Inbox\InboxConversation;
use App\Models\Inbox\InboxMessage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HelpwiseReplyAiService
{
    /**
     * Generate an AI draft reply for a customer message.
     * Returns the draft text in Norwegian, never auto-sends.
     */
    public function generateDraftReply(HelpwiseConversation $conversation, ?HelpwiseMessage $latestMessage = null): ?string
    {
        $studentContext = $this->getStudentContext($conversation);
        $messageHistory = $this->getMessageHistory($conversation);
        $customerMessage = $latestMessage?->body_plain ?? $latestMessage?->body ?? '';

        $prompt = $this->buildPrompt($conversation, $customerMessage, $studentContext, $messageHistory);

        try {
            $reply = $this->callAi($prompt);

            if ($reply) {
                Log::info('Helpwise AI: draft reply generated', [
                    'conversation_id' => $conversation->id,
                    'helpwise_id' => $conversation->helpwise_id,
                    'length' => strlen($reply),
                ]);
            }

            return $reply;
        } catch (\Exception $e) {
            Log::error('Helpwise AI: draft generation failed', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Build the AI prompt with full context about the student and conversation.
     */
    private function buildPrompt(
        HelpwiseConversation $conversation,
        string $customerMessage,
        array $studentContext,
        string $messageHistory
    ): string {
        $inbox = $conversation->inbox ?? 'Ukjent';
        $customerName = $conversation->customer_name ?? 'kunde';

        $studentInfo = '';
        if (!empty($studentContext)) {
            $studentInfo = "\n\nELEVINFORMASJON FRA DATABASEN:\n";
            foreach ($studentContext as $key => $value) {
                if ($value) $studentInfo .= "- {$key}: {$value}\n";
            }
        }

        $historySection = '';
        if ($messageHistory) {
            $historySection = "\n\nTIDLIGERE MELDINGER I SAMTALEN:\n{$messageHistory}";
        }

        $examplesSection = $this->getReplyExamples();
        $knowledgeSection = $this->getKnowledgeContext();

        return <<<PROMPT
Du er en vennlig og profesjonell kundebehandler for Forfatterskolen, Norges ledende nettbaserte skriveskole.

Du skriver ALLTID på norsk (bokmål).
Du er hjelpsom, varm og profesjonell.

VIKTIGE REGLER:
1. SVAR KONKRET med den informasjonen du har. Du har tilgang til elevens data nedenfor — BRUK DEN AKTIVT.
2. Hvis eleven spør om webinar-lenke og du har den i ELEVINFORMASJON — gi den direkte.
3. Hvis eleven spør om frist og du har oppgavestatus — svar med den konkrete datoen.
4. Hvis eleven har innloggingsproblemer — gi dem innloggingslenken fra ELEVINFORMASJON.
5. Hvis eleven spør om kurs/priser — svar med konkrete priser fra listen nedenfor.
6. Lag ALDRI generiske svar som "vi skal sjekke og komme tilbake" når du har svaret i dataene.
7. Si KUN at du skal sjekke videre hvis informasjonen VIRKELIG ikke finnes i konteksten.
8. Svar kort og presist — maks 3-4 setninger for enkle spørsmål.
9. Sjekk ALLTID "AKTUELL KUNNSKAP OG TEKNISKE FORHOLD" nedenfor — hvis kundens problem matcher en kjent feil eller en nylig fiks, nevn det.{$knowledgeSection}

OM FORFATTERSKOLEN:
- Forfatterskolen tilbyr nettbaserte skrivekurs (årskurs, halvårskurs, sjangerkurs, etc.)
- Elever leverer innleveringer/manuskripter som redaktører gir tilbakemelding på
- Forfatterskolen tilbyr også manustjenester (redaktør, språkvask, korrektur) for selvpublisister
- Kurs har faste innleveringsfrister, men utsettelse kan gis ved behov
- Webinarer og mentormøter er en del av kursopplevelsen
- Rektor: Kristine S. Henningsen
- Eier/daglig leder: Sven Inge Henningsen
- Kontakt: post@forfatterskolen.no / 411 23 555
- Nettside: forfatterskolen.no

FAGKUNNSKAP OM SKRIVING (bruk når relevant):
- En roman er typisk 60.000-100.000 ord (ca. 200-400 sider)
- En novelle er typisk 2.000-10.000 ord
- En barnebok er typisk 1.000-10.000 ord avhengig av aldersgruppe
- Sakprosa/selvbiografi varierer mye, ofte 40.000-80.000 ord
- Dikt har ingen fast lengde
- 1 normside = ca. 1.500 tegn med mellomrom (ca. 250 ord)
- 1 A4-side ≈ 340 ord. En bok på 200 sider = ca. 68.000 ord

MANUSTJENESTER OG PRISER:
- Manusutvikling: Profesjonell tilbakemelding på manus. Pris: 0,15 kr/ord
  - Manus 1 = opptil 7.500 ord, Manus 2 = 17.500, Manus 3 = 35.000, Manus 4 = 52.500, Manus 5 = 70.000
- Språkvask: Grundig språklig gjennomgang av manus
- Korrektur: Siste finpuss før trykk
- Selvpublisering via Indiemoon (søsterselskap): Omslag, layout, trykk, e-bok

AKTIVE KURS OG PRISER:
1. Romankurs i gruppe (oppstart 20.04.2026): Basic kr 10.900, Standard kr 13.900, Pro kr 18.900
   - 10 kursmoduler med videoer, skriftlig materiale, ukentlige live-webinarer, redaktørtilbakemelding
2. Årskurs 2026: Standard kr 44.000 — Skriv boken din på ett år med personlig redaktør
3. Påbyggingsår 2026: kr 35.000 — Fordypningsår med nærlesing av manus og egen redaktør
4. Barnebokkurs med Gro Dahle (oppstart 16.02.2026): fra kr 11.900 — Gruppekurs for barnebok
5. Krimkurs med Tom Egeland: fra kr 9.400 — Lær å skrive krim/spenning (øyeblikkelig tilgang)
6. Serieromankurs med Ida & Kaja: fra kr 10.900 — Lær å skrive serier (øyeblikkelig tilgang)
7. Sakprosakurs med Kjersti Wold: fra kr 5.900 — Fagbok, håndbok, dokumentar (øyeblikkelig tilgang)
8. Skriv ditt liv med Kjersti Wold: fra kr 6.900 — Slektshistorie/biografi (øyeblikkelig tilgang)
9. Skriv for film og TV med Kjersti Steinsbø: fra kr 10.900 (øyeblikkelig tilgang)
10. Diktkurs med Gro Dahle: kr 6.900 (øyeblikkelig tilgang)
11. Kom i gang med Gro Dahle: kr 1.900 — Kort igangsetterkurs
12. Mentormøter: kr 1.990/år — Ukentlige nettmøter med kjente forfattere og redaktører
13. Gratis skrivekurs i 3 deler — åpent for alle

PAKKEFORSKJELLER:
- Basic: Kursmoduler + videoer + webinarer
- Standard: Basic + tilbakemelding fra redaktør på manus
- Pro: Standard + ekstra manusutvikling + prioritert tilbakemelding

GENERELT:
- Alle kurs er nettbaserte, kan følges hjemmefra
- Opptak av alle webinarer tilgjengelig i portalen
- Mentormøter inkludert i alle betalte kurs
- Eleven har tilgang til kurset i ett år fra oppstart
- Mulig å forlenge til symbolsk pris
- Innleveringer kan utsettes ved behov
{$examplesSection}

INBOX: {$inbox}
KUNDENS NAVN: {$customerName}
KUNDENS E-POST: {$conversation->customer_email}
{$studentInfo}
{$historySection}

KUNDENS SISTE MELDING:
{$customerMessage}

Skriv et passende svarkutkast. Husk:
- Start med å hilse på kunden ved fornavn hvis mulig
- Svar direkte på spørsmålet
- Vær hjelpsom, varm og positiv - men ikke overdrevent
- Hold deg kort og direkte, lik eksemplene ovenfor
- Bruk gjerne smilefjes som :-) eller :) der det passer naturlig
- ALLTID når du gir en lenke (innloggingslenke, webinar-lenke, etc): bruk markdown-format slik at den blir kort og klikkbar i e-posten. Eksempel: "[innloggingslenke](https://www.forfatterskolen.no/auth/login/email/...)" — IKKE lim inn rå URL-er midt i teksten.
- Avslutt ALLTID med nøyaktig dette (ingen tittel, ingen "Kundebehandler" eller lignende, ingen ekstra linjer mellom):
  {$this->getSignatureBlock()}
- IKKE skriv "Hei [Navn]" hvis du ikke vet navnet
- Matcher stilen og tonen fra eksemplene
PROMPT;
    }

    /**
     * Get student context from the database if the conversation is linked to a user.
     */
    private function getStudentContext(HelpwiseConversation $conversation): array
    {
        if (!$conversation->user_id) {
            // Try to find by email
            $user = $conversation->customer_email
                ? User::where('email', $conversation->customer_email)->first()
                : null;

            if ($user) {
                $conversation->update(['user_id' => $user->id]);
            } else {
                return [];
            }
        } else {
            $user = User::find($conversation->user_id);
        }

        if (!$user) return [];

        $context = [
            'Navn' => $user->first_name . ' ' . $user->last_name,
            'E-post' => $user->email,
            'Rolle' => $this->getRoleName($user->role),
        ];

        // Check active courses
        $courses = $user->coursesTaken()
            ->where('is_active', 1)
            ->with('package')
            ->get();

        if ($courses->isNotEmpty()) {
            $courseNames = $courses->map(fn($ct) => $ct->package?->course?->title ?? 'Ukjent kurs')->implode(', ');
            $context['Aktive kurs'] = $courseNames;
        }

        // Check if they have pending assignments
        try {
            $pendingAssignments = \App\AssignmentManuscript::where('user_id', $user->id)
                ->where('status', 0)
                ->count();
            if ($pendingAssignments > 0) {
                $context['Ventende oppgaver'] = $pendingAssignments;
            }
        } catch (\Exception $e) {
            // Table might not exist
        }

        // Check shop manuscripts
        $manuscripts = \App\ShopManuscriptsTaken::where('user_id', $user->id)
            ->where('is_active', 1)
            ->count();

        if ($manuscripts > 0) {
            $context['Aktive manustjenester'] = $manuscripts;
        }

        // Upcoming webinars (next 14 days)
        try {
            $upcomingWebinars = \App\WebinarRegistrant::where('user_id', $user->id)
                ->whereHas('webinar', fn($q) => $q->where('start_date', '>=', now())->where('start_date', '<=', now()->addDays(14)))
                ->with('webinar')
                ->get();

            if ($upcomingWebinars->isNotEmpty()) {
                $webinarList = $upcomingWebinars->map(function ($reg) {
                    $w = $reg->webinar;
                    $date = \Carbon\Carbon::parse($w->start_date)->format('d.m.Y H:i');
                    $joinUrl = $reg->join_url ?: 'Ingen lenke';
                    return "{$w->title} ({$date}) — Lenke: {$joinUrl}";
                })->implode("\n  ");
                $context['Kommende webinarer'] = $webinarList;
            }
        } catch (\Exception $e) {}

        // Assignment deadlines
        try {
            $assignments = \App\AssignmentManuscript::where('user_id', $user->id)
                ->where('status', 0)
                ->with('assignment')
                ->get();

            if ($assignments->isNotEmpty()) {
                $assignmentList = $assignments->map(function ($m) {
                    $title = $m->assignment->title ?? 'Ukjent';
                    $deadline = $m->editor_expected_finish ?: ($m->assignment->editor_expected_finish ?? 'Ikke satt');
                    $hasFile = $m->filename ? 'Levert' : 'Ikke levert';
                    return "{$title} (Frist: {$deadline}, Status: {$hasFile})";
                })->implode(', ');
                $context['Oppgavestatus'] = $assignmentList;
            }
        } catch (\Exception $e) {}

        // Extension requests
        try {
            $extensions = \App\Models\AssignmentExtensionRequest::where('user_id', $user->id)
                ->whereIn('status', ['pending', 'approved'])
                ->with('assignment')
                ->get();

            if ($extensions->isNotEmpty()) {
                $extList = $extensions->map(fn($e) => $e->assignment->title . ' (' . $e->status . ', ønsket: ' . $e->requested_deadline->format('d.m.Y') . ')')->implode(', ');
                $context['Utsettelsesforespørsler'] = $extList;
            }
        } catch (\Exception $e) {}

        // Login link for this user
        try {
            $context['Innloggingslenke'] = route('auth.login.email', encrypt($user->email));
        } catch (\Exception $e) {}

        // Coaching sessions
        try {
            $coaching = \App\CoachingTimerManuscript::where('user_id', $user->id)
                ->where('status', 0)
                ->whereNotNull('editor_time_slot_id')
                ->with(['editor', 'timeSlot'])
                ->get();

            if ($coaching->isNotEmpty()) {
                $coachingList = $coaching->map(function ($c) {
                    $editor = $c->editor ? $c->editor->full_name : 'Ikke tildelt';
                    $date = $c->timeSlot ? \Carbon\Carbon::parse($c->timeSlot->date . ' ' . $c->timeSlot->start_time)->format('d.m.Y H:i') : 'Ikke satt';
                    return "Coaching med {$editor} ({$date})";
                })->implode(', ');
                $context['Coaching-timer'] = $coachingList;
            }
        } catch (\Exception $e) {}

        return $context;
    }

    /**
     * Get recent message history for the conversation.
     * Checks both HelpwiseMessage and InboxMessage tables.
     */
    private function getMessageHistory(HelpwiseConversation $conversation): string
    {
        // Try HelpwiseMessage first
        $messages = $conversation->messages()
            ->orderBy('message_at')
            ->limit(10)
            ->get();

        // If no HelpwiseMessages, try InboxMessage (used for imported conversations)
        if ($messages->isEmpty()) {
            $inboxMessages = InboxMessage::where('conversation_id', $conversation->id)
                ->where('is_draft', false)
                ->orderBy('created_at')
                ->limit(10)
                ->get();

            if ($inboxMessages->isEmpty()) return '';

            $history = '';
            foreach ($inboxMessages as $msg) {
                $direction = $msg->direction === 'outbound' ? 'AGENT' : 'KUNDE';
                $time = $msg->created_at?->format('d.m H:i') ?? '';
                $body = \Illuminate\Support\Str::limit(strip_tags($msg->body_plain ?? $msg->body ?? ''), 300);
                $history .= "[{$time}] {$direction}: {$body}\n\n";
            }
            return $history;
        }

        $history = '';
        foreach ($messages as $msg) {
            $direction = $msg->direction === 'outbound' ? 'AGENT' : 'KUNDE';
            $time = $msg->message_at?->format('d.m H:i') ?? '';
            $body = \Illuminate\Support\Str::limit(strip_tags($msg->body_plain ?? $msg->body ?? ''), 300);
            $history .= "[{$time}] {$direction}: {$body}\n\n";
        }

        return $history;
    }

    /**
     * Build a knowledge context block combining the curated markdown file
     * and any active known issues from the database. Both sources are
     * optional — if neither has content, returns empty string.
     */
    private function getKnowledgeContext(): string
    {
        $sections = [];

        // 1. Static curated knowledge from docs/ai-knowledge.md
        try {
            $path = base_path('docs/ai-knowledge.md');
            if (File::exists($path)) {
                $content = trim(File::get($path));
                if ($content !== '') {
                    $sections[] = "GENERELL KUNNSKAPSBASE:\n{$content}";
                }
            }
        } catch (\Exception $e) {
            // File missing or unreadable — skip silently
        }

        // 2. Active known issues from the database (managed via admin UI)
        try {
            $issues = AiKnownIssue::active()
                ->orderByRaw("FIELD(severity, 'high', 'medium', 'low', 'info')")
                ->orderByDesc('discovered_at')
                ->limit(30)
                ->get();

            if ($issues->isNotEmpty()) {
                $issueList = "AKTIVE KJENTE PROBLEMER (bruk når relevant):\n";
                foreach ($issues as $issue) {
                    $sevTag = strtoupper($issue->severity);
                    $cat = $issue->category ? " [{$issue->category}]" : '';
                    $disc = $issue->discovered_at ? ' (oppdaget ' . $issue->discovered_at->format('d.m.Y') . ')' : '';
                    $issueList .= "\n- [{$sevTag}]{$cat} {$issue->title}{$disc}";
                    $issueList .= "\n  Beskrivelse: {$issue->description}";
                    if ($issue->workaround) {
                        $issueList .= "\n  Foreslå dette til eleven: {$issue->workaround}";
                    }
                    $issueList .= "\n";
                }
                $sections[] = $issueList;
            }
        } catch (\Exception $e) {
            // Table might not exist yet — skip silently
        }

        if (empty($sections)) {
            return '';
        }

        return "\n\n=== AKTUELL KUNNSKAP OG TEKNISKE FORHOLD ===\n" . implode("\n\n", $sections) . "\n=== SLUTT KUNNSKAP ===\n";
    }

    /**
     * Get real reply examples from the database to teach the AI our writing style.
     */
    private function getReplyExamples(): string
    {
        $examples = HelpwiseReplyExample::orderBy('sent_at', 'desc')
            ->limit(6)
            ->get();

        if ($examples->isEmpty()) {
            return '';
        }

        $section = "\n\nEKSEMPLER PÅ HVORDAN VI SVARER (kopier stilen, IKKE innholdet):\n";

        foreach ($examples as $i => $ex) {
            // Try to find the customer message that was replied to
            $customerMsg = '';
            $inbound = InboxMessage::where('conversation_id', $ex->conversation_id)
                ->where('direction', 'inbound')
                ->orderBy('created_at', 'asc')
                ->first();

            if ($inbound) {
                $customerMsg = \Illuminate\Support\Str::limit(strip_tags($inbound->body ?? ''), 150);
            }

            $replyBody = \Illuminate\Support\Str::limit(strip_tags($ex->reply_body), 250);
            $num = $i + 1;

            $section .= "\nEksempel {$num}:";
            if ($customerMsg) {
                $section .= "\n  Kunde: {$customerMsg}";
            }
            $section .= "\n  Vårt svar: {$replyBody}\n";
        }

        return $section;
    }

    private function getSenderName(): ?string
    {
        $user = auth()->user();
        return $user ? $user->full_name : null;
    }

    /**
     * Build the signature block for the AI prompt.
     * When no user is authenticated (webhook generation), we omit the
     * personal-name line so the signature doesn't end up with
     * "Forfatterskolen" appearing twice in a row.
     */
    private function getSignatureBlock(): string
    {
        $name = $this->getSenderName();
        if ($name) {
            return "Ha en fin dag!\n  Mvh {$name}\n  Forfatterskolen / Easywrite / Indiemoon Publishing";
        }
        return "Ha en fin dag!\n  Mvh\n  Forfatterskolen / Easywrite / Indiemoon Publishing";
    }

    private function callAi(string $prompt): ?string
    {
        $provider = config('ad_os.ai_provider', 'openai');

        if ($provider === 'anthropic') {
            $response = Http::withHeaders([
                'x-api-key' => config('services.anthropic.key'),
                'Content-Type' => 'application/json',
                'anthropic-version' => '2023-06-01',
            ])->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-sonnet-4-20250514',
                'max_tokens' => 1024,
                'messages' => [
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            return $response->json('content.0.text');
        }

        // Default: OpenAI
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.openai.api_key'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-4o',
            'messages' => [
                ['role' => 'system', 'content' => 'Du er en kundebehandler for Forfatterskolen. Skriv alltid på norsk bokmål.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.7,
            'max_tokens' => 1024,
        ]);

        return $response->json('choices.0.message.content');
    }

    /**
     * Generate AI draft reply for an InboxConversation (brukes av inbox webhook).
     */
    public function generateInboxDraftReply(InboxConversation $conversation, InboxMessage $message): ?string
    {
        // Hent elevkontekst via e-post
        $studentContext = [];
        $user = $conversation->user_id
            ? User::find($conversation->user_id)
            : User::where('email', $conversation->customer_email)->first();

        if ($user) {
            if (!$conversation->user_id) {
                $conversation->update(['user_id' => $user->id]);
            }
            $studentContext = [
                'Navn' => $user->first_name . ' ' . $user->last_name,
                'E-post' => $user->email,
                'Rolle' => $this->getRoleName($user->role),
            ];

            $courses = $user->coursesTaken()->where('is_active', 1)->with('package')->get();
            if ($courses->isNotEmpty()) {
                $studentContext['Aktive kurs'] = $courses->map(fn($ct) => $ct->package?->course?->title ?? 'Ukjent kurs')->implode(', ');
            }

            $manuscripts = \App\ShopManuscriptsTaken::where('user_id', $user->id)->where('is_active', 1)->count();
            if ($manuscripts > 0) {
                $studentContext['Aktive manustjenester'] = $manuscripts;
            }

            // Webinarer
            try {
                $webinars = \App\WebinarRegistrant::where('user_id', $user->id)
                    ->whereHas('webinar', fn($q) => $q->where('start_date', '>=', now())->where('start_date', '<=', now()->addDays(14)))
                    ->with('webinar')
                    ->get();
                if ($webinars->isNotEmpty()) {
                    $studentContext['Kommende webinarer'] = $webinars->map(fn($r) => $r->webinar->title . ' (' . \Carbon\Carbon::parse($r->webinar->start_date)->format('d.m.Y H:i') . ') Lenke: ' . ($r->join_url ?: 'mangler'))->implode(', ');
                }
            } catch (\Exception $e) {}

            // Oppgavefrister
            try {
                $assignments = \App\AssignmentManuscript::where('user_id', $user->id)->where('status', 0)->with('assignment')->get();
                if ($assignments->isNotEmpty()) {
                    $studentContext['Oppgavestatus'] = $assignments->map(fn($m) => ($m->assignment->title ?? '?') . ' (Frist: ' . ($m->editor_expected_finish ?: 'ikke satt') . ', ' . ($m->filename ? 'Levert' : 'Ikke levert') . ')')->implode(', ');
                }
            } catch (\Exception $e) {}

            // Innloggingslenke
            try {
                $studentContext['Innloggingslenke'] = route('auth.login.email', encrypt($user->email));
            } catch (\Exception $e) {}
        }

        // Hent meldingshistorikk
        $inboxMessages = InboxMessage::where('conversation_id', $conversation->id)
            ->where('is_draft', false)
            ->orderBy('created_at')
            ->limit(10)
            ->get();

        $history = '';
        foreach ($inboxMessages as $msg) {
            $direction = $msg->direction === 'outbound' ? 'AGENT' : 'KUNDE';
            $time = $msg->created_at?->format('d.m H:i') ?? '';
            $body = \Illuminate\Support\Str::limit(strip_tags($msg->body_plain ?? $msg->body ?? ''), 300);
            $history .= "[{$time}] {$direction}: {$body}\n\n";
        }

        $customerMessage = $message->body_plain ?? strip_tags($message->body ?? '');

        // Gjenbruk buildPrompt via et temporaert HelpwiseConversation-lignende objekt
        // Enklere: bygg prompt direkte med samme mal
        $inbox = $conversation->inbox ?? 'Ukjent';
        $customerName = $conversation->customer_name ?? 'kunde';

        $studentInfo = '';
        if (!empty($studentContext)) {
            $studentInfo = "\n\nELEVINFORMASJON FRA DATABASEN:\n";
            foreach ($studentContext as $key => $value) {
                if ($value) $studentInfo .= "- {$key}: {$value}\n";
            }
        }

        $historySection = $history ? "\n\nTIDLIGERE MELDINGER I SAMTALEN:\n{$history}" : '';
        $examplesSection = $this->getReplyExamples();
        $knowledgeSection = $this->getKnowledgeContext();

        $prompt = <<<PROMPT
Du er en vennlig og profesjonell kundebehandler for Forfatterskolen, Norges ledende nettbaserte skriveskole.

Du skriver ALLTID på norsk (bokmål).
Du er hjelpsom, varm og profesjonell.

VIKTIGE REGLER:
1. SVAR KONKRET med den informasjonen du har. Du har tilgang til elevens data nedenfor — BRUK DEN AKTIVT.
2. Hvis eleven spør om webinar-lenke og du har den i ELEVINFORMASJON — gi den direkte.
3. Hvis eleven spør om frist og du har oppgavestatus — svar med den konkrete datoen.
4. Hvis eleven har innloggingsproblemer — gi dem innloggingslenken fra ELEVINFORMASJON.
5. Hvis eleven spør om kurs/priser — svar med konkrete priser fra listen nedenfor.
6. Lag ALDRI generiske svar som "vi skal sjekke og komme tilbake" når du har svaret i dataene.
7. Si KUN at du skal sjekke videre hvis informasjonen VIRKELIG ikke finnes i konteksten.
8. Svar kort og presist — maks 3-4 setninger for enkle spørsmål.
9. Sjekk ALLTID "AKTUELL KUNNSKAP OG TEKNISKE FORHOLD" nedenfor — hvis kundens problem matcher en kjent feil eller en nylig fiks, nevn det.{$knowledgeSection}
{$examplesSection}

INBOX: {$inbox}
KUNDENS NAVN: {$customerName}
KUNDENS E-POST: {$conversation->customer_email}
{$studentInfo}
{$historySection}

KUNDENS SISTE MELDING:
{$customerMessage}

Skriv et passende svarkutkast. Husk:
- Start med å hilse på kunden ved fornavn hvis mulig
- Svar direkte på spørsmålet
- Vær hjelpsom, varm og positiv - men ikke overdrevent
- Hold deg kort og direkte, lik eksemplene ovenfor
- Bruk gjerne smilefjes som :-) eller :) der det passer naturlig
- ALLTID når du gir en lenke (innloggingslenke, webinar-lenke, etc): bruk markdown-format slik at den blir kort og klikkbar i e-posten. Eksempel: "[innloggingslenke](https://www.forfatterskolen.no/auth/login/email/...)" — IKKE lim inn rå URL-er midt i teksten.
- Avslutt ALLTID med nøyaktig dette (ingen tittel, ingen "Kundebehandler" eller lignende, ingen ekstra linjer mellom):
  {$this->getSignatureBlock()}
- IKKE skriv "Hei [Navn]" hvis du ikke vet navnet
- Matcher stilen og tonen fra eksemplene
PROMPT;

        try {
            $reply = $this->callAi($prompt);
            if ($reply) {
                Log::info('Inbox AI: utkast generert', [
                    'conversation_id' => $conversation->id,
                    'length' => strlen($reply),
                ]);
            }
            return $reply;
        } catch (\Exception $e) {
            Log::error('Inbox AI: utkast-generering feilet', ['error' => $e->getMessage()]);
            return null;
        }
    }

    private function getRoleName(int $role): string
    {
        return match ($role) {
            1 => 'Admin',
            2 => 'Elev',
            3 => 'Redaktør',
            4 => 'Giutbok-admin',
            default => 'Ukjent',
        };
    }
}

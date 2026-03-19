@extends('backend.layouts.app')
@section('content')
<div class="container-fluid" style="padding: 20px;">
    <h2><i class="fa fa-envelope"></i> E-postoversikt</h2>
    <p class="text-muted">Komplett oversikt over alle e-poster som sendes fra systemet.</p>

    <style>
        .email-section { margin-bottom: 30px; }
        .email-section h3 { color: #862736; border-bottom: 2px solid #862736; padding-bottom: 8px; margin-bottom: 15px; }
        .email-card { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; margin-bottom: 10px; }
        .email-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .email-card h5 { margin: 0 0 8px 0; color: #333; }
        .email-meta { font-size: 12px; color: #888; }
        .email-meta span { margin-right: 15px; }
        .badge-trigger { background: #862736; color: #fff; padding: 2px 8px; border-radius: 12px; font-size: 11px; }
        .badge-cron { background: #2196F3; color: #fff; padding: 2px 8px; border-radius: 12px; font-size: 11px; }
        .badge-auto { background: #4CAF50; color: #fff; padding: 2px 8px; border-radius: 12px; font-size: 11px; }
        .badge-template { background: #FF9800; color: #fff; padding: 2px 8px; border-radius: 12px; font-size: 11px; }
        .stats-row { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
        .stat-box { background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px 20px; flex: 1; min-width: 150px; text-align: center; }
        .stat-box .number { font-size: 28px; font-weight: bold; color: #862736; }
        .stat-box .label { font-size: 12px; color: #888; }
    </style>

    {{-- Statistikk --}}
    <div class="stats-row">
        <div class="stat-box">
            <div class="number">{{ \App\EmailTemplate::count() }}</div>
            <div class="label">Database-maler</div>
        </div>
        <div class="stat-box">
            <div class="number">{{ \DB::table('email_history')->count() }}</div>
            <div class="label">E-poster sendt totalt</div>
        </div>
        <div class="stat-box">
            <div class="number">{{ \DB::table('email_history')->whereDate('created_at', today())->count() }}</div>
            <div class="label">Sendt i dag</div>
        </div>
        <div class="stat-box">
            <div class="number">{{ \DB::table('email_history')->whereDate('created_at', '>=', now()->subDays(7))->count() }}</div>
            <div class="label">Siste 7 dager</div>
        </div>
    </div>

    {{-- 1. Transaksjonelle --}}
    <div class="email-section">
        <h3><i class="fa fa-bolt"></i> Transaksjonelle e-poster (bruker-trigget)</h3>

        <div class="email-card">
            <h5>Registrering — Velkommen <span class="badge-trigger">Bruker-trigget</span></h5>
            <p>Sendes automatisk nar en ny bruker oppretter konto.</p>
            <div class="email-meta">
                <span><i class="fa fa-envelope"></i> Fra: post@forfatterskolen.no</span>
                <span><i class="fa fa-file"></i> Mal: emails.registration</span>
            </div>
        </div>

        <div class="email-card">
            <h5>Passord-reset <span class="badge-trigger">Bruker-trigget</span></h5>
            <p>Sendes nar bruker ber om nytt passord via "Glemt passord".</p>
            <div class="email-meta">
                <span><i class="fa fa-envelope"></i> Fra: postmail@forfatterskolen.no</span>
                <span><i class="fa fa-file"></i> Mal: emails.passwordreset</span>
            </div>
        </div>

        <div class="email-card">
            <h5>Magic Link innlogging <span class="badge-trigger">Bruker-trigget</span></h5>
            <p>Sendes nar bruker klikker "Send meg innloggingslenke".</p>
            <div class="email-meta">
                <span><i class="fa fa-envelope"></i> Fra: post@forfatterskolen.no</span>
                <span><i class="fa fa-file"></i> Mal: emails.mail_to_queue_branded</span>
            </div>
        </div>

        <div class="email-card">
            <h5>Kursbestilling — Ordrebekreftelse <span class="badge-trigger">Bruker-trigget</span></h5>
            <p>Sendes automatisk etter kjop av kurs.</p>
            <div class="email-meta">
                <span><i class="fa fa-envelope"></i> Fra: Konfigurerbar per mal</span>
                <span><i class="fa fa-file"></i> Mal: emails.course_order via CourseOrderJob</span>
            </div>
        </div>

        <div class="email-card">
            <h5>Webinar-pamelding — Bekreftelse <span class="badge-trigger">Bruker-trigget</span></h5>
            <p>Sendes nar bruker melder seg pa gratis webinar. Inkluderer join-lenke og dato/tid.</p>
            <div class="email-meta">
                <span><i class="fa fa-envelope"></i> Fra: Forfatterskolen</span>
                <span><i class="fa fa-file"></i> Mal: emails.branded.webinar-confirmation</span>
            </div>
        </div>

        <div class="email-card">
            <h5>Oppgave levert — Varsling til redaktor <span class="badge-trigger">Bruker-trigget</span></h5>
            <p>Sendes til redaktoren nar en elev leverer en oppgave.</p>
            <div class="email-meta">
                <span><i class="fa fa-envelope"></i> Fra: post@forfatterskolen.no</span>
                <span><i class="fa fa-file"></i> Mal: emails.assignment_submitted</span>
            </div>
        </div>

        <div class="email-card">
            <h5>Coaching-dato forslag <span class="badge-trigger">Bruker-trigget</span></h5>
            <p>Sendes til redaktoren nar elev foreslar coachingdatoer.</p>
            <div class="email-meta">
                <span><i class="fa fa-envelope"></i> Fra: post@forfatterskolen.no</span>
                <span><i class="fa fa-file"></i> Mal: emails.suggestion_date</span>
            </div>
        </div>

        <div class="email-card">
            <h5>Ny melding i samtale <span class="badge-trigger">Bruker-trigget</span></h5>
            <p>Sendes til mottaker nar noen sender en melding i samtalesystemet.</p>
            <div class="email-meta">
                <span><i class="fa fa-envelope"></i> Fra: Forfatterskolen</span>
                <span><i class="fa fa-file"></i> Mal: emails.new-conversation-message</span>
            </div>
        </div>

        <div class="email-card">
            <h5>Diskusjon — Nytt innlegg + svar <span class="badge-trigger">Bruker-trigget</span></h5>
            <p>Sendes til gruppemedlemmer ved nye innlegg eller svar i diskusjoner.</p>
            <div class="email-meta">
                <span><i class="fa fa-envelope"></i> Fra: post@forfatterskolen.no</span>
                <span><i class="fa fa-file"></i> Mal: emails.discussion_new / emails.discussion_replies</span>
            </div>
        </div>
    </div>

    {{-- 2. Planlagte --}}
    <div class="email-section">
        <h3><i class="fa fa-clock"></i> Planlagte/Cron e-poster</h3>

        <div class="email-card">
            <h5>Kurs e-post ut <span class="badge-cron">Daglig 08:00</span></h5>
            <p>Planlagte e-poster fra email_out-tabellen. Kan bruke branded kursmaler.</p>
            <div class="email-meta">
                <span><i class="fa fa-terminal"></i> courseemailout:command</span>
            </div>
        </div>

        <div class="email-card">
            <h5>Ukentlig digest <span class="badge-cron">Mandager 07:00</span></h5>
            <p>Oppsummering med mentormater, kurswebinarer, moduler, frister og inspirasjonssitat.</p>
            <div class="email-meta">
                <span><i class="fa fa-terminal"></i> emails:weekly-digest</span>
                <span><i class="fa fa-envelope"></i> Fra: Kristine S. Henningsen</span>
            </div>
        </div>

        <div class="email-card">
            <h5>Inaktiv-nudge <span class="badge-cron">Daglig 11:00</span></h5>
            <p>"Vi savner deg!" — sendes til elever uten innlogging i 14+ dager. 30-dagers cooldown.</p>
            <div class="email-meta">
                <span><i class="fa fa-terminal"></i> emails:inactive-nudge</span>
                <span><i class="fa fa-envelope"></i> Fra: Kristine S. Henningsen</span>
            </div>
        </div>

        <div class="email-card">
            <h5>Webinar-paminnelse <span class="badge-cron">Hvert 15. min</span></h5>
            <p>Sendes dag for kl 18:00 og 1 time for webinarstart.</p>
            <div class="email-meta">
                <span><i class="fa fa-terminal"></i> webinar:send-reminders</span>
            </div>
        </div>

        <div class="email-card">
            <h5>Kursutlop-paminnelse <span class="badge-cron">Daglig 08:30</span></h5>
            <p>Varsling 28, 7 og 1 dag for kurs utloper.</p>
            <div class="email-meta">
                <span><i class="fa fa-terminal"></i> courseexpirationreminder:command</span>
            </div>
        </div>

        <div class="email-card">
            <h5>Forfalt faktura <span class="badge-cron">Daglig 08:00</span></h5>
            <p>Paminning for fakturaer som forfaller i morgen.</p>
            <div class="email-meta">
                <span><i class="fa fa-terminal"></i> dueinvoicecheck:command</span>
            </div>
        </div>

        <div class="email-card">
            <h5>Faktura-paminnelse <span class="badge-cron">Daglig 08:00</span></h5>
            <p>Paminning 14 dager for fakturaforfall.</p>
            <div class="email-meta">
                <span><i class="fa fa-terminal"></i> invoiceduereminder:command</span>
            </div>
        </div>

        <div class="email-card">
            <h5>Auto-forny paminnelse <span class="badge-cron">Daglig 07:00</span></h5>
            <p>Varsling 17 dager for auto-fornyelse av kurs.</p>
            <div class="email-meta">
                <span><i class="fa fa-terminal"></i> autorenewreminder:command</span>
            </div>
        </div>

        <div class="email-card">
            <h5>Ikke kjopt noe <span class="badge-cron">Daglig 11:00</span></h5>
            <p>Oppfolging til brukere som registrerte seg i gar men ikke kjopte noe.</p>
            <div class="email-meta">
                <span><i class="fa fa-terminal"></i> dontavailanything:command</span>
            </div>
        </div>

        <div class="email-card">
            <h5>Gratis kurs oppfolging <span class="badge-cron">Hvert minutt</span></h5>
            <p>Sendes 10 minutter etter registrering for gratis kurs.</p>
            <div class="email-meta">
                <span><i class="fa fa-terminal"></i> freecoursedelayedemail:command</span>
            </div>
        </div>

        <div class="email-card">
            <h5>Webinar e-post ut <span class="badge-cron">Daglig 09:00</span></h5>
            <p>Planlagte webinar-invitasjoner til kurselever.</p>
            <div class="email-meta">
                <span><i class="fa fa-terminal"></i> webinaremailout:command</span>
            </div>
        </div>

        <div class="email-card">
            <h5>Bok-paminnelse <span class="badge-cron">Daglig 06:00</span></h5>
            <p>Paminning til pilotlesere som ikke har vert aktive.</p>
            <div class="email-meta">
                <span><i class="fa fa-terminal"></i> bookreminder:send</span>
            </div>
        </div>
    </div>

    {{-- 3. CRM --}}
    <div class="email-section">
        <h3><i class="fa fa-address-book"></i> CRM / Nyhetsbrev / Automatisering</h3>

        <div class="email-card">
            <h5>Nyhetsbrev <span class="badge-auto">Manuelt/Planlagt</span></h5>
            <p>Segmentert utsendelse til kontakter. 500 per batch med 60 sek mellom.</p>
            <div class="email-meta">
                <span><i class="fa fa-file"></i> Mal: emails.branded.newsletter</span>
                <span><i class="fa fa-cog"></i> SendNewsletterJob</span>
            </div>
        </div>

        <div class="email-card">
            <h5>E-postsekvenser <span class="badge-auto">Automatisk</span></h5>
            <p>Automatiske serier utlost av hendelser (webinar-pamelding, kjop, etc.). Hvert 5. minutt, 500/batch.</p>
            <div class="email-meta">
                <span><i class="fa fa-file"></i> Mal: emails.branded.automation-email</span>
                <span><i class="fa fa-cog"></i> ProcessEmailAutomationQueueJob</span>
            </div>
        </div>

        <div class="email-card">
            <h5>Facebook Lead henting <span class="badge-auto">Hvert 5. min</span></h5>
            <p>Henter nye leads fra Facebook Lead Ads og registrerer pa webinar + CRM.</p>
            <div class="email-meta">
                <span><i class="fa fa-terminal"></i> facebook:fetch-leads</span>
                <span><i class="fa fa-cog"></i> Token fornyes ukentlig automatisk</span>
            </div>
        </div>
    </div>

    {{-- 4. Database-maler --}}
    <div class="email-section">
        <h3><i class="fa fa-database"></i> Database-maler ({{ \App\EmailTemplate::count() }} stk)</h3>
        <p>Rediger via <a href="{{ route('admin.email-admin.index') }}">E-postmaler</a></p>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Malnavn</th>
                    <th>Emne</th>
                    <th>Fra</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach(\App\EmailTemplate::orderBy('page_name')->get() as $template)
                <tr>
                    <td>{{ $template->id }}</td>
                    <td>{{ $template->page_name }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($template->subject, 50) }}</td>
                    <td>{{ $template->from_email }}</td>
                    <td><a href="{{ route('admin.email-admin.edit', $template->id) }}" class="btn btn-xs btn-primary">Rediger</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- 5. Branded kursmaler --}}
    <div class="email-section">
        <h3><i class="fa fa-paint-brush"></i> Branded kursmaler (7 stk)</h3>
        <table class="table table-striped">
            <thead>
                <tr><th>Maltype</th><th>Blade-fil</th><th>Formal</th></tr>
            </thead>
            <tbody>
                <tr><td>welcome</td><td>emails.branded.welcome</td><td>Velkommen til kurset</td></tr>
                <tr><td>module_available</td><td>emails.branded.module-available</td><td>Ny modul tilgjengelig</td></tr>
                <tr><td>assignment_available</td><td>emails.branded.assignment-available</td><td>Ny oppgave tilgjengelig</td></tr>
                <tr><td>assignment_reminder</td><td>emails.branded.assignment-reminder</td><td>Oppgavefrist narmer seg</td></tr>
                <tr><td>assignment_deadline</td><td>emails.branded.assignment-deadline</td><td>Oppgavefrist i dag</td></tr>
                <tr><td>feedback_ready</td><td>emails.branded.feedback-ready</td><td>Tilbakemelding klar</td></tr>
                <tr><td>weekly_update</td><td>emails.branded.weekly-update</td><td>Ukentlig kursoppdatering</td></tr>
            </tbody>
        </table>
    </div>

    {{-- 6. Fra-adresser --}}
    <div class="email-section">
        <h3><i class="fa fa-at"></i> Fra-adresser</h3>
        <table class="table table-striped">
            <thead><tr><th>Adresse</th><th>Bruk</th></tr></thead>
            <tbody>
                <tr><td>post@forfatterskolen.no</td><td>Registrering, diskusjoner, coaching, digest, nudge</td></tr>
                <tr><td>postmail@forfatterskolen.no</td><td>Gratis kurs, utlopspaminnelser, webinar-e-poster</td></tr>
                <tr><td>kristine@forfatterskolen.no</td><td>Manusutvikling, tilbakemeldinger</td></tr>
                <tr><td>support@forfatterskolen.no</td><td>Default fallback</td></tr>
                <tr><td>no-reply@forfatterskolen.no</td><td>Bokpaminnelser</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

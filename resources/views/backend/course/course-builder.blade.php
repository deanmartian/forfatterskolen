@extends('backend.layout')

@section('styles')
<style>
    /* ── Layout ── */
    .cb-container {
        display: flex;
        height: calc(100vh - 60px);
        margin: 0 -15px;
    }
    .cb-sidebar {
        width: 280px;
        min-width: 280px;
        background: #2C3E50;
        color: #fff;
        display: flex;
        flex-direction: column;
        border-right: 1px solid #1a252f;
    }
    .cb-sidebar-header {
        padding: 20px;
        border-bottom: 1px solid rgba(255,255,255,.1);
    }
    .cb-sidebar-header h4 {
        margin: 0 0 4px 0;
        font-weight: 600;
        font-size: 16px;
        color: #fff;
    }
    .cb-sidebar-header p {
        margin: 0;
        font-size: 12px;
        color: rgba(255,255,255,.5);
    }
    .cb-sidebar-body {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
    }
    .cb-sidebar-body h5 {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: rgba(255,255,255,.4);
        margin: 0 0 12px 0;
    }
    .cb-prompt-btn {
        display: block;
        width: 100%;
        text-align: left;
        background: rgba(255,255,255,.06);
        border: 1px solid rgba(255,255,255,.1);
        border-radius: 6px;
        color: #ddd;
        padding: 10px 12px;
        margin-bottom: 8px;
        font-size: 13px;
        cursor: pointer;
        transition: all .15s;
    }
    .cb-prompt-btn:hover {
        background: rgba(255,255,255,.12);
        color: #fff;
        border-color: rgba(255,255,255,.2);
    }
    .cb-prompt-btn i {
        margin-right: 6px;
        color: #e8c468;
    }
    .cb-new-chat-btn {
        display: block;
        width: calc(100% - 32px);
        margin: 16px;
        padding: 10px;
        background: #862736;
        border: none;
        border-radius: 6px;
        color: #fff;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background .15s;
    }
    .cb-new-chat-btn:hover {
        background: #a03045;
    }
    .cb-new-chat-btn i {
        margin-right: 6px;
    }

    /* ── Main chat area ── */
    .cb-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #f4f5f7;
        min-width: 0;
    }
    .cb-header {
        padding: 14px 24px;
        background: #fff;
        border-bottom: 1px solid #e0e0e0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .cb-header h3 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: #2C3E50;
    }
    .cb-header h3 i {
        color: #862736;
        margin-right: 8px;
    }

    /* ── Messages ── */
    .cb-messages {
        flex: 1;
        overflow-y: auto;
        padding: 24px;
        scroll-behavior: smooth;
    }
    .cb-welcome {
        text-align: center;
        padding: 60px 20px;
        max-width: 540px;
        margin: 0 auto;
    }
    .cb-welcome-icon {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: linear-gradient(135deg, #862736, #a03045);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }
    .cb-welcome-icon i {
        font-size: 32px;
        color: #fff;
    }
    .cb-welcome h2 {
        font-size: 22px;
        font-weight: 600;
        color: #2C3E50;
        margin: 0 0 8px;
    }
    .cb-welcome p {
        color: #7f8c8d;
        font-size: 14px;
        line-height: 1.6;
    }

    .cb-msg {
        display: flex;
        margin-bottom: 20px;
        animation: cbFadeIn .25s ease;
    }
    @keyframes cbFadeIn {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .cb-msg.user { justify-content: flex-end; }

    .cb-msg-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        flex-shrink: 0;
        font-weight: 600;
    }
    .cb-msg.ai .cb-msg-avatar {
        background: linear-gradient(135deg, #862736, #a03045);
        color: #fff;
        margin-right: 12px;
    }
    .cb-msg.user .cb-msg-avatar {
        background: #3498db;
        color: #fff;
        margin-left: 12px;
        order: 2;
    }

    .cb-msg-bubble {
        max-width: 75%;
        padding: 14px 18px;
        border-radius: 12px;
        font-size: 14px;
        line-height: 1.7;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
    .cb-msg.ai .cb-msg-bubble {
        background: #fff;
        color: #2c3e50;
        border: 1px solid #e8e8e8;
        border-radius: 12px 12px 12px 2px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .cb-msg.user .cb-msg-bubble {
        background: linear-gradient(135deg, #2980b9, #3498db);
        color: #fff;
        border-radius: 12px 12px 2px 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,.1);
    }

    /* Markdown inside AI bubbles */
    .cb-msg.ai .cb-msg-bubble h1,
    .cb-msg.ai .cb-msg-bubble h2,
    .cb-msg.ai .cb-msg-bubble h3,
    .cb-msg.ai .cb-msg-bubble h4 {
        margin-top: 16px;
        margin-bottom: 8px;
        color: #2C3E50;
    }
    .cb-msg.ai .cb-msg-bubble h1 { font-size: 18px; }
    .cb-msg.ai .cb-msg-bubble h2 { font-size: 16px; }
    .cb-msg.ai .cb-msg-bubble h3 { font-size: 15px; }
    .cb-msg.ai .cb-msg-bubble ul,
    .cb-msg.ai .cb-msg-bubble ol {
        padding-left: 20px;
        margin: 8px 0;
    }
    .cb-msg.ai .cb-msg-bubble li {
        margin-bottom: 4px;
    }
    .cb-msg.ai .cb-msg-bubble code {
        background: #f0f0f0;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 13px;
    }
    .cb-msg.ai .cb-msg-bubble pre {
        background: #2C3E50;
        color: #ecf0f1;
        padding: 14px;
        border-radius: 6px;
        overflow-x: auto;
        margin: 10px 0;
    }
    .cb-msg.ai .cb-msg-bubble pre code {
        background: none;
        padding: 0;
        color: inherit;
    }
    .cb-msg.ai .cb-msg-bubble blockquote {
        border-left: 3px solid #862736;
        padding-left: 12px;
        margin: 10px 0;
        color: #666;
    }
    .cb-msg.ai .cb-msg-bubble strong {
        color: #2C3E50;
    }
    .cb-msg.ai .cb-msg-bubble table {
        width: 100%;
        border-collapse: collapse;
        margin: 10px 0;
    }
    .cb-msg.ai .cb-msg-bubble th,
    .cb-msg.ai .cb-msg-bubble td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
        font-size: 13px;
    }
    .cb-msg.ai .cb-msg-bubble th {
        background: #f7f7f7;
        font-weight: 600;
    }

    /* Loading indicator */
    .cb-loading {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }
    .cb-loading .cb-msg-avatar {
        background: linear-gradient(135deg, #862736, #a03045);
        color: #fff;
        margin-right: 12px;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 600;
    }
    .cb-dots {
        display: flex;
        gap: 5px;
        padding: 14px 18px;
        background: #fff;
        border: 1px solid #e8e8e8;
        border-radius: 12px 12px 12px 2px;
        box-shadow: 0 1px 3px rgba(0,0,0,.04);
    }
    .cb-dots span {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #bbb;
        animation: cbBounce 1.4s infinite;
    }
    .cb-dots span:nth-child(2) { animation-delay: .2s; }
    .cb-dots span:nth-child(3) { animation-delay: .4s; }
    @keyframes cbBounce {
        0%, 80%, 100% { transform: scale(.6); opacity: .4; }
        40% { transform: scale(1); opacity: 1; }
    }

    /* ── Input area ── */
    .cb-input-area {
        padding: 16px 24px 20px;
        background: #fff;
        border-top: 1px solid #e0e0e0;
    }
    .cb-input-wrap {
        display: flex;
        align-items: flex-end;
        gap: 10px;
        max-width: 900px;
        margin: 0 auto;
        background: #f8f9fa;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        padding: 8px 12px;
        transition: border-color .2s;
    }
    .cb-input-wrap:focus-within {
        border-color: #862736;
        box-shadow: 0 0 0 3px rgba(134,39,54,.1);
    }
    .cb-input-wrap.dragover {
        border-color: #862736;
        background: #fdf0f2;
    }
    .cb-input-wrap textarea {
        flex: 1;
        border: none;
        background: transparent;
        resize: none;
        font-size: 14px;
        line-height: 1.5;
        padding: 6px 0;
        min-height: 24px;
        max-height: 150px;
        outline: none;
        font-family: inherit;
    }
    .cb-input-actions {
        display: flex;
        gap: 4px;
        align-items: flex-end;
        padding-bottom: 2px;
    }
    .cb-btn-icon {
        width: 36px;
        height: 36px;
        border: none;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all .15s;
        font-size: 15px;
    }
    .cb-btn-attach {
        background: transparent;
        color: #888;
    }
    .cb-btn-attach:hover {
        background: #eee;
        color: #555;
    }
    .cb-btn-send {
        background: #862736;
        color: #fff;
    }
    .cb-btn-send:hover {
        background: #a03045;
    }
    .cb-btn-send:disabled {
        background: #ccc;
        cursor: not-allowed;
    }

    .cb-file-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eef;
        border: 1px solid #ccd;
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 12px;
        margin-bottom: 6px;
        color: #555;
    }
    .cb-file-badge .cb-file-remove {
        cursor: pointer;
        color: #999;
        font-size: 14px;
    }
    .cb-file-badge .cb-file-remove:hover {
        color: #c00;
    }

    .cb-input-hint {
        text-align: center;
        font-size: 11px;
        color: #aaa;
        margin-top: 8px;
    }

    /* ── Responsive ── */
    @media (max-width: 768px) {
        .cb-sidebar { display: none; }
        .cb-msg-bubble { max-width: 90%; }
    }
</style>
@stop

@section('title')
<title>Kursbygger &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="cb-container">
    {{-- Sidebar --}}
    <div class="cb-sidebar">
        <div class="cb-sidebar-header">
            <h4><i class="fa fa-magic"></i> Kursbygger AI</h4>
            <p>Bygg kurs med kunstig intelligens</p>
        </div>
        <div class="cb-sidebar-body">
            <h5>Forslag til samtaler</h5>
            <button class="cb-prompt-btn" onclick="cbUseSuggestion(this)">
                <i class="fa fa-pencil"></i> Lag et kurs i krimskriving
            </button>
            <button class="cb-prompt-btn" onclick="cbUseSuggestion(this)">
                <i class="fa fa-book"></i> Lag et kurs i barnebokskriving
            </button>
            <button class="cb-prompt-btn" onclick="cbUseSuggestion(this)">
                <i class="fa fa-heart"></i> Lag et kurs i romantisk litteratur
            </button>
            <button class="cb-prompt-btn" onclick="cbUseSuggestion(this)">
                <i class="fa fa-file-text"></i> Lag et kurs i sakprosa og memoarer
            </button>
            <button class="cb-prompt-btn" onclick="cbUseSuggestion(this)">
                <i class="fa fa-refresh"></i> Revider og forbedre et eksisterende kurs
            </button>
            <button class="cb-prompt-btn" onclick="cbUseSuggestion(this)">
                <i class="fa fa-paint-brush"></i> Redesign et kursdokument til profesjonelt format
            </button>
            <h5 style="margin-top: 20px;">Eksisterende kurs</h5>
            @foreach($courses as $course)
                <button class="cb-prompt-btn" onclick="cbUseSuggestion(this)" style="font-size: 12px; padding: 8px 12px;">
                    <i class="fa fa-{{ $course->status ? 'check-circle' : 'circle-o' }}" style="color: {{ $course->status ? '#27ae60' : '#95a5a6' }};"></i>
                    Revider kurset «{{ $course->title }}»
                </button>
            @endforeach
        </div>
        <button class="cb-new-chat-btn" onclick="cbNewChat()">
            <i class="fa fa-plus"></i> Ny samtale
        </button>
    </div>

    {{-- Main --}}
    <div class="cb-main">
        <div class="cb-header">
            <h3><i class="fa fa-commenting"></i> Kursbygger</h3>
            <a href="{{ route('admin.course.index') }}" class="btn btn-default btn-sm">
                <i class="fa fa-arrow-left"></i> Tilbake til kurs
            </a>
        </div>

        <div class="cb-messages" id="cbMessages">
            <div class="cb-welcome" id="cbWelcome">
                <div class="cb-welcome-icon"><i class="fa fa-graduation-cap"></i></div>
                <h2>Velkommen til Kursbyggeren</h2>
                <p>Jeg kan hjelpe deg med å bygge nye kurs, revidere eksisterende kurs, eller redesigne kursdokumenter. Skriv en melding eller velg et forslag fra menyen til venstre.</p>
            </div>
        </div>

        <div class="cb-input-area">
            <div class="cb-input-wrap" id="cbInputWrap">
                <div id="cbFileList"></div>
                <textarea id="cbInput" rows="1" placeholder="Skriv en melding..." onkeydown="cbHandleKey(event)"></textarea>
                <div class="cb-input-actions">
                    <button class="cb-btn-icon cb-btn-attach" onclick="document.getElementById('cbFileInput').click()" title="Last opp fil">
                        <i class="fa fa-paperclip"></i>
                    </button>
                    <button class="cb-btn-icon cb-btn-send" id="cbSendBtn" onclick="cbSend()" title="Send">
                        <i class="fa fa-paper-plane"></i>
                    </button>
                </div>
            </div>
            <input type="file" id="cbFileInput" accept=".pdf,.doc,.docx,.txt" multiple style="display:none" onchange="cbHandleFiles(this.files)">
            <div class="cb-input-hint">Trykk Enter for å sende, Shift+Enter for ny linje. Du kan dra og slippe filer hit.</div>
        </div>
    </div>
</div>
@stop

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
(function() {
    var STORAGE_KEY = 'cb_messages';
    var csrfToken = '{{ csrf_token() }}';
    var chatUrl = '{{ route("admin.course.builder.chat") }}';
    var isLoading = false;
    var attachedFiles = [];

    var courseData = @json($courseContext);
    var systemPrompt = "Du er kursarkitekt og dokumentdesigner for Forfatterskolen (forfatterskolen.no), Norges ledende nettbaserte skriveskole. Du har tre hovedfunksjoner:\n\n" +
        "1. **Bygge nye kurs** fra bunnen av med grundig research\n" +
        "2. **Revidere og forbedre eksisterende kurs** \u2014 legge til moduler, oppdatere utdatert innhold, forbedre struktur\n" +
        "3. **Redesigne kursdokumenter** \u2014 gj\u00f8re om stygge/enkle PDF- og Word-filer til elegante, profesjonelle dokumenter\n\n" +
        "## Om Forfatterskolen\n\n" +
        "Forfatterskolen tilbyr nettbaserte skrivekurs med:\n" +
        "- 10 kursmoduler med skriftlig materiale, videoer og \u00f8velser\n" +
        "- 10 live webinarer (tirsdager kl. 20:00) med kursl\u00e6rere\n" +
        "- Mentorm\u00f8ter med kjente forfattere (mandager kl. 20:00) \u2014 bl.a. Maja Lunde, Tom Egeland, Ingvar Ambj\u00f8rnsen, Herbj\u00f8rg Wassmo, Gro Dahle, Simon Stranger, Gunnar Staalesen\n" +
        "- Profesjonell tilbakemelding fra redakt\u00f8r p\u00e5 innsendt tekst\n" +
        "- Tilgang til alt materialet i ett helt \u00e5r\n" +
        "- Et aktivt skrivemilj\u00f8 med hundrevis av elever\n" +
        "- Over 200 elever har blitt utgitt p\u00e5 forlag (Cappelen Damm, Gyldendal, Aschehoug, Vigmostad & Bj\u00f8rke m.fl.)\n\n" +
        "Eksisterende kurs inkluderer bl.a.: Romankurs, Krimkurs, Barnebokskurs, \u00c5rskurs, P\u00e5bygnings\u00e5r, samt tjenester som manusutvikling og gratis tekstvurdering.\n\n" +
        "Kursene retter seg mot voksne som dr\u00f8mmer om \u00e5 skrive og gi ut bok. Niv\u00e5et er nybegynner til middels \u2014 ingen forkunnskaper kreves. Tonen er varm, oppmuntrende, men faglig solid.\n\n" +
        "## VIKTIG: Skrivestil\n\n" +
        "Innholdet du produserer skal ALDRI f\u00f8les som det er skrevet av en AI. Skriv som en erfaren norsk forfatter og kursutvikler ville gjort:\n" +
        "- Bruk naturlig, variert spr\u00e5k \u2014 unng\u00e5 gjentakende m\u00f8nstre og klisj\u00e9er\n" +
        "- Varier setningslengde og -struktur\n" +
        "- Bruk konkrete, levende eksempler \u2014 ikke generiske\n" +
        "- Ha en personlig, engasjert stemme \u2014 som om du snakker direkte til eleven\n" +
        "- Unng\u00e5 lister der sammenhengende tekst fungerer bedre\n" +
        "- Bruk humor, anekdoter og overraskende vendinger\n" +
        "- Referer til ekte norske og internasjonale forfattere med spesifikke eksempler fra deres b\u00f8ker\n" +
        "- Skriv p\u00e5 norsk bokm\u00e5l med varm, st\u00f8ttende tone. Bruk \u00abdu\u00bb og \u00abvi\u00bb\n\n" +
        "## Kursstruktur\n\n" +
        "Hvert kurs har 10 moduler med: faglig innhold (1500-2500 ord), 3-4 oppgaver, webinarforslag, og anbefalt lesning.\n" +
        "Modulene f\u00f8lger en progresjon fra grunnleggende til avansert.\n\n" +
        "Kvalitetskrav: Minimum 20.000 ord totalt, 15 unike skrive\u00f8velser, 20 referanser til b\u00f8ker/forfattere.\n\n" +
        "## VIKTIG: Bygg \u00e9n modul om gangen\n\n" +
        "Du skal ALDRI skrive alle moduler i ett svar. F\u00f8lg denne prosessen:\n" +
        "1. F\u00f8rst: Vis en oversikt over alle 10 moduler (kun tittel + kort beskrivelse). Sp\u00f8r om godkjenning.\n" +
        "2. Etter godkjenning: Skriv Modul 1 ferdig (fullt innhold, oppgaver, alt). Sp\u00f8r om du skal g\u00e5 videre til Modul 2.\n" +
        "3. Fortsett slik modul for modul til alle er ferdige.\n" +
        "Dette sikrer kvalitet og at brukeren kan gi tilbakemelding underveis.\n\n" +
        "## Forfatterskolens eksisterende kurs\n\n" +
        "Her er kursene som allerede finnes. Bruk denne informasjonen n\u00e5r brukeren ber om \u00e5 revidere et eksisterende kurs:\n\n" +
        JSON.stringify(courseData, null, 2);

    // Configure marked
    if (typeof marked !== 'undefined') {
        marked.setOptions({ breaks: true, gfm: true });
    }

    function getMessages() {
        try {
            return JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
        } catch(e) { return []; }
    }
    function saveMessages(msgs) {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(msgs));
    }

    function renderMarkdown(text) {
        if (typeof marked !== 'undefined') {
            try { return marked.parse(text); } catch(e) {}
        }
        // Fallback: basic conversion
        return text
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            .replace(/\n/g, '<br>');
    }

    var createCourseUrl = '{{ route("admin.course.builder.create") }}';

    function detectModules(text) {
        // Detect course structure: look for "## Modul X:" or "### Modul X:" patterns
        var modulePattern = /##?\s*Modul\s+(\d+)\s*[:–—-]\s*(.+)/gi;
        var matches = [];
        var match;
        while ((match = modulePattern.exec(text)) !== null) {
            matches.push({ num: parseInt(match[1]), title: match[2].trim() });
        }
        return matches;
    }

    function detectCourseTitle(text) {
        // Look for "# Title" at start or "Kurstittel:" pattern
        var titleMatch = text.match(/^#\s+(.+)/m) || text.match(/kurstittel[:\s]+(.+)/i);
        return titleMatch ? titleMatch[1].replace(/\*+/g, '').trim() : null;
    }

    function extractModuleContent(text, modules) {
        var result = [];
        for (var i = 0; i < modules.length; i++) {
            var startPattern = new RegExp('##?\\s*Modul\\s+' + modules[i].num + '\\s*[:–—-]', 'i');
            var startIdx = text.search(startPattern);
            if (startIdx === -1) continue;

            var endIdx;
            if (i < modules.length - 1) {
                var nextPattern = new RegExp('##?\\s*Modul\\s+' + modules[i+1].num + '\\s*[:–—-]', 'i');
                endIdx = text.search(nextPattern);
            } else {
                endIdx = text.length;
            }

            result.push({
                title: 'Modul ' + modules[i].num + ': ' + modules[i].title,
                content: text.substring(startIdx, endIdx > startIdx ? endIdx : text.length).trim()
            });
        }
        return result;
    }

    function appendMessage(role, content, save) {
        var welcome = document.getElementById('cbWelcome');
        if (welcome) welcome.style.display = 'none';

        var container = document.getElementById('cbMessages');
        var div = document.createElement('div');
        div.className = 'cb-msg ' + (role === 'user' ? 'user' : 'ai');

        var avatar = document.createElement('div');
        avatar.className = 'cb-msg-avatar';
        avatar.textContent = role === 'user' ? 'Du' : 'AI';

        var bubble = document.createElement('div');
        bubble.className = 'cb-msg-bubble';
        if (role === 'user') {
            bubble.textContent = content;
        } else {
            bubble.innerHTML = renderMarkdown(content);

            // Check if response contains course modules - show create button
            // Combine all assistant messages to detect modules built one-by-one
            var modules = detectModules(content);
            var allAssistantContent = content;
            if (modules.length < 3) {
                allAssistantContent = getMessages().filter(function(m){ return m.role === 'assistant'; }).map(function(m){ return m.content; }).join('\n\n') + '\n\n' + content;
                modules = detectModules(allAssistantContent);
            }
            if (modules.length >= 2) {
                var title = detectCourseTitle(allAssistantContent) || detectCourseTitle(content) || 'Nytt kurs';
                var createBar = document.createElement('div');
                createBar.style.cssText = 'margin-top:16px; padding:16px; background:#f0f7f0; border:1px solid #c3e6c3; border-radius:8px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;';
                createBar.innerHTML = '<div style="flex:1;min-width:200px;">' +
                    '<strong style="color:#2c6e2c;"><i class="fa fa-check-circle"></i> Klar til å opprette kurs</strong><br>' +
                    '<span style="font-size:13px;color:#555;">«' + title + '» med ' + modules.length + ' moduler (opprettes som inaktivt)</span>' +
                    '</div>' +
                    '<button class="btn btn-success" onclick="cbCreateCourse(this)" ' +
                    'data-content="' + btoa(unescape(encodeURIComponent(allAssistantContent))) + '" ' +
                    'data-title="' + title.replace(/"/g, '&quot;') + '">' +
                    '<i class="fa fa-plus"></i> Opprett kurs' +
                    '</button>';
                bubble.appendChild(createBar);
            }
        }

        div.appendChild(avatar);
        div.appendChild(bubble);
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;

        if (save !== false) {
            var msgs = getMessages();
            msgs.push({ role: role === 'user' ? 'user' : 'assistant', content: content });
            saveMessages(msgs);
        }
    }

    window.cbCreateCourse = function(btn) {
        var content = decodeURIComponent(escape(atob(btn.getAttribute('data-content'))));
        var title = btn.getAttribute('data-title');
        var modules = detectModules(content);
        var moduleData = extractModuleContent(content, modules);

        if (moduleData.length === 0) {
            alert('Kunne ikke parse modulene. Prøv å be AI-en skrive modulene med tydelig "## Modul X: Tittel" format.');
            return;
        }

        // Extract description from first part of content (before first module)
        var firstModuleIdx = content.search(/##?\s*Modul\s+1\s*[:–—-]/i);
        var description = firstModuleIdx > 0 ? content.substring(0, firstModuleIdx).replace(/^#.+\n/, '').trim() : title;
        description = description.substring(0, 2000); // Limit length

        btn.disabled = true;
        btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Oppretter...';

        $.ajax({
            url: createCourseUrl,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            contentType: 'application/json',
            data: JSON.stringify({
                title: title,
                description: description,
                modules: moduleData
            }),
            success: function(data) {
                btn.innerHTML = '<i class="fa fa-check"></i> Opprettet!';
                btn.className = 'btn btn-default';
                btn.style.color = '#2c6e2c';

                var link = document.createElement('a');
                link.href = data.url;
                link.className = 'btn btn-primary';
                link.style.marginLeft = '8px';
                link.innerHTML = '<i class="fa fa-external-link"></i> Gå til kurset';
                btn.parentNode.appendChild(link);

                appendMessage('assistant', '✅ ' + data.message + ' Du finner det under [Kurs](/course) i menyen.');
            },
            error: function(xhr) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-plus"></i> Opprett kurs';
                var msg = 'Kunne ikke opprette kurset.';
                if (xhr.responseJSON) {
                    msg = xhr.responseJSON.error || xhr.responseJSON.message || JSON.stringify(xhr.responseJSON);
                } else {
                    msg += ' (HTTP ' + xhr.status + ')';
                }
                console.error('Course create error:', xhr.status, xhr.responseText);
                alert(msg);
            }
        });
    };

    function showLoading() {
        var container = document.getElementById('cbMessages');
        var div = document.createElement('div');
        div.className = 'cb-loading';
        div.id = 'cbLoadingIndicator';
        div.innerHTML = '<div class="cb-msg-avatar">AI</div><div class="cb-dots"><span></span><span></span><span></span></div>';
        container.appendChild(div);
        container.scrollTop = container.scrollHeight;
    }
    function hideLoading() {
        var el = document.getElementById('cbLoadingIndicator');
        if (el) el.remove();
    }

    window.cbSend = function() {
        if (isLoading) return;
        var input = document.getElementById('cbInput');
        var text = input.value.trim();
        if (!text && attachedFiles.length === 0) return;

        var userContent = text;
        if (attachedFiles.length > 0) {
            var fileNames = attachedFiles.map(function(f) { return f.name; }).join(', ');
            if (text) {
                userContent = text + '\n\n[Vedlagte filer: ' + fileNames + ']';
            } else {
                userContent = '[Vedlagte filer: ' + fileNames + ']';
            }
        }

        appendMessage('user', userContent);
        input.value = '';
        input.style.height = 'auto';
        clearAttachedFiles();

        isLoading = true;
        document.getElementById('cbSendBtn').disabled = true;
        showLoading();

        var messages = getMessages();
        var apiMessages = messages.map(function(m) {
            return { role: m.role, content: m.content };
        });

        var payload = {
            messages: apiMessages,
            system_prompt: systemPrompt
        };

        $.ajax({
            url: chatUrl,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            contentType: 'application/json',
            data: JSON.stringify(payload),
            timeout: 180000,
            success: function(data) {
                hideLoading();
                appendMessage('assistant', data.content || 'Ingen respons.');
            },
            error: function(xhr) {
                hideLoading();
                var errMsg = 'Beklager, det oppstod en feil. Vennligst pr\u00f8v igjen.';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errMsg = xhr.responseJSON.error;
                }
                appendMessage('assistant', errMsg);
            },
            complete: function() {
                isLoading = false;
                document.getElementById('cbSendBtn').disabled = false;
            }
        });
    };

    window.cbHandleKey = function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            cbSend();
        }
        // Auto-resize textarea
        setTimeout(function() {
            var ta = e.target;
            ta.style.height = 'auto';
            ta.style.height = Math.min(ta.scrollHeight, 150) + 'px';
        }, 0);
    };

    window.cbUseSuggestion = function(btn) {
        var text = btn.textContent.trim();
        // Remove icon text prefix
        var input = document.getElementById('cbInput');
        input.value = text;
        input.focus();
        cbSend();
    };

    window.cbNewChat = function() {
        if (isLoading) return;
        localStorage.removeItem(STORAGE_KEY);
        var container = document.getElementById('cbMessages');
        container.innerHTML = '<div class="cb-welcome" id="cbWelcome"><div class="cb-welcome-icon"><i class="fa fa-graduation-cap"></i></div><h2>Velkommen til Kursbyggeren</h2><p>Jeg kan hjelpe deg med \u00e5 bygge nye kurs, revidere eksisterende kurs, eller redesigne kursdokumenter. Skriv en melding eller velg et forslag fra menyen til venstre.</p></div>';
    };

    // File handling
    window.cbHandleFiles = function(fileList) {
        for (var i = 0; i < fileList.length; i++) {
            var file = fileList[i];
            if (file.size > 10 * 1024 * 1024) {
                alert('Filen ' + file.name + ' er for stor (maks 10 MB).');
                continue;
            }
            attachedFiles.push(file);
        }
        renderFileList();
    };

    function clearAttachedFiles() {
        attachedFiles = [];
        renderFileList();
        document.getElementById('cbFileInput').value = '';
    }

    function renderFileList() {
        var container = document.getElementById('cbFileList');
        container.innerHTML = '';
        attachedFiles.forEach(function(file, idx) {
            var badge = document.createElement('div');
            badge.className = 'cb-file-badge';
            badge.innerHTML = '<i class="fa fa-file-o"></i> ' + file.name + ' <span class="cb-file-remove" onclick="cbRemoveFile(' + idx + ')">&times;</span>';
            container.appendChild(badge);
        });
    }

    window.cbRemoveFile = function(idx) {
        attachedFiles.splice(idx, 1);
        renderFileList();
    };

    // Drag and drop
    var wrap = document.getElementById('cbInputWrap');
    wrap.addEventListener('dragover', function(e) {
        e.preventDefault();
        wrap.classList.add('dragover');
    });
    wrap.addEventListener('dragleave', function() {
        wrap.classList.remove('dragover');
    });
    wrap.addEventListener('drop', function(e) {
        e.preventDefault();
        wrap.classList.remove('dragover');
        cbHandleFiles(e.dataTransfer.files);
    });

    // Restore messages on load
    var msgs = getMessages();
    if (msgs.length > 0) {
        msgs.forEach(function(m) {
            appendMessage(m.role === 'user' ? 'user' : 'assistant', m.content, false);
        });
    }
})();
</script>
@stop

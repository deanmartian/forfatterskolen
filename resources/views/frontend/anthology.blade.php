<!DOCTYPE html>
<html lang="no">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Juleantologi 2026 – Forfatterskolen</title>
    <meta name="description" content="Send inn din tekst til Forfatterskolens Juleantologi 2026. Alle kan delta — boken utgis profesjonelt og selges i bokhandlere.">
    <meta property="og:title" content="Juleantologi 2026 – Forfatterskolen">
    <meta property="og:description" content="Vi inviterer alle skribenter i Norge til å bidra med en tekst til årets juleantologi.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/juleantologi') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Source+Sans+3:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --midnight: #0a0e17;
            --deep-blue: #111827;
            --frost: #c8d6e5;
            --ice: #e8eef4;
            --gold: #d4a574;
            --gold-bright: #e8c49a;
            --wine: #862736;
            --wine-glow: rgba(134, 39, 54, 0.3);
            --warm: #f5e6d3;
            --snow-white: #f8fafc;
            --text-light: rgba(255, 255, 255, 0.85);
            --text-dim: rgba(255, 255, 255, 0.45);
            --font-display: 'Cormorant Garamond', 'Georgia', serif;
            --font-body: 'Libre Baskerville', 'Georgia', serif;
            --font-ui: 'Source Sans 3', sans-serif;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: var(--font-body);
            background: var(--midnight);
            color: var(--text-light);
            overflow-x: hidden;
        }

        .snow-container { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 50; overflow: hidden; }
        .snowflake { position: absolute; top: -10px; background: #fff; border-radius: 50%; opacity: 0; animation: snowfall linear infinite; }
        @keyframes snowfall { 0% { opacity: 0; transform: translateX(0) rotate(0deg); } 10% { opacity: 0.8; } 90% { opacity: 0.6; } 100% { opacity: 0; transform: translateX(80px) rotate(360deg); top: 100vh; } }

        .nav { position: fixed; top: 0; left: 0; right: 0; z-index: 100; padding: 1.25rem 2.5rem; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(180deg, rgba(10,14,23,0.9) 0%, transparent 100%); }
        .nav__logo img { height: 24px; filter: brightness(10); opacity: 0.8; }
        .nav__cta { font-family: var(--font-ui); font-size: 0.8rem; font-weight: 600; color: var(--gold); text-decoration: none; border: 1px solid rgba(212,165,116,0.3); padding: 0.5rem 1.25rem; border-radius: 4px; transition: all 0.3s; }
        .nav__cta:hover { background: rgba(212,165,116,0.1); border-color: var(--gold); }

        .hero { min-height: 100vh; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 2rem; position: relative; background: radial-gradient(ellipse at 50% 30%, rgba(134,39,54,0.12) 0%, transparent 60%), radial-gradient(ellipse at 20% 80%, rgba(212,165,116,0.06) 0%, transparent 50%), radial-gradient(ellipse at 80% 60%, rgba(30,58,95,0.15) 0%, transparent 50%), var(--midnight); }
        .hero__ornament { font-size: 2rem; color: var(--gold); opacity: 0.4; margin-bottom: 1.5rem; letter-spacing: 0.5rem; animation: fadeInUp 1.2s ease-out; }
        .hero__subtitle { font-family: var(--font-ui); font-size: 0.7rem; font-weight: 600; letter-spacing: 4px; text-transform: uppercase; color: var(--gold); margin-bottom: 1.5rem; animation: fadeInUp 1.4s ease-out; }
        .hero__title { font-family: var(--font-display); font-size: clamp(3.5rem, 8vw, 7rem); font-weight: 300; line-height: 1; margin-bottom: 0.5rem; background: linear-gradient(135deg, var(--snow-white) 0%, var(--frost) 50%, var(--gold-bright) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; animation: fadeInUp 1.6s ease-out; }
        .hero__title em { font-style: italic; font-weight: 400; }
        .hero__year { font-family: var(--font-display); font-size: clamp(1.5rem, 3vw, 2.5rem); font-weight: 300; color: var(--gold); opacity: 0.6; margin-bottom: 2.5rem; animation: fadeInUp 1.8s ease-out; }
        .hero__tagline { font-family: var(--font-body); font-size: 1.15rem; font-weight: 300; color: var(--text-light); max-width: 580px; line-height: 1.8; margin-bottom: 3rem; animation: fadeInUp 2s ease-out; }

        .countdown { display: flex; gap: 1.5rem; margin-bottom: 3rem; animation: fadeInUp 2.2s ease-out; }
        .countdown__unit { text-align: center; }
        .countdown__number { font-family: var(--font-display); font-size: 2.5rem; font-weight: 300; color: var(--snow-white); line-height: 1; display: block; }
        .countdown__label { font-family: var(--font-ui); font-size: 0.55rem; letter-spacing: 2px; text-transform: uppercase; color: var(--text-dim); margin-top: 0.35rem; }
        .countdown__separator { font-family: var(--font-display); font-size: 2rem; color: var(--gold); opacity: 0.3; align-self: flex-start; margin-top: 0.25rem; }

        .hero__cta { display: inline-block; font-family: var(--font-ui); font-size: 0.85rem; font-weight: 600; letter-spacing: 1px; color: var(--midnight); background: linear-gradient(135deg, var(--gold), var(--gold-bright)); padding: 0.9rem 2.5rem; border-radius: 4px; text-decoration: none; transition: all 0.3s; animation: fadeInUp 2.4s ease-out; }
        .hero__cta:hover { transform: translateY(-2px); box-shadow: 0 12px 40px rgba(212,165,116,0.25); }
        .hero__scroll { position: absolute; bottom: 2.5rem; font-family: var(--font-ui); font-size: 0.6rem; letter-spacing: 3px; text-transform: uppercase; color: var(--text-dim); animation: float 3s ease-in-out infinite; }

        @keyframes float { 0%,100% { transform: translateY(0); } 50% { transform: translateY(8px); } }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

        .section { max-width: 900px; margin: 0 auto; padding: 6rem 2rem; }
        .section--wide { max-width: 1100px; }
        .section__ornament { text-align: center; font-size: 1.25rem; color: var(--gold); opacity: 0.3; margin-bottom: 1.5rem; }
        .section__label { text-align: center; font-family: var(--font-ui); font-size: 0.6rem; font-weight: 600; letter-spacing: 4px; text-transform: uppercase; color: var(--gold); margin-bottom: 0.85rem; }
        .section__title { text-align: center; font-family: var(--font-display); font-size: 2.5rem; font-weight: 300; color: var(--snow-white); margin-bottom: 1.5rem; line-height: 1.2; }
        .section__text { text-align: center; font-size: 1rem; font-weight: 300; color: var(--frost); line-height: 1.9; max-width: 640px; margin: 0 auto 2rem; }
        .divider { width: 60px; height: 1px; background: linear-gradient(90deg, transparent, var(--gold), transparent); margin: 0 auto 3rem; }

        .genre-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.25rem; margin-top: 2.5rem; }
        .genre-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 2rem 1.5rem; text-align: center; transition: all 0.4s; position: relative; overflow: hidden; }
        .genre-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, transparent, var(--gold), transparent); opacity: 0; transition: opacity 0.4s; }
        .genre-card:hover { background: rgba(255,255,255,0.05); transform: translateY(-4px); }
        .genre-card:hover::before { opacity: 1; }
        .genre-card__icon { font-size: 2rem; margin-bottom: 1rem; }
        .genre-card__title { font-family: var(--font-display); font-size: 1.25rem; font-weight: 600; color: var(--snow-white); margin-bottom: 0.5rem; }
        .genre-card__desc { font-family: var(--font-ui); font-size: 0.8rem; color: var(--text-dim); line-height: 1.6; }

        .timeline { position: relative; padding-left: 2rem; margin-top: 2rem; }
        .timeline::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 1px; background: linear-gradient(180deg, var(--gold), rgba(212,165,116,0.1)); }
        .timeline__item { position: relative; padding-bottom: 2.5rem; padding-left: 1.5rem; }
        .timeline__item::before { content: ''; position: absolute; left: -2.35rem; top: 0.35rem; width: 10px; height: 10px; border-radius: 50%; background: var(--gold); box-shadow: 0 0 12px rgba(212,165,116,0.4); }
        .timeline__date { font-family: var(--font-ui); font-size: 0.65rem; font-weight: 600; letter-spacing: 2px; text-transform: uppercase; color: var(--gold); margin-bottom: 0.35rem; }
        .timeline__title { font-family: var(--font-display); font-size: 1.35rem; font-weight: 600; color: var(--snow-white); margin-bottom: 0.35rem; }
        .timeline__desc { font-family: var(--font-ui); font-size: 0.85rem; color: var(--frost); line-height: 1.6; }

        .quote-section { text-align: center; padding: 5rem 2rem; position: relative; }
        .quote-section::before { content: '\201C'; font-family: var(--font-display); font-size: 8rem; color: var(--gold); opacity: 0.1; position: absolute; top: 1rem; left: 50%; transform: translateX(-50%); }
        .quote__text { font-family: var(--font-display); font-size: 1.75rem; font-weight: 300; font-style: italic; color: var(--frost); max-width: 700px; margin: 0 auto; line-height: 1.6; }
        .quote__author { font-family: var(--font-ui); font-size: 0.75rem; color: var(--gold); margin-top: 1.5rem; letter-spacing: 2px; }

        .guidelines { display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-top: 2rem; }
        .guideline { display: flex; gap: 1rem; align-items: flex-start; }
        .guideline__icon { width: 40px; height: 40px; border-radius: 50%; background: rgba(212,165,116,0.1); border: 1px solid rgba(212,165,116,0.2); display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 1rem; }
        .guideline__title { font-family: var(--font-ui); font-size: 0.85rem; font-weight: 600; color: var(--snow-white); margin-bottom: 0.2rem; }
        .guideline__desc { font-family: var(--font-ui); font-size: 0.78rem; color: var(--frost); line-height: 1.5; }

        .submit-section { background: radial-gradient(ellipse at 30% 50%, rgba(134,39,54,0.08) 0%, transparent 50%), radial-gradient(ellipse at 70% 50%, rgba(212,165,116,0.06) 0%, transparent 50%), rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px; padding: 3.5rem; margin-top: 2rem; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
        .form-group { margin-bottom: 0.5rem; }
        .form-group--full { grid-column: 1 / -1; }
        .form-group label { display: block; font-family: var(--font-ui); font-size: 0.7rem; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; color: var(--frost); margin-bottom: 0.4rem; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 0.75rem 1rem; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; color: var(--snow-white); font-family: var(--font-ui); font-size: 0.875rem; outline: none; transition: border-color 0.3s; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: var(--gold); }
        .form-group input::placeholder, .form-group textarea::placeholder { color: rgba(255,255,255,0.2); }
        .form-group select option { background: var(--deep-blue); color: var(--snow-white); }
        .form-group textarea { min-height: 120px; resize: vertical; }

        .upload-area { border: 1.5px dashed rgba(255,255,255,0.12); border-radius: 8px; padding: 2rem; text-align: center; transition: all 0.3s; cursor: pointer; }
        .upload-area:hover { border-color: var(--gold); background: rgba(212,165,116,0.03); }
        .upload-area__icon { font-size: 1.5rem; margin-bottom: 0.5rem; }
        .upload-area__text { font-family: var(--font-ui); font-size: 0.825rem; color: var(--frost); }
        .upload-area__hint { font-family: var(--font-ui); font-size: 0.68rem; color: var(--text-dim); margin-top: 0.35rem; }

        .consent-item { display: flex; align-items: flex-start; gap: 0.5rem; margin: 1rem 0 0.5rem; }
        .consent-item input[type="checkbox"] { width: 16px; height: 16px; margin-top: 2px; accent-color: var(--gold); flex-shrink: 0; }
        .consent-item label { font-family: var(--font-ui); font-size: 0.72rem; color: var(--frost); line-height: 1.5; text-transform: none; letter-spacing: 0; font-weight: 400; }
        .consent-item label a { color: var(--gold); text-decoration: none; }

        .submit-btn { width: 100%; padding: 1rem; background: linear-gradient(135deg, var(--gold), var(--gold-bright)); color: var(--midnight); border: none; border-radius: 6px; font-family: var(--font-ui); font-size: 0.9rem; font-weight: 700; letter-spacing: 0.5px; cursor: pointer; transition: all 0.3s; margin-top: 1rem; }
        .submit-btn:hover { transform: translateY(-2px); box-shadow: 0 12px 40px rgba(212,165,116,0.2); }
        .submit-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

        .prizes { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-top: 2.5rem; }
        .prize-card { text-align: center; padding: 2rem 1.5rem; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 10px; }
        .prize-card__number { font-family: var(--font-display); font-size: 2.5rem; font-weight: 300; color: var(--gold); margin-bottom: 0.5rem; }
        .prize-card__title { font-family: var(--font-display); font-size: 1.1rem; font-weight: 600; color: var(--snow-white); margin-bottom: 0.35rem; }
        .prize-card__desc { font-family: var(--font-ui); font-size: 0.78rem; color: var(--frost); line-height: 1.5; }

        .footer { text-align: center; padding: 3rem 2rem; border-top: 1px solid rgba(255,255,255,0.04); }
        .footer__logo { font-family: var(--font-ui); font-size: 0.75rem; color: var(--text-dim); margin-bottom: 0.5rem; }
        .footer__links { font-family: var(--font-ui); font-size: 0.68rem; color: var(--text-dim); }
        .footer__links a { color: var(--text-dim); text-decoration: none; }
        .footer__links a:hover { color: var(--gold); }

        .alert-error { background: rgba(220,38,38,0.15); border: 1px solid rgba(220,38,38,0.3); border-radius: 8px; padding: 1rem 1.5rem; margin-bottom: 1.5rem; font-family: var(--font-ui); font-size: 0.85rem; color: #fca5a5; }
        .closed-banner { background: rgba(134,39,54,0.2); border: 1px solid rgba(134,39,54,0.3); border-radius: 12px; padding: 2rem; text-align: center; margin-top: 2rem; }
        .closed-banner h3 { font-family: var(--font-display); font-size: 1.5rem; color: var(--snow-white); margin-bottom: 0.5rem; }
        .closed-banner p { font-family: var(--font-ui); font-size: 0.9rem; color: var(--frost); }

        .submission-counter { font-family: var(--font-ui); font-size: 0.75rem; color: var(--text-dim); text-align: center; margin-top: 1rem; animation: fadeInUp 2.6s ease-out; }

        @media (max-width: 768px) {
            .genre-grid { grid-template-columns: 1fr; }
            .guidelines { grid-template-columns: 1fr; }
            .prizes { grid-template-columns: 1fr; }
            .form-grid { grid-template-columns: 1fr; }
            .submit-section { padding: 2rem 1.5rem; }
            .countdown { gap: 1rem; }
            .countdown__number { font-size: 1.75rem; }
            .upsell-grid { grid-template-columns: 1fr !important; }
        }
    </style>
</head>
<body>

<!-- Snow -->
<div class="snow-container" id="snow"></div>

<!-- Nav -->
<nav class="nav">
    <a href="/"><img src="/images-new/logo.png" alt="Forfatterskolen" class="nav__logo"></a>
    @if($isOpen)
        <a href="#send-inn" class="nav__cta">Send inn tekst</a>
    @endif
</nav>

<!-- HERO -->
<section class="hero">
    <div class="hero__ornament">&#10052; &#10022; &#10052;</div>
    <div class="hero__subtitle">Forfatterskolen presenterer</div>
    <h1 class="hero__title"><em>Juleantologi</em></h1>
    <div class="hero__year">2026</div>
    <p class="hero__tagline">
        Vi inviterer alle skribenter i Norge til å bidra med en tekst til årets juleantologi.
        Dine ord. Din stemme. Mellom to permer — i bokhandlene til jul.
    </p>

    @if($isOpen)
    <div class="countdown">
        <div class="countdown__unit">
            <span class="countdown__number" id="cDays">0</span>
            <div class="countdown__label">Dager</div>
        </div>
        <div class="countdown__separator">:</div>
        <div class="countdown__unit">
            <span class="countdown__number" id="cHours">00</span>
            <div class="countdown__label">Timer</div>
        </div>
        <div class="countdown__separator">:</div>
        <div class="countdown__unit">
            <span class="countdown__number" id="cMins">00</span>
            <div class="countdown__label">Minutter</div>
        </div>
    </div>

    <a href="#send-inn" class="hero__cta">Send inn din tekst &#10140;</a>

    @if($submissionCount > 5)
        <div class="submission-counter">{{ $submissionCount }} tekster mottatt</div>
    @endif
    @else
        <div class="closed-banner">
            <h3>Fristen er utløpt</h3>
            <p>Innsendingsfristen for Juleantologien 2026 var 20. august. Takk til alle som sendte inn!</p>
        </div>
    @endif

    <div class="hero__scroll">&#8595; Scroll for å lese mer</div>
</section>

<!-- ABOUT -->
<section class="section">
    <div class="section__ornament">&#10022;</div>
    <div class="section__label">Om antologien</div>
    <h2 class="section__title">Fortellinger som varmer<br>når frosten biter</h2>
    <p class="section__text">
        Juleantologien er Forfatterskolens årlige utgivelse — en samling tekster
        fra skrivende mennesker over hele landet. Boken utgis profesjonelt og selges
        i bokhandlere. Du trenger ikke være elev hos oss — alle kan sende inn.
    </p>
    <div class="divider"></div>

    <div class="prizes">
        <div class="prize-card">
            <div class="prize-card__number">&#128214;</div>
            <div class="prize-card__title">Publisert bok</div>
            <div class="prize-card__desc">Teksten din i en profesjonelt produsert bok, tilgjengelig i bokhandlere og nettbutikker.</div>
        </div>
        <div class="prize-card">
            <div class="prize-card__number">&#127876;</div>
            <div class="prize-card__title">Lansering</div>
            <div class="prize-card__desc">Eksklusiv boklansering i november der du leser fra egen tekst. Gratiseksemplarer inkludert.</div>
        </div>
        <div class="prize-card">
            <div class="prize-card__number">&#9997;&#65039;</div>
            <div class="prize-card__title">Redaktør-feedback</div>
            <div class="prize-card__desc">Profesjonell redaktør gir tilbakemelding og hjelper deg finpusse teksten før trykk.</div>
        </div>
    </div>
</section>

<!-- QUOTE -->
<section class="quote-section">
    <p class="quote__text">
        Noe av det fineste med å drive Forfatterskolen, er å følge forfattere over tid.
        Å se en idé bli til tekst. Å se tekst bli til bok.
    </p>
    <p class="quote__author">— Kristine Storli Henningsen, rektor</p>
</section>

<!-- SJANGRE -->
<section class="section section--wide">
    <div class="section__ornament">&#10052;</div>
    <div class="section__label">Vi søker tekster i</div>
    <h2 class="section__title">Alle sjangre — én stemning</h2>
    <p class="section__text">
        Juleantologien favner bredt. Det eneste kravet er at teksten har en forbindelse
        til vinteren, julen, eller det å samles — på et vis som bare du kan fortelle.
    </p>

    <div class="genre-grid">
        <div class="genre-card">
            <div class="genre-card__icon">&#128367;&#65039;</div>
            <div class="genre-card__title">Novelle</div>
            <div class="genre-card__desc">Korte fortellinger som fanger et øyeblikk, en stemning, en følelse.</div>
        </div>
        <div class="genre-card">
            <div class="genre-card__icon">&#127794;</div>
            <div class="genre-card__title">Krim & spenning</div>
            <div class="genre-card__desc">Mørke vintermysterier. Snøen skjuler mer enn du tror.</div>
        </div>
        <div class="genre-card">
            <div class="genre-card__icon">&#11088;</div>
            <div class="genre-card__title">Barnefortelling</div>
            <div class="genre-card__desc">Magiske julehistorier for de minste. Nisser, dyr, undre.</div>
        </div>
        <div class="genre-card">
            <div class="genre-card__icon">&#10052;&#65039;</div>
            <div class="genre-card__title">Dikt & lyrikk</div>
            <div class="genre-card__desc">Verslinjer som fanger frostens poesi og julens stillhet.</div>
        </div>
        <div class="genre-card">
            <div class="genre-card__icon">&#128293;</div>
            <div class="genre-card__title">Feelgood</div>
            <div class="genre-card__desc">Varme historier. Kakao, strikk og uventede møter.</div>
        </div>
        <div class="genre-card">
            <div class="genre-card__icon">&#128221;</div>
            <div class="genre-card__title">Sakprosa / essay</div>
            <div class="genre-card__desc">Personlige betraktninger om julen, tradisjoner, minner.</div>
        </div>
    </div>
</section>

<!-- TIDSLINJE -->
<section class="section">
    <div class="section__ornament">&#10022;</div>
    <div class="section__label">Veien til bok</div>
    <h2 class="section__title">Viktige datoer</h2>

    <div class="timeline">
        <div class="timeline__item">
            <div class="timeline__date">Nå → 20. august 2026</div>
            <div class="timeline__title">Skriv og send inn</div>
            <div class="timeline__desc">Send inn din tekst via skjemaet nedenfor. Du kan sende inn opptil 3 bidrag.</div>
        </div>
        <div class="timeline__item">
            <div class="timeline__date">20. august 2026</div>
            <div class="timeline__title">Frist for innsending</div>
            <div class="timeline__desc">Absolutt siste frist. Tekster mottatt etter denne datoen vurderes ikke.</div>
        </div>
        <div class="timeline__item">
            <div class="timeline__date">September 2026</div>
            <div class="timeline__title">Redaktørens utvalg</div>
            <div class="timeline__desc">Redaksjonen leser alle bidrag og velger ut tekstene som skal med. Alle får tilbakemelding.</div>
        </div>
        <div class="timeline__item">
            <div class="timeline__date">Oktober 2026</div>
            <div class="timeline__title">Redigering & korrektur</div>
            <div class="timeline__desc">Utvalgte tekster finpusses i samarbeid med redaktør. Omslag og layout ferdigstilles.</div>
        </div>
        <div class="timeline__item">
            <div class="timeline__date">November 2026</div>
            <div class="timeline__title">Lansering & salg</div>
            <div class="timeline__desc">Boken lanseres med eksklusivt arrangement. Tilgjengelig i bokhandlere og på nett.</div>
        </div>
    </div>
</section>

<!-- RETNINGSLINJER -->
<section class="section section--wide">
    <div class="section__ornament">&#10052;</div>
    <div class="section__label">Praktisk info</div>
    <h2 class="section__title">Retningslinjer</h2>

    <div class="guidelines">
        <div class="guideline">
            <div class="guideline__icon">&#128207;</div>
            <div>
                <div class="guideline__title">Lengde</div>
                <div class="guideline__desc">Novelle/prosa: 1 500 – 5 000 ord. Dikt: opptil 40 linjer. Barnefortelling: 500 – 2 000 ord.</div>
            </div>
        </div>
        <div class="guideline">
            <div class="guideline__icon">&#128196;</div>
            <div>
                <div class="guideline__title">Format</div>
                <div class="guideline__desc">Send inn som .docx eller .pdf. Standard formatering — 12pt, 1,5 linjeavstand.</div>
            </div>
        </div>
        <div class="guideline">
            <div class="guideline__icon">&#127876;</div>
            <div>
                <div class="guideline__title">Tema</div>
                <div class="guideline__desc">Fri tolkning av «jul» eller «vinter». Det kan være alt fra julaften til den mørkeste vinternatt.</div>
            </div>
        </div>
        <div class="guideline">
            <div class="guideline__icon">&#10024;</div>
            <div>
                <div class="guideline__title">Originalt</div>
                <div class="guideline__desc">Teksten må være skrevet av deg og ikke tidligere publisert. Alle kan delta — du trenger ikke være elev.</div>
            </div>
        </div>
        <div class="guideline">
            <div class="guideline__icon">3</div>
            <div>
                <div class="guideline__title">Maks 3 bidrag</div>
                <div class="guideline__desc">Du kan sende inn opptil 3 forskjellige tekster. Send gjerne inn i ulike sjangre!</div>
            </div>
        </div>
        <div class="guideline">
            <div class="guideline__icon">&#128236;</div>
            <div>
                <div class="guideline__title">Alle får svar</div>
                <div class="guideline__desc">Alle som sender inn får tilbakemelding fra redaktør — uavhengig av om teksten velges ut.</div>
            </div>
        </div>
    </div>
</section>

<!-- SUBMISSION FORM -->
@if($isOpen)
<section class="section section--wide" id="send-inn">
    <div class="section__ornament">&#10022;</div>
    <div class="section__label">Send inn ditt bidrag</div>
    <h2 class="section__title">Del din fortelling</h2>

    <div class="submit-section">
        @if($errors->any())
            <div class="alert-error">
                <strong>Vennligst rett følgende:</strong><br>
                @foreach($errors->all() as $error)
                    &bull; {{ $error }}<br>
                @endforeach
            </div>
        @endif

        <form action="{{ route('front.anthology.submit') }}" method="POST" enctype="multipart/form-data" id="anthologyForm">
            @csrf
            <div class="form-grid">
                <div class="form-group">
                    <label>Fornavn</label>
                    <input type="text" name="first_name" placeholder="Ola" value="{{ old('first_name') }}" required>
                </div>
                <div class="form-group">
                    <label>Etternavn</label>
                    <input type="text" name="last_name" placeholder="Nordmann" value="{{ old('last_name') }}" required>
                </div>
                <div class="form-group">
                    <label>E-post</label>
                    <input type="email" name="email" placeholder="ola@eksempel.no" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label>Tilknytning</label>
                    <select name="connection" required onchange="toggleStudentField(this.value)">
                        <option value="">Velg</option>
                        <option value="elev" {{ old('connection') == 'elev' ? 'selected' : '' }}>Jeg er elev hos Forfatterskolen</option>
                        <option value="tidligere_elev" {{ old('connection') == 'tidligere_elev' ? 'selected' : '' }}>Jeg er tidligere elev</option>
                        <option value="ny" {{ old('connection') == 'ny' ? 'selected' : '' }}>Jeg er ny — dette er mitt første møte med Forfatterskolen</option>
                    </select>
                </div>
                <div class="form-group form-group--full" id="courseField" style="display:{{ in_array(old('connection'), ['elev','tidligere_elev']) ? 'block' : 'none' }};">
                    <label>Hvilket kurs går/gikk du på?</label>
                    <input type="text" name="course_name" placeholder="F.eks. Romankurs 2026, Årskurs 2025..." value="{{ old('course_name') }}">
                </div>
                <div class="form-group">
                    <label>Sjanger</label>
                    <select name="genre" required>
                        <option value="">Velg sjanger</option>
                        <option value="novelle" {{ old('genre') == 'novelle' ? 'selected' : '' }}>Novelle</option>
                        <option value="krim" {{ old('genre') == 'krim' ? 'selected' : '' }}>Krim & spenning</option>
                        <option value="barnefortelling" {{ old('genre') == 'barnefortelling' ? 'selected' : '' }}>Barnefortelling</option>
                        <option value="dikt" {{ old('genre') == 'dikt' ? 'selected' : '' }}>Dikt & lyrikk</option>
                        <option value="feelgood" {{ old('genre') == 'feelgood' ? 'selected' : '' }}>Feelgood</option>
                        <option value="sakprosa" {{ old('genre') == 'sakprosa' ? 'selected' : '' }}>Sakprosa / essay</option>
                    </select>
                </div>
                <div class="form-group form-group--full">
                    <label>Tittel på teksten</label>
                    <input type="text" name="title" placeholder="Tittelen på bidraget ditt" value="{{ old('title') }}" required>
                </div>
                <div class="form-group form-group--full">
                    <label>Kort beskrivelse (valgfritt)</label>
                    <textarea name="description" placeholder="En setning eller to om hva teksten handler om...">{{ old('description') }}</textarea>
                </div>
                <div class="form-group form-group--full">
                    <label>Last opp tekst</label>
                    <div class="upload-area" onclick="document.getElementById('fileInput').click()">
                        <div class="upload-area__icon">&#128206;</div>
                        <div class="upload-area__text" id="uploadText">Klikk for å velge fil, eller dra den hit</div>
                        <div class="upload-area__hint">.docx eller .pdf &middot; Maks 10 MB</div>
                    </div>
                    <input type="file" id="fileInput" name="manuscript" accept=".docx,.pdf" style="display:none" required>
                </div>
            </div>

            <div class="consent-item">
                <input type="checkbox" id="consent" name="consent" required {{ old('consent') ? 'checked' : '' }}>
                <label for="consent">
                    Jeg bekrefter at teksten er mitt eget originalverk og ikke tidligere publisert. Jeg godtar at Forfatterskolen vurderer teksten for antologien og gir meg tilbakemelding. Jeg godtar <a href="/terms/all" target="_blank">vilkårene</a>.
                </label>
            </div>

            <div class="consent-item">
                <input type="checkbox" id="consent_tips" name="consent_marketing" value="1" {{ old('consent_marketing') ? 'checked' : '' }}>
                <label for="consent_tips">
                    Ja, send meg gratis skrivetips og informasjon om kurs og utgivelser fra Forfatterskolen. (Valgfritt)
                </label>
            </div>

            <button type="submit" class="submit-btn" id="submitBtn">Send inn bidrag &#10022;</button>
        </form>
    </div>
</section>
@endif

<!-- UPSELL -->
<section class="section section--wide">
    <div class="section__ornament">&#10022;</div>
    <div class="section__label">Mer fra Forfatterskolen</div>
    <h2 class="section__title">Drømmer du om mer enn<br>én tekst mellom to permer?</h2>
    <p class="section__text">
        Antologien er bare begynnelsen. Uansett hvor du er i skriveprosessen —
        vi hjelper deg videre.
    </p>

    <div class="genre-grid upsell-grid" style="grid-template-columns: 1fr 1fr;">
        <div class="genre-card" style="text-align:left;padding:2.5rem;">
            <div style="font-size:0.55rem;font-family:var(--font-ui);font-weight:600;letter-spacing:3px;text-transform:uppercase;color:var(--gold);margin-bottom:1rem;">Skrivekurs</div>
            <div style="font-family:var(--font-display);font-size:1.5rem;font-weight:600;color:var(--snow-white);margin-bottom:0.75rem;">Lær håndverket</div>
            <p style="font-family:var(--font-ui);font-size:0.85rem;color:var(--frost);line-height:1.7;margin-bottom:1.25rem;">
                10 uker med profesjonelle forfattere og redaktører. Tilbakemelding på manus,
                ukentlige webinarer, og et skrivemiljø som bærer deg hele veien til ferdig bok.
            </p>
            <div style="margin-bottom:1.25rem;">
                <span style="font-family:var(--font-display);font-size:1.75rem;font-weight:600;color:var(--gold);">Fra kr 5 400</span>
                <span style="font-family:var(--font-ui);font-size:0.75rem;color:var(--text-dim);margin-left:0.35rem;">earlybird</span>
            </div>
            <a href="/course" style="font-family:var(--font-ui);font-size:0.825rem;font-weight:600;color:var(--gold);text-decoration:none;border-bottom:1px solid rgba(212,165,116,0.3);">Se alle kurs &#10140;</a>
        </div>

        <div class="genre-card" style="text-align:left;padding:2.5rem;">
            <div style="font-size:0.55rem;font-family:var(--font-ui);font-weight:600;letter-spacing:3px;text-transform:uppercase;color:var(--gold);margin-bottom:1rem;">Gi ut bok</div>
            <div style="font-family:var(--font-display);font-size:1.5rem;font-weight:600;color:var(--snow-white);margin-bottom:0.75rem;">Publiser med oss</div>
            <p style="font-family:var(--font-ui);font-size:0.85rem;color:var(--frost);line-height:1.7;margin-bottom:1.25rem;">
                Har du et ferdig manus? Gjennom Indiemoon Publishing hjelper vi deg med
                redaktør, omslag, trykk, distribusjon og markedsføring. Din bok — overalt.
            </p>
            <div style="margin-bottom:1.25rem;">
                <span style="font-family:var(--font-display);font-size:1.25rem;font-weight:600;color:var(--gold);">Komplett utgivelse</span>
            </div>
            <a href="https://indiemoon.no" style="font-family:var(--font-ui);font-size:0.825rem;font-weight:600;color:var(--gold);text-decoration:none;border-bottom:1px solid rgba(212,165,116,0.3);">Les mer om utgivelse &#10140;</a>
        </div>
    </div>

    <!-- Gratis tekstvurdering CTA -->
    <div style="text-align:center;margin-top:3rem;padding:2.5rem;background:rgba(134,39,54,0.08);border:1px solid rgba(134,39,54,0.15);border-radius:12px;">
        <div style="font-family:var(--font-display);font-size:1.35rem;font-weight:600;color:var(--snow-white);margin-bottom:0.5rem;">
            Usikker på om teksten din er god nok?
        </div>
        <p style="font-family:var(--font-ui);font-size:0.875rem;color:var(--frost);margin-bottom:1.25rem;">
            Send inn en smakebit (opptil 500 ord) og få gratis tilbakemelding fra en profesjonell redaktør. Helt uforpliktende.
        </p>
        <a href="/gratis-tekstvurdering" style="display:inline-block;font-family:var(--font-ui);font-size:0.85rem;font-weight:600;color:var(--midnight);background:linear-gradient(135deg,var(--gold),var(--gold-bright));padding:0.75rem 2rem;border-radius:4px;text-decoration:none;">Gratis tekstvurdering &#10140;</a>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="footer__logo">&#10052; Forfatterskolen — Juleantologi 2026 &#10052;</div>
    <div class="footer__links">
        <a href="/">forfatterskolen.no</a> &middot;
        <a href="/terms/all">Vilkår</a> &middot;
        <a href="mailto:post@forfatterskolen.no">post@forfatterskolen.no</a>
        <br>&copy; 2026 Forfatterskolen
    </div>
</footer>

@if(config('services.tracking.enabled'))
<!-- Meta Pixel -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,document,'script','https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '{{ config("services.meta_pixel.id") }}');
fbq('track', 'PageView');
</script>
<!-- Google tag -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.google_ads.id') }}"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','{{ config("services.google_ads.id") }}');</script>
@endif

<script>
    // Snow particles
    function createSnow() {
        var container = document.getElementById('snow');
        for (var i = 0; i < 60; i++) {
            var flake = document.createElement('div');
            flake.className = 'snowflake';
            flake.style.left = Math.random() * 100 + '%';
            flake.style.width = (2 + Math.random() * 4) + 'px';
            flake.style.height = flake.style.width;
            flake.style.animationDuration = (8 + Math.random() * 12) + 's';
            flake.style.animationDelay = Math.random() * 15 + 's';
            container.appendChild(flake);
        }
    }
    createSnow();

    // Countdown to 20. august 2026
    var deadline = new Date('2026-08-20T23:59:59+02:00').getTime();

    function updateCountdown() {
        var now = new Date().getTime();
        var diff = deadline - now;
        if (diff <= 0) {
            document.getElementById('cDays').textContent = '0';
            document.getElementById('cHours').textContent = '00';
            document.getElementById('cMins').textContent = '00';
            return;
        }
        document.getElementById('cDays').textContent = Math.floor(diff / 86400000);
        document.getElementById('cHours').textContent = String(Math.floor((diff % 86400000) / 3600000)).padStart(2, '0');
        document.getElementById('cMins').textContent = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
    }

    if (document.getElementById('cDays')) {
        updateCountdown();
        setInterval(updateCountdown, 60000);
    }

    // Toggle course field
    function toggleStudentField(val) {
        document.getElementById('courseField').style.display =
            (val === 'elev' || val === 'tidligere_elev') ? 'block' : 'none';
    }

    // File upload display
    document.getElementById('fileInput')?.addEventListener('change', function() {
        document.getElementById('uploadText').textContent = this.files[0]?.name || 'Klikk for å velge fil';
    });

    // Drag & drop
    var uploadArea = document.querySelector('.upload-area');
    if (uploadArea) {
        uploadArea.addEventListener('dragover', function(e) { e.preventDefault(); this.style.borderColor = '#d4a574'; this.style.background = 'rgba(212,165,116,0.05)'; });
        uploadArea.addEventListener('dragleave', function() { this.style.borderColor = ''; this.style.background = ''; });
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.borderColor = ''; this.style.background = '';
            var fileInput = document.getElementById('fileInput');
            fileInput.files = e.dataTransfer.files;
            document.getElementById('uploadText').textContent = e.dataTransfer.files[0]?.name || 'Klikk for å velge fil';
        });
    }

    // Prevent double submit
    document.getElementById('anthologyForm')?.addEventListener('submit', function() {
        var btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.textContent = 'Sender inn...';
    });

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(function(a) {
        a.addEventListener('click', function(e) {
            e.preventDefault();
            var target = document.querySelector(this.getAttribute('href'));
            if (target) target.scrollIntoView({ behavior: 'smooth' });
        });
    });
</script>

</body>
</html>

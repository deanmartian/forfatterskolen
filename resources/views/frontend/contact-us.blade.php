@extends('frontend.layout')

@section('page_title', 'Forfatterskolen &rsaquo; Om oss')
@section('meta_desc', 'Ta kontakt med Forfatterskolen. Vi hjelper deg med spørsmål om kurs, manusutvikling og publisering.')

@section('content')
<style>
/* ── OM OSS REDESIGN — scoped under .om-redesign ── */
.om-redesign {
    --om-wine: #862736;
    --om-wine-hover: #9c2e40;
    --om-wine-dark: #5c1a25;
    --om-wine-light: rgba(134, 39, 54, 0.08);
    --om-wine-light-solid: #f4e8ea;
    --om-cream: #faf8f5;
    --om-text: #1a1a1a;
    --om-text-sec: #5a5550;
    --om-text-muted: #8a8580;
    --om-border: rgba(0, 0, 0, 0.08);
    --om-border-strong: rgba(0, 0, 0, 0.12);
    --om-font-display: 'Playfair Display', Georgia, serif;
    --om-font-body: 'Source Sans 3', -apple-system, sans-serif;
    --om-max-w: 1080px;
    --om-radius: 10px;
    --om-radius-lg: 14px;
    font-family: var(--om-font-body);
    color: var(--om-text);
    -webkit-font-smoothing: antialiased;
}

/* ── HERO ── */
.om-hero {
    max-width: var(--om-max-w);
    margin: 0 auto;
    padding: 4rem 2rem 3.5rem;
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 3.5rem;
    align-items: center;
}
.om-hero__eyebrow {
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--om-wine);
    margin-bottom: 1rem;
}
.om-hero__heading {
    font-family: var(--om-font-display);
    font-size: clamp(2rem, 3.5vw, 2.5rem);
    font-weight: 700;
    line-height: 1.15;
    color: var(--om-text);
    margin-bottom: 1.25rem;
}
.om-hero__heading em { color: var(--om-wine); font-style: italic; }
.om-hero__desc {
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.8;
    color: var(--om-text-sec);
    margin-bottom: 1.5rem;
}
.om-hero__desc p + p { margin-top: 0.75rem; }
.om-hero__social {
    display: flex;
    gap: 0.5rem;
}
.om-social-link {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--om-cream);
    border: 1px solid var(--om-border);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s, border-color 0.2s, transform 0.15s;
    text-decoration: none;
}
.om-social-link:hover {
    background: var(--om-wine);
    border-color: var(--om-wine);
    transform: translateY(-2px);
    text-decoration: none;
}
.om-social-link svg {
    width: 16px; height: 16px;
    fill: var(--om-text-muted);
    transition: fill 0.2s;
}
.om-social-link:hover svg { fill: #fff; }
.om-hero__image-frame {
    aspect-ratio: 3 / 4;
    background: linear-gradient(145deg, #e8e2da, #d4cec6);
    border-radius: var(--om-radius-lg);
    overflow: hidden;
}
.om-hero__image-frame img {
    width: 100%; height: 100%;
    object-fit: cover;
}
.om-hero__image-caption {
    font-size: 0.7rem;
    color: var(--om-text-muted);
    text-align: right;
    margin-top: 0.5rem;
}

/* ── ADVISORY ── */
.om-advisory {
    max-width: var(--om-max-w);
    margin: 0 auto;
    padding: 0 2rem 1rem;
}
.om-advisory__box {
    background: #fff3cd;
    border: 1px solid #ffc107;
    border-radius: 8px;
    padding: 1rem 1.25rem;
    font-size: 0.9rem;
    color: #664d03;
}

/* ── STORY SECTION ── */
.om-story {
    background: var(--om-cream);
    border-top: 1px solid var(--om-border);
    border-bottom: 1px solid var(--om-border);
    padding: 4rem 2rem;
}
.om-story__inner {
    max-width: 720px;
    margin: 0 auto;
}
.om-section-heading {
    font-family: var(--om-font-display);
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--om-text);
    text-align: center;
    margin-bottom: 0.5rem;
}
.om-section-heading::after {
    content: '';
    display: block;
    width: 40px; height: 3px;
    background: var(--om-wine);
    border-radius: 2px;
    margin: 0.75rem auto 0;
}
.om-section-sub {
    font-size: 0.95rem;
    color: var(--om-text-sec);
    text-align: center;
    margin-top: 0.75rem;
    margin-bottom: 2.5rem;
}
.om-story__text {
    font-size: 1rem;
    line-height: 1.85;
    color: var(--om-text-sec);
}
.om-story__text p + p { margin-top: 1.25rem; }
.om-story__quote {
    font-family: var(--om-font-display);
    font-size: 1.35rem;
    font-style: italic;
    line-height: 1.5;
    color: var(--om-wine-dark);
    border-left: 3px solid var(--om-wine);
    padding-left: 1.5rem;
    margin: 2rem 0;
}

/* ── VALUES ── */
.om-values {
    padding: 4rem 2rem;
}
.om-values__inner {
    max-width: var(--om-max-w);
    margin: 0 auto;
}
.om-values__grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1.5rem;
}
.om-value-card {
    padding: 2rem 1.5rem;
    border: 1px solid var(--om-border);
    border-radius: var(--om-radius-lg);
    text-align: center;
    transition: border-color 0.2s;
}
.om-value-card:hover { border-color: var(--om-border-strong); }
.om-value-card__icon {
    width: 52px; height: 52px;
    margin: 0 auto 1.25rem;
    background: var(--om-wine-light-solid);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.om-value-card__icon svg { width: 24px; height: 24px; }
.om-value-card__title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--om-text);
    margin-bottom: 0.5rem;
}
.om-value-card__desc {
    font-size: 0.85rem;
    color: var(--om-text-sec);
    line-height: 1.6;
}

/* ── TEAM ── */
.om-team {
    padding: 4rem 2rem;
    background: var(--om-cream);
    border-top: 1px solid var(--om-border);
    border-bottom: 1px solid var(--om-border);
}
.om-team__inner {
    max-width: var(--om-max-w);
    margin: 0 auto;
}
.om-team__grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 1.25rem;
}
.om-team-card {
    background: #fff;
    border: 1px solid var(--om-border);
    border-radius: var(--om-radius-lg);
    padding: 1.5rem;
    text-align: center;
    transition: border-color 0.2s, box-shadow 0.2s;
}
.om-team-card:hover {
    border-color: var(--om-border-strong);
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}
.om-team-card__avatar {
    width: 72px; height: 72px;
    border-radius: 50%;
    margin: 0 auto 1rem;
    overflow: hidden;
    background: var(--om-wine-light-solid);
    display: flex;
    align-items: center;
    justify-content: center;
}
.om-team-card__avatar img {
    width: 100%; height: 100%;
    object-fit: cover;
    border-radius: 50%;
}
.om-team-card__avatar-initials {
    font-family: var(--om-font-display);
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--om-wine);
}
.om-team-card__name {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--om-text);
    margin-bottom: 0.2rem;
}
.om-team-card__role {
    font-size: 0.78rem;
    color: var(--om-wine);
    font-weight: 500;
    margin-bottom: 0.6rem;
}
.om-team-card__bio {
    font-size: 0.8rem;
    color: var(--om-text-sec);
    line-height: 1.6;
}
.om-team-card__email {
    display: inline-block;
    margin-top: 0.75rem;
    font-size: 0.78rem;
    color: var(--om-wine);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.15s;
}
.om-team-card__email:hover { color: var(--om-wine-hover); text-decoration: none; }

/* ── CONTACT ── */
.om-contact {
    padding: 4rem 2rem;
}
.om-contact__inner {
    max-width: var(--om-max-w);
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 3rem;
    align-items: start;
}
.om-contact-form {
    background: var(--om-cream);
    border: 1px solid var(--om-border);
    border-radius: var(--om-radius-lg);
    padding: 2rem;
}
.om-contact-form__heading {
    font-family: var(--om-font-display);
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--om-text);
    margin-bottom: 0.35rem;
}
.om-contact-form__sub {
    font-size: 0.85rem;
    color: var(--om-text-sec);
    margin-bottom: 1.5rem;
}
.om-form-group {
    margin-bottom: 1rem;
}
.om-form-group label {
    display: block;
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--om-text);
    margin-bottom: 0.35rem;
}
.om-form-group input,
.om-form-group textarea {
    width: 100%;
    padding: 0.7rem 0.9rem;
    border: 1px solid var(--om-border-strong);
    border-radius: 6px;
    font-family: var(--om-font-body);
    font-size: 0.875rem;
    color: var(--om-text);
    background: #fff;
    outline: none;
    transition: border-color 0.2s;
}
.om-form-group input:focus,
.om-form-group textarea:focus {
    border-color: var(--om-wine);
}
.om-form-group textarea {
    resize: vertical;
    min-height: 120px;
}
.om-form-group input::placeholder,
.om-form-group textarea::placeholder {
    color: var(--om-text-muted);
}
.om-form-checkbox {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    margin-bottom: 1.25rem;
}
.om-form-checkbox input[type="checkbox"] {
    width: 16px; height: 16px;
    margin-top: 2px;
    accent-color: var(--om-wine);
    flex-shrink: 0;
}
.om-form-checkbox label {
    font-size: 0.78rem;
    color: var(--om-text-sec);
    line-height: 1.5;
}
.om-form-checkbox a {
    color: var(--om-wine);
    text-decoration: none;
}
.om-form-submit {
    width: 100%;
    padding: 0.8rem;
    background: var(--om-wine);
    color: #fff;
    border: none;
    border-radius: 6px;
    font-family: var(--om-font-body);
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}
.om-form-submit:hover { background: var(--om-wine-hover); }
.om-form-note {
    font-size: 0.72rem;
    color: var(--om-text-muted);
    margin-top: 0.75rem;
    text-align: center;
}
.om-form-alert {
    margin-top: 1rem;
    padding: 0.75rem 1rem;
    border-radius: 6px;
    font-size: 0.85rem;
}
.om-form-alert--success {
    background: #e8f5e9;
    color: #2e7d32;
    border: 1px solid #c8e6c9;
}
.om-form-alert--danger {
    background: #fce8e8;
    color: #c62828;
    border: 1px solid #f5c6c6;
}
.om-form-alert ul {
    margin: 0;
    padding-left: 1.25rem;
    list-style: disc;
}
.om-form-alert .om-alert-close {
    float: right;
    background: none;
    border: none;
    font-size: 1.1rem;
    cursor: pointer;
    color: inherit;
    opacity: 0.6;
    line-height: 1;
}
.om-form-alert .om-alert-close:hover { opacity: 1; }

/* Contact info sidebar */
.om-contact-info__heading {
    font-family: var(--om-font-display);
    font-size: 1.35rem;
    font-weight: 700;
    color: var(--om-text);
    margin-bottom: 1.5rem;
}
.om-contact-info-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.om-contact-info-item__icon {
    width: 44px; height: 44px;
    background: var(--om-wine-light-solid);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.om-contact-info-item__icon svg { width: 20px; height: 20px; }
.om-contact-info-item__label {
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--om-text-muted);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 0.2rem;
}
.om-contact-info-item__value {
    font-size: 0.95rem;
    color: var(--om-text);
    font-weight: 500;
}
.om-contact-info-item__value a {
    color: var(--om-text);
    text-decoration: none;
    transition: color 0.15s;
}
.om-contact-info-item__value a:hover { color: var(--om-wine); text-decoration: none; }
.om-response-badge {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    background: #e8f5e9;
    border-radius: 8px;
    padding: 0.85rem 1rem;
    margin-top: 2rem;
}
.om-response-badge__dot {
    width: 8px; height: 8px;
    border-radius: 50%;
    background: #4caf50;
    flex-shrink: 0;
}
.om-response-badge__text {
    font-size: 0.8rem;
    color: #2e7d32;
    font-weight: 500;
}

/* ── RESPONSIVE ── */
@media (max-width: 900px) {
    .om-hero {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    .om-hero__image-frame { max-width: 320px; }
    .om-contact__inner {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
}
@media (max-width: 600px) {
    .om-values__grid { grid-template-columns: 1fr; }
    .om-team__grid { grid-template-columns: 1fr; }
    .om-hero { padding: 2.5rem 1.25rem 2rem; }
    .om-story { padding: 3rem 1.25rem; }
    .om-values { padding: 3rem 1.25rem; }
    .om-team { padding: 3rem 1.25rem; }
    .om-contact { padding: 3rem 1.25rem; }
}
</style>

<div class="om-redesign">

    {{-- ═══════════ HERO ═══════════ --}}
    <section class="om-hero">
        <div>
            <p class="om-hero__eyebrow">Om Forfatterskolen</p>
            <h1 class="om-hero__heading">Din litterære familie <em>siden 2015</em></h1>
            <div class="om-hero__desc">
                <p>Grunnlegger og rektor for skolen er Kristine Storli Henningsen — en erfaren forfatter med flere titalls bøker utgitt på Gyldendal, Cappelen Damm, Vigmostad &amp; Bjørke og Flux. Debutromanen hennes ble solgt til flere land.</p>
                <p>Med bakgrunn som journalist, spaltist og redaktør i over 15 år, bygget Kristine Forfatterskolen for å gi aspirerende forfattere det samme verktøysettet hun selv brukte.</p>
            </div>
            <div class="om-hero__social">
                <a href="https://www.facebook.com/bliforfatter/" class="om-social-link" aria-label="Facebook" target="_blank" rel="noopener">
                    <svg viewBox="0 0 24 24"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                </a>
                <a href="https://www.instagram.com/forfatterskolen_norge/" class="om-social-link" aria-label="Instagram" target="_blank" rel="noopener">
                    <svg viewBox="0 0 24 24"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8zm0 6.4A2.4 2.4 0 1 1 12 9.6a2.4 2.4 0 0 1 0 4.8zM17.2 7.6a.96.96 0 1 1-1.92 0 .96.96 0 0 1 1.92 0z"/></svg>
                </a>
                <a href="https://no.pinterest.com/forfatterskolen_norge/" class="om-social-link" aria-label="Pinterest" target="_blank" rel="noopener">
                    <svg viewBox="0 0 24 24"><path d="M12 2C6.477 2 2 6.477 2 12c0 4.236 2.636 7.855 6.356 9.312-.088-.791-.167-2.005.035-2.868.182-.78 1.172-4.97 1.172-4.97s-.299-.598-.299-1.482c0-1.388.806-2.425 1.808-2.425.853 0 1.265.64 1.265 1.408 0 .858-.546 2.14-.828 3.33-.236.995.5 1.807 1.48 1.807 1.778 0 3.144-1.874 3.144-4.58 0-2.393-1.72-4.068-4.177-4.068-2.845 0-4.515 2.134-4.515 4.34 0 .859.331 1.781.745 2.282a.3.3 0 0 1 .069.288l-.278 1.133c-.044.183-.145.222-.335.134-1.249-.581-2.03-2.407-2.03-3.874 0-3.154 2.292-6.052 6.608-6.052 3.469 0 6.165 2.473 6.165 5.776 0 3.447-2.173 6.22-5.19 6.22-1.013 0-1.965-.527-2.291-1.148l-.623 2.378c-.226.869-.835 1.958-1.244 2.621.937.29 1.931.446 2.962.446 5.523 0 10-4.477 10-10S17.523 2 12 2z"/></svg>
                </a>
            </div>
        </div>

        <div>
            <div class="om-hero__image-frame">
                <img src="https://www.forfatterskolen.no/images-new/kristine1.png" alt="Kristine Storli Henningsen">
            </div>
            <p class="om-hero__image-caption">Foto: Angel Carlos</p>
        </div>
    </section>

    {{-- Advisory notice --}}
    @if($hasAdvisory)
        <div class="om-advisory">
            <div class="om-advisory__box">
                {!! $advisory->message ?? 'Advisory' !!}
            </div>
        </div>
    @endif

    {{-- ═══════════ VÅR HISTORIE ═══════════ --}}
    <section class="om-story">
        <div class="om-story__inner">
            <h2 class="om-section-heading">Vår historie</h2>
            <p class="om-section-sub">Fra en idé til Norges største nettbaserte skriveskole.</p>

            <div class="om-story__text">
                <p>
                    Forfatterskolen startet med en enkel tanke: at alle som har en historie å fortelle, fortjener profesjonell hjelp til å fortelle den. Kristine Storli Henningsen visste fra egen erfaring som forfatter og redaktør at veien fra idé til ferdig bok er lang — og at riktig veiledning gjør hele forskjellen.
                </p>

                <div class="om-story__quote">
                    «Kan jeg, kan du.»
                </div>

                <p>
                    I dag teller Forfatterskolen flere tusen elever og et knippe erfarne redaktører. Vi har fulgt mange av dem helt fra første utkast til utgivelse — på alt fra store forlag til eget forlag. Skolen drives nettbasert, noe som betyr at du kan delta uansett hvor du bor, og i ditt eget tempo.
                </p>

                <p>
                    Hver uke samler vi elever til mentormøter med kjente forfattere og redaktører — live og direkte. Det er her magien skjer: du lærer håndverket fra folk som lever av det, og du blir en del av et fellesskap av skriveglade mennesker.
                </p>
            </div>
        </div>
    </section>

    {{-- ═══════════ VERDIER ═══════════ --}}
    <section class="om-values">
        <div class="om-values__inner">
            <h2 class="om-section-heading">Hva vi tror på</h2>
            <p class="om-section-sub">Tre pilarer som driver alt vi gjør.</p>

            <div class="om-values__grid">
                <div class="om-value-card">
                    <div class="om-value-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                            <path d="M2 17l10 5 10-5"/>
                            <path d="M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <div class="om-value-card__title">Håndverk</div>
                    <div class="om-value-card__desc">Skriving er et håndverk som kan læres. Vi gir deg verktøyene og teknikkene som erfarne forfattere bruker — ikke vage råd, men konkret, praktisk kunnskap.</div>
                </div>
                <div class="om-value-card">
                    <div class="om-value-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                            <circle cx="9" cy="7" r="4"/>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
                        </svg>
                    </div>
                    <div class="om-value-card__title">Fellesskap</div>
                    <div class="om-value-card__desc">Å skrive trenger ikke være ensomt. Våre mentormøter, kursgrupper og redaktørsamtaler gir deg et støttende nettverk av likesinnede.</div>
                </div>
                <div class="om-value-card">
                    <div class="om-value-card__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                        </svg>
                    </div>
                    <div class="om-value-card__title">Fremdrift</div>
                    <div class="om-value-card__desc">Vi dytter deg videre — med ærlige tilbakemeldinger, tydelige mål og en struktur som gjør at manuset faktisk blir ferdig.</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════ TEAM ═══════════ --}}
    <section class="om-team">
        <div class="om-team__inner">
            <h2 class="om-section-heading">Team Forfatterskolen</h2>
            <p class="om-section-sub">Menneskene bak skolen.</p>

            <div class="om-team__grid">
                <div class="om-team-card">
                    <div class="om-team-card__avatar">
                        <img src="https://www.forfatterskolen.no/images-new/kristine1.png" alt="Kristine Storli Henningsen">
                    </div>
                    <div class="om-team-card__name">Kristine Storli Henningsen</div>
                    <div class="om-team-card__role">Grunnlegger &amp; rektor</div>
                    <div class="om-team-card__bio">Forfatter med titalls utgivelser på Gyldendal, Cappelen Damm og Vigmostad &amp; Bjørke. 15 års erfaring som journalist og redaktør.</div>
                </div>

                <div class="om-team-card">
                    <div class="om-team-card__avatar">
                        <span class="om-team-card__avatar-initials">MR</span>
                    </div>
                    <div class="om-team-card__name">Marit Reiersgård</div>
                    <div class="om-team-card__role">Mentormøter &amp; webinarer</div>
                    <div class="om-team-card__bio">Forfatter med bøker oversatt til flere språk, nominert til Rivertonprisen. Ansvarlig for å booke forfattere og drifte mentormøtene.</div>
                    <a href="mailto:marit@forfatterskolen.no" class="om-team-card__email">marit@forfatterskolen.no</a>
                </div>

                <div class="om-team-card">
                    <div class="om-team-card__avatar">
                        <span class="om-team-card__avatar-initials">AF</span>
                    </div>
                    <div class="om-team-card__name">Annina Forsblom</div>
                    <div class="om-team-card__role">Kursansvarlig EasyWrite &amp; support</div>
                    <div class="om-team-card__bio">Kursansvarlig for EasyWrite Sverige og ansvarlig for support til kursdeltagere.</div>
                </div>

                <div class="om-team-card">
                    <div class="om-team-card__avatar">
                        <span class="om-team-card__avatar-initials">SH</span>
                    </div>
                    <div class="om-team-card__name">Sven Inge Henningsen</div>
                    <div class="om-team-card__role">Webutvikling &amp; publisering</div>
                    <div class="om-team-card__bio">Ansvarlig for nettportal, redaksjonell støtte og publiseringstjenester. Utvikler og vedlikeholder skolens digitale plattform.</div>
                </div>
            </div>
        </div>
    </section>

    {{-- ═══════════ KONTAKT ═══════════ --}}
    <section class="om-contact">
        <div class="om-contact__inner">

            {{-- Contact form --}}
            <div class="om-contact-form">
                <h2 class="om-contact-form__heading">Kontakt oss</h2>
                <p class="om-contact-form__sub">Har du spørsmål? Vi svarer gjerne!</p>

                <form method="POST" action="" onsubmit="disableSubmit(this)">
                    {{ csrf_field() }}
                    <div class="om-form-group">
                        <label for="om-fullname">Navn</label>
                        <input type="text" id="om-fullname" name="fullname" placeholder="Ditt fulle navn" required value="{{ old('fullname') }}">
                    </div>
                    <div class="om-form-group">
                        <label for="om-email">E-postadresse</label>
                        <input type="email" id="om-email" name="email" placeholder="din@epost.no" required value="{{ old('email') }}">
                    </div>
                    <div class="om-form-group">
                        <label for="om-message">Melding</label>
                        <textarea id="om-message" name="message" placeholder="Hva kan vi hjelpe deg med?" required>{{ old('message') }}</textarea>
                    </div>
                    <div class="om-form-checkbox">
                        <input type="checkbox" name="terms" required id="om-terms">
                        {!! str_replace(
                            ['_start_label_', '_end_label_', '_start_link_','_end_link_'],
                            ['<label for="om-terms">','</label>','<a href="'.url('/opt-in-terms').'" target="_blank">','</a>'],
                            trans('site.front.contact-us.accept-terms')
                        ) !!}
                    </div>

                    <p class="om-form-note" style="margin-bottom: 0.75rem;">
                        {{ trans('site.front.contact-us.note') }}
                    </p>

                    {!! \Anhskohbo\NoCaptcha\Facades\NoCaptcha::display() !!}

                    <div style="margin-top: 1rem;">
                        <button type="submit" class="om-form-submit">Send melding</button>
                    </div>
                </form>

                @if ($errors->any())
                    @php
                        $alert_type = session('alert_type', 'danger');
                    @endphp
                    <div class="om-form-alert om-form-alert--{{ $alert_type }}">
                        <button type="button" class="om-alert-close" onclick="this.parentElement.remove()">&times;</button>
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            {{-- Contact info sidebar --}}
            <div class="om-contact-info">
                <h2 class="om-contact-info__heading">Finn oss</h2>

                <div class="om-contact-info-item">
                    <div class="om-contact-info-item__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                    </div>
                    <div>
                        <div class="om-contact-info-item__label">E-post</div>
                        <div class="om-contact-info-item__value"><a href="mailto:post@forfatterskolen.no">post@forfatterskolen.no</a></div>
                    </div>
                </div>

                <div class="om-contact-info-item">
                    <div class="om-contact-info-item__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="om-contact-info-item__label">Telefon</div>
                        <div class="om-contact-info-item__value"><a href="tel:+4741123555">+47 411 23 555</a></div>
                    </div>
                </div>

                <div class="om-contact-info-item">
                    <div class="om-contact-info-item__icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="1.5" stroke-linecap="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                            <circle cx="12" cy="10" r="3"/>
                        </svg>
                    </div>
                    <div>
                        <div class="om-contact-info-item__label">Adresse</div>
                        <div class="om-contact-info-item__value">Lihagen 21, 3029 Drammen</div>
                    </div>
                </div>

                <div class="om-response-badge">
                    <span class="om-response-badge__dot"></span>
                    <span class="om-response-badge__text">Vi svarer vanligvis innen 24 timer</span>
                </div>
            </div>
        </div>
    </section>

</div>
@stop

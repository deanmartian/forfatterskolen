<!DOCTYPE html>
<html lang="no">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Skriv ditt liv - Kurs i memoarskriving | Forfatterskolen</title>
<meta name="description" content="Har du historier du vil gi videre? Forfatterskolen hjelper deg skrive dine minner - og gi dem til dem du er glad i.">
<meta name="robots" content="noindex, nofollow">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Source+Sans+3:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
  :root {
    --primary: #862736;
    --primary-dark: #5f1a25;
    --primary-light: #a83040;
    --bg-warm: #fdf8f4;
    --bg-cream: #f5ede4;
    --text: #1a1a1a;
    --text-muted: #555555;
    --white: #ffffff;
  }

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    font-family: 'Source Sans 3', sans-serif;
    color: var(--text);
    background: var(--white);
    font-size: 18px;
    line-height: 1.7;
  }

  h1, h2, h3, h4 {
    font-family: 'Playfair Display', serif;
    line-height: 1.2;
  }

  /* NAV */
  .sp-nav {
    background: var(--white);
    border-bottom: 1px solid #e8e0d8;
    padding: 18px 0;
    position: sticky;
    top: 0;
    z-index: 100;
  }
  .sp-nav-inner {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .sp-logo {
    font-family: 'Playfair Display', serif;
    font-size: 1.2rem;
    color: var(--primary);
    text-decoration: none;
    font-weight: 700;
    letter-spacing: 0.02em;
  }
  .sp-nav-cta {
    background: var(--primary);
    color: var(--white);
    padding: 10px 24px;
    border-radius: 3px;
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 600;
    transition: background 0.2s;
  }
  .sp-nav-cta:hover { background: var(--primary-dark); }

  /* HERO */
  .sp-hero {
    background: var(--bg-warm);
    position: relative;
    overflow: hidden;
    padding: 100px 32px 90px;
  }
  .sp-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(ellipse at 70% 50%, rgba(134,39,54,0.06) 0%, transparent 70%);
    pointer-events: none;
  }
  .sp-hero-inner {
    max-width: 1100px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 64px;
    align-items: center;
  }
  .sp-hero-tag {
    display: inline-block;
    background: rgba(134,39,54,0.1);
    color: var(--primary);
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    padding: 6px 14px;
    border-radius: 2px;
    margin-bottom: 20px;
  }
  .sp-hero h1 {
    font-size: clamp(2.2rem, 4vw, 3.4rem);
    color: var(--text);
    margin-bottom: 22px;
    font-style: italic;
  }
  .sp-hero h1 em {
    color: var(--primary);
    font-style: italic;
  }
  .sp-hero p {
    font-size: 1.15rem;
    color: var(--text-muted);
    margin-bottom: 36px;
    max-width: 480px;
  }
  .sp-btn-primary {
    display: inline-block;
    background: var(--primary);
    color: var(--white);
    padding: 16px 36px;
    border-radius: 3px;
    text-decoration: none;
    font-size: 1rem;
    font-weight: 600;
    letter-spacing: 0.02em;
    transition: background 0.2s, transform 0.1s;
  }
  .sp-btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); }
  .sp-btn-outline {
    display: inline-block;
    border: 2px solid var(--primary);
    color: var(--primary);
    padding: 14px 32px;
    border-radius: 3px;
    text-decoration: none;
    font-size: 1rem;
    font-weight: 600;
    margin-left: 16px;
    transition: all 0.2s;
  }
  .sp-btn-outline:hover { background: var(--primary); color: var(--white); }

  .sp-hero-image {
    border-radius: 4px;
    overflow: hidden;
    aspect-ratio: 4/3;
    background: var(--bg-cream);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
  }
  .sp-hero-image-placeholder {
    text-align: center;
    color: var(--text-muted);
    font-size: 0.9rem;
    font-family: 'Source Sans 3', sans-serif;
  }
  .sp-hero-image-placeholder svg {
    display: block;
    margin: 0 auto 12px;
    opacity: 0.3;
  }
  .sp-hero-badge {
    position: absolute;
    bottom: 20px;
    left: 20px;
    background: var(--white);
    padding: 12px 18px;
    border-radius: 3px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    font-size: 0.85rem;
  }
  .sp-hero-badge strong {
    display: block;
    color: var(--primary);
    font-family: 'Playfair Display', serif;
    font-size: 1.1rem;
  }

  /* SECTION COMMONS */
  section { padding: 80px 32px; }
  .sp-section-inner { max-width: 1100px; margin: 0 auto; }
  .sp-section-label {
    font-size: 0.78rem;
    font-weight: 600;
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: var(--primary);
    margin-bottom: 12px;
  }
  .sp-section-title {
    font-size: clamp(1.7rem, 3vw, 2.5rem);
    margin-bottom: 16px;
  }
  .sp-section-lead {
    font-size: 1.1rem;
    color: var(--text-muted);
    max-width: 560px;
    margin-bottom: 48px;
  }

  /* QUOTE SECTION */
  .sp-quote-section {
    background: var(--bg-warm);
    padding: 80px 32px;
  }
  .sp-quote-inner {
    max-width: 1100px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    align-items: center;
  }
  .sp-quote-mark {
    font-family: 'Playfair Display', serif;
    font-size: 7rem;
    color: var(--primary);
    opacity: 0.15;
    line-height: 0.6;
    margin-bottom: 16px;
    display: block;
  }
  .sp-quote-text {
    font-family: 'Playfair Display', serif;
    font-size: 1.6rem;
    font-style: italic;
    line-height: 1.5;
    color: var(--text);
    margin-bottom: 20px;
  }
  .sp-quote-author {
    font-size: 0.9rem;
    color: var(--text-muted);
    font-weight: 600;
  }
  .sp-quote-body p {
    font-size: 1.1rem;
    color: var(--text-muted);
    line-height: 1.8;
    margin-bottom: 20px;
  }
  .sp-divider {
    width: 40px;
    height: 3px;
    background: var(--primary);
    margin-bottom: 28px;
    border: none;
  }

  /* FEATURES / CARDS */
  .sp-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 28px;
    margin-top: 48px;
  }
  .sp-card {
    border: 1px solid #e8e0d8;
    border-top: 3px solid var(--primary);
    padding: 36px 28px;
    border-radius: 3px;
    transition: box-shadow 0.2s;
  }
  .sp-card:hover { box-shadow: 0 8px 30px rgba(134,39,54,0.08); }
  .sp-card-icon {
    font-size: 2rem;
    margin-bottom: 16px;
    display: block;
  }
  .sp-card h3 {
    font-size: 1.2rem;
    margin-bottom: 12px;
    color: var(--text);
  }
  .sp-card p {
    font-size: 0.95rem;
    color: var(--text-muted);
    line-height: 1.7;
  }

  /* STORIES */
  .sp-stories {
    background: var(--bg-warm);
  }
  .sp-story-cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 28px;
    margin-top: 48px;
  }
  .sp-story-card {
    background: var(--white);
    border-radius: 3px;
    overflow: hidden;
    box-shadow: 0 2px 16px rgba(0,0,0,0.06);
    transition: transform 0.2s, box-shadow 0.2s;
  }
  .sp-story-card:hover { transform: translateY(-4px); box-shadow: 0 8px 30px rgba(0,0,0,0.1); }
  .sp-story-img {
    height: 180px;
    background: var(--bg-cream);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-muted);
    font-size: 0.85rem;
  }
  .sp-story-body { padding: 24px; }
  .sp-story-body h4 {
    font-size: 1.05rem;
    margin-bottom: 10px;
    color: var(--text);
  }
  .sp-story-quote {
    font-style: italic;
    font-size: 0.9rem;
    color: var(--text-muted);
    border-left: 3px solid var(--primary);
    padding-left: 14px;
    margin: 12px 0;
  }
  .sp-story-link {
    font-size: 0.85rem;
    color: var(--primary);
    font-weight: 600;
    text-decoration: none;
  }
  .sp-story-link:hover { text-decoration: underline; }

  /* KURS SECTION */
  .sp-kurs {
    background: var(--white);
  }
  .sp-kurs-inner {
    max-width: 1100px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    align-items: start;
  }
  .sp-kurs-list {
    list-style: none;
    margin: 24px 0 32px;
  }
  .sp-kurs-list li {
    padding: 12px 0;
    border-bottom: 1px solid #f0e8e0;
    display: flex;
    gap: 12px;
    font-size: 0.98rem;
    color: var(--text-muted);
  }
  .sp-kurs-list li::before {
    content: '\2713';
    color: var(--primary);
    font-weight: 700;
    flex-shrink: 0;
  }
  .sp-pris-box {
    background: var(--bg-warm);
    border: 1px solid #e8e0d8;
    border-radius: 4px;
    padding: 36px;
    position: sticky;
    top: 100px;
  }
  .sp-pris-label {
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-bottom: 8px;
  }
  .sp-pris-amount {
    font-family: 'Playfair Display', serif;
    font-size: 3rem;
    color: var(--primary);
    font-weight: 700;
    line-height: 1;
    margin-bottom: 4px;
  }
  .sp-pris-avdrag {
    font-size: 0.9rem;
    color: var(--text-muted);
    margin-bottom: 28px;
  }
  .sp-pris-box .sp-btn-primary {
    width: 100%;
    text-align: center;
    margin-bottom: 12px;
    display: block;
  }
  .sp-pris-note {
    font-size: 0.82rem;
    color: var(--text-muted);
    text-align: center;
  }
  .sp-pris-features {
    list-style: none;
    margin: 24px 0;
  }
  .sp-pris-features li {
    font-size: 0.9rem;
    padding: 6px 0;
    color: var(--text-muted);
    display: flex;
    gap: 10px;
  }
  .sp-pris-features li::before {
    content: '\2713';
    color: var(--primary);
    font-weight: 700;
  }

  /* FAQ */
  .sp-faq {
    background: var(--bg-warm);
  }
  .sp-faq-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
    margin-top: 48px;
  }
  .sp-faq-item {
    background: var(--white);
    padding: 28px;
    border-radius: 3px;
    border-left: 3px solid var(--primary);
  }
  .sp-faq-item h4 {
    font-size: 1rem;
    margin-bottom: 10px;
    color: var(--text);
  }
  .sp-faq-item p {
    font-size: 0.92rem;
    color: var(--text-muted);
    line-height: 1.7;
  }

  /* INDIEMOON UPSELL */
  .sp-indiemoon {
    background: var(--white);
  }
  .sp-indiemoon-box {
    background: var(--bg-cream);
    border-radius: 4px;
    padding: 56px 64px;
    display: grid;
    grid-template-columns: 1fr auto;
    gap: 48px;
    align-items: center;
  }
  .sp-indiemoon-box h3 {
    font-size: 1.8rem;
    margin-bottom: 14px;
  }
  .sp-indiemoon-box p {
    font-size: 1rem;
    color: var(--text-muted);
    max-width: 500px;
  }

  /* FINAL CTA */
  .sp-final-cta {
    background: var(--primary);
    text-align: center;
    padding: 100px 32px;
  }
  .sp-final-cta h2 {
    font-size: clamp(1.8rem, 3.5vw, 3rem);
    color: var(--white);
    margin-bottom: 16px;
  }
  .sp-final-cta p {
    color: rgba(255,255,255,0.8);
    font-size: 1.1rem;
    margin-bottom: 40px;
  }
  .sp-btn-white {
    display: inline-block;
    background: var(--white);
    color: var(--primary);
    padding: 16px 40px;
    border-radius: 3px;
    text-decoration: none;
    font-weight: 700;
    font-size: 1rem;
    transition: transform 0.1s, box-shadow 0.2s;
  }
  .sp-btn-white:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.2); }

  /* FOOTER */
  .sp-footer {
    background: #1a1a1a;
    color: rgba(255,255,255,0.6);
    text-align: center;
    padding: 32px;
    font-size: 0.85rem;
  }
  .sp-footer a { color: rgba(255,255,255,0.5); text-decoration: none; }

  /* RESPONSIVE */
  @media (max-width: 768px) {
    .sp-hero-inner,
    .sp-quote-inner,
    .sp-kurs-inner,
    .sp-indiemoon-box { grid-template-columns: 1fr; }
    .sp-cards,
    .sp-story-cards,
    .sp-faq-grid { grid-template-columns: 1fr; }
    .sp-hero-image { display: none; }
    .sp-btn-outline { display: none; }
    .sp-indiemoon-box { padding: 32px; }
  }
</style>
</head>
<body>

<!-- NAV -->
<nav class="sp-nav">
  <div class="sp-nav-inner">
    <a href="/" class="sp-logo">Forfatterskolen</a>
    <a href="#kurs" class="sp-nav-cta">Meld meg på</a>
  </div>
</nav>

<!-- HERO -->
<section class="sp-hero">
  <div class="sp-hero-inner">
    <div class="sp-hero-content">
      <span class="sp-hero-tag">Nytt kurs</span>
      <h1>Din historie<br>fortjener å bli <em>husket</em></h1>
      <p>Du har levd et liv fullt av historier. La oss hjelpe deg å skrive dem ned - og gi dem til dem du er glad i.</p>
      <a href="#kurs" class="sp-btn-primary">Se kurset</a>
      <a href="#historier" class="sp-btn-outline">Les historier</a>
    </div>
    <div class="sp-hero-image">
      <div class="sp-hero-image-placeholder">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
          <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/>
        </svg>
        Bilde: eldre person skriver ved vindu
      </div>
      <div class="sp-hero-badge">
        <strong>76 år</strong>
        da hun debuterte som forfatter
      </div>
    </div>
  </div>
</section>

<!-- QUOTE -->
<section class="sp-quote-section">
  <div class="sp-quote-inner">
    <div>
      <span class="sp-quote-mark">&ldquo;</span>
      <p class="sp-quote-text">Jeg ville at barnebarna mine skulle kjenne meg - ikke bare huske meg.</p>
      <p class="sp-quote-author">&mdash; Reidun Kristine Vålberg, 63 år, Kongsvinger</p>
    </div>
    <div class="sp-quote-body">
      <hr class="sp-divider">
      <p>De fleste av oss bærer på historier vi aldri har fortalt. Barndomsminner. Kjærlighetsbrev. Avgjørelser som forandret alt.</p>
      <p>Disse historiene fortjener å leve videre - ikke bare i hodet ditt, men på papir, i hendene på dem du er glad i.</p>
    </div>
  </div>
</section>

<!-- HVA DU FÅR -->
<section>
  <div class="sp-section-inner">
    <p class="sp-section-label">Hva du får</p>
    <h2 class="sp-section-title">Fra første setning til ferdig bok</h2>
    <p class="sp-section-lead">Vi følger deg gjennom hele prosessen - uansett om du aldri har skrevet før.</p>
    <div class="sp-cards">
      <div class="sp-card">
        <span class="sp-card-icon">&#9998;</span>
        <h3>Skriv din historie</h3>
        <p>Veiledet kurs i memoarskriving, tilpasset deg uten skriverfaring. Konkrete teknikker for å finne din stemme.</p>
      </div>
      <div class="sp-card">
        <span class="sp-card-icon">&#128214;</span>
        <h3>Bli en ekte forfatter</h3>
        <p>Vi hjelper deg fra råmanus til ferdig, gjennomarbeidet tekst. Med tilbakemelding fra erfarne veiledere.</p>
      </div>
      <div class="sp-card">
        <span class="sp-card-icon">&#128230;</span>
        <h3>Hold en bok i hendene</h3>
        <p>Via Indiemoon kan vi trykke boken din - profesjonelt, til familien, til ettertiden.</p>
      </div>
    </div>
  </div>
</section>

<!-- HISTORIER -->
<section class="sp-stories" id="historier">
  <div class="sp-section-inner">
    <p class="sp-section-label">Inspirasjon</p>
    <h2 class="sp-section-title">De tok steget. Nå holder de sin bok.</h2>
    <p class="sp-section-lead">Elever som deg - som bestemte seg for at historien deres fortjente å bli fortalt.</p>
    <div class="sp-story-cards">
      <div class="sp-story-card">
        <div class="sp-story-img">Bilde av forfatter</div>
        <div class="sp-story-body">
          <h4>Diktsamlingsdebutant som 76-åring</h4>
          <p class="sp-story-quote">Det er aldri for sent å begynne.</p>
          <a href="/utgitte-elever" class="sp-story-link">Les hele historien &rarr;</a>
        </div>
      </div>
      <div class="sp-story-card">
        <div class="sp-story-img">Bilde av Reidun</div>
        <div class="sp-story-body">
          <h4>Reidun (63) - fra blanke ark til roman</h4>
          <p class="sp-story-quote">Jeg hadde aldri skrevet noe lengre enn en e-post.</p>
          <a href="/utgitte-elever" class="sp-story-link">Les hele historien &rarr;</a>
        </div>
      </div>
      <div class="sp-story-card">
        <div class="sp-story-img">Bilde av forfatter</div>
        <div class="sp-story-body">
          <h4>Drømte om å skrive besteforeldrenes historie</h4>
          <p class="sp-story-quote">Nå finnes historien - for alltid.</p>
          <a href="/utgitte-elever" class="sp-story-link">Les hele historien &rarr;</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- KURS -->
<section class="sp-kurs" id="kurs">
  <div class="sp-kurs-inner">
    <div>
      <p class="sp-section-label">Kurset</p>
      <h2 class="sp-section-title">Skriv ditt liv</h2>
      <p style="color: var(--text-muted); font-size: 1rem; margin-bottom: 8px;">Kurs i memoarer og livshistorie &middot; 6 uker &middot; Nettbasert</p>
      <ul class="sp-kurs-list">
        <li>Ukentlige skrivemoduler med konkrete oppgaver</li>
        <li>Teknikker for å strukturere en livshistorie</li>
        <li>Finn din stemme som skriver</li>
        <li>Tilbakemelding fra erfaren veileder</li>
        <li>Deltakergruppe med likesinnede</li>
        <li>Ca. 2-3 timer per uke - passer for travle liv</li>
        <li>Livstidsadgang til kursmateriell</li>
      </ul>
    </div>
    <div class="sp-pris-box">
      <p class="sp-pris-label">Kurspris</p>
      <p class="sp-pris-amount">3 490<span style="font-size: 1.5rem">,-</span></p>
      <p class="sp-pris-avdrag">eller 3 &times; 1 200 kr</p>
      <ul class="sp-pris-features">
        <li>6 uker med veiledning</li>
        <li>Ukentlig tilbakemelding</li>
        <li>Tilgang til deltakergruppe</li>
        <li>Kursmateriell for alltid</li>
      </ul>
      <a href="#pamelding" class="sp-btn-primary">Meld meg på nå</a>
      <p class="sp-pris-note">Eller start med <a href="/gratis-tekstvurdering" style="color: var(--primary);">gratis tekstvurdering</a></p>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="sp-faq">
  <div class="sp-section-inner">
    <p class="sp-section-label">Spørsmål og svar</p>
    <h2 class="sp-section-title">Det du lurer på</h2>
    <div class="sp-faq-grid">
      <div class="sp-faq-item">
        <h4>Jeg har aldri skrevet noe før</h4>
        <p>Det er nettopp derfor kurset finnes. Vi starter fra begynnelsen, og ingen forkunnskaper kreves. Det viktigste er at du har en historie du vil fortelle.</p>
      </div>
      <div class="sp-faq-item">
        <h4>Har jeg noe interessant å fortelle?</h4>
        <p>Alle liv inneholder historier verdt å fortelle. Vi hjelper deg å finne dem - de som betyr noe, de som andre trenger å høre.</p>
      </div>
      <div class="sp-faq-item">
        <h4>Hva skjer med teksten etterpå?</h4>
        <p>Du eier alt du skriver. Vil du gi ut boken - til familien eller offentlig - kan Indiemoon hjelpe deg hele veien til trykk.</p>
      </div>
      <div class="sp-faq-item">
        <h4>Er det for sent å begynne?</h4>
        <p>Vår eldste debutant var 76 år. Det er aldri for sent. Faktisk er erfaringen du har samlet gjennom livet din største styrke som skriver.</p>
      </div>
    </div>
  </div>
</section>

<!-- INDIEMOON UPSELL -->
<section class="sp-indiemoon">
  <div class="sp-section-inner">
    <div class="sp-indiemoon-box">
      <div>
        <p class="sp-section-label">Neste steg</p>
        <h3>Fra manus til trykt bok - med Indiemoon</h3>
        <p>Når manuskriptet ditt er ferdig, kan vi hjelpe deg å gi det ut profesjonelt. En bok til familien. Til barnebarna. Til deg selv.</p>
      </div>
      <div>
        <a href="https://indiemoon.no" class="sp-btn-primary" target="_blank" rel="noopener">Les mer om publisering</a>
      </div>
    </div>
  </div>
</section>

<!-- FINAL CTA -->
<section class="sp-final-cta">
  <h2>Er du klar til å skrive din historie?</h2>
  <p>Din historie fortjener å bli husket. Start i dag.</p>
  <a href="#kurs" class="sp-btn-white">Start i dag</a>
</section>

<!-- FOOTER -->
<footer class="sp-footer">
  <p>&copy; {{ date('Y') }} Forfatterskolen AS &middot; Lihagen 21, 3029 Drammen &middot; <a href="mailto:post@forfatterskolen.no">post@forfatterskolen.no</a></p>
</footer>

</body>
</html>

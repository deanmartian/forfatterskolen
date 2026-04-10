@extends('frontend.layout')

@section('page_title', 'Utgitte elever – Forfatterskolen')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&family=Source+Sans+3:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
/* ── PUBLISHING REDESIGN — scoped under .pub-redesign ── */
.pub-redesign {
    --wine: #862736;
    --wine-hover: #9c2e40;
    --wine-dark: #5c1a25;
    --wine-light: rgba(134, 39, 54, 0.08);
    --wine-light-solid: #f4e8ea;
    --pub-cream: #faf8f5;
    --pub-text: #1a1a1a;
    --pub-text-sec: #5a5550;
    --pub-text-muted: #8a8580;
    --pub-border: rgba(0, 0, 0, 0.08);
    --pub-border-strong: rgba(0, 0, 0, 0.12);
    --pub-font-display: 'Playfair Display', Georgia, serif;
    --pub-font-body: 'Source Sans 3', -apple-system, sans-serif;
    --pub-max: 1080px;
    --pub-radius: 10px;
    --pub-radius-lg: 14px;
    font-family: var(--pub-font-body);
    color: var(--pub-text);
    -webkit-font-smoothing: antialiased;
}

/* ── HERO ── */
.pub-hero {
    background: var(--pub-cream);
    padding: 4.5rem 2rem 3.5rem;
    text-align: center;
    border-bottom: 1px solid var(--pub-border);
}
.pub-hero__inner {
    max-width: 720px;
    margin: 0 auto;
}
.pub-hero__eyebrow {
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--wine);
    margin-bottom: 1rem;
}
.pub-hero__heading {
    font-family: var(--pub-font-display);
    font-size: clamp(2rem, 4vw, 2.75rem);
    font-weight: 700;
    line-height: 1.15;
    color: var(--pub-text);
    margin-bottom: 1rem;
}
.pub-hero__heading em { color: var(--wine); font-style: italic; }
.pub-hero__desc {
    font-size: 1.05rem;
    font-weight: 300;
    line-height: 1.7;
    color: var(--pub-text-sec);
    max-width: 560px;
    margin: 0 auto 0;
}

/* ── FEATURED STORIES ── */
.pub-stories {
    padding: 4rem 2rem;
}
.pub-stories__inner {
    max-width: var(--pub-max);
    margin: 0 auto;
}
.pub-section-heading {
    font-family: var(--pub-font-display);
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--pub-text);
    text-align: center;
    margin-bottom: 0.5rem;
}
.pub-section-heading::after {
    content: '';
    display: block;
    width: 40px; height: 3px;
    background: var(--wine);
    border-radius: 2px;
    margin: 0.75rem auto 0;
}
.pub-section-sub {
    font-size: 0.95rem;
    color: var(--pub-text-sec);
    text-align: center;
    margin-top: 0.75rem;
    margin-bottom: 2.5rem;
}

/* Story cards */
.pub-story {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 2.5rem;
    align-items: center;
    padding: 2rem 0;
    border-bottom: 1px solid var(--pub-border);
}
.pub-story:last-child { border-bottom: none; }
.pub-story:nth-child(even) { direction: rtl; }
.pub-story:nth-child(even) > * { direction: ltr; }

.pub-story__img {
    aspect-ratio: 3 / 4;
    border-radius: var(--pub-radius-lg);
    overflow: hidden;
    position: relative;
    background: linear-gradient(145deg, #e8e2da, #d4cec6);
}
.pub-story__img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.pub-story__badge {
    position: absolute;
    bottom: 1rem;
    left: 1rem;
    background: #fff;
    border-radius: 6px;
    padding: 0.4rem 0.75rem;
    font-size: 0.7rem;
    font-weight: 600;
    color: var(--wine-dark);
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.pub-story__content { padding: 0.5rem 0; }
.pub-story__title {
    font-family: var(--pub-font-display);
    font-size: 1.5rem;
    font-weight: 700;
    line-height: 1.25;
    color: var(--pub-text);
    margin-bottom: 0.75rem;
}
.pub-story__excerpt {
    font-size: 0.9rem;
    color: var(--pub-text-sec);
    line-height: 1.7;
    margin-bottom: 1.25rem;
}
.pub-story__link {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--wine);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    transition: gap 0.2s;
    cursor: pointer;
}
.pub-story__link:hover { gap: 0.5rem; color: var(--wine-hover); }

/* ── PUBLISHERS BAR ── */
.pub-publishers {
    padding: 2.5rem 2rem;
    background: var(--pub-cream);
    border-top: 1px solid var(--pub-border);
    border-bottom: 1px solid var(--pub-border);
    text-align: center;
}
.pub-publishers__label {
    font-size: 0.7rem;
    font-weight: 600;
    letter-spacing: 2px;
    text-transform: uppercase;
    color: var(--pub-text-muted);
    margin-bottom: 1.25rem;
}
.pub-publishers__list {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 2rem;
    flex-wrap: wrap;
}
.pub-publisher-name {
    font-family: var(--pub-font-display);
    font-size: 1.1rem;
    font-weight: 400;
    color: var(--pub-text-muted);
    opacity: 0.7;
    transition: opacity 0.2s;
}
.pub-publisher-name:hover { opacity: 1; }

/* ── BOOK GALLERY ── */
.pub-gallery {
    padding: 4rem 2rem;
}
.pub-gallery__inner {
    max-width: var(--pub-max);
    margin: 0 auto;
}

/* Search bar */
.pub-search-bar {
    max-width: 400px;
    margin: 0 auto 2rem;
    position: relative;
}
.pub-search-bar__input {
    width: 100%;
    padding: 0.65rem 1rem 0.65rem 2.5rem;
    border: 1px solid var(--pub-border-strong);
    border-radius: 24px;
    font-family: var(--pub-font-body);
    font-size: 0.875rem;
    color: var(--pub-text);
    background: #fff;
    outline: none;
    transition: border-color 0.2s;
}
.pub-search-bar__input:focus { border-color: var(--wine); }
.pub-search-bar__input::placeholder { color: var(--pub-text-muted); }
.pub-search-bar__icon {
    position: absolute;
    left: 0.85rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--pub-text-muted);
    pointer-events: none;
    font-size: 0.85rem;
}

/* Book grid */
.pub-book-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(155px, 1fr));
    gap: 1.5rem;
}
.pub-book-card {
    text-align: center;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
    display: block;
}
.pub-book-card[style*="display: none"] { display: none !important; }
.pub-book-card__cover {
    aspect-ratio: 2 / 3;
    background: linear-gradient(145deg, #e8e2da, #d8d2ca);
    border-radius: 6px;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.25s, box-shadow 0.25s;
    position: relative;
    overflow: hidden;
}
.pub-book-card:hover .pub-book-card__cover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.1);
}
.pub-book-card__cover img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.pub-book-card__cover-placeholder {
    font-family: var(--pub-font-display);
    font-size: 0.85rem;
    color: rgba(0,0,0,0.12);
    font-weight: 700;
    padding: 1rem;
    text-align: center;
    line-height: 1.3;
}
.pub-book-card__title {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--pub-text);
    margin-bottom: 0.15rem;
    line-height: 1.3;
}
.pub-book-card__author {
    font-size: 0.75rem;
    color: var(--pub-text-muted);
}

/* No results */
.pub-no-results {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--pub-text-muted);
    font-size: 0.95rem;
    display: none;
}

/* ── STORY MODAL ── */
.pub-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    z-index: 9999;
    justify-content: center;
    align-items: center;
    padding: 2rem;
}
.pub-modal-overlay.pub-modal--open {
    display: flex;
}
.pub-modal {
    background: #fff;
    border-radius: var(--pub-radius-lg);
    max-width: 700px;
    width: 100%;
    max-height: 85vh;
    overflow-y: auto;
    position: relative;
    padding: 2.5rem;
}
.pub-modal__close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: var(--pub-text-muted);
    line-height: 1;
    padding: 0.25rem;
    z-index: 1;
}
.pub-modal__close:hover { color: var(--pub-text); }
.pub-modal__img {
    width: 100%;
    max-height: 300px;
    object-fit: cover;
    border-radius: var(--pub-radius);
    margin-bottom: 1.5rem;
}
.pub-modal__title {
    font-family: var(--pub-font-display);
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--pub-text);
    margin-bottom: 1rem;
}
.pub-modal__text {
    font-size: 0.95rem;
    color: var(--pub-text-sec);
    line-height: 1.8;
    margin-bottom: 1.5rem;
}
.pub-modal__text p { margin-bottom: 1rem; }
.pub-modal__quote {
    font-family: var(--pub-font-display);
    font-style: italic;
    font-size: 1.05rem;
    color: var(--wine);
    border-left: 3px solid var(--wine);
    padding-left: 1rem;
    margin-bottom: 1.5rem;
}
.pub-modal__books {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}
.pub-modal__book-thumb {
    width: 90px;
    border-radius: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.pub-modal__book-link {
    display: inline-block;
    margin-top: 1rem;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--wine);
    text-decoration: none;
}
.pub-modal__book-link:hover { text-decoration: underline; }

/* ── CTA BANNER ── */
.pub-cta {
    padding: 0 2rem 4rem;
}
.pub-cta__banner {
    max-width: var(--pub-max);
    margin: 0 auto;
    background: var(--wine);
    border-radius: var(--pub-radius-lg);
    padding: 3rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 2rem;
    position: relative;
    overflow: hidden;
}
.pub-cta__banner::after {
    content: '';
    position: absolute;
    top: -40%; right: -10%;
    width: 400px; height: 400px;
    background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, transparent 70%);
    pointer-events: none;
}
.pub-cta__text { position: relative; z-index: 1; }
.pub-cta__heading {
    font-family: var(--pub-font-display);
    font-size: 1.75rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 0.5rem;
}
.pub-cta__heading em { font-style: italic; color: rgba(255,255,255,0.7); }
.pub-cta__sub {
    font-size: 0.95rem;
    color: rgba(255,255,255,0.6);
    font-weight: 300;
}
.pub-cta__buttons {
    display: flex;
    gap: 0.75rem;
    flex-shrink: 0;
    position: relative;
    z-index: 1;
}
.pub-cta-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    font-family: var(--pub-font-body);
    font-size: 0.9rem;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}
.pub-cta-btn--white {
    background: #fff;
    color: var(--wine);
    border: 2px solid #fff;
}
.pub-cta-btn--white:hover { background: transparent; color: #fff; }
.pub-cta-btn--outline {
    background: transparent;
    color: #fff;
    border: 2px solid rgba(255,255,255,0.3);
}
.pub-cta-btn--outline:hover { border-color: #fff; }

/* ── RESPONSIVE ── */
@media (max-width: 800px) {
    .pub-story {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    .pub-story:nth-child(even) { direction: ltr; }
    .pub-story__img { max-width: 280px; }
    .pub-cta__banner {
        flex-direction: column;
        text-align: center;
        padding: 2.5rem 2rem;
    }
}
@media (max-width: 500px) {
    .pub-book-grid { grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 1rem; }
    .pub-cta__buttons { flex-direction: column; width: 100%; }
    .pub-cta-btn { text-align: center; }
    .pub-hero { padding: 3rem 1.5rem 2.5rem; }
    .pub-stories, .pub-gallery { padding: 3rem 1.5rem; }
}
</style>
@stop

@section('content')
<div class="pub-redesign">

    {{-- ═══════════ HERO ═══════════ --}}
    <section class="pub-hero">
        <div class="pub-hero__inner">
            <p class="pub-hero__eyebrow">Utgitte elever</p>
            <h1 class="pub-hero__heading">Fra Forfatterskolen til <em>bokhylla</em></h1>
            <p class="pub-hero__desc">
                Mange av våre elever har fått gitt ut bøkene sine — på forlag og på egenhånd.
                Vi har fulgt dem helt fra starten. Er du den neste?
            </p>
        </div>
    </section>

    {{-- ═══════════ FEATURED STORIES (first 3 books) ═══════════ --}}
    <section class="pub-stories">
        <div class="pub-stories__inner">
            <h2 class="pub-section-heading">Forfatterhistorier</h2>
            <p class="pub-section-sub">Bli kjent med noen av elevene som har gått veien fra kurs til utgivelse.</p>

            @foreach($books->take(3) as $featured)
            <div class="pub-story">
                <div class="pub-story__img">
                    @if($featured->author_image)
                        <img src="{{ $featured->author_image_jpg }}" alt="{{ $featured->title }}">
                    @endif
                </div>
                <div class="pub-story__content">
                    <h3 class="pub-story__title">{{ $featured->title }}</h3>
                    <p class="pub-story__excerpt">
                        {!! Str::limit(strip_tags($featured->description), 250) !!}
                    </p>
                    <span class="pub-story__link" onclick="pubOpenModal({{ $featured->id }})">Les hele historien &rarr;</span>
                </div>
            </div>
            @endforeach
        </div>
    </section>

    {{-- ═══════════ PUBLISHERS BAR ═══════════ --}}
    <section class="pub-publishers">
        <p class="pub-publishers__label">Elevene våre er utgitt på blant annet</p>
        <div class="pub-publishers__list">
            <span class="pub-publisher-name">Cappelen Damm</span>
            <span class="pub-publisher-name">Gyldendal</span>
            <span class="pub-publisher-name">Aschehoug</span>
            <span class="pub-publisher-name">Vigmostad &amp; Bjørke</span>
            <span class="pub-publisher-name">Bladkompaniet</span>
            <span class="pub-publisher-name">Eget forlag</span>
        </div>
    </section>

    {{-- ═══════════ BOOK GALLERY ═══════════ --}}
    <section class="pub-gallery">
        <div class="pub-gallery__inner">
            <h2 class="pub-section-heading">Alle utgivelser</h2>
            <p class="pub-section-sub">Klikk på en bok for å lese mer.</p>

            <div class="pub-search-bar">
                <span class="pub-search-bar__icon">&#128269;</span>
                <input type="text" class="pub-search-bar__input" id="pubSearchInput" placeholder="Søk etter tittel..." oninput="pubSearchBooks()">
            </div>

            <div class="pub-book-grid" id="pubBookGrid">
                @foreach($books as $book)
                @php
                    $libImg = $book->libraries->first()?->book_image_jpg;
                    $bookImg = $libImg ?: ($book->book_image ?: null);
                    $bookLink = $book->libraries->first()?->book_link;
                @endphp
                <a href="{{ $bookLink ?: 'javascript:void(0)' }}"
                   class="pub-book-card"
                   data-title="{{ strtolower($book->title) }}"
                   data-id="{{ $book->id }}"
                   @if(!$bookLink) onclick="pubOpenModal({{ $book->id }}); return false;" @endif
                   @if($bookLink) target="_blank" @endif>
                    <div class="pub-book-card__cover">
                        @if($bookImg)
                            <img src="{{ $bookImg }}" alt="{{ $book->title }}" loading="lazy">
                        @else
                            <span class="pub-book-card__cover-placeholder">{{ Str::limit($book->title, 30) }}</span>
                        @endif
                    </div>
                    <div class="pub-book-card__title">{{ Str::limit($book->title, 45) }}</div>
                </a>
                @endforeach
            </div>

            <p class="pub-no-results" id="pubNoResults">Ingen treff. Prøv et annet søkeord.</p>
        </div>
    </section>

    {{-- ═══════════ CTA BANNER ═══════════ --}}
    <section class="pub-cta">
        <div class="pub-cta__banner">
            <div class="pub-cta__text">
                <h2 class="pub-cta__heading">Er du <em>den neste?</em></h2>
                <p class="pub-cta__sub">Start skrivedrømmen med Forfatterskolen — fra første utkast til ferdig bok.</p>
            </div>
            <div class="pub-cta__buttons">
                <a href="/course" class="pub-cta-btn pub-cta-btn--white">Se alle kurs</a>
                <a href="/gratis-tekstvurdering" class="pub-cta-btn pub-cta-btn--outline">Gratis tekstvurdering</a>
            </div>
        </div>
    </section>

    {{-- ═══════════ STORY MODAL ═══════════ --}}
    <div class="pub-modal-overlay" id="pubModalOverlay" onclick="if(event.target===this)pubCloseModal()">
        <div class="pub-modal">
            <button class="pub-modal__close" onclick="pubCloseModal()">&times;</button>
            <img class="pub-modal__img" id="pubModalImg" src="" alt="" style="display:none;">
            <h2 class="pub-modal__title" id="pubModalTitle"></h2>
            <div class="pub-modal__text" id="pubModalText"></div>
            <blockquote class="pub-modal__quote" id="pubModalQuote" style="display:none;"></blockquote>
            <div class="pub-modal__books" id="pubModalBooks"></div>
        </div>
    </div>

</div>
@stop

@section('scripts')
<script>
var pubBooksData = {!! json_encode($books->map(function($book) {
    return [
        'id' => $book->id,
        'title' => $book->title,
        'description' => $book->description,
        'quote' => $book->quote_description,
        'author_image' => $book->author_image_jpg,
        'libraries' => $book->libraries->map(function($lib) {
            return [
                'book_image' => $lib->book_image_jpg,
                'book_link' => $lib->book_link,
            ];
        }),
    ];
})->keyBy('id')) !!};

function pubOpenModal(id) {
    var book = pubBooksData[id];
    if (!book) return;

    var overlay = document.getElementById('pubModalOverlay');
    var img = document.getElementById('pubModalImg');
    var title = document.getElementById('pubModalTitle');
    var text = document.getElementById('pubModalText');
    var quote = document.getElementById('pubModalQuote');
    var booksDiv = document.getElementById('pubModalBooks');

    title.textContent = book.title;
    text.innerHTML = book.description || '';

    if (book.author_image) {
        img.src = book.author_image;
        img.alt = book.title;
        img.style.display = 'block';
    } else {
        img.style.display = 'none';
    }

    if (book.quote) {
        quote.textContent = book.quote;
        quote.style.display = 'block';
    } else {
        quote.style.display = 'none';
    }

    booksDiv.innerHTML = '';
    if (book.libraries && book.libraries.length > 0) {
        book.libraries.forEach(function(lib) {
            var wrap = document.createElement('div');
            if (lib.book_image) {
                var thumb = document.createElement('img');
                thumb.src = lib.book_image;
                thumb.className = 'pub-modal__book-thumb';
                thumb.alt = book.title;
                wrap.appendChild(thumb);
            }
            if (lib.book_link) {
                var link = document.createElement('a');
                link.href = lib.book_link;
                link.target = '_blank';
                link.className = 'pub-modal__book-link';
                link.textContent = 'Kjøp boken \u2192';
                wrap.appendChild(link);
            }
            booksDiv.appendChild(wrap);
        });
    }

    overlay.classList.add('pub-modal--open');
    document.body.style.overflow = 'hidden';
}

function pubCloseModal() {
    document.getElementById('pubModalOverlay').classList.remove('pub-modal--open');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') pubCloseModal();
});

function pubSearchBooks() {
    var query = document.getElementById('pubSearchInput').value.toLowerCase().trim();
    var cards = document.querySelectorAll('.pub-book-card');
    var visible = 0;

    cards.forEach(function(card) {
        var title = card.getAttribute('data-title') || '';
        if (query === '' || title.indexOf(query) !== -1) {
            card.style.display = '';
            visible++;
        } else {
            card.style.display = 'none';
        }
    });

    document.getElementById('pubNoResults').style.display = visible === 0 ? 'block' : 'none';
}
</script>
@stop

@extends('frontend.layout')

@section('page_title', 'Utgitte elever – Forfatterskolen')
@section('meta_desc', 'Se Forfatterskolens utgitte forfattere. Over 200 elever har gitt ut bok etter kurs hos oss.')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&family=Source+Sans+3:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/pages/publishing-library.css') }}">
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

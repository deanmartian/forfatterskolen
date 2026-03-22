{{--
  INDIEMOON BASE BOOK LAYOUT
  
  Denne filen er grunnlaget for alle bokmaler.
  Format (trimstørrelse) settes som CSS-variabler via $format.
  Tema (visuell stil) inkluderes via @yield('theme-styles').
  
  Brukes slik i Laravel:
    return view('publishing.layouts.book-base', [
        'format'   => '140x220',
        'theme'    => 'classic',
        'book'     => $publication,
        'chapters' => $chapters,
        // Valgfrie overstyringer — utelat for å bruke standardverdier:
        'overrides' => [
            'bodySize'    => '12pt',     // Brødtekst (standard: avhenger av format)
            'lineHeight'  => 1.5,        // Linjehøyde (standard: 1.45–1.6)
            'h1Size'      => '28pt',     // Kapitteltittel (standard: 22–30pt)
            'chNumSize'   => '12pt',     // Kapittelnummer (standard: 10–14pt)
            'headerSize'  => '8pt',      // Løpende header (standard: 7–8pt)
            'pageNumSize' => '9pt',      // Sidetall (standard: 8.5–10pt)
            'indent'      => '1.5em',    // Avsnittinnrykk (standard: 1.3–1.8em)
            'mt'          => 20,         // Marg topp i mm
            'mb'          => 24,         // Marg bunn i mm
            'mi'          => 22,         // Marg innside (rygg) i mm
            'mo'          => 17,         // Marg utside i mm
        ],
    ]);
--}}
<!DOCTYPE html>
<html lang="{{ $book->language ?? 'nb' }}">
<head>
<meta charset="UTF-8">
<title>{{ $book->title }} — {{ $book->author_name }}</title>
<style>
/* Font imports must come first */
@yield('theme-fonts')

/* ═══════════════════════════════════════════════════════════
   FORMAT-VARIABLER — ScandinavianBook/BookBox
   Standardverdier per format. Kan overstyres via $overrides.
   ═══════════════════════════════════════════════════════════ */

@php
$formats = [
    '140x220' => [
        'w' => 140, 'h' => 220,
        'mt' => 18, 'mb' => 22, 'mi' => 20, 'mo' => 15,
        'sink' => 35, 'bodySize' => '11pt', 'lineHeight' => 1.45,
        'h1Size' => '24pt', 'chNumSize' => '11pt',
        'headerSize' => '7.5pt', 'pageNumSize' => '9pt',
        'label' => '140 × 220 mm',
    ],
    '148x210' => [
        'w' => 148, 'h' => 210,
        'mt' => 18, 'mb' => 22, 'mi' => 20, 'mo' => 15,
        'sink' => 32, 'bodySize' => '10.5pt', 'lineHeight' => 1.45,
        'h1Size' => '22pt', 'chNumSize' => '10pt',
        'headerSize' => '7pt', 'pageNumSize' => '8.5pt',
        'label' => '148 × 210 mm (A5)',
    ],
    '155x230' => [
        'w' => 155, 'h' => 230,
        'mt' => 20, 'mb' => 24, 'mi' => 22, 'mo' => 17,
        'sink' => 38, 'bodySize' => '11pt', 'lineHeight' => 1.5,
        'h1Size' => '24pt', 'chNumSize' => '11pt',
        'headerSize' => '7.5pt', 'pageNumSize' => '9pt',
        'label' => '155 × 230 mm',
    ],
    '170x240' => [
        'w' => 170, 'h' => 240,
        'mt' => 22, 'mb' => 26, 'mi' => 24, 'mo' => 18,
        'sink' => 40, 'bodySize' => '11.5pt', 'lineHeight' => 1.55,
        'h1Size' => '26pt', 'chNumSize' => '12pt',
        'headerSize' => '8pt', 'pageNumSize' => '9pt',
        'label' => '170 × 240 mm',
    ],
    '210x297' => [
        'w' => 210, 'h' => 297,
        'mt' => 25, 'mb' => 30, 'mi' => 25, 'mo' => 20,
        'sink' => 50, 'bodySize' => '12pt', 'lineHeight' => 1.6,
        'h1Size' => '30pt', 'chNumSize' => '14pt',
        'headerSize' => '8pt', 'pageNumSize' => '10pt',
        'label' => '210 × 297 mm (A4)',
    ],
];
$f = $formats[$format] ?? $formats['140x220'];

// Bruker-overstyringer: overskriver standardverdier der de er satt
$ov = $overrides ?? [];
foreach (['bodySize','lineHeight','h1Size','chNumSize','headerSize','pageNumSize','mt','mb','mi','mo'] as $key) {
    if (isset($ov[$key])) {
        $f[$key] = $ov[$key];
    }
}
@endphp

:root {
    /* ── Format-dimensjoner ── */
    --page-width: {{ $f['w'] }}mm;
    --page-height: {{ $f['h'] }}mm;
    --margin-top: {{ $f['mt'] }}mm;
    --margin-bottom: {{ $f['mb'] }}mm;
    --margin-inside: {{ $f['mi'] }}mm;
    --margin-outside: {{ $f['mo'] }}mm;
    
    /* ── Typografisk skala (tilpasset format) ── */
    --body-size: {{ $f['bodySize'] }};
    --line-height: {{ $f['lineHeight'] }};
    --h1-size: {{ $f['h1Size'] }};
    --ch-num-size: {{ $f['chNumSize'] }};
    --header-size: {{ $f['headerSize'] }};
    --pagenum-size: {{ $f['pageNumSize'] }};
    
    /* ── Innrykk og mellomrom (skalerer med format, overstyres via $overrides) ── */
    --indent: {{ $ov['indent'] ?? ($f['w'] >= 170 ? '1.8em' : ($f['w'] >= 155 ? '1.5em' : '1.3em')) }};
    --scene-break-margin: {{ $ov['sceneBreakMargin'] ?? ($f['h'] >= 240 ? '7mm' : '5mm') }};
    --chapter-ornament-margin: {{ $ov['chapterOrnamentMargin'] ?? ($f['h'] >= 230 ? '10mm' : '7mm') }};
    --sink: {{ $ov['sink'] ?? $f['sink'] }}mm;
}

/* ═══════════════════════════════════════════
   SIDEOPPSETT — felles for alle temaer
   ═══════════════════════════════════════════ */

@page {
    size: var(--page-width) var(--page-height);
    margin-top: var(--margin-top);
    margin-bottom: var(--margin-bottom);
}

@page :left {
    margin-left: var(--margin-outside);
    margin-right: var(--margin-inside);
}

@page :right {
    margin-left: var(--margin-inside);
    margin-right: var(--margin-outside);
}

@page chapter-first {
    @top-left { content: none; }
    @top-right { content: none; }
}

@page frontmatter {
    @top-left { content: none; }
    @top-right { content: none; }
    @bottom-center { content: none; }
    @bottom-left-corner { content: none; }
    @bottom-right-corner { content: none; }
}

/* ═══════════════════════════════════════════
   NORSK TYPOGRAFI — felles for alle temaer
   ═══════════════════════════════════════════ */

body {
    font-size: var(--body-size);
    line-height: var(--line-height);
    text-align: justify;
    hyphens: auto;
    -webkit-hyphens: auto;
    hyphenate-limit-chars: 6 3 3;
    hyphenate-limit-zone: 8%;
    hyphenate-character: "-";
    orphans: 3;
    widows: 3;
    font-kerning: normal;
    font-variant-ligatures: common-ligatures;
    margin: 0;
    padding: 0;
}

h1, h2, h3, h4, h5, h6 {
    hyphens: none;
    -webkit-hyphens: none;
    break-after: avoid;
}

a, code, .no-hyphen, .proper-noun {
    hyphens: none;
}

p { margin: 0; }

p + p {
    text-indent: var(--indent);
    margin: 0;
}

/* ═══════════════════════════════════════════
   STRUKTURELLE ELEMENTER — felles for alle
   ═══════════════════════════════════════════ */

.halftitle {
    page: frontmatter;
    break-before: right;
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-height: 100%;
    page-break-after: always;
}

.titlepage {
    page: frontmatter;
    break-before: right;
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-height: 100%;
    page-break-after: always;
}

.colophon {
    page: frontmatter;
    break-before: left;
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    min-height: 100%;
    page-break-after: always;
    padding-bottom: 15mm;
}

.colophon p {
    margin-bottom: 2mm;
    text-indent: 0;
}

.dedication {
    page: frontmatter;
    break-before: right;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100%;
    page-break-after: always;
}

.dedication p {
    text-indent: 0;
}

.toc {
    page: frontmatter;
    break-before: right;
}

.toc ul {
    list-style: none;
    padding: 0;
}

.chapter {
    page: chapter-first;
    break-before: right;
}

.chapter > p:first-of-type {
    text-indent: 0;
}

.scene-break + p {
    text-indent: 0;
}

.section-break + p {
    text-indent: 0;
}

p.no-indent {
    text-indent: 0;
}

.book-title-string {
    string-set: book-title content();
    display: none;
}

/* Forhindre sideskift midt i korte avsnitt */
p { break-inside: avoid; }

blockquote {
    break-inside: avoid;
}

.factbox {
    break-inside: avoid;
}

/* ═══════════════════════════════════════════
   TEMA-SPESIFIKK CSS — lastes fra tema-fil
   ═══════════════════════════════════════════ */

@yield('theme-styles')

</style>
</head>
<body>

<span class="book-title-string">{{ $book->title }}</span>

{{-- HALVTITTEL --}}
<div class="halftitle">
    <h1>{{ $book->title }}</h1>
</div>

{{-- TITTELSIDE --}}
<div class="titlepage">
    @yield('titlepage-content')
</div>

{{-- KOLOFON --}}
<div class="colophon">
    <p>© {{ date('Y') }} {{ $book->author_name }}</p>
    <p>Utgitt av {{ $book->publisher ?? 'Indiemoon' }}</p>
    @if($book->isbn)<p>ISBN {{ $book->isbn }}</p>@endif
    <p>Sats og layout: Indiemoon</p>
    <p>Trykk: ScandinavianBook</p>
    <p>@yield('colophon-fonts')</p>
    @if($book->colophon_extra)<p style="margin-top:4mm;">{{ $book->colophon_extra }}</p>@endif
    <p style="margin-top:4mm;">Alle rettigheter forbeholdt. Ingen del av denne boken
    kan gjengis uten skriftlig tillatelse fra forlaget.</p>
</div>

{{-- DEDIKASJON --}}
@if($book->dedication)
<div class="dedication">
    <p>{{ $book->dedication }}</p>
</div>
@endif

{{-- INNHOLDSFORTEGNELSE --}}
<div class="toc">
    @yield('toc-header')
    <ul>
        @foreach($chapters as $ch)
        <li>
            <span>{{ $ch['title'] }}</span>
            <span class="page-num">{{ $ch['page'] ?? '' }}</span>
        </li>
        @endforeach
    </ul>
</div>

{{-- KAPITLER --}}
@foreach($chapters as $ch)
<div class="chapter">
    @yield('chapter-header', '')
    <span class="chapter-number">@yield('chapter-number-prefix'){{ $ch['number'] }}</span>
    <h1>{{ $ch['title'] }}</h1>
    @yield('chapter-decoration')
    {!! $ch['html'] !!}
</div>
@endforeach

@yield('backmatter')

</body>
</html>

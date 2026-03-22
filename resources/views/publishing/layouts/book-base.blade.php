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

    /* ── Pre-calculated sizes for WeasyPrint 52 compatibility (no calc+var) ── */
    @php
        $bodyNum = (float) $f['bodySize'];
        $h1Num = (float) $f['h1Size'];
        $headerNum = (float) $f['headerSize'];
        $sinkNum = (float) ($ov['sink'] ?? $f['sink']);
    @endphp
    --body-size-95: {{ round($bodyNum * 0.95, 1) }}pt;
    --body-size-91: {{ round($bodyNum * 0.91, 1) }}pt;
    --body-size-90: {{ round($bodyNum * 0.90, 1) }}pt;
    --body-size-85: {{ round($bodyNum * 0.85, 1) }}pt;
    --body-size-97: {{ round($bodyNum * 0.97, 1) }}pt;
    --h1-size-75: {{ round($h1Num * 0.75, 1) }}pt;
    --h1-size-70: {{ round($h1Num * 0.70, 1) }}pt;
    --h1-size-92: {{ round($h1Num * 0.92, 1) }}pt;
    --h1-size-110: {{ round($h1Num * 1.1, 1) }}pt;
    --h1-size-120: {{ round($h1Num * 1.2, 1) }}pt;
    --h1-size-125: {{ round($h1Num * 1.25, 1) }}pt;
    --h1-size-135: {{ round($h1Num * 1.35, 1) }}pt;
    --h1-size-200: {{ round($h1Num * 2.0, 1) }}pt;
    --h1-size-250: {{ round($h1Num * 2.5, 1) }}pt;
    --header-size-85: {{ round($headerNum * 0.85, 1) }}pt;
    --header-size-88: {{ round($headerNum * 0.88, 1) }}pt;
    --sink-50: {{ round($sinkNum * 0.5, 1) }}mm;
    --sink-70: {{ round($sinkNum * 0.7, 1) }}mm;
    --sink-115: {{ round($sinkNum * 1.15, 1) }}mm;
}

/* ═══════════════════════════════════════════
   SIDEOPPSETT — felles for alle temaer
   ═══════════════════════════════════════════ */

@page {
    size: {{ $f['w'] }}mm {{ $f['h'] }}mm;
    margin-top: {{ $f['mt'] }}mm;
    margin-bottom: {{ $f['mb'] }}mm;
}

@page :left {
    margin-left: {{ $f['mo'] }}mm;
    margin-right: {{ $f['mi'] }}mm;
}

@page :right {
    margin-left: {{ $f['mi'] }}mm;
    margin-right: {{ $f['mo'] }}mm;
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
    orphans: 2;
    widows: 2;
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
    height: 100%;
}

.titlepage {
    page: frontmatter;
    break-before: right;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    height: 100%;
    padding-top: 15mm;
    text-align: center;
}
.titlepage .author { margin-top: 8mm; }
.titlepage .subtitle { margin-top: 4mm; font-style: italic; }

/* Feil 5: Kolofon nederst — position absolute fungerer bedre i WeasyPrint */
.colophon {
    page: frontmatter;
    break-before: page;
    position: relative;
    height: 100%;
}
.colophon-content {
    position: absolute;
    bottom: 15mm;
    left: 0;
    right: 0;
    font-size: 8pt;
    color: #888;
    line-height: 1.7;
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
    height: 100%;
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

/* Feil 4: Innholdsfortegnelse med automatisk sidetall via target-counter */
.toc a {
    text-decoration: none;
    color: inherit;
    display: flex;
    justify-content: space-between;
    align-items: baseline;
}

.toc a .toc-page::after {
    content: target-counter(attr(href), page);
}

.toc li {
    padding: 1.5mm 0;
    border-bottom: 0.5pt dotted #ccc;
}

/* Første kapittel på høyreside, resten bare ny side (unngår tomme sider) */
.chapter:first-of-type {
    page: chapter-first;
    break-before: right;
}
.chapter {
    page: chapter-first;
    break-before: page;
}

/* Kapittelmeta (dato, sted) — vises mellom tittel og brødtekst */
.chapter-meta {
    text-align: center;
    font-weight: 600;
    font-size: 10pt;
    margin-bottom: 5mm;
    color: #333;
    text-indent: 0;
}

/* Drop cap gjelder kun første <p>, ikke .chapter-meta */
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

/* Feil 7: string-set fungerer ikke med display:none i WeasyPrint */
.book-title-string {
    string-set: book-title content();
    visibility: hidden;
    height: 0;
    overflow: hidden;
    font-size: 0;
    line-height: 0;
    margin: 0;
    padding: 0;
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
    @hasSection('titlepage-content')
        @yield('titlepage-content')
    @else
        <div class="author">{{ $book->author_name }}</div>
        <h1>{{ $book->title }}</h1>
        @if($book->subtitle)<div class="subtitle">{{ $book->subtitle }}</div>@endif
        <div class="rule"></div>
        <div class="publisher">{{ $book->publisher ?? 'Indiemoon' }}</div>
    @endif
</div>

{{-- KOLOFON --}}
<div class="colophon">
    <div class="colophon-content">
        <p>&copy; {{ date('Y') }} {{ $book->author_name }}</p>
        <p>Utgitt av {{ $book->publisher ?? 'Indiemoon' }}</p>
        @if($book->isbn)<p>ISBN {{ $book->isbn }}</p>@endif
        <p>Sats og layout: Indiemoon</p>
        <p>Trykk: ScandinavianBook</p>
        @if(!empty($fontText))<p>{{ $fontText }}</p>@endif
        @if($book->colophon_extra)<p style="margin-top:4mm;">{{ $book->colophon_extra }}</p>@endif
        <p style="margin-top:4mm;">Alle rettigheter forbeholdt. Ingen del av denne boken
        kan gjengis uten skriftlig tillatelse fra forlaget.</p>
    </div>
</div>

{{-- DEDIKASJON --}}
@if($book->dedication)
<div class="dedication">
    <p>{{ $book->dedication }}</p>
</div>
@endif

{{-- INNHOLDSFORTEGNELSE --}}
@if(count($chapters) > 1)
<div class="toc">
    @yield('toc-header')
    <ul>
        @foreach($chapters as $i => $ch)
        @if(!empty($ch['title']))
        <li>
            <a href="#chapter-{{ $i }}">
                <span class="toc-title">{{ $ch['title'] }}</span>
                <span class="toc-page"></span>
            </a>
        </li>
        @endif
        @endforeach
    </ul>
</div>
@endif

{{-- KAPITLER --}}
@foreach($chapters as $i => $ch)
<div class="chapter" id="chapter-{{ $i }}">
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

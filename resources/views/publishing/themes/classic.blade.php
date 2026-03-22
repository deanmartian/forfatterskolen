@extends('publishing.layouts.book-base')

@section('theme-fonts')
@import url('https://fonts.googleapis.com/css2?family=Crimson+Text:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;0,700;1,400&display=swap');
@endsection

@section('theme-styles')

/* ── Fonter ── */
body {
    font-family: 'Crimson Text', 'Georgia', 'Times New Roman', serif;
    color: #1a1a1a;
}

h1, h2, h3 {
    font-family: 'Cormorant Garamond', 'Georgia', serif;
}

/* ── Sidetall og header ── */
@page {
    @bottom-center {
        content: counter(page);
        font-family: 'Crimson Text', serif;
        font-size: var(--pagenum-size);
        color: #666;
    }
}

@page :left {
    @top-left {
        content: string(book-title);
        font-family: 'Crimson Text', serif;
        font-size: var(--header-size);
        font-style: italic;
        color: #999;
        letter-spacing: 0.03em;
    }
}

@page :right {
    @top-right {
        content: string(chapter-title);
        font-family: 'Crimson Text', serif;
        font-size: var(--header-size);
        font-style: italic;
        color: #999;
        letter-spacing: 0.03em;
    }
}

/* ── Halvtittel ── */
.halftitle {
    text-align: center;
}

.halftitle h1 {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: var(--h1-size-75);
    letter-spacing: 0.15em;
    text-transform: uppercase;
    color: #333;
}

/* ── Tittelside ── */
.titlepage {
    text-align: center;
    align-items: center;
}

.titlepage h1 {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 600;
    font-size: var(--h1-size-120);
    letter-spacing: 0.08em;
    margin-bottom: 6mm;
    color: #1a1a1a;
}

.titlepage .subtitle {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-style: italic;
    font-size: 14pt;
    color: #555;
    margin-bottom: 12mm;
}

.titlepage .ornament {
    font-size: 16pt;
    color: #ccc;
    margin: 8mm 0;
}

.titlepage .author {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 400;
    font-size: 14pt;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #333;
    margin-top: 8mm;
}

.titlepage .publisher {
    font-family: 'Crimson Text', serif;
    font-size: 10pt;
    color: #888;
    margin-top: auto;
    padding-bottom: 15mm;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

/* ── Kolofon ── */
.colophon {
    font-size: 8.5pt;
    line-height: 1.6;
    color: #666;
}

/* ── Dedikasjon ── */
.dedication p {
    font-family: 'Cormorant Garamond', serif;
    font-style: italic;
    font-size: 13pt;
    color: #555;
    max-width: 70%;
    text-align: center;
}

/* ── Innholdsfortegnelse ── */
.toc h2 {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 600;
    font-size: 16pt;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    text-align: center;
    margin-bottom: 10mm;
    margin-top: var(--sink);
}

.toc li {
    font-size: var(--body-size-95);
    padding: 2mm 0;
    border-bottom: 0.3pt dotted #ddd;
    display: flex;
    justify-content: space-between;
}

.toc li .page-num {
    color: #999;
    font-variant-numeric: tabular-nums;
}

/* ── Kapittel ── */
.chapter .chapter-number {
    font-family: 'Cormorant Garamond', serif;
    font-weight: 300;
    font-size: var(--ch-num-size);
    letter-spacing: 0.25em;
    text-transform: uppercase;
    text-align: center;
    color: #999;
    margin-top: var(--sink);
    margin-bottom: 3mm;
    display: block;
}

.chapter h1 {
    string-set: chapter-title content();
    font-weight: 300;
    font-size: var(--h1-size);
    letter-spacing: 0.1em;
    text-align: center;
    margin-bottom: 5mm;
    color: #1a1a1a;
}

.chapter .chapter-ornament {
    text-align: center;
    font-size: 14pt;
    color: #ccc;
    margin-bottom: var(--chapter-ornament-margin);
}

/* Drop cap */
.chapter > p:first-of-type::first-letter {
    float: left;
    font-family: 'Cormorant Garamond', serif;
    font-weight: 600;
    font-size: 3.8em;
    line-height: 0.82;
    padding-right: 2pt;
    padding-top: 2pt;
    color: #1a1a1a;
}

/* ── Sceneskift ── */
.scene-break {
    text-align: center;
    margin: var(--scene-break-margin) 0;
    font-size: 10pt;
    color: #bbb;
    letter-spacing: 0.5em;
}

.scene-break::before {
    content: "· · ·";
}

/* ── Sitater ── */
blockquote {
    font-style: italic;
    margin: 5mm 8mm;
    padding-left: 4mm;
    border-left: 0.5pt solid #ccc;
    color: #444;
    font-size: var(--body-size-91);
}

blockquote cite {
    display: block;
    text-align: right;
    font-style: normal;
    font-size: 8.5pt;
    color: #888;
    margin-top: 2mm;
}

.epigraph {
    margin: 15mm 12mm 10mm;
    text-align: right;
    font-style: italic;
    font-size: var(--body-size-91);
    color: #666;
    text-indent: 0;
}

/* ── Underoverskrifter ── */
h2 {
    font-weight: 600;
    font-size: 14pt;
    margin-top: 8mm;
    margin-bottom: 3mm;
}

h3 {
    font-weight: 400;
    font-style: italic;
    font-size: 12pt;
    margin-top: 5mm;
    margin-bottom: 2mm;
}

@endsection

@section('colophon-fonts')
Satt i Crimson Text / Cormorant Garamond
@endsection

@section('toc-header')
<h2>Innhold</h2>
@endsection

@section('chapter-number-prefix')
Kapittel @endsection

@section('chapter-decoration')
<div class="chapter-ornament">◆</div>
@endsection

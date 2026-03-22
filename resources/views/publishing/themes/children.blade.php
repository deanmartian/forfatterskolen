{{-- BARNEBOK — Leken og varm. Literata + Nunito --}}
@extends('publishing.layouts.book-base')
@section('theme-styles')

@import url('https://fonts.googleapis.com/css2?family=Literata:ital,wght@0,400;0,500;0,700;1,400&family=Nunito:ital,wght@0,400;0,600;0,700;0,800;1,400&display=swap');

body { font-family:'Literata','Georgia',serif; color:#2d2d2d; text-align:left; /* Venstrejustert for barn */ hyphenate-limit-chars:8 4 4; }
h1,h2,h3 { font-family:'Nunito',sans-serif; }

@page { @bottom-center { content:"– " counter(page) " –"; font-family:'Nunito',sans-serif; font-size:var(--pagenum-size); font-weight:600; color:#2E86AB; } }
@page chapter-first { @bottom-center { content:"★ " counter(page) " ★"; } }

.halftitle { text-align:center; }
.halftitle h1 { font-family:'Nunito',sans-serif; font-weight:700; font-size:calc(var(--h1-size)*0.7); color:#2E86AB; }

.titlepage { text-align:center; align-items:center; }
.titlepage .stars { font-size:18pt; color:#E77728; letter-spacing:0.5em; margin-bottom:8mm; }
.titlepage h1 { font-family:'Nunito',sans-serif; font-weight:800; font-size:calc(var(--h1-size)*1.25); line-height:1.15; color:#2E86AB; margin-bottom:5mm; }
.titlepage .subtitle { font-family:'Literata',serif; font-style:italic; font-size:14pt; color:#777; margin-bottom:10mm; }
.titlepage .author { font-family:'Nunito',sans-serif; font-weight:600; font-size:14pt; color:#E77728; }
.titlepage .illustrator { font-family:'Nunito',sans-serif; font-weight:400; font-size:11pt; color:#999; margin-top:2mm; }
.titlepage .publisher { font-family:'Nunito',sans-serif; font-weight:400; font-size:10pt; color:#bbb; margin-top:auto; padding-bottom:15mm; }

.colophon { font-family:'Nunito',sans-serif; font-size:8pt; font-weight:400; line-height:1.7; color:#999; }

.dedication p { font-style:italic; font-size:14pt; color:#2E86AB; text-align:center; max-width:80%; }

.toc h2 { font-family:'Nunito',sans-serif; font-weight:700; font-size:16pt; color:#2E86AB; text-align:center; margin-top:var(--sink); margin-bottom:8mm; }
.toc li { font-size:calc(var(--body-size)*0.95); padding:2.5mm 0; display:flex; justify-content:space-between; color:#333; }
.toc li .page-num { font-family:'Nunito',sans-serif; font-weight:600; color:#2E86AB; font-size:calc(var(--body-size)*0.85); }

.chapter .chapter-number { font-family:'Nunito',sans-serif; font-weight:800; font-size:calc(var(--h1-size)*2.5); color:rgba(231,119,40,0.12); text-align:center; margin-top:calc(var(--sink)*0.5); line-height:1; display:block; }
.chapter h1 { string-set:chapter-title content(); font-weight:700; font-size:calc(var(--h1-size)*0.92); text-align:center; color:#2E86AB; margin-top:-8mm; margin-bottom:3mm; }
.chapter .chapter-deco { text-align:center; font-size:14pt; color:#E77728; margin-bottom:var(--chapter-ornament-margin); }

/* Drop cap — leken og rund */
.chapter > p:first-of-type::first-letter { float:left; font-family:'Nunito',sans-serif; font-weight:800; font-size:3.5em; line-height:0.82; padding-right:3pt; color:#E77728; }

.scene-break { text-align:center; margin:var(--scene-break-margin) 0; font-size:12pt; color:#2E86AB; }
.scene-break::before { content:"✦ ✦ ✦"; letter-spacing:0.3em; }

blockquote { font-style:normal; margin:4mm 6mm; padding:3mm 5mm; background:#F0F7FA; border-radius:3mm; border-left:2pt solid #2E86AB; font-size:calc(var(--body-size)*0.97); color:#333; }

figure { text-align:center; margin:8mm 0; break-inside:avoid; }
figcaption { font-family:'Nunito',sans-serif; font-size:9pt; font-style:italic; color:#999; margin-top:2mm; }

h2 { font-weight:700; font-size:14pt; color:#2E86AB; margin-top:8mm; margin-bottom:3mm; }

@endsection
@section('colophon-fonts')Satt i Literata / Nunito@endsection
@section('toc-header')<h2>Innhold</h2>@endsection
@section('chapter-number-prefix')@endsection
@section('chapter-decoration')<div class="chapter-deco">✿</div>@endsection

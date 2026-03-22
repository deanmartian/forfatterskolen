{{-- KRIM / NOIR — Skandinavisk noir. Libre Baskerville + Oswald --}}
@extends('publishing.layouts.book-base')
@section('theme-styles')

@import url('https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=Oswald:wght@300;400;500;600;700&display=swap');

body { font-family: 'Libre Baskerville','Georgia',serif; color:#1a1a1a; }
h1,h2,h3 { font-family: 'Oswald',sans-serif; }

@page { @bottom-center { content:counter(page); font-family:'Oswald',sans-serif; font-size:var(--pagenum-size); font-weight:300; letter-spacing:0.1em; color:#888; } }
@page :left { @top-left { content:string(book-title); font-family:'Oswald',sans-serif; font-size:calc(var(--header-size)*0.85); font-weight:300; letter-spacing:0.2em; text-transform:uppercase; color:#bbb; } }
@page :right { @top-right { content:string(chapter-title); font-family:'Oswald',sans-serif; font-size:calc(var(--header-size)*0.85); font-weight:300; letter-spacing:0.2em; text-transform:uppercase; color:#bbb; } }

.halftitle { text-align:center; }
.halftitle h1 { font-family:'Oswald',sans-serif; font-weight:600; font-size:calc(var(--h1-size)*0.7); letter-spacing:0.15em; text-transform:uppercase; color:#333; }

.titlepage { text-align:center; align-items:center; }
.titlepage .author-top { font-family:'Oswald',sans-serif; font-weight:300; font-size:10pt; letter-spacing:0.25em; text-transform:uppercase; color:#666; margin-bottom:10mm; }
.titlepage h1 { font-family:'Oswald',sans-serif; font-weight:700; font-size:calc(var(--h1-size)*1.35); letter-spacing:0.02em; text-transform:uppercase; line-height:1.1; color:#000; margin-bottom:4mm; }
.titlepage .subtitle { font-family:'Libre Baskerville',serif; font-style:italic; font-size:11pt; color:#666; margin-bottom:8mm; }
.titlepage .rule { width:40mm; height:2pt; background:#c0392b; margin:5mm 0; }
.titlepage .publisher { font-family:'Oswald',sans-serif; font-weight:300; font-size:8pt; letter-spacing:0.2em; text-transform:uppercase; color:#999; margin-top:auto; padding-bottom:12mm; }
.titlepage .author { display:none; } /* Forfatter vises øverst via .author-top */

.colophon { font-family:'Oswald',sans-serif; font-weight:300; font-size:7pt; line-height:1.8; color:#999; }

.dedication p { font-style:italic; font-size:12pt; color:#555; max-width:75%; text-align:center; }

.toc h2 { font-family:'Oswald',sans-serif; font-weight:600; font-size:12pt; letter-spacing:0.15em; text-transform:uppercase; text-align:center; margin-bottom:8mm; margin-top:var(--sink); }
.toc li { font-size:calc(var(--body-size)*0.95); padding:2mm 0; display:flex; justify-content:space-between; }
.toc li .page-num { font-family:'Oswald',sans-serif; font-weight:300; color:#999; font-size:8.5pt; }

.chapter .chapter-number { font-family:'Oswald',sans-serif; font-weight:300; font-size:calc(var(--h1-size)*2); color:#e8e8e8; text-align:center; margin-top:calc(var(--sink)*0.7); line-height:1; display:block; }
.chapter h1 { string-set:chapter-title content(); font-weight:600; font-size:calc(var(--h1-size)*0.7); letter-spacing:0.08em; text-transform:uppercase; text-align:center; margin-top:2mm; margin-bottom:3mm; padding-bottom:4mm; border-bottom:1.5pt solid #1a1a1a; color:#1a1a1a; }

.chapter .timestamp { font-family:'Oswald',sans-serif; font-weight:300; font-size:8pt; letter-spacing:0.15em; text-transform:uppercase; text-align:center; color:#999; margin-bottom:var(--chapter-ornament-margin); display:block; }

/* Drop cap — fete, kompakte */
.chapter > p:first-of-type::first-letter { float:left; font-family:'Oswald',sans-serif; font-weight:700; font-size:3.2em; line-height:0.85; padding-right:2pt; color:#000; }

.scene-break { text-align:center; margin:var(--scene-break-margin) 0; }
.scene-break::before { content:"■"; font-size:5pt; color:#333; }

blockquote { font-style:italic; margin:5mm 5mm; color:#555; font-size:calc(var(--body-size)*0.91); border-left:1.5pt solid #c0392b; padding-left:4mm; }

h2 { font-weight:500; font-size:12pt; letter-spacing:0.05em; text-transform:uppercase; margin-top:8mm; margin-bottom:3mm; }

@endsection
@section('colophon-fonts')Satt i Libre Baskerville / Oswald@endsection
@section('toc-header')<h2>Innhold</h2>@endsection
@section('chapter-number-prefix')@endsection
@section('chapter-decoration')@endsection

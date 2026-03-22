{{-- MODERNE — Ren skandinavisk minimalisme. Source Serif 4 + Inter --}}
@extends('publishing.layouts.book-base')
@section('theme-styles')

@import url('https://fonts.googleapis.com/css2?family=Source+Serif+4:ital,wght@0,300;0,400;0,600;1,400&family=Inter:wght@300;400;500;600&display=swap');

body { font-family: 'Source Serif 4', 'Georgia', serif; color: #222; }
h1,h2,h3 { font-family: 'Inter', sans-serif; }

@page { @bottom-right-corner { content: counter(page); font-family:'Inter',sans-serif; font-size:var(--pagenum-size); font-weight:300; color:#aaa; } }
@page :left {
    @bottom-left-corner { content:counter(page); font-family:'Inter',sans-serif; font-size:var(--pagenum-size); font-weight:300; color:#aaa; }
    @bottom-right-corner { content:none; }
    @top-left { content:string(book-title); font-family:'Inter',sans-serif; font-size:calc(var(--header-size) * 0.88); font-weight:300; letter-spacing:0.15em; text-transform:uppercase; color:#bbb; }
}
@page :right {
    @top-right { content:string(chapter-title); font-family:'Inter',sans-serif; font-size:calc(var(--header-size) * 0.88); font-weight:300; letter-spacing:0.15em; text-transform:uppercase; color:#bbb; }
}

.halftitle { align-items:flex-start; padding-left:5mm; }
.halftitle h1 { font-family:'Inter',sans-serif; font-weight:300; font-size:14pt; letter-spacing:0.2em; text-transform:uppercase; color:#888; }

.titlepage { align-items:flex-start; padding-left:5mm; }
.titlepage h1 { font-family:'Inter',sans-serif; font-weight:600; font-size:calc(var(--h1-size)*1.1); letter-spacing:-0.01em; line-height:1.15; color:#111; margin-bottom:4mm; }
.titlepage .subtitle { font-family:'Source Serif 4',serif; font-weight:300; font-style:italic; font-size:13pt; color:#666; margin-bottom:15mm; }
.titlepage .rule { width:30mm; height:0.5pt; background:#ddd; margin:8mm 0; }
.titlepage .author { font-family:'Inter',sans-serif; font-weight:300; font-size:11pt; letter-spacing:0.15em; text-transform:uppercase; color:#555; }
.titlepage .publisher { font-family:'Inter',sans-serif; font-weight:300; font-size:8pt; letter-spacing:0.2em; text-transform:uppercase; color:#bbb; margin-top:auto; padding-bottom:15mm; }

.colophon { font-family:'Inter',sans-serif; font-size:7.5pt; font-weight:300; line-height:1.7; color:#888; }

.dedication { justify-content:flex-start; padding-left:5mm; }
.dedication p { font-style:italic; font-weight:300; font-size:12pt; color:#777; }

.toc h2 { font-family:'Inter',sans-serif; font-weight:300; font-size:8pt; letter-spacing:0.25em; text-transform:uppercase; color:#aaa; margin-top:var(--sink); margin-bottom:8mm; }
.toc li { font-size:calc(var(--body-size)*0.95); padding:2.5mm 0; display:flex; justify-content:space-between; align-items:baseline; color:#333; }
.toc li .page-num { font-family:'Inter',sans-serif; font-weight:300; font-size:8pt; color:#bbb; }

.chapter .chapter-number { font-family:'Inter',sans-serif; font-weight:300; font-size:8pt; letter-spacing:0.3em; text-transform:uppercase; color:#bbb; margin-top:calc(var(--sink)*1.15); margin-bottom:5mm; display:block; }
.chapter h1 { string-set:chapter-title content(); font-weight:600; font-size:var(--h1-size); letter-spacing:-0.01em; line-height:1.2; color:#111; margin-bottom:3mm; text-align:left; }
.chapter .chapter-rule { width:20mm; height:0.5pt; background:#ddd; margin:5mm 0 8mm; }

/* Lead line i stedet for drop cap */
.chapter > p:first-of-type::first-line { font-variant:small-caps; letter-spacing:0.04em; font-size:1.05em; }

.scene-break { text-align:left; margin:var(--scene-break-margin) 0; height:0; border-bottom:0.4pt solid #e0e0e0; width:15mm; }

blockquote { font-style:italic; margin:5mm 0 5mm 5mm; color:#555; font-size:calc(var(--body-size)*0.95); }
blockquote cite { display:block; font-style:normal; font-family:'Inter',sans-serif; font-size:7.5pt; font-weight:300; color:#aaa; margin-top:2mm; letter-spacing:0.05em; text-transform:uppercase; }

h2 { font-weight:500; font-size:12pt; letter-spacing:0.02em; margin-top:8mm; margin-bottom:3mm; }

@endsection
@section('colophon-fonts')Satt i Source Serif 4 / Inter@endsection
@section('toc-header')<h2>Innhold</h2>@endsection
@section('chapter-number-prefix')Kapittel @endsection
@section('chapter-decoration')<div class="chapter-rule"></div>@endsection

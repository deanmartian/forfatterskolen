{{-- SAKPROSA — Autoritet og klarhet. Merriweather + Source Sans 3 --}}
@extends('publishing.layouts.book-base')

@section('theme-fonts')
@import url('https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;1,400&family=Source+Sans+3:ital,wght@0,300;0,400;0,600;0,700;1,400&display=swap');
@endsection

@section('theme-styles')

body { font-family:'Merriweather','Georgia',serif; color:#222; }
h1,h2,h3 { font-family:'Source Sans 3',sans-serif; }

@page { @bottom-center { content:counter(page); font-family:'Source Sans 3',sans-serif; font-size:var(--pagenum-size); font-weight:400; color:#999; } }
@page :left { @top-left { content:string(book-title); font-family:'Source Sans 3',sans-serif; font-size:var(--header-size); font-weight:400; letter-spacing:0.08em; color:#bbb; } }
@page :right { @top-right { content:string(chapter-title); font-family:'Source Sans 3',sans-serif; font-size:var(--header-size); font-weight:400; letter-spacing:0.08em; color:#bbb; } }

.halftitle { text-align:center; }
.halftitle h1 { font-family:'Source Sans 3',sans-serif; font-weight:600; font-size:calc(var(--h1-size)*0.7); letter-spacing:0.1em; text-transform:uppercase; color:#2C3E50; }

.titlepage { text-align:center; align-items:center; }
.titlepage .author-top { font-family:'Source Sans 3',sans-serif; font-weight:400; font-size:12pt; letter-spacing:0.1em; text-transform:uppercase; color:#666; margin-bottom:12mm; }
.titlepage h1 { font-family:'Source Sans 3',sans-serif; font-weight:700; font-size:calc(var(--h1-size)*1.1); line-height:1.2; color:#111; margin-bottom:4mm; }
.titlepage .subtitle { font-family:'Merriweather',serif; font-weight:300; font-size:12pt; color:#666; line-height:1.5; max-width:85%; margin:0 auto 10mm; }
.titlepage .rule { width:35mm; height:1pt; background:#2C3E50; margin:5mm auto; }
.titlepage .author { font-family:'Source Sans 3',sans-serif; font-weight:400; font-size:12pt; letter-spacing:0.1em; text-transform:uppercase; color:#555; }
.titlepage .publisher { font-family:'Source Sans 3',sans-serif; font-weight:300; font-size:9pt; letter-spacing:0.15em; text-transform:uppercase; color:#aaa; margin-top:auto; padding-bottom:15mm; }

.colophon { font-family:'Source Sans 3',sans-serif; font-size:7.5pt; font-weight:300; line-height:1.7; color:#888; }

.dedication p { font-style:italic; font-weight:300; font-size:12pt; color:#666; max-width:75%; text-align:center; }

.toc h2 { font-family:'Source Sans 3',sans-serif; font-weight:700; font-size:14pt; color:#2C3E50; text-align:center; margin-top:calc(var(--sink)*0.7); margin-bottom:8mm; }
.toc .part-title { font-family:'Source Sans 3',sans-serif; font-weight:600; font-size:9pt; letter-spacing:0.1em; text-transform:uppercase; color:#2C3E50; margin-top:5mm; margin-bottom:2mm; padding-top:3mm; border-top:0.5pt solid #ddd; }
.toc li { font-size:calc(var(--body-size)*0.95); padding:1.5mm 0; display:flex; justify-content:space-between; }
.toc li .page-num { font-family:'Source Sans 3',sans-serif; font-size:8.5pt; color:#aaa; }

.chapter .chapter-number { font-family:'Source Sans 3',sans-serif; font-weight:300; font-size:var(--ch-num-size); letter-spacing:0.15em; text-transform:uppercase; color:#aaa; text-align:center; margin-top:var(--sink); display:block; }
.chapter h1 { string-set:chapter-title content(); font-weight:700; font-size:var(--h1-size); line-height:1.2; text-align:center; color:#2C3E50; margin-top:3mm; margin-bottom:3mm; }

.chapter .chapter-intro { font-family:'Merriweather',serif; font-style:italic; font-size:calc(var(--body-size)*0.95); color:#777; text-align:center; max-width:85%; margin:0 auto var(--chapter-ornament-margin); text-indent:0; line-height:1.5; }
.chapter .chapter-rule { width:25mm; height:1pt; background:#2C3E50; margin:5mm auto var(--chapter-ornament-margin); }

/* Lead lines i stedet for drop cap */
.chapter > p:first-of-type::first-line { font-variant:small-caps; letter-spacing:0.03em; }

/* Faktabokser */
.factbox { margin:6mm 0; padding:4mm 5mm; background:#F5F7F9; border-left:2pt solid #2C3E50; break-inside:avoid; border-radius:0; }
.factbox h4 { font-family:'Source Sans 3',sans-serif; font-weight:600; font-size:9.5pt; letter-spacing:0.05em; text-transform:uppercase; color:#2C3E50; margin-bottom:2mm; }
.factbox p { font-size:calc(var(--body-size)*0.9); line-height:1.5; text-indent:0; color:#444; }

.section-break { text-align:center; margin:var(--scene-break-margin) 0; height:0; border-bottom:0.5pt solid #ddd; width:100%; }

/* Bruker .section-break, ikke .scene-break for sakprosa */
.scene-break { text-align:center; margin:var(--scene-break-margin) 0; height:0; border-bottom:0.5pt solid #ddd; width:100%; }
.scene-break::before { content:none; }

blockquote { font-style:italic; margin:5mm 8mm; padding-left:4mm; border-left:0.5pt solid #2C3E50; color:#555; font-size:calc(var(--body-size)*0.95); }
blockquote cite { display:block; text-align:right; font-style:normal; font-family:'Source Sans 3',sans-serif; font-size:8pt; color:#999; margin-top:2mm; }

h2 { font-weight:600; font-size:13pt; color:#2C3E50; margin-top:8mm; margin-bottom:3mm; }
h3 { font-weight:600; font-size:11pt; font-style:italic; color:#444; margin-top:5mm; margin-bottom:2mm; }

@endsection
@section('colophon-fonts')Satt i Merriweather / Source Sans 3@endsection
@section('toc-header')<h2>Innhold</h2>@endsection
@section('chapter-number-prefix')Kapittel @endsection
@section('chapter-decoration')<div class="chapter-rule"></div>@endsection

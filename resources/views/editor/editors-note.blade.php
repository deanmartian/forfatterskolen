@extends('editor.layout')

@section('title')
<title>Redaktørinnstruks &rsaquo; Forfatterskolen Redaktørportal</title>
@stop

@section('page-title', 'Redaktørinnstruks')

@section('styles')
<style>
    .en-wrapper { max-width: 900px; margin: 0 auto; padding: 0 16px; }
    .en-wrapper * { word-wrap: break-word; overflow-wrap: break-word; }

    .en-header {
        background: linear-gradient(135deg, #862736 0%, #5e1a26 100%);
        border-radius: 12px;
        padding: 28px 32px;
        color: #fff;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
    }
    .en-header::after {
        content: '';
        position: absolute;
        top: -30px; right: -30px;
        width: 140px; height: 140px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }
    .en-header h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 6px;
        position: relative;
    }
    .en-header p {
        font-size: 0.95rem;
        opacity: 0.85;
        margin: 0;
        position: relative;
    }

    .en-card {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.08);
        border-radius: 12px;
        overflow: hidden;
        max-width: 100%;
    }
    .en-card__body {
        padding: 32px 36px;
        font-size: 16px !important;
        line-height: 1.8;
        color: #1a1a1a;
    }
    .en-card__body p,
    .en-card__body li,
    .en-card__body td,
    .en-card__body span { font-size: 16px !important; }

    /* Typography for the note content */
    .en-card__body h1,
    .en-card__body h2,
    .en-card__body h3,
    .en-card__body h4 {
        color: #862736;
        margin-top: 1.5em;
        margin-bottom: 0.5em;
        line-height: 1.3;
    }
    .en-card__body h1 { font-size: 26px !important; }
    .en-card__body h2 { font-size: 22px !important; }
    .en-card__body h3 { font-size: 19px !important; }
    .en-card__body h4 { font-size: 17px !important; }
    .en-card__body h1:first-child,
    .en-card__body h2:first-child,
    .en-card__body h3:first-child { margin-top: 0; }

    .en-card__body p { margin-bottom: 1em; }

    .en-card__body ul, .en-card__body ol {
        margin: 0.75em 0 1em 1.5em;
        padding: 0;
    }
    .en-card__body li { margin-bottom: 0.4em; }

    .en-card__body strong { color: #1a1a1a; }

    .en-card__body a { color: #862736; text-decoration: underline; }

    .en-card__body blockquote {
        border-left: 4px solid #862736;
        padding: 12px 20px;
        margin: 1em 0;
        background: #faf8f5;
        border-radius: 0 8px 8px 0;
        font-style: italic;
        color: #5a5550;
    }

    .en-card__body table {
        width: 100%;
        border-collapse: collapse;
        margin: 1em 0;
    }
    .en-card__body table th,
    .en-card__body table td {
        border: 1px solid #e8e4de;
        padding: 10px 14px;
        text-align: left;
        font-size: 0.95rem;
    }
    .en-card__body table th {
        background: #faf8f5;
        font-weight: 600;
        color: #5a5550;
    }
    .en-card__body table tr:nth-child(even) { background: #fafafa; }

    .en-card__body img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 1em 0;
    }

    .en-card__body hr {
        border: none;
        border-top: 2px solid #e8e4de;
        margin: 2em 0;
    }

    .en-empty {
        text-align: center;
        padding: 48px;
        color: #8a8580;
    }
    .en-empty i { font-size: 2.5rem; display: block; opacity: 0.3; margin-bottom: 12px; }

    .en-footer {
        text-align: center;
        padding: 20px;
        font-size: 0.8rem;
        color: #8a8580;
    }

    @media print {
        .en-header { background: #862736 !important; -webkit-print-color-adjust: exact; }
        .ed-sidebar, .ed-header, .ed-footer { display: none !important; }
        .ed-main { margin: 0 !important; padding: 0 !important; }
    }
</style>
@stop

@section('content')
<div class="en-wrapper">

    <div class="en-header">
        <h2><i class="fa fa-file-text-o"></i> Redaktørinnstruks</h2>
        <p>Retningslinjer og instrukser for redaktørarbeidet.</p>
    </div>

    <div class="en-card">
        <div class="en-card__body">
            @if($note)
                {!! $note !!}
            @else
                <div class="en-empty">
                    <i class="fa fa-file-text-o"></i>
                    <p>Ingen redaktørinnstruks er lagt inn ennå.</p>
                </div>
            @endif
        </div>
    </div>

    <div class="en-footer">
        <button onclick="window.print()" style="background:none;border:1px solid #ddd;border-radius:6px;padding:8px 16px;cursor:pointer;font-size:0.85rem;color:#5a5550;">
            <i class="fa fa-print"></i> Skriv ut
        </button>
    </div>

</div>
@stop

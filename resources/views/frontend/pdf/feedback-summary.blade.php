<!DOCTYPE html>
<html lang="no">
<head>
<meta charset="UTF-8">
<style>
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 12px;
        color: #1a1a1a;
        line-height: 1.6;
        margin: 0;
        padding: 0;
    }

    .header {
        background: #862736;
        color: #fff;
        padding: 24px 32px;
        margin-bottom: 24px;
    }
    .header h1 {
        font-size: 20px;
        margin: 0 0 4px;
    }
    .header p {
        font-size: 11px;
        opacity: 0.85;
        margin: 0;
    }

    .logo {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        opacity: 0.7;
        margin-bottom: 8px;
    }

    .section {
        margin-bottom: 20px;
        padding: 0 32px;
    }

    .section-title {
        font-size: 13px;
        font-weight: 700;
        color: #862736;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 2px solid #862736;
        padding-bottom: 6px;
        margin-bottom: 12px;
    }

    .info-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 16px;
    }
    .info-table td {
        padding: 6px 12px;
        border-bottom: 1px solid #e8e4de;
        font-size: 11px;
    }
    .info-table td:first-child {
        font-weight: 700;
        color: #5a5550;
        width: 35%;
    }

    .grade-box {
        background: #faf8f5;
        border: 1px solid #e8e4de;
        border-radius: 4px;
        padding: 16px;
        text-align: center;
        margin: 12px 0;
    }
    .grade-box .grade-value {
        font-size: 28px;
        font-weight: 700;
        color: #862736;
    }
    .grade-box .grade-label {
        font-size: 10px;
        color: #8a8580;
        text-transform: uppercase;
    }

    .feedback-content {
        background: #faf8f5;
        border-left: 3px solid #862736;
        padding: 16px 20px;
        margin: 12px 0;
        font-size: 11px;
        line-height: 1.7;
    }

    .files-list {
        margin: 8px 0;
    }
    .files-list .file-item {
        padding: 4px 0;
        font-size: 11px;
        color: #5a5550;
    }

    .footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        padding: 12px 32px;
        border-top: 1px solid #e8e4de;
        font-size: 9px;
        color: #8a8580;
        text-align: center;
    }

    .page-break { page-break-before: always; }
</style>
</head>
<body>

<div class="header">
    <div class="logo">Forfatterskolen</div>
    <h1>Tilbakemelding fra redaktør</h1>
    <p>{{ $assignment->title ?? 'Oppgave' }} — {{ $course->title ?? '' }}</p>
</div>

<div class="section">
    <div class="section-title">Oppgaveinformasjon</div>
    <table class="info-table">
        <tr>
            <td>Elev</td>
            <td>{{ $student->full_name }}</td>
        </tr>
        <tr>
            <td>Elevnummer</td>
            <td>#{{ $student->id }}</td>
        </tr>
        <tr>
            <td>Oppgave</td>
            <td>{{ $assignment->title ?? '—' }}</td>
        </tr>
        <tr>
            <td>Kurs</td>
            <td>{{ $course->title ?? '—' }}</td>
        </tr>
        @if($manuscript->words)
        <tr>
            <td>Antall ord</td>
            <td>{{ number_format($manuscript->words) }}</td>
        </tr>
        @endif
        @if($manuscript->uploaded_at)
        <tr>
            <td>Innlevert</td>
            <td>{{ \Carbon\Carbon::parse($manuscript->uploaded_at)->format('d.m.Y') }}</td>
        </tr>
        @endif
        <tr>
            <td>Redaktør</td>
            <td>{{ $editor->full_name ?? 'Forfatterskolens redaktør' }}</td>
        </tr>
        <tr>
            <td>Tilbakemelding gitt</td>
            <td>{{ $feedback->created_at ? $feedback->created_at->format('d.m.Y') : '—' }}</td>
        </tr>
    </table>
</div>

@if($manuscript->grade)
<div class="section">
    <div class="grade-box">
        <div class="grade-label">Karakter</div>
        <div class="grade-value">{{ $manuscript->grade }}</div>
    </div>
</div>
@endif

@if(!empty($feedbackContent))
<div class="section">
    <div class="section-title">Redaktørens tilbakemelding</div>
    <div style="font-size:11px;line-height:1.8;color:#1a1a1a;">
        {!! $feedbackContent !!}
    </div>
</div>
@endif

@if($feedback->filename)
<div class="section">
    <div class="section-title">Vedlagte filer</div>
    <div class="files-list">
        @foreach(explode(',', $feedback->filename) as $file)
            <div class="file-item">📄 {{ basename(trim($file)) }}</div>
        @endforeach
    </div>
    @if(empty($feedbackContent))
    <p style="font-size:10px;color:#8a8580;margin-top:8px;">
        Tilbakemeldingen finnes i filene ovenfor. Last dem ned fra elevportalen på forfatterskolen.no
    </p>
    @endif
</div>
@endif

@if($manuscript->letter_to_editor)
<div class="section">
    <div class="section-title">Ditt brev til redaktør</div>
    <div class="feedback-content">
        {!! nl2br(e($manuscript->letter_to_editor)) !!}
    </div>
</div>
@endif

<div class="footer">
    Forfatterskolen AS — forfatterskolen.no — post@forfatterskolen.no — Generert {{ now()->format('d.m.Y H:i') }}
</div>

</body>
</html>

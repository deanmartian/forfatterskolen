@extends('frontend.layouts.course-portal')

@section('title')
    <title>Forbered coaching-time &rsaquo; Forfatterskolen</title>
@endsection

@section('styles')
<style>
    .prep-wrapper { max-width: 720px; margin: 0 auto; }

    .prep-header { margin-bottom: 2rem; }
    .prep-header__title { font-size: 1.5rem; font-weight: 700; color: #1a1a1a; margin-bottom: 0.25rem; }
    .prep-header__desc { font-size: 0.875rem; color: #5a5550; margin: 0; line-height: 1.6; }

    .prep-card {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.08);
        border-radius: 14px;
        padding: 1.75rem;
        margin-bottom: 1.25rem;
    }

    .prep-card__title {
        font-size: 1rem;
        font-weight: 700;
        color: #1a1a1a;
        margin-bottom: 1.25rem;
    }

    .prep-detail-row {
        display: flex;
        justify-content: space-between;
        padding: 0.65rem 0;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        font-size: 0.875rem;
    }
    .prep-detail-row:last-child { border-bottom: none; }
    .prep-detail-row__label { color: #8a8580; font-weight: 500; }
    .prep-detail-row__value { color: #1a1a1a; font-weight: 600; text-align: right; }

    .prep-tip {
        background: #faf8f5;
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: 10px;
        padding: 1.25rem;
        margin-bottom: 1.25rem;
    }
    .prep-tip__title {
        font-size: 0.8rem;
        font-weight: 700;
        color: #862736;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .prep-tip__text {
        font-size: 0.825rem;
        color: #5a5550;
        line-height: 1.65;
        margin: 0;
    }

    .prep-uploaded {
        background: #f0f7f0;
        border: 1px solid #c3e6cb;
        border-radius: 10px;
        padding: 1rem 1.25rem;
        margin-top: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .prep-uploaded__icon { color: #2e7d32; font-size: 1.2rem; flex-shrink: 0; }
    .prep-uploaded__text { font-size: 0.85rem; color: #155724; }
    .prep-uploaded__file { font-weight: 600; }

    .prep-notes-display {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 1rem 1.25rem;
        margin-top: 0.75rem;
        font-size: 0.85rem;
        color: #333;
        line-height: 1.6;
    }

    .prep-form-label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        font-size: 0.85rem;
        color: #1a1a1a;
    }
    .prep-form-hint {
        font-size: 0.75rem;
        color: #8a8580;
        margin-top: 0.25rem;
    }

    .prep-file-input {
        width: 100%;
        padding: 0.75rem;
        border: 2px dashed rgba(0,0,0,0.12);
        border-radius: 8px;
        background: #faf8f5;
        cursor: pointer;
        transition: border-color 0.15s;
    }
    .prep-file-input:hover { border-color: #862736; }

    .prep-textarea {
        width: 100%;
        padding: 0.75rem;
        border: 1px solid rgba(0,0,0,0.12);
        border-radius: 8px;
        resize: vertical;
        font-family: inherit;
        font-size: 0.875rem;
        min-height: 120px;
        transition: border-color 0.15s;
    }
    .prep-textarea:focus { outline: none; border-color: #862736; }

    .btn-prep {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.7rem 1.5rem;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        border: none;
        transition: all 0.15s;
    }
    .btn-prep--primary { background: #862736; color: #fff; }
    .btn-prep--primary:hover { background: #9c2e40; color: #fff; }
    .btn-prep--back {
        background: transparent;
        color: #5a5550;
        border: 1px solid rgba(0,0,0,0.12);
        padding: 0.7rem 1.25rem;
    }
    .btn-prep--back:hover { border-color: #862736; color: #862736; text-decoration: none; }

    .prep-actions {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-top: 1.5rem;
    }

    @media (max-width: 600px) {
        .prep-detail-row { flex-direction: column; gap: 0.15rem; }
        .prep-detail-row__value { text-align: left; }
        .prep-actions { flex-direction: column; }
        .prep-actions .btn-prep { width: 100%; justify-content: center; }
    }
</style>
@stop

@section('content')
<div class="learner-container">
    <div class="container prep-wrapper">

        <div class="prep-header">
            <h1 class="prep-header__title">Forbered deg til coachingtimen</h1>
            <p class="prep-header__desc">
                Last opp manuset eller teksten du vil jobbe med, slik at redaktøren kan forberede seg.
                Jo bedre forberedt dere begge er, jo mer får du ut av timen.
            </p>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="border-radius: 10px; margin-bottom: 1.25rem;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger" style="border-radius: 10px; margin-bottom: 1.25rem;">
                {{ session('error') }}
            </div>
        @endif

        {{-- ═══════ SESSION DETAILS ═══════ --}}
        <div class="prep-card">
            <div class="prep-card__title">Detaljer om coachingtimen</div>

            @if($timer->editor)
            <div class="prep-detail-row">
                <span class="prep-detail-row__label">Redaktør</span>
                <span class="prep-detail-row__value">{{ $timer->editor->first_name }} {{ $timer->editor->last_name }}</span>
            </div>
            @endif

            @if($timer->timeSlot && $timer->timeSlot->date)
            <div class="prep-detail-row">
                <span class="prep-detail-row__label">Dato</span>
                <span class="prep-detail-row__value">
                    @php
                        $sessionDate = \Carbon\Carbon::parse($timer->timeSlot->date.' '.$timer->timeSlot->start_time, 'UTC')
                            ->setTimezone(config('app.timezone'));
                        $norwegianDays = ['søndag','mandag','tirsdag','onsdag','torsdag','fredag','lørdag'];
                    @endphp
                    {{ ucfirst($norwegianDays[$sessionDate->dayOfWeek]) }} {{ $sessionDate->format('d.m.Y') }} kl. {{ $sessionDate->format('H:i') }}
                </span>
            </div>
            @endif

            @if($timer->call_type)
            <div class="prep-detail-row">
                <span class="prep-detail-row__label">Type</span>
                <span class="prep-detail-row__value">{{ $timer->call_type_label ?: ucfirst($timer->call_type) }}</span>
            </div>
            @endif

            <div class="prep-detail-row">
                <span class="prep-detail-row__label">Varighet</span>
                <span class="prep-detail-row__value">{{ $timer->plan_type == 1 ? '60 minutter' : '30 minutter' }}</span>
            </div>

            @if($timer->preparation_file)
                <div class="prep-uploaded">
                    <span class="prep-uploaded__icon">✓</span>
                    <div class="prep-uploaded__text">
                        Opplastet fil: <span class="prep-uploaded__file">{{ basename($timer->preparation_file) }}</span>
                    </div>
                </div>
            @endif

            @if($timer->preparation_notes)
                <div class="prep-notes-display">
                    <strong>Dine notater:</strong><br>
                    {!! nl2br(e($timer->preparation_notes)) !!}
                </div>
            @endif
        </div>

        {{-- ═══════ TIP BOX ═══════ --}}
        <div class="prep-tip">
            <div class="prep-tip__title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#862736" stroke-width="2" stroke-linecap="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                Tips for en god coaching-time
            </div>
            <p class="prep-tip__text">
                Last opp den delen av manuset du ønsker å jobbe med. Skriv gjerne noen notater om hva du
                ønsker å fokusere på — for eksempel dialog, spenningsoppbygging, eller strukturen i teksten.
                Da kan redaktøren forberede konkrete tilbakemeldinger til deg.
            </p>
        </div>

        {{-- ═══════ UPLOAD FORM ═══════ --}}
        <div class="prep-card">
            <div class="prep-card__title">
                {{ $timer->preparation_file ? 'Oppdater fil og notater' : 'Last opp fil og notater' }}
            </div>

            <form action="{{ route('learner.coaching-timer.preparation', $timer->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div style="margin-bottom: 1.25rem;">
                    <label class="prep-form-label">Manus eller tekstutdrag</label>
                    <input type="file" name="preparation_file" accept=".pdf,.doc,.docx,.odt" class="prep-file-input">
                    <div class="prep-form-hint">PDF, DOC, DOCX eller ODT — maks 10 MB</div>
                    @error('preparation_file')
                        <div style="color: #dc3545; margin-top: 0.35rem; font-size: 0.8rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div style="margin-bottom: 0.5rem;">
                    <label class="prep-form-label">Notater til redaktøren (valgfritt)</label>
                    <textarea name="preparation_notes" class="prep-textarea"
                              placeholder="Hva ønsker du å fokusere på i timen? Er det noe spesielt du vil ha tilbakemelding på?">{{ old('preparation_notes', $timer->preparation_notes) }}</textarea>
                    @error('preparation_notes')
                        <div style="color: #dc3545; margin-top: 0.35rem; font-size: 0.8rem;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="prep-actions">
                    <button type="submit" class="btn-prep btn-prep--primary">
                        {{ $timer->preparation_file ? 'Oppdater' : 'Last opp' }}
                    </button>
                    <a href="{{ route('learner.coaching-time') }}" class="btn-prep btn-prep--back">
                        ← Tilbake til coaching
                    </a>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

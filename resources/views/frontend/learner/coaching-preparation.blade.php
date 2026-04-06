@extends('frontend.layout')

@section('content')
<div class="container" style="max-width: 700px; margin: 60px auto; padding: 0 20px;">

    <div style="text-align: center; margin-bottom: 40px;">
        <h2 style="color: #862736; font-weight: 700;">Last opp tekst til redaktoren</h2>
        <p style="color: #555;">Forbered deg til coachingtimen ved a laste opp tekst eller notater.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif

    <div style="background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 30px; margin-bottom: 30px;">
        <h4 style="margin-bottom: 20px; color: #333;">Detaljer om coachingtimen</h4>
        <table style="width: 100%; border-collapse: collapse;">
            @if($timer->call_type)
            <tr>
                <td style="padding: 8px 0; color: #666; width: 40%;"><strong>Type:</strong></td>
                <td style="padding: 8px 0;">{{ $timer->call_type_label ?: ucfirst($timer->call_type) }}</td>
            </tr>
            @endif
            @if($timer->timeSlot && $timer->timeSlot->date)
            <tr>
                <td style="padding: 8px 0; color: #666;"><strong>Dato:</strong></td>
                <td style="padding: 8px 0;">{{ \Carbon\Carbon::parse($timer->timeSlot->date)->format('d.m.Y') }}
                    @if($timer->timeSlot->start_time)
                        kl. {{ $timer->timeSlot->start_time }}
                    @endif
                </td>
            </tr>
            @endif
            @if($timer->editor)
            <tr>
                <td style="padding: 8px 0; color: #666;"><strong>Redaktor:</strong></td>
                <td style="padding: 8px 0;">{{ $timer->editor->first_name }} {{ $timer->editor->last_name }}</td>
            </tr>
            @endif
        </table>

        @if($timer->preparation_file)
            <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 6px;">
                <strong>Opplastet fil:</strong> {{ basename($timer->preparation_file) }}
            </div>
        @endif

        @if($timer->preparation_notes)
            <div style="margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 6px;">
                <strong>Notater:</strong><br>
                {!! nl2br(e($timer->preparation_notes)) !!}
            </div>
        @endif
    </div>

    <div style="background: #fff; border: 1px solid #e0e0e0; border-radius: 8px; padding: 30px;">
        <h4 style="margin-bottom: 20px; color: #333;">Last opp fil og notater</h4>

        <form action="{{ route('learner.coaching-timer.preparation', $timer->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Fil (PDF, DOC, DOCX, ODT - maks 10MB)</label>
                <input type="file" name="preparation_file" accept=".pdf,.doc,.docx,.odt"
                       style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                @error('preparation_file')
                    <div style="color: #dc3545; margin-top: 5px; font-size: 14px;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #333;">Notater til redaktoren (valgfritt)</label>
                <textarea name="preparation_notes" rows="5"
                          style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; resize: vertical;"
                          placeholder="Skriv eventuelle notater eller sporsmal du vil at redaktoren skal forberede seg pa...">{{ old('preparation_notes', $timer->preparation_notes) }}</textarea>
                @error('preparation_notes')
                    <div style="color: #dc3545; margin-top: 5px; font-size: 14px;">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit"
                    style="background-color: #862736; color: #fff; border: none; padding: 14px 32px; border-radius: 6px; font-weight: 600; font-size: 16px; cursor: pointer;">
                Last opp
            </button>
        </form>
    </div>
</div>
@endsection

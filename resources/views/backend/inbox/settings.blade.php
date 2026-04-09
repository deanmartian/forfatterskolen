@extends('backend.layout')

@section('content')
<div class="page-toolbar">
    <h3><i class="fa fa-cog"></i> Inbox-innstillinger</h3>
    <a href="{{ route('admin.inbox.index') }}" class="btn btn-default btn-sm pull-right"><i class="fa fa-arrow-left"></i> Tilbake til inbox</a>
</div>

<div class="col-md-8 col-md-offset-2">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Personlig signatur --}}
    <div class="panel panel-info">
        <div class="panel-heading">
            <strong><i class="fa fa-pencil"></i> Min e-postsignatur</strong>
        </div>
        <div class="panel-body">
            <p class="text-muted" style="margin-bottom: 16px;">
                Denne signaturen blir lagt automatisk til på alle dine svar i inboxen, og brukes
                også når AI genererer utkast til deg. Hvis du lar feltet stå tomt, brukes
                standardsignaturen.
            </p>

            <form action="{{ route('admin.inbox.settings.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="inbox_signature"><strong>Din signatur</strong></label>
                    <textarea
                        name="inbox_signature"
                        id="inbox_signature"
                        class="form-control"
                        rows="6"
                        placeholder="{{ $defaultSignature }}"
                        style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, monospace; font-size: 14px;"
                    >{{ old('inbox_signature', $savedSignature) }}</textarea>
                    <small class="text-muted">
                        Du kan bruke flere linjer. Linjeskift bevares når svaret sendes.
                    </small>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-save"></i> Lagre signatur
                    </button>
                    @if($savedSignature)
                        <button type="submit" name="inbox_signature" value="" class="btn btn-default" onclick="return confirm('Tilbakestill til standardsignaturen?')">
                            <i class="fa fa-undo"></i> Tilbakestill til standard
                        </button>
                    @endif
                </div>
            </form>

            <hr>

            <div>
                <strong style="font-size: 13px; color: #666;">Slik ser signaturen ut akkurat nå:</strong>
                <pre style="background: #f9f9f9; border: 1px solid #e5e5e5; border-radius: 6px; padding: 14px; margin-top: 8px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, monospace; font-size: 13px; white-space: pre-wrap; color: #333;">{{ $effectiveSignature }}</pre>
                @if(!$savedSignature)
                    <small class="text-muted"><i class="fa fa-info-circle"></i> Du bruker for tiden standardsignaturen. Skriv noe i feltet over og lagre for å bytte.</small>
                @endif
            </div>
        </div>
    </div>
</div>
@stop

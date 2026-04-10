@extends('backend.layout')
@section('uses-tinymce', true)

@section('page_title', 'Rediger: ' . $template->page_name)

@section('content')
<div class="container-fluid" style="padding: 20px; max-width: 1200px;">
    <a href="{{ route('admin.email-admin.index') }}" class="btn btn-sm btn-outline-secondary mb-3">← Tilbake til oversikt</a>

    <h3><i class="fa fa-pencil"></i> Rediger: {{ $template->page_name }}</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        {{-- Venstre: Innstillinger --}}
        <div class="col-md-3">
            <div class="card" style="padding: 20px;">
                <h5>Innstillinger</h5>

                <form method="POST" action="{{ route('admin.email-admin.update', $template->id) }}" id="templateForm">
                    @csrf

                    <div class="form-group">
                        <label><strong>Emne</strong></label>
                        <input type="text" name="subject" class="form-control" value="{{ $template->subject }}" required>
                    </div>

                    <div class="form-group">
                        <label><strong>Fra e-post</strong></label>
                        <select name="from_email" class="form-control">
                            <option value="post@forfatterskolen.no" {{ $template->from_email === 'post@forfatterskolen.no' ? 'selected' : '' }}>post@forfatterskolen.no</option>
                            <option value="kristine@forfatterskolen.no" {{ $template->from_email === 'kristine@forfatterskolen.no' ? 'selected' : '' }}>kristine@forfatterskolen.no</option>
                            <option value="sven.inge@forfatterskolen.no" {{ $template->from_email === 'sven.inge@forfatterskolen.no' ? 'selected' : '' }}>sven.inge@forfatterskolen.no</option>
                        </select>
                    </div>

                    <hr>

                    <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-save"></i> Lagre</button>

                    <a href="{{ route('admin.email-admin.preview', $template->id) }}" class="btn btn-info btn-block mt-2" target="_blank">
                        <i class="fa fa-eye"></i> Forhåndsvis
                    </a>
                </form>

                <hr>

                <h6>Send test-e-post</h6>
                <form method="POST" action="{{ route('admin.email-admin.send-test', $template->id) }}">
                    @csrf
                    <div class="form-group">
                        <input type="email" name="email" class="form-control form-control-sm" placeholder="din@epost.no" value="{{ auth()->user()->email }}" required>
                    </div>
                    <button type="submit" class="btn btn-warning btn-sm btn-block"><i class="fa fa-paper-plane"></i> Send test</button>
                </form>

                <hr>

                <h6>Tilgjengelige variabler</h6>
                <div style="max-height: 200px; overflow-y: auto;">
                    @foreach($variables as $var => $desc)
                        <div style="margin-bottom: 4px;">
                            <code style="cursor: pointer; font-size: 12px;" onclick="insertVariable('{{ $var }}')" title="Klikk for å sette inn">{{ $var }}</code>
                            <small class="text-muted d-block" style="font-size: 10px;">{{ $desc }}</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Høyre: Editor --}}
        <div class="col-md-9">
            <div class="card" style="padding: 20px;">
                <h5>Innhold</h5>
                <textarea name="email_content" id="email_content" class="form-control tinymce" rows="20" form="templateForm">{{ $template->email_content }}</textarea>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
function insertVariable(varName) {
    if (typeof tinymce !== 'undefined' && tinymce.get('email_content')) {
        tinymce.get('email_content').insertContent(varName);
    }
}
</script>
@endsection

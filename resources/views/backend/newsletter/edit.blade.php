@extends('backend.layout')

@section('title')
    <title>Rediger nyhetsbrev — Forfatterskolen Admin</title>
@stop

@section('content')
<div class="container-fluid" style="padding: 20px; max-width: 1000px;">
    <a href="{{ route('admin.newsletter.index') }}" class="btn btn-sm btn-outline-secondary mb-3">← Tilbake</a>

    <h3>Rediger nyhetsbrev</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.newsletter.update', $newsletter->id) }}">
        @csrf @method('PUT')

        <div class="form-group">
            <label><strong>Emne</strong></label>
            <input type="text" name="subject" class="form-control" value="{{ old('subject', $newsletter->subject) }}" required>
        </div>

        <div class="form-group">
            <label><strong>Forhåndsvisningstekst</strong></label>
            <input type="text" name="preview_text" class="form-control" value="{{ old('preview_text', $newsletter->preview_text) }}">
        </div>

        <div class="form-group">
            <label><strong>Innhold</strong></label>
            <textarea name="body_html" id="body_html" class="form-control tinymce" rows="20">{{ old('body_html', $newsletter->body_html) }}</textarea>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label><strong>Segment</strong></label>
                    <select name="segment" class="form-control">
                        <option value="all" {{ $newsletter->segment === 'all' ? 'selected' : '' }}>Alle kontakter</option>
                        <option value="no_active_course" {{ $newsletter->segment === 'no_active_course' ? 'selected' : '' }}>Uten aktivt kurs</option>
                        <option value="active_course" {{ $newsletter->segment === 'active_course' ? 'selected' : '' }}>Med aktivt kurs</option>
                        <option value="webinar_registrants" {{ $newsletter->segment === 'webinar_registrants' ? 'selected' : '' }}>Webinar-påmeldte</option>
                        <option value="course_17" {{ $newsletter->segment === 'course_17' ? 'selected' : '' }}>Kurs 17 (mentormøter)</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label><strong>Fra-adresse</strong></label>
                    <input type="email" name="from_address" class="form-control" value="{{ $newsletter->from_address }}">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label><strong>Fra-navn</strong></label>
                    <input type="text" name="from_name" class="form-control" value="{{ $newsletter->from_name }}">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Lagre</button>
    </form>

    <hr>

    <!-- Handlinger -->
    <div class="row mt-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Test-sending</h5>
                    <form method="POST" action="{{ route('admin.newsletter.test', $newsletter->id) }}">
                        @csrf
                        <div class="input-group">
                            <input type="email" name="test_email" class="form-control" value="{{ auth()->user()->email }}" placeholder="Test e-post...">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-outline-primary">Send test</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Planlegg</h5>
                    <form method="POST" action="{{ route('admin.newsletter.send', $newsletter->id) }}">
                        @csrf
                        <div class="input-group">
                            <input type="datetime-local" name="scheduled_at" class="form-control">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-outline-warning">Planlegg</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5>Send nå</h5>
                    <form method="POST" action="{{ route('admin.newsletter.send', $newsletter->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block" onclick="return confirm('Send nyhetsbrevet til alle mottakere nå?')">
                            <i class="fa fa-paper-plane"></i> Send nå
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js"></script>
<script>
tinymce.init({
    selector: '#body_html',
    height: 500,
    menubar: true,
    plugins: 'link image code lists table',
    toolbar: 'undo redo | formatselect | bold italic underline | alignleft aligncenter | link image | bullist numlist | table | code',
    content_style: 'body { font-family: Georgia, serif; font-size: 16px; }'
});
</script>
@stop

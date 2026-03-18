@extends('backend.layout')

@section('title')
    <title>Nytt nyhetsbrev — Forfatterskolen Admin</title>
@stop

@section('content')
<div class="container-fluid" style="padding: 20px; max-width: 1000px;">
    <a href="{{ route('admin.newsletter.index') }}" class="btn btn-sm btn-outline-secondary mb-3">← Tilbake</a>

    <h3>Nytt nyhetsbrev</h3>

    <form method="POST" action="{{ route('admin.newsletter.store') }}">
        @csrf

        <div class="form-group">
            <label><strong>Emne</strong></label>
            <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required>
        </div>

        <div class="form-group">
            <label><strong>Forhåndsvisningstekst</strong> <small class="text-muted">(valgfritt)</small></label>
            <input type="text" name="preview_text" class="form-control" value="{{ old('preview_text') }}">
        </div>

        <div class="form-group">
            <label><strong>Innhold</strong></label>
            <textarea name="body_html" id="body_html" class="form-control tinymce" rows="20">{{ old('body_html') }}</textarea>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label><strong>Segment</strong></label>
                    <select name="segment" class="form-control">
                        <option value="all">Alle kontakter</option>
                        <option value="no_active_course">Uten aktivt kurs</option>
                        <option value="active_course">Med aktivt kurs</option>
                        <option value="webinar_registrants">Webinar-påmeldte</option>
                        <option value="course_17">Kurs 17 (mentormøter)</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label><strong>Fra-adresse</strong></label>
                    <input type="email" name="from_address" class="form-control" value="post@nyhetsbrev.forfatterskolen.no">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label><strong>Fra-navn</strong></label>
                    <input type="text" name="from_name" class="form-control" value="Forfatterskolen">
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Lagre som utkast</button>
    </form>
</div>
@endsection


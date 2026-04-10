@extends('backend.layout')
@section('uses-tinymce', true)

@section('page_title')Rediger steg — {{ $sequence->name }}@endsection

@section('content')
<div class="container-fluid" style="padding: 20px; max-width: 900px;">
    <a href="{{ route('admin.crm.sequences.show', $sequence->id) }}" class="btn btn-sm btn-outline-secondary mb-3">← Tilbake</a>

    <h3>Rediger steg {{ $step->step_number }} — {{ $sequence->name }}</h3>

    <form method="POST" action="{{ route('admin.crm.sequences.steps.update', [$sequence->id, $step->id]) }}">
        @csrf @method('PUT')

        <div class="form-group">
            <label><strong>Emne</strong></label>
            <input type="text" name="subject" class="form-control" value="{{ old('subject', $step->subject) }}" required>
            <small class="text-muted">Variabler: [fornavn], [webinar_tittel], [webinar_dato], [webinar_tid]</small>
        </div>

        <div class="form-group">
            <label><strong>Innhold (HTML)</strong></label>
            <textarea name="body_html" id="body_html" class="form-control" rows="15">{{ old('body_html', $step->body_html) }}</textarea>
        </div>

        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label><strong>Planlegging</strong></label>
                    <select id="scheduleType" class="form-control" onchange="toggleSchedule()">
                        <option value="delay" {{ empty($step->scheduled_date) ? 'selected' : '' }}>Forsinkelse</option>
                        <option value="date" {{ !empty($step->scheduled_date) ? 'selected' : '' }}>Fast dato</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2" id="delayField">
                <div class="form-group">
                    <label><strong>Forsinkelse (timer)</strong></label>
                    <input type="number" name="delay_hours" class="form-control" value="{{ old('delay_hours', $step->delay_hours) }}" min="0">
                </div>
            </div>
            <div class="col-md-2" id="dateField" style="display:{{ !empty($step->scheduled_date) ? 'block' : 'none' }}">
                <div class="form-group">
                    <label><strong>Send dato</strong></label>
                    <input type="date" name="scheduled_date" class="form-control" value="{{ old('scheduled_date', $step->scheduled_date ?? '') }}">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label><strong>Send kl. (valgfritt)</strong></label>
                    <input type="time" name="send_time" class="form-control" value="{{ old('send_time', $step->send_time) }}">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label><strong>Fra-type</strong></label>
                    <select name="from_type" class="form-control">
                        <option value="transactional" {{ $step->from_type === 'transactional' ? 'selected' : '' }}>Transaksjonell</option>
                        <option value="newsletter" {{ $step->from_type === 'newsletter' ? 'selected' : '' }}>Nyhetsbrev</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label><strong>&nbsp;</strong></label>
                    <div class="form-check mt-2">
                        <input type="hidden" name="only_without_active_course" value="0">
                        <input type="checkbox" name="only_without_active_course" value="1" class="form-check-input"
                            {{ $step->only_without_active_course ? 'checked' : '' }}>
                        <label class="form-check-label">Kun uten aktivt kurs</label>
                    </div>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Lagre endringer</button>
        <button type="button" class="btn btn-outline-secondary ml-2" onclick="togglePreview()">Forhåndsvisning</button>
    </form>

    <div id="emailPreview" style="display:none; margin-top:20px;">
        <h4>Forhåndsvisning</h4>
        <div style="background:#f8f8f8;border:1px solid #ddd;border-radius:8px;padding:20px;max-width:700px;">
            <div id="previewSubject" style="font-weight:bold;font-size:16px;margin-bottom:16px;padding-bottom:12px;border-bottom:1px solid #ddd;"></div>
            <div id="previewBody"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleSchedule() {
    var type = document.getElementById('scheduleType').value;
    document.getElementById('delayField').style.display = type === 'delay' ? 'block' : 'none';
    document.getElementById('dateField').style.display = type === 'date' ? 'block' : 'none';
}
</script>
<script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
<script>
tinymce.init({
    selector: '#body_html',
    height: 400,
    menubar: false,
    plugins: 'link code lists image',
    toolbar: 'undo redo | bold italic underline | link image | bullist numlist | code',
    content_style: 'body { font-family: Georgia, serif; font-size: 16px; }',
    image_title: true,
    automatic_uploads: true,
    file_picker_types: 'image',
    images_upload_handler: function (blobInfo) {
        return new Promise(function (resolve, reject) {
            var formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            formData.append('_token', '{{ csrf_token() }}');
            fetch('/upload-image', {
                method: 'POST',
                body: formData
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.location) resolve(data.location);
                else reject('Opplasting feilet');
            })
            .catch(function() { reject('Opplasting feilet'); });
        });
    }
});

function togglePreview() {
    var preview = document.getElementById('emailPreview');
    if (preview.style.display === 'none') {
        preview.style.display = 'block';
        document.getElementById('previewSubject').textContent = document.querySelector('input[name="subject"]').value;
        document.getElementById('previewBody').innerHTML = tinymce.get('body_html').getContent();
    } else {
        preview.style.display = 'none';
    }
}
</script>
@stop

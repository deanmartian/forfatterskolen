<form id="bulkActionForm" method="POST" action="{{ route('admin.email-out.bulk', $course->id) }}">
    {{ csrf_field() }}
    <input type="hidden" name="action" id="bulkActionType" value="">

    <div style="margin-bottom:10px;display:none;" id="bulkBar">
        <span id="selectedCount">0</span> valgt &mdash;
        <button type="button" class="btn btn-danger btn-xs" onclick="submitBulk('delete')" title="Slett valgte">
            <i class="fa fa-trash"></i> Slett
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-side-bordered table-white">
            <thead>
            <tr>
                <th><input type="checkbox" id="selectAll"></th>
                <th></th>
                <th>{{ trans('site.subject') }}</th>
                <th width="500">{{ trans('site.message') }}</th>
                <th>Type</th>
                <th>{{ trans('site.availability') }}</th>
                <th>Send Immediately</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
                @foreach($emails as $email)
                    <tr>
                        <td><input type="checkbox" name="email_ids[]" value="{{ $email->id }}" class="bulk-check"></td>
                        <td>
                            @if($email->auto_generated)
                                <span title="Auto-generert">&#129302;</span>
                            @else
                                <span title="Manuell">&#9998;</span>
                            @endif
                        </td>
                        <td>{{ $email->subject }}</td>
                        <td>
                            @if($email->template_type)
                                <span class="label label-info" style="font-size:11px;">Branded</span>
                            @else
                                {!! \Illuminate\Support\Str::limit(strip_tags($email->message), 100) !!}
                            @endif
                        </td>
                        <td>
                            @if($email->template_type)
                                @php
                                    $typeLabels = [
                                        'welcome' => ['Velkomst', 'info'],
                                        'module_available' => ['Modul', 'primary'],
                                        'assignment_available' => ['Oppgave', 'success'],
                                        'assignment_reminder' => ['Påminnelse', 'warning'],
                                        'assignment_deadline' => ['Frist', 'danger'],
                                        'feedback_ready' => ['Tilbakemelding', 'info'],
                                    ];
                                    $label = $typeLabels[$email->template_type] ?? [$email->template_type, 'default'];
                                @endphp
                                <span class="label label-{{ $label[1] }}">{{ $label[0] }}</span>
                            @else
                                <span class="text-muted">&mdash;</span>
                            @endif
                        </td>
                        <td>
                            @if(\App\Http\AdminHelpers::isDate($email->delay))
                                {{date_format(date_create($email->delay), 'M d, Y')}}
                            @else
                                {{$email->delay}} {{ trans('site.days-delay') }}
                            @endif
                        </td>
                        <td>
                            {{ $email->send_immediately_text }}
                        </td>
                        <td>
                            @if($email->template_type)
                            <a href="{{ route('admin.email-out.preview-branded', ['course_id' => $course->id, 'email_out' => $email->id]) }}" target="_blank" class="btn btn-default btn-xs" title="Preview">
                                <i class="fa fa-eye"></i>
                            </a>
                            @endif
                            <button class="btn btn-success btn-xs sendEmailBtn" data-toggle="modal"
                                    data-target="#sendEmailModal"
                                    data-action="{{
                                    route('admin.email-out.send-email',
                                    ['course_id' => $course->id, 'email_out' => $email->id])
                                    }}">
                                <i class="fa fa-paper-plane"></i>
                            </button>
                            <button class="btn btn-info btn-xs editEmailBtn loadScriptButton" data-toggle="modal"
                            data-target="#emailModal" data-fields="{{ json_encode($email) }}"
                            data-action="{{ route('admin.email-out.update', ['course_id' => $course->id, 'email_out' => $email->id]) }}"
                            data-filename="{{ \App\Http\AdminHelpers::extractFileName($email->attachment) }}"
                            data-fileloc="{{ asset($email->attachment) }}">
                                <i class="fa fa-pencil"></i>
                            </button>
                            <button class="btn btn-danger btn-xs deleteEmailBtn" data-toggle="modal" data-target="#deleteEmailModal"
                            data-action="{{ route('admin.email-out.destroy', ['course_id' => $course->id, 'email_out' => $email->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</form>

<script>
document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.bulk-check').forEach(function(cb) { cb.checked = this.checked; }.bind(this));
    updateBulkBar();
});
document.querySelectorAll('.bulk-check').forEach(function(cb) {
    cb.addEventListener('change', updateBulkBar);
});
function updateBulkBar() {
    var count = document.querySelectorAll('.bulk-check:checked').length;
    document.getElementById('selectedCount').textContent = count;
    document.getElementById('bulkBar').style.display = count > 0 ? 'block' : 'none';
}
function submitBulk(action) {
    var count = document.querySelectorAll('.bulk-check:checked').length;
    if (count === 0) return;
    if (!confirm('Er du sikker? ' + count + ' e-poster vil bli slettet.')) return;
    document.getElementById('bulkActionType').value = action;
    document.getElementById('bulkActionForm').submit();
}
</script>

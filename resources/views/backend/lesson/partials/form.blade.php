<div class="container">
	<a class="btn btn-info margin-bottom" href="{{route('admin.course.show', $course->id)}}?section=lessons"><i class="fa fa-arrow-left"></i> {{ trans('site.back-to-lessons') }}</a>
	@if(Request::is('course/*/lesson/create'))
	<form action="{{route('admin.lesson.store', $course->id)}}" method="post" enctype="multipart/form-data" 
        onsubmit="disableSubmit(this)">
	@else
	@include('backend.lesson.partials.delete')
    @include('backend.lesson.partials.delete-document')
	<form action="{{route('admin.lesson.update', ['course_id' => $course->id, 'lesson' => $lesson['id']])}}" method="post" id="lessonForm"
          enctype="multipart/form-data" onsubmit="disableSubmit(this)">
	{{method_field('PUT')}}
	@endif
		{{csrf_field()}}
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="row">
					<div class="col-sm-4">
						<div class="form-group">
							<label for="title">{{ trans('site.lesson-title') }}</label>
							<input type="text" class="form-control" name="title" id="title" required value="{{$lesson['title']}}">
						</div>
					</div>
					<div class="col-sm-2">
						<div class="form-group">
							<label>Leksjonstype</label>
							<select class="form-control" name="type">
								<option value="module" {{ ($lesson['type'] ?? 'module') === 'module' ? 'selected' : '' }}>Modul</option>
								<option value="resource" {{ ($lesson['type'] ?? '') === 'resource' ? 'selected' : '' }}>Ressurs</option>
								<option value="reprise" {{ ($lesson['type'] ?? '') === 'reprise' ? 'selected' : '' }}>Reprise</option>
							</select>
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group">
							<label>{{ trans('site.delay-type') }}</label>
							<select class="form-control" id="lesson-delay-toggle">
								<option value="days">Days</option>
								<option value="date" @if(AdminHelpers::isDate($lesson['delay'])) selected @endif>Date</option>
							</select>
						</div>
					</div>
					<div class="col-sm-3">
						<label>{{ trans('site.delay') }}</label>
						<div class="input-group">
							@if(AdminHelpers::isDate($lesson['delay']))
						  	<input type="date" class="form-control" name="delay" id="lesson-delay" min="0" required value="{{$lesson['delay']}}">
							@else
						  	<input type="number" class="form-control" name="delay" id="lesson-delay" min="0" required value="{{$lesson['delay']}}">
						  	@endif
						  	<span class="input-group-addon lesson-delay-text" id="basic-addon2">
						  	@if(AdminHelpers::isDate($lesson['delay']))
						  	date
						  	@else
						  	days
						  	@endif
						  	</span>
						</div>
					</div>
				</div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>Whole Lesson File</label>
                            <input type="file" name="whole_lesson_file" class="form-control"
                               accept="application/pdf">

                            @if($lesson['whole_lesson_file'])
                                <a href="{{ '/js/ViewerJS/#../..' . $lesson['whole_lesson_file'] }}">
                                   {{ basename($lesson['whole_lesson_file'])}}
                                </a>
                                <a href="#" data-toggle="modal" data-target="#deleteLessonFileModal"
                                    data-action="{{ route('admin.lesson.delete-lesson-whole-file', $lesson['id']) }}"
                                    class="deleteLessonFileBtn" style="color: red">&times;</a>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6">
                        @foreach($documents as $document)
                            <ul>
                                <li class="lesson-document-container">
                                    <a href="{{ route('admin.lesson.download-lesson-document', $document->id) }}">
                                        {{ $document->name }}
                                    </a>
                                    <a href="#" data-toggle="modal" data-target="#deleteLessonDocumentModal"
                                    data-action="{{ route('admin.lesson.delete-lesson-document', $document->id) }}"
                                    data-document-name="{{ $document->name }}"
                                    class="deleteLessonDocumentBtn">&times;</a>
                                </li>
                            </ul>
                        @endforeach
                        <input type="file" name="documents[]" class="form-control" multiple
                               accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document,
                               application/pdf,
                               application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                    </div>
                </div>
				<div class="row margin-top">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label>
                                Allow download of whole lesson
                            </label> <br>
                            <input type="checkbox" name="allow_lesson_download" data-toggle="toggle" 
                                data-on="{{ trans('site.front.yes') }}" class="lock-toggle" 
                                data-off="{{ trans('site.front.no') }}"
                                data-size="small" @if($lesson['allow_lesson_download']) 
                                    {{ 'checked' }} 
                                @endif>
                        </div>
                    </div>
					<div class="col-sm-6 text-right">
						@if(Request::is('course/*/lesson/create'))
						<button type="submit" class="btn btn-info">{{ trans('site.create-lesson') }}</button>
						@else
                            <input type="text" name="copyClip"
                                   value="{{ config('app.live_url')."/account/course/".$course->id."/lesson/".$lesson['id'] }}"
                                   style="position: absolute; left: -10000px;">
                            <button type="button" class="btn btn-success copyToClipboard">
                                Copy Link
                            </button>
						<button type="submit" class="btn btn-info">{{ trans('site.update-lesson') }}</button>
						<button type="button" data-toggle="modal" data-target="#deleteLessonModal" class="btn btn-danger">{{ trans('site.delete-lesson') }}</button>
						@endif
						{{--<textarea id="description-ct" class="hidden" name="content">{{$lesson['content']}}</textarea>--}}
					</div>
				</div>
			</div>
		</div>
	{{--</form>--}}
</div>

<!-- check if not webinar-pakke -->
@if ($course->id !== 17)
    <div class="content-tools-container">
        <div class="container">
            {{--<div data-editable data-name="main_content">
                {!! $lesson['content'] !!}
            </div>--}}
            <div class="form-group">
                <textarea id="lesson-content-ct" class="tinymce" name="content" placeholder="Content">{!! $lesson['content'] !!}</textarea>
            </div>
            </form>
        </div>
    </div>
@else
    @if(!Request::is('course/*/lesson/create'))
        <div class="content-tools-container">
            <div class="container">
                {{-- check if the last lesson id before updating structure --}}
                @if ($lesson['id'] <= 169)
                    <div class="form-group">
                        <textarea id="lesson-content" name="content" placeholder="Content">{!! $lesson['content'] !!}</textarea>
                    </div>
                    </form>
                @else
                    <button class="btn btn-primary" onclick="methods.addNewStructureContent()"
                            type="button" id="addNewContentBtn">{{ trans('site.add-content') }}</button>

                    <form action=""></form>
                    <form action="{{ route('admin.lesson.add_content', $lesson['id']) }}" id="newStructureForm"
                    method="POST" enctype="multipart/form-data" style="display: inline">
                        {{ csrf_field() }}
                        <button type="button" class="btn btn-primary hidden newStructureSaveChanges"
                                onclick="methods.saveLessonContent(this)">{{ trans('site.save-changes') }}</button>
                        <div id="content_container"></div>
                        <button type="button" class="btn btn-primary margin-top hidden newStructureSaveChanges"
                        onclick="methods.saveLessonContent(this)">{{ trans('site.save-changes') }}</button>
                    </form>
                    <input type="hidden" name="webinar_pakke">
                @endif
            </div>
        </div>
    @endif
@endif




{{-- ═══ AI AUTO-GENERATE ═══ --}}
@if(!Request::is('course/*/lesson/create'))
<div class="container" style="margin-top:30px;">
    <div class="panel panel-default" style="border-left:3px solid #862736;">
        <div class="panel-heading" style="background:#faf8f5;">
            <h3 class="panel-title"><i class="fa fa-magic" style="color:#862736;"></i> AI-assistent</h3>
        </div>
        <div class="panel-body">
            <p style="color:#5a5550;font-size:0.9rem;margin-bottom:1rem;">
                La AI lese igjennom leksjonsteksten og foreslå oppgaver og quiz-spørsmål automatisk.
                Du kan godkjenne, redigere eller forkaste forslagene.
            </p>
            <button type="button" class="btn btn-primary" id="aiGenerateBtn" onclick="aiGenerate()" style="background:#862736;border-color:#862736;">
                <i class="fa fa-magic"></i> Generer oppgaver og quiz med AI
            </button>
            <div id="aiGenerateStatus" style="margin-top:0.75rem;display:none;"></div>
            <div id="aiGenerateResults" style="margin-top:1rem;display:none;">
                <h4 style="font-size:0.9rem;font-weight:600;">AI-forslag:</h4>
                <div id="aiAssignmentSuggestions"></div>
                <div id="aiQuizSuggestions"></div>
            </div>
        </div>
    </div>
</div>

<script>
function aiGenerate() {
    var btn = document.getElementById('aiGenerateBtn');
    var status = document.getElementById('aiGenerateStatus');
    var results = document.getElementById('aiGenerateResults');

    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-pulse"></i> AI analyserer leksjonen...';
    status.style.display = 'block';
    status.innerHTML = '<span style="color:#8a8580;">Dette kan ta 10-30 sekunder...</span>';
    results.style.display = 'none';

    fetch('{{ route("admin.lesson.ai_generate", $lesson["id"]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-magic"></i> Generer oppgaver og quiz med AI';

        if (data.error) {
            status.innerHTML = '<span style="color:#c62828;"><i class="fa fa-exclamation-triangle"></i> ' + data.error + '</span>';
            return;
        }

        if (data.success && data.data) {
            status.style.display = 'none';
            results.style.display = 'block';

            var assHtml = '';
            if (data.data.assignments && data.data.assignments.length) {
                assHtml = '<div style="margin-bottom:1rem;"><strong>Oppgaver:</strong></div>';
                data.data.assignments.forEach(function(a, i) {
                    assHtml += '<div style="border:1px solid #d4edda;border-radius:8px;padding:0.75rem;margin-bottom:0.5rem;background:#f8fff8;">' +
                        '<div style="display:flex;justify-content:space-between;align-items:flex-start;">' +
                        '<div style="flex:1;font-size:0.9rem;">' + a.question_text.replace(/</g,'&lt;') + '</div>' +
                        '<button class="btn btn-sm btn-success" onclick="acceptAssignment(this, \'' + a.question_text.replace(/'/g, "\\'").replace(/"/g, '&quot;') + '\')" style="margin-left:0.5rem;white-space:nowrap;">' +
                        '<i class="fa fa-check"></i> Godta</button>' +
                        '</div></div>';
                });
            }
            document.getElementById('aiAssignmentSuggestions').innerHTML = assHtml;

            var quizHtml = '';
            if (data.data.quizzes && data.data.quizzes.length) {
                quizHtml = '<div style="margin-bottom:1rem;margin-top:1rem;"><strong>Quiz-spørsmål:</strong></div>';
                data.data.quizzes.forEach(function(q, i) {
                    var optLabels = ['A', 'B', 'C', 'D'];
                    var optHtml = q.options.map(function(o, oi) {
                        return '<span style="display:inline-block;padding:2px 8px;margin:2px;border-radius:4px;font-size:0.85rem;' +
                            (oi === q.correct_option ? 'background:#e8f5e9;color:#2e7d32;font-weight:600;' : 'background:#f0f0f0;') + '">' +
                            optLabels[oi] + ': ' + o + (oi === q.correct_option ? ' ✓' : '') + '</span>';
                    }).join(' ');

                    quizHtml += '<div style="border:1px solid #bbdefb;border-radius:8px;padding:0.75rem;margin-bottom:0.5rem;background:#f0f7ff;">' +
                        '<div style="display:flex;justify-content:space-between;align-items:flex-start;">' +
                        '<div style="flex:1;"><div style="font-size:0.9rem;font-weight:600;margin-bottom:0.25rem;">' + q.question.replace(/</g,'&lt;') + '</div>' +
                        '<div>' + optHtml + '</div></div>' +
                        '<button class="btn btn-sm btn-success" onclick=\'acceptQuiz(this, ' + JSON.stringify(q) + ')\' style="margin-left:0.5rem;white-space:nowrap;">' +
                        '<i class="fa fa-check"></i> Godta</button>' +
                        '</div></div>';
                });
            }
            document.getElementById('aiQuizSuggestions').innerHTML = quizHtml;
        }
    })
    .catch(function(err) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-magic"></i> Generer oppgaver og quiz med AI';
        status.innerHTML = '<span style="color:#c62828;">Noe gikk galt. Prøv igjen.</span>';
    });
}

function acceptAssignment(btn, questionText) {
    fetch('{{ route("admin.lesson.save_assignment", $lesson["id"]) }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ question_text: questionText })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            btn.closest('div[style*="border"]').style.background = '#e8f5e9';
            btn.outerHTML = '<span style="color:#2e7d32;font-weight:600;margin-left:0.5rem;"><i class="fa fa-check"></i> Lagt til!</span>';
        }
    });
}

function acceptQuiz(btn, quizData) {
    fetch('{{ route("admin.lesson.save_quiz", $lesson["id"]) }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
        body: JSON.stringify({ question: quizData.question, options: quizData.options, correct_option: quizData.correct_option })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            btn.closest('div[style*="border"]').style.background = '#e8f5e9';
            btn.outerHTML = '<span style="color:#2e7d32;font-weight:600;margin-left:0.5rem;"><i class="fa fa-check"></i> Lagt til!</span>';
        }
    });
}
</script>
@endif

{{-- ═══ ASSIGNMENT ADMIN ═══ --}}
@if(!Request::is('course/*/lesson/create'))
<div class="container" style="margin-top:30px;">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-pencil"></i> Oppgaver (AI-tilbakemelding)</h3>
        </div>
        <div class="panel-body">
            <div id="assignmentList">
                @if(isset($lessonAssignments))
                    @foreach($lessonAssignments as $la)
                        <div class="assignment-item" data-assignment-id="{{ $la->id }}" style="border:1px solid #e8e4de;border-radius:8px;padding:1rem;margin-bottom:0.75rem;background:#faf8f5;">
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                                <div style="flex:1;">
                                    <strong>{{ $la->question_text }}</strong>
                                    <div style="margin-top:0.25rem;font-size:0.8rem;color:#8a8580;">
                                        {{ $la->submissions()->count() }} innsendte svar
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteAssignment({{ $la->id }}, this)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <hr>
            <h4 style="font-size:0.9rem;font-weight:600;">Legg til ny oppgave</h4>
            <div class="form-group">
                <label>Oppgavetekst</label>
                <textarea class="form-control" id="assignmentQuestion" rows="3" placeholder="Skriv oppgaven eleven skal besvare..."></textarea>
            </div>
            <button type="button" class="btn btn-success" onclick="addAssignment()">
                <i class="fa fa-plus"></i> Legg til oppgave
            </button>
        </div>
    </div>
</div>

<script>
function addAssignment() {
    var question = document.getElementById('assignmentQuestion').value.trim();
    if (!question) { alert('Skriv inn en oppgavetekst'); return; }

    fetch('{{ route("admin.lesson.save_assignment", $lesson["id"]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ question_text: question })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) { location.reload(); }
        else { alert('Feil ved lagring'); }
    });
}

function deleteAssignment(id, btn) {
    if (!confirm('Slett denne oppgaven?')) return;

    fetch('/course/lesson/lesson-assignment/' + id, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) { btn.closest('.assignment-item').remove(); }
    });
}
</script>
@endif

{{-- ═══ QUIZ ADMIN ═══ --}}
@if(!Request::is('course/*/lesson/create'))
<div class="container" style="margin-top:30px;">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><i class="fa fa-question-circle"></i> Quiz-spørsmål</h3>
        </div>
        <div class="panel-body">
            <div id="quizList">
                @if(isset($quizzes))
                    @foreach($quizzes as $quiz)
                        <div class="quiz-item" data-quiz-id="{{ $quiz->id }}" style="border:1px solid #e8e4de;border-radius:8px;padding:1rem;margin-bottom:0.75rem;background:#faf8f5;">
                            <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                                <div>
                                    <strong>{{ $quiz->question }}</strong>
                                    <div style="margin-top:0.5rem;">
                                        @foreach($quiz->options as $oi => $opt)
                                            <span style="display:inline-block;padding:2px 8px;margin:2px;border-radius:4px;font-size:0.85rem;{{ $oi === $quiz->correct_option ? 'background:#e8f5e9;color:#2e7d32;font-weight:600;' : 'background:#f0f0f0;' }}">
                                                {{ $opt }} {{ $oi === $quiz->correct_option ? '✓' : '' }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteQuiz({{ $quiz->id }}, this)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <hr>
            <h4 style="font-size:0.9rem;font-weight:600;">Legg til nytt spørsmål</h4>
            <div class="form-group">
                <label>Spørsmål</label>
                <input type="text" class="form-control" id="quizQuestion" placeholder="Skriv spørsmålet her...">
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Alternativ A</label>
                        <input type="text" class="form-control quiz-option" data-idx="0" placeholder="Alternativ A">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Alternativ B</label>
                        <input type="text" class="form-control quiz-option" data-idx="1" placeholder="Alternativ B">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Alternativ C</label>
                        <input type="text" class="form-control quiz-option" data-idx="2" placeholder="Alternativ C (valgfritt)">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Alternativ D</label>
                        <input type="text" class="form-control quiz-option" data-idx="3" placeholder="Alternativ D (valgfritt)">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>Riktig svar</label>
                <select class="form-control" id="quizCorrect" style="max-width:200px;">
                    <option value="0">Alternativ A</option>
                    <option value="1">Alternativ B</option>
                    <option value="2">Alternativ C</option>
                    <option value="3">Alternativ D</option>
                </select>
            </div>
            <button type="button" class="btn btn-success" onclick="addQuiz()">
                <i class="fa fa-plus"></i> Legg til spørsmål
            </button>
        </div>
    </div>
</div>

<script>
function addQuiz() {
    var question = document.getElementById('quizQuestion').value.trim();
    var options = [];
    document.querySelectorAll('.quiz-option').forEach(function(el) {
        if (el.value.trim()) options.push(el.value.trim());
    });
    var correct = parseInt(document.getElementById('quizCorrect').value);

    if (!question) { alert('Skriv inn et spørsmål'); return; }
    if (options.length < 2) { alert('Minst 2 alternativer er påkrevd'); return; }

    fetch('{{ route("admin.lesson.save_quiz", $lesson["id"]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ question: question, options: options, correct_option: correct })
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            location.reload();
        } else {
            alert('Feil ved lagring');
        }
    });
}

function deleteQuiz(id, btn) {
    if (!confirm('Slett dette quiz-spørsmålet?')) return;

    fetch('/course/lesson/quiz/' + id, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (data.success) {
            btn.closest('.quiz-item').remove();
        }
    });
}
</script>
@endif

<span class="title-text hidden">{{ trans('site.title') }}</span>
<span class="video-text hidden">{{ trans('site.video') }}</span>
@section('scripts')
<script src="{{asset('content_tools/content-tools.js')}}"></script>
<script src="{{ asset('js/toastr/toastr.min.js') }}"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script>
jQuery(document).ready(function(){
	$('#lessonForm').on('submit', function(e){
		if( $('#description-ct').val().length  <= 0 ) {
			alert('Content must not be empty.');
			e.preventDefault();
			return false;
		}
	});

	$(".deleteLessonDocumentBtn").click(function(){
	   var document_name = $(this).data('document-name');
	   var action = $(this).data('action');
	   var modal = $("#deleteLessonDocumentModal");
        modal.find('form').attr('action', action);
        modal.find('.modal-title').find('em').text(document_name);
    });

    $(".deleteLessonFileBtn").click(function(){
	   var document_name = $(this).data('document-name');
	   var action = $(this).data('action');
	   var modal = $("#deleteLessonFileModal");
        modal.find('form').attr('action', action);
        modal.find('.modal-title').find('em').text(document_name);
    });

	$(".lesson-document-container").hover(function(){
	    $(this).find('.deleteLessonDocumentBtn').toggle();
    });


	if ($("[name=webinar_pakke]").length) {
        let get_content_url = '{{ route('admin.lesson.get_lesson_content', $lesson['id'] ? $lesson['id'] : 0) }}';
	    methods.getLessonContents(get_content_url);
    }

    // not working on hidden fields
    $(".copyToClipboard").click(function(){
        let copyText = $('[name=copyClip]');
        /* Select the text field */
        copyText.select();
        /* Copy the text inside the text field */
        document.execCommand("copy");

        toastr.success('Copied to clipboard.', "Success");
        if (window.getSelection) {
            if (window.getSelection().empty) {  // Chrome
                window.getSelection().empty();
            } else if (window.getSelection().removeAllRanges) {  // Firefox
                window.getSelection().removeAllRanges();
            }
        } else if (document.selection) {  // IE?
            document.selection.empty();
        }
    });
});

const methods = {

    loadEditor: function(id) {
        let loadEditor_config = {
            path_absolute: "{{ URL::to('/') }}",
            height: '23em',
            selector: '#'+id,
            menubar:false,
            statusbar: false,
            plugins: ['advlist autolink lists link image charmap print preview hr anchor pagebreak',
                'searchreplace wordcount visualblocks visualchars code fullscreen',
                'insertdatetime media nonbreaking save table contextmenu directionality',
                'emoticons template paste textcolor colorpicker textpattern'],
            toolbar1: 'media',
            relative_urls: false,
            file_picker_callback : function(callback, value, meta) {
                let x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
                let y = window.innerHeight|| document.documentElement.clientHeight|| document.getElementsByTagName('body')[0].clientHeight;

                let cmsURL = tiny_editor_config.path_absolute + '/laravel-filemanager?editor=tinymce5';
                if (meta.filetype == 'image') {
                    cmsURL = cmsURL + "&type=Images";
                } else {
                    cmsURL = cmsURL + "&type=Files";
                }

                tinyMCE.activeEditor.windowManager.openUrl({
                    url : cmsURL,
                    title : 'Filemanager',
                    width : x * 0.8,
                    height : y * 0.8,
                    resizable : "yes",
                    close_previous : "no",
                    onMessage: (api, message) => {
                        callback(message.content);
                    }
                });
            }
        };
        tinymce.init(loadEditor_config);
    },

    addNewStructureContent: function() {
        this.createContent();
    },

    createContent: function(title = '', tags = '', date = '', description = '', content = '', content_id = '') {
        let title_text = $(".title-text").text();
        let video_text = $(".video-text").text();
        let id = this.uniqueId()+'_editor';
        let remove_text = '{{ trans('site.remove') }}';
        let form = `<div class="newStructureFormContainer margin-top">
                        <div class="form-group row">
                            <div class="col-xs-6">
                                <label>${ title_text }</label>
                                <input class="form-control" type="text" name="title[]" value="${ title }">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-xs-6">
                                <label>Tags</label>
                                <input class="form-control" type="text" name="tags[]" value="${ tags }">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-xs-6">
                                <label>Date</label>
                                <input class="form-control" type="date" name="date[]" value="${ date }">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-xs-6">
                                <label>Description</label>
                                <textarea class="form-control" name="description[]" rows="10" cols="10">${ description }</textarea>
                            </div>
                        </div>
                        <div class="newStructureFormContainer row">
                            <div class="form-group col-xs-6 padding-left-15">
                                <div style="padding-left: 0">
                                <label>${ video_text }</label>
                                <textarea class="form-control" name="lesson_video[]" id="${ id }" rows="5" cols="10">${ content || '' }</textarea>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="content_id[]" value="${ content_id }">
                        <button class='btn btn-danger' onclick='methods.cancelNewStructure(this)' type='button'>${remove_text}</button>
                    </div>`;


        $("#content_container").prepend(form);
        $(".newStructureSaveChanges").removeClass('hidden');
        //this.loadEditor(id);
        if (!content_id) {
            toastr.success('Content form added.', "Success");
        }
    },

    cancelNewStructure: function(el) {
        $(el).closest('.newStructureFormContainer').remove();

        if ($(".newStructureFormContainer").length === 0) {
            $(".newStructureSaveChanges").addClass('hidden');
        }

        let content_id = $(el).closest('.newStructureFormContainer').find('[name="content_id[]"]').val();
        if (content_id) {
            this.deleteLessonContent(content_id);
        } else {
            toastr.success('Content form removed.', "Success");
        }

    },

    uniqueId :function() {
        return Math.round(new Date().getTime() + (Math.random() * 100));
    },

    saveLessonContent: function(el) {
        $(el).closest('form').submit();
    },

    getLessonContents: function(url){
        let self = this;

        $.get(url).then(function (response) {
           let contents = response.data;
           $.each(contents, function(key, content){
              self.createContent(content.title, content.tags ? content.tags : '', content.date ? content.date : '',
               content.description ? content.description : '', content.lesson_content, content.id);
           });
        });
    },

    deleteLessonContent: function(content_id) {
        $.post('/lesson-content/'+content_id+'/delete-lesson-content', {}).then(function (response) {
            toastr.success(response.success, "Success");
        }).catch(function (response) {
            response = response.responseJSON;
            toastr.error(response.error, "Error");
        });
    }

};

// Define settings for the uploader
var CLOUDINARY_PRESET_NAME = 'ely_preset';
var CLOUDINARY_RETRIEVE_URL = 'http://res.cloudinary.com/ely/image/upload/';
var CLOUDINARY_UPLOAD_URL = 'https://api.cloudinary.com/v1_1/ely/image/upload';

window.addEventListener('load', function() {

    var editor = ContentTools.EditorApp.get();
	editor.init('*[data-editable]', 'data-name');

	editor.addEventListener('saved', function (ev) {
	    var regions;

	    regions = ev.detail().regions;
	    if (Object.keys(regions).length == 0) {
	        return;
	    }

	    $('#description-ct').val(regions.main_content);
	});

    ContentTools.IMAGE_UPLOADER = imageUploader;

});

function imageUploader(dialog) {
    var image, xhr, xhrComplete, xhrProgress;

    dialog.addEventListener('imageuploader.fileready', function (ev) {
        // Upload a file to Cloudinary
        var formData;
        var file = ev.detail().file;

        // Define functions to handle upload progress and completion
        function xhrProgress(ev) {
            // Set the progress for the upload
            dialog.progress((ev.loaded / ev.total) * 100);
        }

        function xhrComplete(ev) {
            var response;

            // Check the request is complete
            if (ev.target.readyState != 4) {
                return;
            }

            // Clear the request
            xhr = null
            xhrProgress = null
            xhrComplete = null

            // Handle the result of the upload
            if (parseInt(ev.target.status) == 200) {
                // Unpack the response (from JSON)
                response = JSON.parse(ev.target.responseText);

                // Store the image details
                image = {
                    angle: 0,
                    height: parseInt(response.height),
                    maxWidth: parseInt(response.width),
                    width: parseInt(response.width)
                };

                // Apply a draft size to the image for editing
                image.filename = parseCloudinaryURL(response.url)[0];
                image.url = buildCloudinaryURL(
                    image.filename,
                    [{c: 'fit', h: 600, w: 600}]
                );

                // Populate the dialog
                dialog.populate(image.url, [image.width, image.height]);

            } else {
                // The request failed, notify the user
                new ContentTools.FlashUI('no');
            }
        }

        // Set the dialog state to uploading and reset the progress bar to 0
        dialog.state('uploading');
        dialog.progress(0);

        // Build the form data to post to the server
        formData = new FormData();
        formData.append('file', file);
        formData.append('upload_preset', CLOUDINARY_PRESET_NAME);

        // Make the request
        xhr = new XMLHttpRequest();
        xhr.upload.addEventListener('progress', xhrProgress);
        xhr.addEventListener('readystatechange', xhrComplete);
        xhr.open('POST', CLOUDINARY_UPLOAD_URL, true);
        xhr.send(formData);
    });

    dialog.addEventListener('imageuploader.cancelupload', function () {
        // Cancel the current upload

        // Stop the upload
        if (xhr) {
            xhr.upload.removeEventListener('progress', xhrProgress);
            xhr.removeEventListener('readystatechange', xhrComplete);
            xhr.abort();
        }

        // Set the dialog to empty
        dialog.state('empty');
    });

    dialog.addEventListener('imageuploader.clear', function () {
        // Clear the current image
        dialog.clear();
        image = null;
    });

    function rotate(angle) {
        // Handle a request by the user to rotate the image
        var height, transforms, width;

        // Update the angle of the image
        image.angle += angle;

        // Stay within 0-360 degree range
        if (image.angle < 0) {
            image.angle += 360;
        } else if (image.angle > 270) {
            image.angle -= 360;
        }

        // Rotate the image's dimensions
        width = image.width;
        height = image.height;
        image.width = height;
        image.height = width;
        image.maxWidth = width;

        // Build the transform to rotate the image
        transforms = [{c: 'fit', h: 600, w: 600}];
        if (image.angle > 0) {
            transforms.unshift({a: image.angle});
        }

        // Build a URL for the transformed image
        image.url = buildCloudinaryURL(image.filename, transforms);

        // Update the image in the dialog
        dialog.populate(image.url, [image.width, image.height]);
        console.log(angle);
    }

    dialog.addEventListener('imageuploader.rotateccw', function () { rotate(-90); });
    dialog.addEventListener('imageuploader.rotatecw', function () { rotate(90); });

    dialog.addEventListener('imageuploader.save', function () {
        // Handle a user saving an image
        var cropRegion, cropTransform, imageAttrs, ratio, transforms;

        // Build a list of transforms
        transforms = [];

        // Angle
        if (image.angle != 0) {
            transforms.push({a: image.angle});
        }

        // Crop
        cropRegion = dialog.cropRegion();
        if (cropRegion.toString() != [0, 0, 1, 1].toString()) {
            cropTransform = {
                c: 'crop',
                x: parseInt(image.width * cropRegion[1]),
                y: parseInt(image.height * cropRegion[0]),
                w: parseInt(image.width * (cropRegion[3] - cropRegion[1])),
                h: parseInt(image.height * (cropRegion[2] - cropRegion[0]))
            };
            transforms.push(cropTransform);

            // Update the image size based on the crop
            image.width = cropTransform.w;
            image.height = cropTransform.h;
            image.maxWidth = cropTransform.w;
        }

        // Resize (the image is inserted in the page at a default size)
        if (image.width > 400 || image.height > 400) {
            transforms.push({c: 'fit', w: 400, h: 400});

            // Update the size of the image in-line with the resize
            ratio = Math.min(400 / image.width, 400 / image.height);
            image.width *= ratio;
            image.height *= ratio;
        }

        // Build a URL for the image we'll insert
        image.url = buildCloudinaryURL(image.filename, transforms);

        // Build attributes for the image
        imageAttrs = {'alt': '', 'data-ce-max-width': image.maxWidth};

        // Save/insert the image
        dialog.save(image.url, [image.width, image.height]);
    });
}

function buildCloudinaryURL(filename, transforms) {
    // Build a Cloudinary URL from a filename and the list of transforms
    // supplied. Transforms should be specified as objects (e.g {a: 90} becomes
    // 'a_90').
    var i, name, transform, transformArgs, transformPaths, urlParts;

    // Convert the transforms to paths
    transformPaths = [];
    for  (i = 0; i < transforms.length; i++) {
        transform = transforms[i];

        // Convert each of the object properties to a transform argument
        transformArgs = [];
        for (name in transform) {
            if (transform.hasOwnProperty(name)) {
                transformArgs.push(name + '_' + transform[name]);
            }
        }

        transformPaths.push(transformArgs.join(','));
    }

    // Build the URL
    urlParts = [CLOUDINARY_RETRIEVE_URL];
    if (transformPaths.length > 0) {
        urlParts.push(transformPaths.join('/'));
    }
    urlParts.push(filename);

    return urlParts.join('/');
}

function parseCloudinaryURL(url) {
    // Parse a Cloudinary URL and return the filename and list of transforms
    var filename, i, j, transform, transformArgs, transforms, urlParts;

    // Strip the URL down to just the transforms, version (optional) and
    // filename.
    url = url.replace(CLOUDINARY_RETRIEVE_URL, '');

    // Split the remaining path into parts
    urlParts = url.split('/');

    // The path starts with a '/' so the first part will be empty and can be
    // discarded.
    urlParts.shift();

    // Extract the filename
    filename = urlParts.pop();

    // Strip any version number from the URL
    if (urlParts.length > 0 && urlParts[urlParts.length - 1].match(/v\d+/)) {
        urlParts.pop();
    }

    // Convert the remaining parts into transforms (e.g `w_90,h_90,c_fit >
    // {w: 90, h: 90, c: 'fit'}`).
    transforms = [];
    for (i = 0; i < urlParts.length; i++) {
        transformArgs = urlParts[i].split(',');
        transform = {};
        for (j = 0; j < transformArgs.length; j++) {
            transform[transformArgs[j].split('_')[0]] =
                transformArgs[j].split('_')[1];
        }
        transforms.push(transform);
    }

    return [filename, transforms];
}

// tinymce
var editor_config = {
    path_absolute: "{{ URL::to('/') }}",
    height: '35em',
    selector: '#ckeditor',
    plugins: ['advlist autolink lists link image charmap print preview hr anchor pagebreak',
        'searchreplace wordcount visualblocks visualchars code fullscreen',
        'insertdatetime media nonbreaking save table contextmenu directionality',
        'emoticons template paste textcolor colorpicker textpattern'],
    toolbar1: 'formatselect fontselect fontsizeselect | bold italic underline strikethrough subscript superscript | forecolor backcolor | link | alignleft aligncenter alignright ' +
    'alignjustify  | removeformat',
    toolbar2: 'undo redo | bullist numlist | outdent indent blockquote | link unlink anchor image media code | print fullscreen',
    relative_urls: false,
    /*textcolor_map: [
        "000000", "Black",
        "993300", "Burnt orange",
        "333300", "Dark olive",
        "003300", "Dark green",
        "003366", "Dark azure",
        "000080", "Navy Blue",
        "333399", "Indigo",
        "333333", "Very dark gray",
        "800000", "Maroon",
        "FF6600", "Orange",
        "808000", "Olive",
        "008000", "Green",
        "008080", "Teal",
        "0000FF", "Blue",
        "666699", "Grayish blue",
        "808080", "Gray",
        "FF0000", "Red",
        "FF9900", "Amber",
        "99CC00", "Yellow green",
        "339966", "Sea green",
        "33CCCC", "Turquoise",
        "3366FF", "Royal blue",
        "800080", "Purple",
        "999999", "Medium gray",
        "FF00FF", "Magenta",
        "FFCC00", "Gold",
        "FFFF00", "Yellow",
        "00FF00", "Lime",
        "00FFFF", "Aqua",
        "00CCFF", "Sky blue",
        "993366", "Red violet",
        "FFFFFF", "White",
        "FF99CC", "Pink",
        "FFCC99", "Peach",
        "FFFF99", "Light yellow",
        "CCFFCC", "Pale green",
        "CCFFFF", "Pale cyan",
        "99CCFF", "Light sky blue",
        "CC99FF", "Plum",
        "e14d43", "Color 1 Name",
        "d83131", "Color 2 Name",
        "ed1c24", "Color 3 Name",
        "f99b1c", "Color 4 Name",
        "50b848", "Color 5 Name",
        "00a859", "Color 6 Name",
        "00aae7", "Color 7 Name",
        "282828", "Color 8 Name"
    ],
    textcolor_rows: "6",*/
    file_browser_callback : function(field_name, url, type, win) {
        var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
        var y = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;

        var cmsURL = editor_config.path_absolute + '/laravel-filemanager?field_name=' + field_name;
        if (type == 'image') {
            cmsURL = cmsURL + '&type=Images';
        } else {
            cmsURL = cmsURL + '&type=Files';
        }

        tinyMCE.activeEditor.windowManager.open({
            file : cmsURL,
            title : 'Filemanager',
            width : x * 0.8,
            height : y * 0.8,
            resizable : 'yes',
            close_previous : 'no'
        });
    }
};
tinymce.init(editor_config);

</script>
@stop
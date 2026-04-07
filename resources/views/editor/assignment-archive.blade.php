@extends('editor.layout')

@section('title')
<title>Tidligere oppgaver &rsaquo; Forfatterskolen Redaktørportal</title>
@stop

@section('styles')
<style>
    .arch-wrapper { max-width: 1100px; margin: 0 auto; padding: 0 16px; }

    .arch-header {
        background: linear-gradient(135deg, #2C3E50 0%, #1a252f 100%);
        border-radius: 12px;
        padding: 24px 28px;
        color: #fff;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }
    .arch-header h2 { font-size: 1.3rem; font-weight: 700; margin: 0 0 4px; }
    .arch-header p { font-size: 0.82rem; opacity: 0.7; margin: 0; }
    .arch-stats { display: flex; gap: 12px; }
    .arch-stat {
        background: rgba(255,255,255,0.12);
        border-radius: 8px;
        padding: 10px 16px;
        text-align: center;
        min-width: 70px;
    }
    .arch-stat__num { font-size: 1.4rem; font-weight: 700; line-height: 1; }
    .arch-stat__label { font-size: 0.6rem; opacity: 0.7; text-transform: uppercase; letter-spacing: 0.5px; }

    .arch-section {
        background: #fff;
        border: 1px solid rgba(0,0,0,0.08);
        border-radius: 10px;
        margin-bottom: 16px;
        overflow: hidden;
    }
    .arch-section__header {
        padding: 14px 20px;
        border-bottom: 1px solid rgba(0,0,0,0.06);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 8px;
    }
    .arch-section__title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1a1a1a;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .arch-section__count {
        background: #862736;
        color: #fff;
        font-size: 0.65rem;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 10px;
    }
    .arch-section__search {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .arch-section__search input {
        border: 1px solid rgba(0,0,0,0.12);
        border-radius: 6px;
        padding: 6px 10px;
        font-size: 0.8rem;
        width: 140px;
    }
    .arch-section__search button {
        background: #f5f3f0;
        border: 1px solid rgba(0,0,0,0.12);
        border-radius: 6px;
        padding: 6px 10px;
        cursor: pointer;
        font-size: 0.8rem;
    }
    .arch-section__search button:hover { background: #e8e4de; }

    .arch-table { width: 100%; border-collapse: collapse; }
    .arch-table th {
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #8a8580;
        padding: 10px 16px;
        border-bottom: 1px solid rgba(0,0,0,0.06);
        text-align: left;
    }
    .arch-table td {
        padding: 10px 16px;
        font-size: 0.85rem;
        color: #1a1a1a;
        border-bottom: 1px solid rgba(0,0,0,0.04);
    }
    .arch-table tbody tr:hover { background: #faf8f5; }
    .arch-table a { color: #862736; text-decoration: none; font-weight: 500; }
    .arch-table a:hover { text-decoration: underline; }

    .arch-pagination { padding: 12px 16px; text-align: right; }

    .arch-empty { text-align: center; padding: 32px; color: #8a8580; font-size: 0.85rem; }

    .arch-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

    @media (max-width: 768px) {
        .arch-grid { grid-template-columns: 1fr; }
        .arch-stats { flex-wrap: wrap; }
    }
</style>
@stop

@section('content')
@php($cacheBuster = now()->timestamp)
<div class="arch-wrapper">

    <div class="arch-header">
        <div>
            <h2><i class="fa fa-archive"></i> Tidligere oppgaver</h2>
            <p>Arkiv over ferdige tilbakemeldinger og coaching-timer.</p>
        </div>
        <div class="arch-stats">
            <div class="arch-stat">
                <div class="arch-stat__num">{{ $assignedAssignmentManuscripts->total() }}</div>
                <div class="arch-stat__label">Personlige</div>
            </div>
            <div class="arch-stat">
                <div class="arch-stat__num">{{ $assigned_shop_manuscripts->total() }}</div>
                <div class="arch-stat__label">Manus</div>
            </div>
            <div class="arch-stat">
                <div class="arch-stat__num">{{ $assignedAssignments->total() }}</div>
                <div class="arch-stat__label">Kurs</div>
            </div>
            <div class="arch-stat">
                <div class="arch-stat__num">{{ $coachingTimers->total() }}</div>
                <div class="arch-stat__label">Coaching</div>
            </div>
        </div>
    </div>

    <div class="arch-grid">
        {{-- PERSONLIGE OPPGAVER --}}
        <div class="arch-section">
            <div class="arch-section__header">
                <div class="arch-section__title">
                    <i class="fa fa-user"></i> {{ trans('site.personal-assignment') }}
                    <span class="arch-section__count">{{ $assignedAssignmentManuscripts->total() }}</span>
                </div>
                <form class="arch-section__search" method="get">
                    <input type="text" name="search_personal_assignment" placeholder="Elevnr..." value="{{ request('search_personal_assignment') }}">
                    <button type="submit"><i class="fa fa-search"></i></button>
                </form>
            </div>
            @if($assignedAssignmentManuscripts->isEmpty())
                <div class="arch-empty">Ingen ferdige oppgaver ennå.</div>
            @else
                <table class="arch-table">
                    <thead><tr><th>Manus</th><th>Sendt</th><th>Elev</th><th></th></tr></thead>
                    <tbody>
                    @foreach($assignedAssignmentManuscripts as $m)
                        <tr>
                            <td><a href="{{ $m->filename }}?v={{ $cacheBuster }}" download><i class="fa fa-download"></i></a> {{ basename($m->filename) }}</td>
                            <td style="font-size:0.8rem;color:#8a8580;">{{ $m->noGroupFeedbacks->first()?->updated_at?->format('d.m.Y') ?? '—' }}</td>
                            <td>{{ $m->user->id }}</td>
                            <td>
                                @if($m->noGroupFeedbacks->first())
                                    <button class="btn btn-primary btn-xs personalAssignmentShowFeedbackBtn" data-toggle="modal" data-target="#personalAssignmentShowFeedbackModal"
                                        data-feedback_file="{{ $m->noGroupFeedbacks->first()?->filename }}"
                                        data-feedback_date="{{ $m->noGroupFeedbacks->first()?->created_at }}"
                                        data-feedback_grade="{{ $m->grade }}">
                                        <i class="fa fa-eye"></i> Vis
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="arch-pagination">{{ $assignedAssignmentManuscripts->appends(request()->query())->links() }}</div>
            @endif
        </div>

        {{-- MANUSUTVIKLING --}}
        <div class="arch-section">
            <div class="arch-section__header">
                <div class="arch-section__title">
                    <i class="fa fa-book"></i> {{ trans_choice('site.shop-manuscripts', 2) }}
                    <span class="arch-section__count">{{ $assigned_shop_manuscripts->total() }}</span>
                </div>
                <form class="arch-section__search" method="get">
                    <input type="text" name="search_shop_manuscript" placeholder="Elevnr..." value="{{ request('search_shop_manuscript') }}">
                    <button type="submit"><i class="fa fa-search"></i></button>
                </form>
            </div>
            @if($assigned_shop_manuscripts->isEmpty())
                <div class="arch-empty">Ingen ferdige manusbestillinger.</div>
            @else
                <table class="arch-table">
                    <thead><tr><th>Manus</th><th>Sendt</th><th>Sjanger</th><th>Elev</th><th></th></tr></thead>
                    <tbody>
                    @foreach($assigned_shop_manuscripts as $m)
                        @if($m->status != 'Started' && $m->status != 'Pending')
                        <tr>
                            <td><a href="{{ route('editor.backend.download_shop_manuscript', ['id' => $m->id, 'v' => $cacheBuster]) }}"><i class="fa fa-download"></i></a> {{ $m->shop_manuscript->title }}</td>
                            <td style="font-size:0.8rem;color:#8a8580;">{{ $m->feedbacks->first()?->updated_at?->format('d.m.Y') ?? '—' }}</td>
                            <td>@if($m->genre > 0) {{ \App\Http\FrontendHelpers::assignmentType($m->genre) }} @endif</td>
                            <td>{{ $m->user->id }}</td>
                            <td>
                                @if($m->feedbacks->first())
                                    <button class="btn btn-primary btn-xs shopManuscriptShowFeedbackBtn" data-toggle="modal" data-target="#shopManuscriptShowFeedbackModal"
                                        data-feedback_file="{{ implode(',', $m->feedbacks->first()?->filename ?? []) }}"
                                        data-feedback_notes="{{ $m->feedbacks->first()?->notes }}"
                                        data-feedback_grade="{{ $m->grade }}"
                                        data-feedback_created_at="{{ $m->feedbacks->first()?->created_at }}">
                                        <i class="fa fa-eye"></i> Vis
                                    </button>
                                @endif
                            </td>
                        </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
                <div class="arch-pagination">{{ $assigned_shop_manuscripts->appends(request()->query())->links() }}</div>
            @endif
        </div>

        {{-- KURSOPPGAVER --}}
        <div class="arch-section">
            <div class="arch-section__header">
                <div class="arch-section__title">
                    <i class="fa fa-graduation-cap"></i> {{ trans('site.my-assignments') }}
                    <span class="arch-section__count">{{ $assignedAssignments->total() }}</span>
                </div>
                <form class="arch-section__search" method="get">
                    <input type="text" name="search_my_assignments" placeholder="Elevnr..." value="{{ request('search_my_assignments') }}">
                    <button type="submit"><i class="fa fa-search"></i></button>
                </form>
            </div>
            @if($assignedAssignments->isEmpty())
                <div class="arch-empty">Ingen ferdige kursoppgaver.</div>
            @else
                <table class="arch-table">
                    <thead><tr><th>Kurs</th><th>Sendt</th><th>Elev</th><th></th></tr></thead>
                    <tbody>
                    @foreach($assignedAssignments as $m)
                        <?php
                            $groupDetails = DB::select("SELECT A.id as assignment_group_id, B.id AS assignment_group_learner_id FROM assignment_groups A JOIN assignment_group_learners B ON A.id = B.assignment_group_id AND B.user_id = {$m->user_id} WHERE A.assignment_id = {$m->assignment_id}");
                            $feedback = $groupDetails ? DB::select("SELECT A.* FROM assignment_feedbacks A JOIN assignment_group_learners B ON A.assignment_group_learner_id = B.id WHERE B.user_id = {$m->user_id} AND A.assignment_group_learner_id = " . $groupDetails[0]->assignment_group_learner_id . " AND A.is_admin = 1") : null;
                        ?>
                        <tr>
                            <td><a href="{{ route('editor.backend.download_assigned_manuscript', ['id' => $m->id, 'v' => $cacheBuster]) }}"><i class="fa fa-download"></i></a> {{ $m->assignment->course->title ?? $m->assignment->title }}</td>
                            <td style="font-size:0.8rem;color:#8a8580;">
                                @if($groupDetails && $feedback)
                                    {{ \Carbon\Carbon::parse($feedback[0]->updated_at)->format('d.m.Y') }}
                                @else
                                    {{ $m->noGroupFeedbacks->first()?->updated_at?->format('d.m.Y') ?? '—' }}
                                @endif
                            </td>
                            <td>{{ $m->user_id }}</td>
                            <td>
                                @if($groupDetails && $feedback)
                                    <button class="btn btn-primary btn-xs courseAssignmentShowFeedbackBtn" data-toggle="modal" data-target="#courseAssignmentShowFeedbackModal"
                                        data-feedback_file="{{ $feedback[0]->filename }}"
                                        data-feedback_grade="{{ $m->grade }}"
                                        data-feedback_created_at="{{ $feedback[0]->created_at }}">
                                        <i class="fa fa-eye"></i> Vis
                                    </button>
                                @elseif($m->noGroupFeedbacks->first())
                                    <button class="btn btn-primary btn-xs personalAssignmentShowFeedbackBtn" data-toggle="modal" data-target="#personalAssignmentShowFeedbackModal"
                                        data-feedback_file="{{ $m->noGroupFeedbacks->first()?->filename }}"
                                        data-feedback_grade="{{ $m->grade }}"
                                        data-feedback_date="{{ $m->noGroupFeedbacks->first()?->created_at }}">
                                        <i class="fa fa-eye"></i> Vis
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="arch-pagination">{{ $assignedAssignments->appends(request()->query())->links() }}</div>
            @endif
        </div>

        {{-- COACHING --}}
        <div class="arch-section">
            <div class="arch-section__header">
                <div class="arch-section__title">
                    <i class="fa fa-comments"></i> {{ trans('site.my-coaching-timer') }}
                    <span class="arch-section__count">{{ $coachingTimers->total() }}</span>
                </div>
                <form class="arch-section__search" method="get">
                    <input type="text" name="search_coaching_timer" placeholder="Elevnr..." value="{{ request('search_coaching_timer') }}">
                    <button type="submit"><i class="fa fa-search"></i></button>
                </form>
            </div>
            @if($coachingTimers->isEmpty())
                <div class="arch-empty">Ingen ferdige coaching-timer.</div>
            @else
                <table class="arch-table">
                    <thead><tr><th>Elev</th><th>Godkjent</th><th>Varighet</th><th></th></tr></thead>
                    <tbody>
                    @foreach($coachingTimers as $ct)
                        <tr>
                            <td><a href="{{ $ct->file }}?v={{ $cacheBuster }}" download><i class="fa fa-download"></i></a> {{ $ct->user->id }}</td>
                            <td style="font-size:0.8rem;color:#8a8580;">{{ $ct->approved_date ? \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($ct->approved_date) : '—' }}</td>
                            <td>{{ \App\Http\FrontendHelpers::getCoachingTimerPlanType($ct->plan_type) }}</td>
                            <td>
                                <button class="btn btn-primary btn-xs coachingTimerFeedbackBtn" data-toggle="modal" data-target="#coachingTimerFeedbackModal"
                                    data-replay_link="{{ $ct->replay_link }}"
                                    data-comment="{{ $ct->comment }}"
                                    data-document="{{ $ct->document }}">
                                    <i class="fa fa-eye"></i> Vis
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="arch-pagination">{{ $coachingTimers->appends(request()->query())->links() }}</div>
            @endif
        </div>
    </div>

    @if($corrections->count() || $copyEditings->count())
    <div class="arch-grid">
        {{-- KORREKTUR --}}
        <div class="arch-section">
            <div class="arch-section__header">
                <div class="arch-section__title">
                    <i class="fa fa-check-circle"></i> {{ trans('site.my-correction') }}
                    <span class="arch-section__count">{{ $corrections->total() }}</span>
                </div>
            </div>
            @if($corrections->isEmpty())
                <div class="arch-empty">Ingen ferdige korrekturoppgaver.</div>
            @else
                <table class="arch-table">
                    <thead><tr><th>Manus</th><th>Sendt</th><th>Elev</th><th></th></tr></thead>
                    <tbody>
                    @foreach($corrections as $c)
                        <tr>
                            <td><a href="{{ route('editor.other-service.download-doc', ['id' => $c->id, 'type' => 2, 'v' => $cacheBuster]) }}"><i class="fa fa-download"></i></a> {{ basename($c->file) }}</td>
                            <td style="font-size:0.8rem;color:#8a8580;">{{ $c->feedback?->created_at?->format('d.m.Y') ?? '—' }}</td>
                            <td>{{ $c->user->id }}</td>
                            <td>
                                @if($c->feedback)
                                    <button class="btn btn-primary btn-xs approveOtherServiceFeedbackBtn" data-toggle="modal" data-target="#approveOtherServiceFeedbackModal"
                                        data-feedback_file="{{ $c->feedback->manuscript }}"
                                        data-created_at="{{ $c->feedback->created_at }}">
                                        <i class="fa fa-eye"></i> Vis
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="arch-pagination">{{ $corrections->appends(request()->query())->links() }}</div>
            @endif
        </div>

        {{-- SPRÅKVASK --}}
        <div class="arch-section">
            <div class="arch-section__header">
                <div class="arch-section__title">
                    <i class="fa fa-magic"></i> {{ trans('site.my-copy-editing') }}
                    <span class="arch-section__count">{{ $copyEditings->total() }}</span>
                </div>
            </div>
            @if($copyEditings->isEmpty())
                <div class="arch-empty">Ingen ferdige språkvaskoppgaver.</div>
            @else
                <table class="arch-table">
                    <thead><tr><th>Manus</th><th>Sendt</th><th>Elev</th><th></th></tr></thead>
                    <tbody>
                    @foreach($copyEditings as $ce)
                        <tr>
                            <td><a href="{{ route('editor.other-service.download-doc', ['id' => $ce->id, 'type' => 1, 'v' => $cacheBuster]) }}"><i class="fa fa-download"></i></a> {{ basename($ce->file) }}</td>
                            <td style="font-size:0.8rem;color:#8a8580;">{{ $ce->feedback?->created_at?->format('d.m.Y') ?? '—' }}</td>
                            <td>{{ $ce->user->id }}</td>
                            <td>
                                @if($ce->feedback)
                                    <button class="btn btn-primary btn-xs approveOtherServiceFeedbackBtn" data-toggle="modal" data-target="#approveOtherServiceFeedbackModal"
                                        data-feedback_file="{{ $ce->feedback->manuscript }}"
                                        data-created_at="{{ $ce->feedback->created_at }}">
                                        <i class="fa fa-eye"></i> Vis
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="arch-pagination">{{ $copyEditings->appends(request()->query())->links() }}</div>
            @endif
        </div>
    </div>
    @endif
</div>

{{-- MODALS --}}
<div id="personalAssignmentShowFeedbackModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Tilbakemeldingsdetaljer</h4></div>
        <div class="modal-body">
            <div class="form-group"><label>Dato</label><br><p id="feedback_date"></p></div>
            <div class="form-group"><label>Fil</label><br><div id="feedbackFileAppend"></div></div>
            <div class="form-group"><label>Karakter</label><br><p id="feedback_grade"></p></div>
        </div>
    </div></div>
</div>

<div id="shopManuscriptShowFeedbackModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Tilbakemeldingsdetaljer</h4></div>
        <div class="modal-body">
            <div class="form-group"><label>Dato</label><br><p id="created_at"></p></div>
            <div class="form-group"><label>Fil</label><br><div id="feedbackFileAppend"></div></div>
            <div class="form-group"><label>Karakter</label><br><p id="grade"></p></div>
            <div class="form-group"><label>Notater</label><br><p id="notes"></p></div>
        </div>
    </div></div>
</div>

<div id="courseAssignmentShowFeedbackModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Tilbakemeldingsdetaljer</h4></div>
        <div class="modal-body">
            <div class="form-group"><label>Dato</label><br><p id="created_at"></p></div>
            <div class="form-group"><label>Fil</label><br><div id="feedbackFileAppend"></div></div>
            <div class="form-group"><label>Karakter</label><br><p id="grade"></p></div>
        </div>
    </div></div>
</div>

<div id="coachingTimerFeedbackModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Coaching-detaljer</h4></div>
        <div class="modal-body">
            <div class="form-group"><label>Repriselenke</label><br><a href="" id="replay_link"></a></div>
            <div class="form-group"><label>Dokument</label><br><a href="" name="document" download></a></div>
            <div class="form-group"><label>Kommentar</label><br><p id="comment"></p></div>
        </div>
    </div></div>
</div>

<div id="approveOtherServiceFeedbackModal" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title">Tilbakemeldingsdetaljer</h4></div>
        <div class="modal-body">
            <div class="form-group"><label>Dato</label><br><p id="created_at"></p></div>
            <div class="form-group"><label>Manus</label><br><div id="feedbackFileAppend"></div></div>
        </div>
    </div></div>
</div>
@stop

@section('scripts')
<script>
    var cacheBuster = '{{ $cacheBuster }}';

    function showFeedbackFiles(modal, files) {
        var el = modal.find('#feedbackFileAppend');
        el.html('');
        files.split(',').forEach(function(f) {
            if (f.trim()) el.append('<a href="' + f.trim() + '?v=' + cacheBuster + '" download style="display:block;margin:2px 0;"><i class="fa fa-download"></i> ' + f.trim().split('/').pop() + '</a>');
        });
    }

    $('.personalAssignmentShowFeedbackBtn').click(function(){
        var modal = $('#personalAssignmentShowFeedbackModal');
        showFeedbackFiles(modal, $(this).data('feedback_file'));
        modal.find('#feedback_date').text($(this).data('feedback_date'));
        modal.find('#feedback_grade').text($(this).data('feedback_grade'));
    });

    $('.shopManuscriptShowFeedbackBtn').click(function(){
        var modal = $('#shopManuscriptShowFeedbackModal');
        showFeedbackFiles(modal, $(this).data('feedback_file'));
        modal.find('#notes').text($(this).data('feedback_notes'));
        modal.find('#grade').text($(this).data('feedback_grade'));
        modal.find('#created_at').text($(this).data('feedback_created_at'));
    });

    $('.courseAssignmentShowFeedbackBtn').click(function(){
        var modal = $('#courseAssignmentShowFeedbackModal');
        showFeedbackFiles(modal, $(this).data('feedback_file'));
        modal.find('#grade').text($(this).data('feedback_grade'));
        modal.find('#created_at').text($(this).data('feedback_created_at'));
    });

    $('.coachingTimerFeedbackBtn').click(function(){
        var modal = $('#coachingTimerFeedbackModal');
        var doc = $(this).data('document');
        modal.find('[name=document]').attr('href', doc + '?v=' + cacheBuster).text(doc);
        modal.find('#comment').text($(this).data('comment'));
        modal.find('#replay_link').text($(this).data('replay_link')).attr('href', $(this).data('replay_link'));
    });

    $('.approveOtherServiceFeedbackBtn').click(function(){
        var modal = $('#approveOtherServiceFeedbackModal');
        showFeedbackFiles(modal, $(this).data('feedback_file'));
        modal.find('#created_at').text($(this).data('created_at'));
    });
</script>
@stop

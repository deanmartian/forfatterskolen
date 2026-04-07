{{-- Stats overview --}}
<div class="row" style="margin: 20px 0;">
    <div class="col-sm-3">
        <div style="background:#fff;border-radius:6px;padding:20px;text-align:center;border-left:4px solid #e65100;box-shadow:0 1px 3px rgba(0,0,0,0.08);">
            <div style="font-size:28px;font-weight:700;color:#e65100;">{{ $unassignedAssignmentManuscripts->count() + $unassignedShopManuscripts->count() }}</div>
            <div style="font-size:12px;color:#888;text-transform:uppercase;font-weight:600;">Ikke tildelt</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div style="background:#fff;border-radius:6px;padding:20px;text-align:center;border-left:4px solid #1565c0;box-shadow:0 1px 3px rgba(0,0,0,0.08);">
            <div style="font-size:28px;font-weight:700;color:#1565c0;">{{ $unfinishedAssignments->count() }}</div>
            <div style="font-size:12px;color:#888;text-transform:uppercase;font-weight:600;">Oppgaver under arbeid</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div style="background:#fff;border-radius:6px;padding:20px;text-align:center;border-left:4px solid #862736;box-shadow:0 1px 3px rgba(0,0,0,0.08);">
            <div style="font-size:28px;font-weight:700;color:#862736;">{{ $unfinishedShopManuscripts->count() }}</div>
            <div style="font-size:12px;color:#888;text-transform:uppercase;font-weight:600;">Manusutvikling under arbeid</div>
        </div>
    </div>
    <div class="col-sm-3">
        <div style="background:#fff;border-radius:6px;padding:20px;text-align:center;border-left:4px solid #2e7d32;box-shadow:0 1px 3px rgba(0,0,0,0.08);">
            <div style="font-size:28px;font-weight:700;color:#2e7d32;">{{ $unassignedAssignmentManuscripts->count() + $unassignedShopManuscripts->count() + $unfinishedAssignments->count() + $unfinishedShopManuscripts->count() }}</div>
            <div style="font-size:12px;color:#888;text-transform:uppercase;font-weight:600;">Totalt</div>
        </div>
    </div>
</div>

{{-- Unassigned assignments --}}
@if($unassignedAssignmentManuscripts->count())
<div class="panel panel-default" style="border-top:3px solid #e65100;">
    <div class="panel-heading" style="background:#fff3e0;">
        <h4 style="margin:0;color:#e65100;"><i class="fa fa-exclamation-triangle"></i> Ikke-tildelte oppgaver ({{ $unassignedAssignmentManuscripts->count() }})</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table dt-table">
                <thead>
                    <tr>
                        <th>Oppgave</th>
                        <th>Manus</th>
                        <th>Elev</th>
                        <th>Type</th>
                        <th>Sted i manus</th>
                        <th>Ord</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unassignedAssignmentManuscripts as $m)
                        <tr>
                            <td>
                                @if ($m->assignment->course_id)
                                    <a href="{{ route('admin.assignment.show', ['course_id' => $m->assignment->course_id, 'assignment' => $m->assignment_id]) }}">{{ $m->assignment->title }}</a>
                                @else
                                    <a href="{{ route('admin.learner.assignment', ['user_id' => $m->user_id, 'id' => $m->assignment_id]) }}">{{ $m->assignment->title }}</a>
                                @endif
                            </td>
                            <td>{!! $m->file_link !!}</td>
                            <td><a href="{{ route('admin.learner.show', $m->user->id) }}">{{ $m->user->full_name }}</a></td>
                            <td>{{ $m->assignment_type }}</td>
                            <td>{{ $m->where_in_script }}</td>
                            <td>{{ $m->words }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- Unassigned shop manuscripts --}}
@if($unassignedShopManuscripts->count())
<div class="panel panel-default" style="border-top:3px solid #e65100;">
    <div class="panel-heading" style="background:#fff3e0;">
        <h4 style="margin:0;color:#e65100;"><i class="fa fa-exclamation-triangle"></i> Ikke-tildelte manusbestillinger ({{ $unassignedShopManuscripts->count() }})</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table dt-table">
                <thead>
                    <tr>
                        <th>Manus</th>
                        <th>Elev</th>
                        <th>Sjanger</th>
                        <th>Ord</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unassignedShopManuscripts as $m)
                        <tr>
                            <td><a href="{{ route('shop_manuscript_taken', ['id' => $m->user->id, 'shop_manuscript_taken_id' => $m->id]) }}">{{ $m->shop_manuscript->title }}</a></td>
                            <td><a href="{{ route('admin.learner.show', $m->user->id) }}">{{ $m->user->full_name }}</a></td>
                            <td>@if($m->genre > 0) {{ \App\Http\FrontendHelpers::assignmentType($m->genre) }} @endif</td>
                            <td>{{ $m->words }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- Unfinished assignments --}}
<div class="panel panel-default" style="border-top:3px solid #1565c0;">
    <div class="panel-heading" style="background:#e3f2fd;">
        <h4 style="margin:0;color:#1565c0;"><i class="fa fa-pencil"></i> Oppgaver under arbeid ({{ $unfinishedAssignments->count() }})</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table dt-table">
                <thead>
                    <tr>
                        <th>Oppgave</th>
                        <th>Manus</th>
                        <th>Elev</th>
                        <th>Frist</th>
                        <th>Redaktørfrist</th>
                        <th>Redaktør</th>
                        <th>Type</th>
                        <th>Ord</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unfinishedAssignments as $m)
                        <tr>
                            <td>
                                @if ($m->assignment->course_id)
                                    <a href="{{ route('admin.assignment.show', ['course_id' => $m->assignment->course_id, 'assignment' => $m->assignment_id]) }}">{{ $m->assignment->title }}</a>
                                @else
                                    <a href="{{ route('admin.learner.assignment', ['user_id' => $m->user_id, 'id' => $m->assignment_id]) }}">{{ $m->assignment->title }}</a>
                                @endif
                            </td>
                            <td>{!! $m->file_link !!}</td>
                            <td><a href="{{ route('admin.learner.show', $m->user->id) }}">{{ $m->user->full_name }}</a></td>
                            <td>{{ $m->expected_finish }}</td>
                            <td>{{ $m->editor_expected_finish }}</td>
                            <td>
                                @if($m->editor)
                                    {{ $m->editor->full_name }}
                                @else
                                    <em style="color:#e65100;">Ikke tildelt</em>
                                @endif
                            </td>
                            <td>{{ $m->assignment_type }}</td>
                            <td>{{ $m->words }}</td>
                            <td>
                                <button class="btn btn-success btn-xs finishAssignmentManuscriptBtn" data-toggle="modal"
                                data-target="#finishAssignmentManuscriptModal"
                                data-action="{{ route('admin.assignment-manuscript.mark-finished', $m->id) }}">
                                    <i class="fa fa-check"></i> Ferdig
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Unfinished shop manuscripts --}}
<div class="panel panel-default" style="border-top:3px solid #862736;">
    <div class="panel-heading" style="background:#fce8ea;">
        <h4 style="margin:0;color:#862736;"><i class="fa fa-book"></i> Manusutvikling under arbeid ({{ $unfinishedShopManuscripts->count() }})</h4>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table dt-table">
                <thead>
                    <tr>
                        <th>Manus</th>
                        <th>Elev</th>
                        <th>Redaktør</th>
                        <th>Sjanger</th>
                        <th>Ord</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unfinishedShopManuscripts as $m)
                        <tr>
                            <td><a href="{{ route('shop_manuscript_taken', ['id' => $m->user->id, 'shop_manuscript_taken_id' => $m->id]) }}">{{ $m->shop_manuscript->title }}</a></td>
                            <td><a href="{{ route('admin.learner.show', $m->user->id) }}">{{ $m->user->full_name }}</a></td>
                            <td>
                                @if($m->admin)
                                    {{ $m->admin->full_name }}
                                @else
                                    <em style="color:#e65100;">Ikke tildelt</em>
                                @endif
                            </td>
                            <td>@if($m->genre > 0) {{ \App\Http\FrontendHelpers::assignmentType($m->genre) }} @endif</td>
                            <td>{{ $m->words }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

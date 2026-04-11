@extends('backend.layout')
@section('page_title', 'Bemanningsplan — ' . $course->title)

@section('content')
<div class="page-toolbar">
    <h3>{{ $course->title }}</h3>
</div>

<div class="col-md-12">
    @include('backend.partials.course_submenu')

    <div class="col-sm-12 col-md-10 sub-right-content">
        @if(session('message'))
            <div class="alert alert-{{ session('alert_type', 'success') }}">{{ session('message') }}</div>
        @endif

        @php
        try {
            $staff = \App\Models\CourseStaff::where('course_id', $course->id)->with(['staff', 'student'])->get();
            $editors = \App\User::where('role', 3)->orderBy('first_name')->get();
            $admins = \App\User::where('role', 1)->orderBy('first_name')->get();
            $allStaff = $editors->merge($admins)->sortBy('first_name');

            $courseLeaders = $staff->where('role', 'course_leader');
            $mentors = $staff->where('role', 'mentor');
            $guestEditors = $staff->where('role', 'guest_editor');
            $editorAssignments = $staff->where('role', 'editor');

            // Elever på kurset
            try {
                $learners = \App\CoursesTaken::whereHas('package', fn($q) => $q->where('course_id', $course->id))
                    ->with('user')
                    ->get()
                    ->map(fn($ct) => $ct->user)
                    ->filter()
                    ->unique('id')
                    ->sortBy('first_name');
            } catch (\Exception $e) {
                $learners = collect();
            }

            // Redaktør per elev
            $editorMap = $editorAssignments->pluck('user_id', 'student_user_id');
        } catch (\Exception $e) {
            $staff = collect(); $allStaff = collect(); $courseLeaders = collect();
            $mentors = collect(); $guestEditors = collect(); $editorAssignments = collect();
            $learners = collect(); $editorMap = collect();
            \Log::error('Bemanningsplan feil: ' . $e->getMessage());
        }
        @endphp

        {{-- Roller --}}
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><i class="fa fa-users"></i> Kursroller</strong>
                <button class="btn btn-xs btn-success pull-right" data-toggle="modal" data-target="#addRoleModal"><i class="fa fa-plus"></i> Legg til</button>
            </div>
            <table class="table table-condensed" style="margin:0;">
                <thead>
                    <tr><th>Rolle</th><th>Navn</th><th>Periode</th><th>Notater</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach($courseLeaders->merge($mentors)->merge($guestEditors) as $s)
                        <tr>
                            <td><span class="label label-{{ $s->role === 'course_leader' ? 'primary' : ($s->role === 'mentor' ? 'info' : 'warning') }}">{{ \App\Models\CourseStaff::roleLabel($s->role) }}</span></td>
                            <td><strong>{{ $s->staff->first_name ?? '?' }} {{ $s->staff->last_name ?? '' }}</strong></td>
                            <td>{{ $s->start_date?->format('d.m.Y') ?? '' }} {{ $s->end_date ? '→ ' . $s->end_date->format('d.m.Y') : '' }}</td>
                            <td><small>{{ $s->notes }}</small></td>
                            <td>
                                <form method="POST" action="{{ route('admin.course.staff.delete', [$course->id, $s->id]) }}" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('Fjerne?')"><i class="fa fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    @if($courseLeaders->merge($mentors)->merge($guestEditors)->isEmpty())
                        <tr><td colspan="5" class="text-muted text-center">Ingen roller tildelt ennå.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        {{-- Webinar-plan --}}
        @php
            try {
                $webinars = \App\Webinar::where('course_id', $course->id)->orderBy('start_date')->get();
                $webinarHosts = $staff->where('role', 'webinar_host');
                $webinarHostMap = $webinarHosts->pluck('user_id', 'webinar_id');
            } catch (\Exception $e) {
                $webinars = collect();
                $webinarHostMap = collect();
            }
        @endphp
        @if($webinars->count() > 0)
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><i class="fa fa-play-circle"></i> Webinar-plan ({{ $webinars->count() }} webinarer)</strong>
                <span class="pull-right text-muted" style="font-size:12px;">
                    {{ $webinarHostMap->count() }} av {{ $webinars->count() }} tildelt
                </span>
            </div>
            <div class="panel-body" style="padding:0;max-height:500px;overflow-y:auto;">
                <table class="table table-condensed table-striped" style="margin:0;">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Dato</th>
                            <th>Dag</th>
                            <th>Kl.</th>
                            <th>Tittel</th>
                            <th>Vert</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($webinars as $i => $w)
                            @php
                                $wDate = \Carbon\Carbon::parse($w->start_date);
                                $isPast = $wDate->isPast();
                                $isThisWeek = $wDate->isCurrentWeek();
                                $hostId = $webinarHostMap[$w->id] ?? null;
                                $days = ['søndag','mandag','tirsdag','onsdag','torsdag','fredag','lørdag'];
                            @endphp
                            <tr style="{{ $isPast ? 'opacity:0.5;' : '' }}{{ $isThisWeek ? 'background:#fff8e1;' : '' }}">
                                <td><small class="text-muted">{{ $i + 1 }}</small></td>
                                <td><strong>{{ $wDate->format('d.m.Y') }}</strong></td>
                                <td>{{ ucfirst($days[$wDate->dayOfWeek]) }}</td>
                                <td>{{ $wDate->format('H:i') }}</td>
                                <td>{{ $w->title }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.course.staff.assign-webinar-host', $course->id) }}" style="display:inline;">
                                        @csrf
                                        <input type="hidden" name="webinar_id" value="{{ $w->id }}">
                                        <select name="host_id" class="input-sm" onchange="this.form.submit()" style="padding:2px;font-size:11px;width:140px;{{ $hostId ? 'color:#22c55e;font-weight:bold;' : 'color:#999;' }}">
                                            <option value="">Ikke tildelt</option>
                                            @foreach($allStaff as $e)
                                                <option value="{{ $e->id }}" {{ $hostId == $e->id ? 'selected' : '' }}>{{ $e->first_name }} {{ $e->last_name }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="panel-footer" style="font-size:12px;">
                <form method="POST" action="{{ route('admin.course.staff.bulk-assign-webinar', $course->id) }}" class="form-inline" style="display:inline;">
                    @csrf
                    <select name="host_id" class="input-sm" style="padding:3px;font-size:12px;">
                        <option value="">Velg vert...</option>
                        @foreach($allStaff as $e)
                            <option value="{{ $e->id }}">{{ $e->first_name }} {{ $e->last_name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-xs btn-warning" onclick="return confirm('Tildele alle webinarer uten vert?')">Tildel alle uten vert</button>
                </form>
            </div>
        </div>
        @endif

        {{-- Redaktør per elev --}}
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong><i class="fa fa-pencil"></i> Redaktør-tildeling ({{ $learners->count() }} elever)</strong>
                <div class="pull-right">
                    <form method="POST" action="{{ route('admin.course.staff.bulk-assign', $course->id) }}" class="form-inline" style="display:inline;">
                        @csrf
                        <select name="editor_id" class="input-sm" style="padding:3px;font-size:12px;">
                            <option value="">Velg redaktør...</option>
                            @foreach($allStaff as $e)
                                <option value="{{ $e->id }}">{{ $e->first_name }} {{ $e->last_name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-xs btn-warning" onclick="return confirm('Tildele alle uten redaktør til valgt person?')">Tildel alle uten</button>
                    </form>
                </div>
            </div>
            <table class="table table-condensed table-striped" style="margin:0;">
                <thead>
                    <tr><th>Elev</th><th>E-post</th><th>Redaktør</th><th></th></tr>
                </thead>
                <tbody>
                    @foreach($learners as $learner)
                        @php $assignedEditorId = $editorMap[$learner->id] ?? null; @endphp
                        <tr>
                            <td><a href="{{ route('admin.learner.show', $learner->id) }}">{{ $learner->first_name }} {{ $learner->last_name }}</a></td>
                            <td><small>{{ $learner->email }}</small></td>
                            <td>
                                <form method="POST" action="{{ route('admin.course.staff.assign-editor', $course->id) }}" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="student_user_id" value="{{ $learner->id }}">
                                    <select name="editor_id" class="input-sm" onchange="this.form.submit()" style="padding:3px;font-size:12px;{{ $assignedEditorId ? 'color:#22c55e;font-weight:bold;' : 'color:#999;' }}">
                                        <option value="">Ikke tildelt</option>
                                        @foreach($allStaff as $e)
                                            <option value="{{ $e->id }}" {{ $assignedEditorId == $e->id ? 'selected' : '' }}>{{ $e->first_name }} {{ $e->last_name }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                            <td>
                                @if($assignedEditorId)
                                    <span class="label label-success" style="font-size:10px;">Tildelt</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @if($learners->isEmpty())
                        <tr><td colspan="4" class="text-muted text-center">Ingen elever påmeldt ennå.</td></tr>
                    @endif
                </tbody>
            </table>
            @if($learners->count() > 0)
                <div class="panel-footer" style="font-size:12px;">
                    <strong>Kapasitet:</strong>
                    @php
                        $editorCounts = $editorAssignments->groupBy('user_id')->map->count();
                    @endphp
                    @foreach($editorCounts as $editorId => $count)
                        @php $editor = $allStaff->firstWhere('id', $editorId); @endphp
                        <span class="label label-default" style="margin-right:4px;">{{ $editor->first_name ?? '?' }}: {{ $count }} elever</span>
                    @endforeach
                    @php $unassigned = $learners->count() - $editorAssignments->count(); @endphp
                    @if($unassigned > 0)
                        <span class="label label-danger">{{ $unassigned }} uten redaktør</span>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal: Legg til rolle --}}
<div class="modal fade" id="addRoleModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background:#862736;color:#fff;">
                <button type="button" class="close" data-dismiss="modal" style="color:#fff;">&times;</button>
                <h4 class="modal-title"><i class="fa fa-plus"></i> Legg til kursrolle</h4>
            </div>
            <form method="POST" action="{{ route('admin.course.staff.store', $course->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Rolle</label>
                        <select name="role" class="form-control" required>
                            <option value="course_leader">Kursholder</option>
                            <option value="mentor">Mentor</option>
                            <option value="guest_editor">Gjesteredaktør</option>
                            <option value="webinar_host">Webinar-vert</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Person</label>
                        <select name="user_id" class="form-control" required>
                            @foreach($allStaff as $e)
                                <option value="{{ $e->id }}">{{ $e->first_name }} {{ $e->last_name }} ({{ $e->role == 3 ? 'Redaktør' : 'Admin' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fra dato (valgfritt)</label>
                                <input type="date" name="start_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Til dato (valgfritt)</label>
                                <input type="date" name="end_date" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Notater</label>
                        <input type="text" name="notes" class="form-control" placeholder="F.eks. 'Holder alle onsdags-webinarer'">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Legg til</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Avbryt</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

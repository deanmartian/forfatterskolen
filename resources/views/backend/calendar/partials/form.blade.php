<form method="POST" action="{{Request::is('calendar-note/*/edit')
? route('admin.calendar-note.update', $calendar['id'])
: route('admin.calendar-note.store')}}" enctype="multipart/form-data">
    @if(Request::is('calendar-note/*/edit'))
        {{ method_field('PUT') }}
    @endif
        {{csrf_field()}}

        <div class="col-sm-12">
            @if(Request::is('calendar-note/*/edit'))
                <h3>Edit Note</h3>
            @else
                <h3>Add New Note</h3>
            @endif
        </div>

        <div class="col-sm-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="form-group">
                        <label>Note</label>
                        <textarea name="note" class="form-control" cols="30" rows="10" required>{{ $calendar['note'] }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Course</label>
                        <select class="form-control" name="course_id" required>
                            <option value="" disabled="disabled" selected>Select Course</option>
                            @foreach(\App\Course::all() as $course)
                                <option value="{{ $course->id }}" @if ($calendar['course_id'] == $course->id) selected @endif> {{ $course->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>From Date</label>
                        <input type="date" class="form-control" name="from_date"
                               @if( $calendar['from_date'] ) value="{{ date_format(date_create($calendar['from_date']), 'Y-m-d') }}" @endif
                               required>
                    </div>
                    <div class="form-group">
                        <label>To Date</label>
                        <input type="date" class="form-control" name="to_date"
                               @if( $calendar['to_date'] ) value="{{ date_format(date_create($calendar['to_date']), 'Y-m-d') }}" @endif
                               required>
                    </div>
                    @if(Request::is('calendar-note/*/edit'))
                        <button type="submit" class="btn btn-primary">Update Note</button>
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteNoteModal">Delete Note</button>
                    @else
                        <button type="submit" class="btn btn-primary btn-block btn-lg">Create Note</button>
                    @endif
                </div>
            </div>

            {{--@if ( $errors->any() )
                <div class="alert alert-danger no-bottom-margin">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{$error}}</li>
                        @endforeach
                    </ul>
                </div>
            @endif--}}
        </div>

</form>

@if(Request::is('calendar-note/*/edit'))
    @include('backend.calendar.partials.delete')
@endif
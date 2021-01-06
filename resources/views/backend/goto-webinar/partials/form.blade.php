<form method="POST" action="@if(Request::is('goto-webinar/*/edit')){{route('admin.goto-webinar.update', $webinar['id'])}}@else{{route('admin.goto-webinar.store')}}@endif">

    {{ csrf_field() }}
    @if(Request::is('goto-webinar/*/edit'))
        {{ method_field('PUT') }}
    @endif

    <div class="col-sm-12">
        @if(Request::is('goto-webinar/*/edit'))
            <h3>{{ trans('site.edit') }} <em>{{$webinar['title']}}</em></h3>
        @else
            <h3>GotoWebinar Create Email Notification</h3>
        @endif
    </div>

    <div class="col-sm-12 col-md-8">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label>{{ trans('site.title') }}</label>
                    <input type="text" class="form-control" name="title" value="{{ old('title', $webinar['title']) }}" required>
                </div>
                <div class="form-group">
                    <label>Webinar Key</label>
                    <input type="text" class="form-control" name="gt_webinar_key" value="{{ old('gt_webinar_key', $webinar['gt_webinar_key']) }}" required>
                </div>
                <div class="form-group">
                    <label>Webinar Date</label>
                    <input type="datetime-local" class="form-control" name="webinar_date" value="{{ old('webinar_date', $webinar['webinar_date'] ?
                    strftime('%Y-%m-%dT%H:%M:%S', strtotime($webinar['webinar_date'])) : '') }}" required>
                </div>
                <div class="form-group">
                    <label>Reminder Date</label>
                    <input type="datetime-local" class="form-control" name="reminder_date" value="{{ old('reminder_date', $webinar['reminder_date'] ?
                    strftime('%Y-%m-%dT%H:%M:%S', strtotime($webinar['reminder_date'])) : '') }}" required>
                </div>
                <div class="form-group">
                    <label>Confirmation Email</label>
                    <textarea name="confirmation_email" cols="30" rows="10" class="form-control tinymce">{{ old('confirmation_email', $webinar['confirmation_email']) }}</textarea>
                </div>

                <div class="reminder-email-wrap">
                    <h4 class="d-block">Reminder Email</h4>
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-secondary {{$webinar['send_reminder'] ?
                            '' : 'active' }}">
                            <input type="radio" name="send_reminder" value="0" autocomplete="off" {{$webinar['send_reminder'] ?
                            '' : 'checked' }}> None
                        </label>
                        <label class="btn btn-secondary {{$webinar['send_reminder'] ?
                            'active' : '' }}">
                            <input type="radio" name="send_reminder" value="1" autocomplete="off" {{$webinar['send_reminder'] ?
                            'checked' : '' }}> Send Email
                        </label>
                    </div>

                    <div class="reminder-container margin-top">
                        <div id="no_email" class="{{$webinar['send_reminder'] ? 'hide' : ''}}">
                            <b>No reminder emails will be sent to registrants</b>
                        </div>

                        <div id="have_email" class="{{$webinar['send_reminder'] ? '' : 'hide'}}">
                            <div class="form-group">
                                <label>Reminder</label>
                                <?php
                                    $reminder_email = $webinar['reminder_email'] ? $webinar['reminder_email'] : App\Settings::gtReminderEmailTemplate()
                                ?>
                                <textarea name="reminder_email" cols="30" rows="10" class="form-control editor">{{ old('reminder_email', $reminder_email) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-md-4">
        <div class="panel panel-default">
            <div class="panel-body">
                @if(Request::is('goto-webinar/*/edit'))
                    <button type="submit" class="btn btn-primary">Update Webinar Notification</button> <br>
                    <button type="button" class="btn btn-danger margin-top" data-toggle="modal" data-target="#deleteWebinarModal">Delete Webinar Notification</button>
                @else
                    <button type="submit" class="btn btn-primary btn-block btn-lg">Create Webinar Notification</button>
                @endif
            </div>
        </div>
    </div>
</form>

@section('scripts')
    <script>
        $(".reminder-email-wrap .btn").click(function(){
           let radio_value = $(this).children().val();
           if (radio_value === '0') {
               $("#no_email").removeClass('hide');
               $("#have_email").addClass('hide')
           } else {
               $("#no_email").addClass('hide');
               $("#have_email").removeClass('hide')
           }
        });
    </script>
@stop
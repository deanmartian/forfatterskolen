<div class="table-responsive">
    <table class="table table-side-bordered table-white">
        <thead>
        <tr>
            <th>{{ trans('site.subject') }}</th>
            <th width="500">{{ trans('site.message') }}</th>
            <th>{{ trans('site.availability') }}</th>
            <th>Send Immediately</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
            @foreach($emails as $email)
                <tr>
                    <td>{{ $email->subject }}</td>
                    <td>{!! \Illuminate\Support\Str::limit(strip_tags($email->message), 100) !!}</td>
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
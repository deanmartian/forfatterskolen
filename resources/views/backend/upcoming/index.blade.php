@extends('backend.layout')

@section('title')
    <title>Email History &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-clock-o"></i> Upcoming Section</h3>
    </div>

    <div class="col-md-12">
        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>Section</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Date</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    @foreach($upcomingSections as $upcomingSection)
                        <tr>
                            <td>
                                {{ $upcomingSection->name }}
                            </td>
                            <td>
                                {{ $upcomingSection->title }}
                            </td>
                            <td>
                                {!! $upcomingSection->description !!}
                            </td>
                            <td>
                                {{ $upcomingSection->date ? \App\Http\FrontendHelpers::formatToYMDtoPrettyDate($upcomingSection->date) : NULL }}
                            </td>
                            <td>
                                <button class="btn btn-primary btn-xs editSectionBtn" data-toggle="modal"
                                        data-target="#editSectionModal"
                                        data-action="{{ route('admin.upcoming.save', $upcomingSection->id) }}"
                                        data-fields="{{ json_encode($upcomingSection) }}">
                                    <i class="fa fa-pencil"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div id="editSectionModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Update Section</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" cols="30" rows="10" class="form-control"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Date</label>
                            <input type="datetime-local" name="date" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Link</label>
                            <input type="text" name="link" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Link Label</label>
                            <input type="text" name="link_label" class="form-control" required>
                        </div>

                        <button type="submit" class="btn btn-success pull-right margin-top">{{ trans('site.save') }}</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop

@section('scripts')
    <script>
        $(".editSectionBtn").click(function() {
            let modal = $("#editSectionModal");
            let action = $(this).data('action');
            let fields = $(this).data('fields');
            let form = modal.find('form');

            form.attr('action', action);
            form.find('[name=name]').val(fields.name);
            form.find('[name=title]').val(fields.title);
            form.find('[name=description]').text(fields.description);
            form.find('[name=date]').val(fields.date_field);
            form.find('[name=link]').val(fields.link);
            form.find('[name=link_label]').val(fields.link_label);
        });
    </script>
@stop
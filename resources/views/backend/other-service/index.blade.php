@extends('backend.layout')

@section('title')
    <title>Other Services &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> Other Services</h3>
        <div class="clearfix"></div>
    </div>

    <div class="margin-top">
        <ul class="nav nav-tabs margin-top">
            <li @if( Request::input('tab') == 'coaching-timer' || Request::input('tab') == '') class="active" @endif><a href="?tab=coaching-timer">Coaching Timer</a></li>
            <li @if( Request::input('tab') == 'correction' ) class="active" @endif><a href="?tab=correction">Correction</a></li>
            <li @if( Request::input('tab') == 'copy-editing' ) class="active" @endif><a href="?tab=copy-editing">Copy Editing</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade in active">
                @if( Request::input('tab') == 'correction' )
                    <div class="panel panel-default" style="border-top: 0">
                        <div class="panel-body">
                            <div class="table-users table-responsive">
                                <table class="table no-margin-bottom">
                                    <thead>
                                    <tr>
                                        <th>Manus</th>
                                        <th>Learner</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($corrections as $correction)
                                        <?php $extension = explode('.', basename($correction->file)); ?>
                                        <tr>
                                            <td>
                                                @if( end($extension) == 'pdf' || end($extension) == 'odt' )
                                                    <a href="/js/ViewerJS/#../../{{ $correction->file }}">{{ basename($correction->file) }}</a>
                                                @elseif( end($extension) == 'docx' )
                                                    <a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$correction->file}}">{{ basename($correction->file) }}</a>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.learner.show', $correction->user->id) }}">
                                                    {{ $correction->user->full_name }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @elseif( Request::input('tab') == 'copy-editing' )
                    <div class="panel panel-default" style="border-top: 0">
                        <div class="panel-body">
                            <div class="table-users table-responsive">
                                <table class="table no-margin-bottom">
                                    <thead>
                                    <tr>
                                        <th>Manus</th>
                                        <th>Learner</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($copyEditing as $editing)
                                            <?php $extension = explode('.', basename($editing->file)); ?>
                                            <tr>
                                                <td>
                                                    @if( end($extension) == 'pdf' || end($extension) == 'odt' )
                                                        <a href="/js/ViewerJS/#../../{{ $editing->file }}">{{ basename($editing->file) }}</a>
                                                    @elseif( end($extension) == 'docx' )
                                                        <a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$editing->file}}">{{ basename($editing->file) }}</a>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('admin.learner.show', $editing->user->id) }}">
                                                        {{ $editing->user->full_name }}
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="panel panel-default" style="border-top: 0">
                        <div class="panel-body">
                            <div class="table-users table-responsive">
                                <table class="table no-margin-bottom">
                                    <thead>
                                    <tr>
                                        <th>Manus</th>
                                        <th>Learner</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($coachingTimers as $coachingTimer)
                                        <?php $extension = explode('.', basename($coachingTimer->file)); ?>
                                        <tr>
                                            <td>
                                                @if( end($extension) == 'pdf' || end($extension) == 'odt' )
                                                    <a href="/js/ViewerJS/#../../{{ $coachingTimer->file }}">{{ basename($coachingTimer->file) }}</a>
                                                @elseif( end($extension) == 'docx' )
                                                    <a href="https://view.officeapps.live.com/op/embed.aspx?src={{url('')}}/{{$coachingTimer->file}}">{{ basename($coachingTimer->file) }}</a>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.learner.show', $coachingTimer->user->id) }}">
                                                    {{ $coachingTimer->user->full_name }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@stop
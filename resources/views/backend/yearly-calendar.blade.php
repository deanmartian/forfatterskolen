@extends('backend.layout')

@section('title')
    <title>Yearly Calendar &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="col-md-12">
    <ul class="nav nav-tabs margin-top">
        <li @if( Request::input('tab') == 'howManyManuscriptEditorCanTake' || Request::input('tab') == '') class="active" @endif><a href="?tab=howManyManuscriptEditorCanTake">How Many Manuscript You Can Take</a></li>
        <li @if( Request::input('tab') == 'yearlyCalendar' ) class="active" @endif><a href="?tab=yearlyCalendar">Yearly Calendar</a></li>
    </ul>
    <div class="col-sm-12 dashboard-left">
        @if( Request::input('tab') == 'yearlyCalendar')

            <div class="container">
                <h3><i class="fa fa-file-text-o"></i> Yearly Calendar</h3>
                <div class="clearfix"></div>
                <div style="max-width:1000px;width:100%;"><div style="position: relative;padding-bottom: 117%;padding-top: 35px;height: 0;overflow: hidden;"><iframe src="https://create.plandisc.com/wheel/embed/pB6HbNe" scrolling="no" frameborder="0" style="position:absolute;top:0;left:0;width:100%;height:100%;"></iframe></div></div><a style="font-size:10px;" href="https://create.plandisc.com/pB6HbNe">Problemer med å se Plandiscen? Trykk her</a>
            </div>

        @elseif( Request::input('tab') == 'howManyManuscriptEditorCanTake' || Request::input('tab') == '')
            
            <div class="table-users table-responsive">
        
                @foreach($editor as $key)
                    @if($key->HowManyManuscriptYouCanTake->count() > 0)
                        <table class="table margin-top">
                            <thead>
                            <tr>
                                <th style="width: 260px;">{{ $key->FullName }}</th>
                                <th style="width: 100px;">{{ trans('site.start-date') }}</th>
                                <th style="width: 100px;">{{ trans('site.end-date') }}</th>
                                <th>{{ trans('site.how-many-manuscript') }}</th>
                                <th>{{ trans('site.how-many-hours') }}</th>
                                <th style="width: 779px;">{{ trans_choice('site.notes', 1) }}</th>
                            </tr>
                            </thead>

                            <tbody>
                                <?php $hm = \App\ManuscriptEditorCanTake::where('editor_id', $key->id)->paginate(10, ["*"], $key->id); ?>
                                @foreach($hm as $i)
                                    <tr>
                                        <td></td>
                                        <td>{{ $i->date_from }}</td>
                                        <td>{{ $i->date_to }}</td>
                                        <td>{{ $i->how_many_script }}</td>
                                        <td>{{ $i->how_many_hours }}</td>
                                        <td>{{ $i->note }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                    <div style="margin-top: -13px;" class="pull-right">
                        {{$hm->render()}}
                    </div>

                @endforeach

            </div>

        @endif
    </div>
</div>

<div id="showModal" class="modal fade" role="dialog" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
		  <div class="modal-body">
            <table class="table margin-top">
                    <thead>
                        <tr>
                            <th style="min-width: 84px;">{{ trans('site.start-date') }}</th>
                            <th style="min-width: 84px;">{{ trans('site.end-date') }}</th>
                            <th>{{ trans('site.how-many-manuscript') }}</th>
                            <th>{{ trans('site.how-many-hours') }}</th>
                            <th>{{ trans_choice('site.notes', 1) }}</th>
                        </tr>
                    </thead>
                    <tbody class="content">
                    </tbody>
                </table>
		  </div>
		</div>
	</div>
</div>

@stop

@section('scripts')
    <script>

    $('.showBtn').click(function(){
		var data = $(this).data('data');
        var count = $(this).data('count');
		var modal = $('#showModal');
        modal.find('.content').html('');
        for(var i=0; i < count; i++){
            modal.find('.content').append('<tr><td>'+data[i]['date_from']+'</td><td>'+data[i]['date_to']+'</td><td>'+data[i]['how_many_script']+'</td><td>'+data[i]['how_many_hours']+'</td><td>'+data[i]['note']+'</td></tr>');
        }
	});

    </script>
@stop
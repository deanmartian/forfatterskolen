<ul class="nav nav-tabs sub-nav margin-top">
    <li @if( $tab != 'archive' ) class="active" @endif>
        <a href="?p={{ $page }}&tab=new">{{ trans('site.new') }}</a>
    </li>
    <li @if( $tab == 'archive' ) class="active" @endif>
        <a href="?p={{ $page }}&tab=archive">{{ trans('site.archive') }}</a>
    </li>
</ul>

@if( $tab != 'archive' )
    <div class="table-users table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>{{ trans_choice('site.manuscripts', 1) }}</th>
                <th>{{ trans_choice('site.learners', 1) }}</th>
                <th>{{ trans('site.date-sold') }}</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($newManuscriptsTaken as $newManuscriptTaken)
                <tr>
                    <td>
                        {{ $newManuscriptTaken->manuscript_title }}
                    </td>
                    <td>
                        <a href="{{ route('admin.learner.show', $newManuscriptTaken->user_id) }}">
                            {{ $newManuscriptTaken->first_name . " " . $newManuscriptTaken->last_name }}
                        </a>
                    </td>
                    <td>
                        {{ $newManuscriptTaken->created_at }}
                    </td>
                    <td>
                        <button class="btn btn-success btn-xs sendEmailBtn"
                            data-toggle="modal"
                            data-target="#sendEmailModal"
                            data-email-template="{{ json_encode($shopManuscriptEmail) }}"
                            data-action="{{ route('admin.sales.send-email',
                            [$newManuscriptTaken->id, 'shop-manuscripts-taken-welcome']) }}">
                            {{ trans('site.send-email') }}
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div> <!-- end new shop-manuscript -->
    <div class="pull-right">{{$newManuscriptsTaken->appends(request()->except('page'))}}</div>
@else <!-- archive -->
    <div class="table-users table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>{{ trans_choice('site.manuscripts', 1) }}</th>
                <th>{{ trans_choice('site.learners', 1) }}</th>
                <th>{{ trans('site.date-sold') }}</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($archiveManuscriptsTaken as $archiveManuscriptTaken)
                <tr>
                    <td>
                        @if($archiveManuscriptTaken->is_active)
                            <a href="{{ route('shop_manuscript_taken',
                            ['id' => $archiveManuscriptTaken->user_id,
                            'shop_manuscript_taken_id' => $archiveManuscriptTaken->id]) }}">
                                {{$archiveManuscriptTaken->manuscript_title}}
                            </a>
                        @else
                            {{$archiveManuscriptTaken->manuscript_title}}
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.learner.show', $archiveManuscriptTaken->user_id) }}">
                            {{ $archiveManuscriptTaken->first_name . " " . $archiveManuscriptTaken->last_name }}
                        </a>
                    </td>
                    <td>
                        {{ $archiveManuscriptTaken->created_at }}
                    </td>
                    <td>
                        <button class="btn btn-primary btn-xs viewEmailBtn"
                                data-toggle="modal"
                                data-target="#viewEmailModal"
                                data-record="{{ json_encode($archiveManuscriptTaken) }}"
                                data-type="shop-manuscripts-taken">
                            View Email
                        </button>

                        <button class="btn btn-success btn-xs sendEmailBtn"
                                data-toggle="modal"
                                data-target="#sendEmailModal"
                                data-email-template="{{ json_encode($followUpEmailShopManuscript) }}"
                                data-action="{{ route('admin.sales.send-email',
                            [$archiveManuscriptTaken->id, 'shop-manuscripts-taken-follow-up']) }}">
                            Send following up email
                        </button>
                    </td>
                </tr> 
            @endforeach
            </tbody>
        </table>
    </div> <!-- end new shop-manuscript -->
    <div class="pull-right">{{$archiveManuscriptsTaken->appends(request()->except('page'))}}</div>
@endif
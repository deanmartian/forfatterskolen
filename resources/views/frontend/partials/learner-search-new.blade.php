<div class="row w-100 learner-search-container adjust-left-padding">
    <form role="search" class="w-100" method="get" action="{{ route('learner.account.search') }}">
        <div class="col-md-4 col-sm-12">
            <h1 class="page-title">@yield('heading')</h1>

            @if(Request::is('account/invoice'))
                <?php
                    $hasVipps = Auth::user()->address && Auth::user()->address->vipps_phone_number;
                ?>
                @if ($hasVipps)
                    <a href="javascript:void(0)" class="btn btn-danger stopVippsEFakturaBtn" data-toggle="modal"
                       data-target="#stopVippsEFakturaModal"
                       data-vipps-number="{{ NULL }}">
                        {!! trans('site.stop-vipps-efaktura') !!}
                    </a>
                @else
                    <a href="javascript:void(0)" class="btn btn-primary setVippsEFakturaBtn" data-toggle="modal"
                       data-target="#setVippsEFakturaModal"
                       data-vipps-number="{{ Auth::user()->address->vipps_phone_numberc }}">
                        {!! trans('site.set-vipps-efaktura') !!}
                    </a>
                @endif
            @endif
        </div>
        {{-- <div class="col-md-5 col-sm-12 float-right">
            <div class="input-group">
                <input type="text" class="form-control" name="search" value="{{ Request::input('search') }}"
                       placeholder="{{ trans('site.learner.search-placeholder') }}" required>
                <span class="input-group-btn">
                    <button class="btn" type="submit"><i class="fa fa-search"></i></button>
                </span>
            </div>
        </div> --}}
    </form>
</div>
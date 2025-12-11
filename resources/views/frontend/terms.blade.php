@extends('frontend.layout')

@section('title')
	<title>Forfatterskolen &rsaquo; Terms</title>
@stop

@section('styles')
	<style>
		@media (min-width: 1200px) {
			.container {
				max-width: 1240px;
			}
		}
	</style>
@stop

@section('content')
	<div class="container terms-page">
		@if ($slug == 'all')
			<div class="col-md-12 py-5">
				<div class="row">
					<div class="theme-tabs">
						<ul class="nav nav-tabs" role="tablist">
							@foreach($terms as $k => $data)
								<?php
									switch ($data->setting_name) {
									    case 'course-terms';
									    	$termsTitle = trans('site.purchase-condition-course');
									    	break;
										case 'manuscript-terms';
                                            $termsTitle = trans('site.purchase-condition-script');
											break;
                                        case 'workshop-terms';
                                            $termsTitle = trans('site.purchase-condition-writing-workshop');
                                            break;
                                        case 'coaching-terms';
                                            $termsTitle = trans('site.purchase-condition-coaching-time');
                                            break;
                                        case 'privacy-policy-terms';
                                            $termsTitle = trans('site.privacy-policy');
                                            break;
										default:
                                            $termsTitle = trans('site.terms');
                                            break;
									}
								?>
								<li class="nav-item">
									<a data-toggle="tab" href="#{{$data->setting_name}}" class="nav-link {{ $k == 0 ? 'active' : ''}}" role="tab">
										<span>{{ $termsTitle }}</span>
									</a>
								</li>
							@endforeach
						</ul>

						<div class="tab-content p-5">
							@foreach($terms as $k => $data)
								<div id="{{$data->setting_name}}" class="tab-pane fade {{ $k == 0 ? 'in active' : ''}}" role="tabpanel">
									{!! $data->setting_value !!}
								</div>
							@endforeach
						</div>
					</div> <!-- end theme-tabs -->
				</div> <!-- end row -->
			</div> <!-- end column -->
		@else
			<div class="col-xs-12 py-5">
				{!! $terms !!}
			</div>
		@endif
	</div>
@stop
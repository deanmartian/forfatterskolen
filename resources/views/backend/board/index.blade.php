@extends('backend.layout-pulse')

@section('title')
<title>Board &rsaquo; Forfatterskolen Admin</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('styles')
	<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
@stop

@section('content')
	<h1>{{ $result->name }}</h1>

	<?php
		$column_labels = new stdClass(); //define new object
		$columnOrder = array();
    	$pulseStatusIndex = '';
    $i=1;
	?>

	@foreach($result->groups as $group)
		<h4 class="margin-bottom" contenteditable="true" style="color:{{ $group->color }}; width: 30%; line-height: 1.5"
		data-id="{{ $group->id }}" data-board-id="{{ \Session::get('board_id') }}">{{ $group->title }}</h4>
		{{-- check if board has pulse --}}
		@if (\App\Helpers\DapulseRepository::getBoardPulses($group->board_id))
			<table class="table">
			<thead>
			<tr>
				{{-- loop the columns and get the corresponding status values and labels --}}
				@foreach ($result->columns as $column)
					@if($column->id == "status")
                        <?php
                        $column_labels = $column->labels;
                        ?>
					@endif

					@if($column->id != 'last_post')

						<th {{ $column->id == 'person' ? 'style=width:20px' : '' }}>{{ $column->title }}</th>
						<?php $columnOrder[] = $column->id;?>
					@endif
				@endforeach
			</tr>
			</thead>
			<tbody>

			@foreach(\App\Helpers\DapulseRepository::getBoardPulses($group->board_id) as $pulse)
				@if ($pulse->board_meta->group_id == $group->id)

					<tr>
					@foreach ($columnOrder as $column){{-- loop the column order --}}

						@foreach ($pulse->column_values as $column_value) {{-- loop all the pulse and display according to column order --}}

							@if($column == $column_value->cid) {{-- check if the id of the column is equal to the column order --}}

								@if(isset($column_value->name)) {{-- check if the column have name attribute --}}
									<td>
										<p contenteditable="true" style="line-height: 2"
										data-id="{{ $column_value->cid }}" data-board-id="{{ \Session::get('board_id') }}"
										data-pulse-id="{{ $pulse->pulse->id }}"
										class="pulse-name">{{ $column_value->name }}</p>
									</td>
								@else
									<td @if ($column_value->cid == 'person') class="image-wrapper-container" @endif>
										@if ($column_value->cid == 'person')
											@if (isset($column_value->value->name))
                                                <div class="image-wrapper">
												<img src="{{ $column_value->value->photo_url }}" alt="{{ $column_value->value->name }}"
													 title="{{ $column_value->value->name }}" class="img-responsive small-image">
                                                <span class="close-button" data-board-id="{{ \Session::get('board_id') }}"
													  data-pulse-id="{{ $pulse->pulse->id }}"
													  data-owner-id="{{ $column_value->value->id }}"></span>
                                                </div>
											@else
												<a rel="popover" data-fields="{{ json_encode($pulse->pulse) }}"
												   data-placement="bottom" data-popover-content="#popover-content" style="cursor: pointer">
												<img src="https://dziaodg7d4has.cloudfront.net/assets/default_profile_empty.png"
												class="img-responsive small-image">
												</a>

											@endif
										@endif

										@if ($column_value->cid == 'status')
											<a rel="popover" data-fields="{{ json_encode($pulse->pulse) }}"
											   data-placement="bottom" data-popover-content="#popover-content-phase"
											   class="phase-link">
												@if(isset($column_value->value->index))
													<?php
													$pulseStatusIndex = $column_value->value->index;
													// check if label is set then display it
													if (isset($column_labels->$pulseStatusIndex)) {
													    $current_phase = \Session::get('current_phase');
														echo \Session::has('current_phase') && \Session::get('current_pulse') == $pulse->pulse->id ? $column_labels->$current_phase : $column_labels->$pulseStatusIndex;
													}
													?>
												@endif
											</a>
										@endif

										@if ($column_value->cid == 'timeline')
												<input type="text" name="timeline" @if ($column_value->value) value="{{ $column_value->value->from.' - '
												.$column_value->value->to }}" @endif class="daterange form-control"
												data-pulse-id="{{ $pulse->pulse->id }}">
											<?php $i++;?>
										@endif
									</td>
								@endif {{-- end check if the column have name attribute --}}
							@endif {{-- end check if the id of the column is equal to the column order --}}
						@endforeach {{-- end loop all the pulse and display according to column order --}}
					@endforeach{{-- end loop the column order --}}
					</tr>
				@endif
			@endforeach

			<?php $columnOrder = array(); /*remove the value stored*/ ?>

			</tbody>
		</table>
			<div class="group-footer-wrapper">
				<div class="group-footer-component">
					<div class="add-pulse-component ">
						<div class="add-pulse-left-indicator"></div>
						<input type="text" class="add-pulse-input" placeholder="+ Create a New Pulse"
							   data-rel="popover" data-trigger="focus" data-placement="bottom"
							   data-popover-content="#popover-content-add-pulse">
						<button class="add-pulse-button" onclick="addPulseToBoard(this,'{{ $group->id }}')">Add</button>
						<div class="add-pulse-right-indicator"></div>
					</div>
				</div>
			</div>
		@else
			<div class="group-footer-wrapper">
				<div class="group-footer-component">
					<div class="add-pulse-component ">
						<div class="add-pulse-left-indicator"></div>
						<input type="text" class="add-pulse-input" placeholder="+ Create a New Pulse"
							   data-rel="popover" data-trigger="focus" data-placement="bottom"
							   data-popover-content="#popover-content-add-pulse">
						<button class="add-pulse-button" onclick="addPulseToBoard(this,'{{ $group->id }}')">Add</button>
						<div class="add-pulse-right-indicator"></div>
					</div>
				</div>
			</div>
		@endif

	@endforeach
	<div id="popover-content" class="hide">
		<ul class="list-group custom-popover">
			@foreach(\App\Helpers\DapulseRepository::getUsers() as $user)
				<li class="list-group-item">
					<a href="javascript:void(0);" onclick="setPulseOwner({{ $user->id }})">
						<img src="{{ $user->photo_url }}"
							 class="img-responsive small-image"> {{ $user->name }}
					</a>
				</li>
			@endforeach
		</ul>
	</div>

	<div id="popover-content-add-pulse" class="hide">
		<ul class="list-group custom-popover">
			@foreach(\App\Helpers\DapulseRepository::getUsers() as $user)
				<li class="list-group-item">
					<a href="javascript:void(0);" onclick="setNewPulseOwner({{ $user->id }})">
						<img src="{{ $user->photo_url }}"
							 class="img-responsive small-image"> {{ $user->name }}
					</a>
				</li>
			@endforeach
		</ul>
	</div>

	<div id="popover-content-phase" class="hide">
		<ul class="list-group custom-popover">
			@foreach($column_labels as $phase => $phase_text)
				<li class="list-group-item">
					<a href="javascript:void(0);" onclick="setPhase({{ $phase }})">
						{{ $phase_text }}
					</a>
				</li>
			@endforeach
		</ul>
	</div>

	<form action="{{ route('admin.board.assign-user', \Session::get('board_id')) }}" id="assignUserForm" method="POST">
		{{ csrf_field() }}
		<input type="hidden" name="pulse_id" value="">
		<input type="hidden" name="user_id" value="">
	</form>

	<form action="{{ route('admin.board.add-pulse', \Session::get('board_id')) }}" id="addPulseToBoardForm" method="POST">
		{{ csrf_field() }}
		<input type="hidden" name="user_id" value="">
		<input type="hidden" name="pulse_name" value="">
		<input type="hidden" name="group_id" value="">
	</form>

	<form action="{{ route('admin.pulse.remove-subscriber') }}" id="removePulseSubscriberForm" method="POST">
		{{ csrf_field() }}
		<input type="hidden" name="user_id" value="">
		<input type="hidden" name="pulse_id" value="">
	</form>

	<form action="{{ route('admin.board.update-pulse-status', \Session::get('board_id')) }}" id="setPhaseForm" method="POST">
		{{ csrf_field() }}
		<input type="hidden" name="pulse_id" value="">
		<input type="hidden" name="phase" value="">
	</form>

	<form action="{{ route('admin.board.update-timeline', \Session::get('board_id')) }}" id="setTimelineForm" method="POST">
		{{ csrf_field() }}
		<input type="hidden" name="pulse_id" value="">
		<input type="hidden" name="timeline" value="">
	</form>
@stop

@section('scripts')
	<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
	<script>
        $(function(){
            $('[rel="popover"]').popover({
                container: 'body',
                html: true,
                content: function () {
                    return $($(this).data('popover-content')).clone(true).removeClass('hide');
                }
            }).click(function(e) {
                e.preventDefault();
                var form = $("#assignUserForm");
				var fields = $(this).data('fields');
				form.find('input[name=pulse_id]').val(fields.id);

                var form2 = $("#setPhaseForm");
                var fields2 = $(this).data('fields');
                form2.find('input[name=pulse_id]').val(fields2.id);
            });

            $('[data-rel="popover"]').popover({
                container: 'body',
                html: true,
                content: function () {
                    return $($(this).data('popover-content')).clone(true).removeClass('hide');
                }
            });

            // close popover when clicked outside
            $('body').on('click', function (e) {
                $('[rel="popover"]').each(function () {
                    //the 'is' for buttons that trigger popups
                    //the 'has' for icons within a button that triggers a popup
                    if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                        $(this).popover('hide');
                    }
                });
            })
				.on('blur',"h4[contenteditable=true]",function() {
				    //for updating the group title
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

				    var title = $.trim($(this).text()),
						group_id = $(this).data('id'),
						board_id = $(this).data('board-id'),
						data = {title: title, group_id: group_id};

                    $.post('/board/'+board_id+'/update-group-title', data, function(){}, 'json')
						.fail(function(data){
                            alert(data.statusText);
						});
            })
                .on('blur', ".pulse-name",function() {
                    //for update title in pulse

                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    var name = $.trim($(this).text()),
						pulse_id = $(this).data('pulse-id'),
						data = {name:name};

                    $.post('/pulse/'+pulse_id+'/update-pulse-title', data, function(){}, 'json')
                        .fail(function(data){
                            alert(data.statusText);
                        });
				});

            $(".close-button").click(function(){
                var pulse_id = $(this).data('pulse-id'),
					owner_id = $(this).data('owner-id');

				var form = $("#removePulseSubscriberForm");
					form.find("input[name=user_id]").val(owner_id);
					form.find("input[name=pulse_id]").val(pulse_id);
                	form.submit()
			});

        });

        function setPulseOwner(user_id) {
            $("input[name=user_id]").val(user_id);
            $("#assignUserForm").submit();
		}

		function setPhase(phase) {
            var form 		= $("#setPhaseForm");
            form.find("input[name=phase]").val(phase);
            form.submit();
		}

		function addPulseToBoard(t, group_id) {
            var form 		= $("#addPulseToBoardForm");
            var pulse_name 	= $(t).parent('.add-pulse-component').find('.add-pulse-input').val(),
                user_id 	= form.find('input[name=user_id]').val(),
				error 		= 0;
            $("input[name=pulse_name]").val(pulse_name);
            $("input[name=group_id]").val(group_id);


            if (!pulse_name && !user_id) {
                alert('Please add a pulse name and select an owner for this pulse.');
                error++;
			} else {
                if (!user_id) {
                    alert('Please select an owner for this pulse.');
                    error++;
                }

                if (!pulse_name) {
                    alert('Please add a pulse name.');
                    error++;
                }
			}

			if (error === 0) {
                $("#addPulseToBoardForm").submit();
			}

		}

		function setNewPulseOwner(user_id) {
            var form = $("#addPulseToBoardForm");
            form.find('input[name=user_id]').val(user_id);
		}

		$(".add-pulse-input").on('focus',function(){
		   $(this).closest(".add-pulse-component").addClass("focused");
		})
		.on('blur',function(){
            $(".add-pulse-component").removeClass("focused");
		});

        $('.daterange').daterangepicker({
            autoUpdateInput: false,
			locale: {
                format: 'YYYY-MM-DD'
			}
        }).on('click',function(){
            var form = $("#setTimelineForm");
            form.find("input[name=pulse_id]").val($(this).data('pulse-id'));
		});

        $(".applyBtn").click(function(){
            var form = $("#setTimelineForm");
            var from_date = $("input[name=daterangepicker_start]").val();
            var to_date = $("input[name=daterangepicker_end]").val();
            form.find("input[name=timeline]").val(from_date+" - "+to_date);
            form.submit();
		});

	</script>
@stop

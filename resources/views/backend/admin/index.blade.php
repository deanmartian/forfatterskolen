@extends('backend.layout')

@section('title')
<title>Admins &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3><i class="fa fa-users"></i> All Admins</h3>
	<div class="navbar-form navbar-right">
	  	<div class="form-group">
		  	<form role="search" method="get" action="">
				<div class="input-group">
				  	<input type="text" class="form-control" placeholder="Search assignment..">
				    <span class="input-group-btn">
				    	<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
				    </span>
				</div>
			</form>
		</div>
	</div>
	<div class="clearfix"></div>
</div>

<div class="col-md-12">
	<button class="btn btn-primary margin-top" data-toggle="modal" data-target="#addAdminModal">Create admin</button>
	<a class="btn btn-primary margin-top" href="{{ route('admin.admin.export_nearly_expired_courses') }}">Export Nearly Expired Courses</a>
	<a class="btn btn-success margin-top" href="{{ route('admin.editor.index') }}">Editors</a>

	<ul class="nav nav-tabs margin-top">
		<li @if( Request::input('tab') == 'admin' || Request::input('tab') == '') class="active" @endif><a href="?tab=admin">Admin</a></li>
		<li @if( Request::input('tab') == 'options' ) class="active" @endif><a href="?tab=options">Options</a></li>
		<li @if( Request::input('tab') == 'terms' ) class="active" @endif><a href="?tab=terms">Terms</a></li>
		<li><a href="{{ action('\Barryvdh\TranslationManager\Controller@getView') }}/site">Translations</a></li>
	</ul>

	<div class="tab-content">
		<div class="tab-pane fade in active">
			@if( Request::input('tab') == 'options')

				<!-- Welcome Email -->
					<div class="row margin-top">
						<div class="col-sm-12">
							<div class="panel panel-default ">
								<div class="panel-heading">
									<button type="button" class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#editEmailModal"><i class="fa fa-pencil"></i></button>
									<h4>Welcome Email</h4>
								</div>
								<div class="panel-body">
									{!! nl2br(App\Settings::welcomeEmail()) !!}
								</div>
							</div>
						</div>
					</div>

					<!-- Custom Links -->
					<div class="row">
						<div class="col-sm-12">
							<div class="panel panel-default">
								<div class="panel-heading"><h4>Custom Links</h4></div>
								<table class="table">
									<thead>
									<tr>
										<th>Link</th>
										<th>Last Run</th>
									</tr>
									</thead>
									<tbody>
									@foreach($customActions as $customAction)
										<tr>
											<td>
												<a href="{{ $customAction->link }}">{{ $customAction->name }}</a>
												@if($customAction->id == 1)
													({{ $nearlyExpiredCoursesCount }})
												@endif
											</td>
											<td>
												@if($customAction->last_run)
													{{ date_format(date_create($customAction->last_run), 'M d, Y h:i a') }}
												@endif
											</td>
										</tr>
									@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>

					<!-- Page Meta -->
					<div class="row">
						<div class="col-sm-12">
							<div class="panel panel-default">
								<div class="panel-heading">
									<button type="button" class="btn btn-success btn-xs pull-right" data-toggle="modal"
											data-target="#addPageMetaModal"><i class="fa fa-plus"></i></button>
									<h4>Page Meta</h4>
								</div>
								<table class="table">
									<thead>
									<tr>
										<th>Link</th>
										<th>Meta Title</th>
										<th>Meta Description</th>
										<th>Action</th>
									</tr>
									</thead>
									<tbody>
									@foreach($pageMetas as $pageMeta)
										<tr>
											<td>
												<a href="{{ $pageMeta->url }}">{{ $pageMeta->url }}</a>
											</td>
											<td>
												{{ $pageMeta->meta_title }}
											</td>
											<td>{{ $pageMeta->meta_description }}</td>
											<td>
												<button type="button" class="btn btn-primary btn-xs pull-right editPageMetaBtn"
														data-toggle="modal" data-target="#editPageMetaModal" data-fields="{{ json_encode($pageMeta) }}"
														data-action="{{ route('admin.page_meta.update', $pageMeta->id) }}">
													<i class="fa fa-pencil"></i>
												</button>
												<div class="clearfix"></div>
												<button type="button" class="btn btn-danger btn-xs pull-right"
														data-toggle="modal" data-target="#deletePageMetaModal"
														id="deletePageMetaBtn" data-action="{{ route('admin.page_meta.delete', $pageMeta->id) }}"
														style="margin-top: 5px">
													<i class="fa fa-close"></i>
												</button>
											</td>
										</tr>
									@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>

			@elseif( Request::input('tab') == 'terms' )
				<!-- Welcome Email -->
				<div class="row margin-top">
					<div class="col-sm-12">
						<div class="panel panel-default ">
							<div class="panel-heading">
								<button type="button" class="btn btn-primary btn-xs pull-right" data-toggle="modal" data-target="#editTermsModal"><i class="fa fa-pencil"></i></button>
							</div>
							<div class="panel-body">
								{!! App\Settings::terms() !!}
							</div>
						</div>

						<?php
							$other_tabs = ['course', 'manuscript', 'workshop', 'coaching'];
						?>

						<div class="col-sm-12">
							<nav>
								<ul class="nav nav-tabs" id="other-terms-tab">
									@foreach($other_tabs as $other_tab)
										<li>
											<a href="#nav-{{ $other_tab }}" data-toggle="tab">{{ ucwords($other_tab === 'coaching' ?
											'Coaching Timer' :
											($other_tab === 'manuscript' ? 'Manuscript/Språkvask/Korrektur' : $other_tab)) }}</a>
										</li>
									@endforeach
								</ul>
							</nav>

							<div class="tab-content">
								@foreach($other_tabs as $other_tab)
									<div class="tab-pane fade" id="nav-{{ $other_tab }}">
										<div class="panel panel-default" style="border-top: 0">
											<div class="panel-body">
												<div class="panel-heading">
													<button type="button" class="btn btn-primary btn-xs pull-right otherTermsBtn" data-toggle="modal" data-target="#editOtherTermsModal"
															data-terms="{{ App\Settings::getByName($other_tab.'-terms') }}"
															data-terms-type="{{ $other_tab }}"><i class="fa fa-pencil"></i></button>
												</div>
												<div class="panel-body">
													{!! App\Settings::getByName($other_tab.'-terms') !!}
												</div>
											</div>
										</div>
									</div>
								@endforeach
							</div> <!-- end tab-content -->
						</div>
					</div>
				</div>
			@else
				<div class="table-users table-responsive">
					<table class="table">
						<thead>
						<tr>
							<th>Name</th>
							<th>Email</th>
							<th></th>
						</tr>
						</thead>

						<tbody>
						@foreach($admins as $admin)
							<tr>
								<td>{{ $admin->full_name }}</td>
								<td>{{ $admin->email }}</td>
								<td>
									<div class="pull-right">
										<button class="btn btn-info btn-xs editAdminAccessPageBtn" data-action="{{ route('admin.admin.page-access', $admin->id) }}" data-toggle="modal" data-target="#editAdminAccessPageModal" data-fields="{{ json_encode($admin) }}"
												data-pages="{{ json_encode($admin->pageAccess) }}"><i class="fa fa-clipboard"></i></button>
										<button class="btn btn-primary btn-xs editAdminBtn" data-action="{{ route('admin.admin.update', $admin->id) }}" data-toggle="modal" data-target="#editAdminModal" data-fields="{{ json_encode($admin) }}"><i class="fa fa-pencil"></i></button>
										<button class="btn btn-danger btn-xs deleteAdminBtn" data-action="{{ route('admin.admin.destroy', $admin->id) }}" data-toggle="modal" data-target="#deleteAdminModal"><i class="fa fa-trash"></i></button>
									</div>
								</td>
							</tr>
						@endforeach
						</tbody>
					</table>
				</div>

				<div class="pull-right">
					{{$admins->render()}}
				</div>
			@endif
		</div>
	</div>
	<div class="clearfix"></div>
</div>


<div id="addAdminModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">Create Admin User</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="{{ route('admin.admin.store') }}">
		      {{ csrf_field() }}
		      <div class="form-group">
		      	<label>First name</label>
		      	<input type="text" class="form-control" name="first_name" required>
		      </div>
		      <div class="form-group">
		      	<label>Last name</label>
		      	<input type="text" class="form-control" name="last_name" required>
		      </div>
		      <div class="form-group">
		      	<label>Email</label>
		      	<input type="email" class="form-control" name="email" required>
		      </div>
		      <div class="form-group">
		      	<label>Password</label>
		      	<input type="password" class="form-control" name="password" required>
		      </div>
		      <button type="submit" class="btn btn-primary pull-right margin-top">Save</button>
		      <div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>

<div id="editAdminModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">Edit Admin User</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="">
		      {{ csrf_field() }}
		      {{ method_field('PUT') }}
		      <div class="form-group">
		      	<label>First name</label>
		      	<input type="text" class="form-control" name="first_name" required>
		      </div>
		      <div class="form-group">
		      	<label>Last name</label>
		      	<input type="text" class="form-control" name="last_name" required>
		      </div>
		      <div class="form-group">
		      	<label>Email</label>
		      	<input type="email" class="form-control" name="email" required>
		      </div>
		      <div class="form-group">
		      	<label>Password</label>
		      	<input type="password" class="form-control" name="password">
		      </div>
				{{--<div class="form-group">
					<input type="checkbox" name="minimal_access"> Allow manuscript and learners page only
				</div>--}}
				<div class="form-group">
					<input type="checkbox" name="is_editor"> Is Editor?
				</div>
		      <button type="submit" class="btn btn-primary pull-right margin-top">Save</button>
		      <div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>

<div id="editAdminAccessPageModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Edit Admin Access Page</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="">
					{{ csrf_field() }}

					<div class="form-group">
						<label>Pages</label> <br>
						@foreach(\App\Http\AdminHelpers::pageList() as $page)
						<input type="checkbox" name="pages[]" class="form-check-input" value="{{ $page['id'] }}">
						{{ $page['option'] }}
						<br>
						@endforeach
					</div>
					<button type="submit" class="btn btn-primary pull-right margin-top">Save</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="deleteAdminModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">Delete admin</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="">
		      {{ csrf_field() }}
		      {{ method_field('DELETE') }}
		  		Are you sure to delete this admin user?
		      <button type="submit" class="btn btn-danger pull-right margin-top">Delete</button>
		      <div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>

<div id="editEmailModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Welcome Email</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.settings.update.welcome_email') }}">
					{{ csrf_field() }}
					<textarea class="form-control" name="welcome_email" rows="6">{{ App\Settings::welcomeEmail() }}</textarea>
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
				</form>
			</div>
		</div>

	</div>
</div>

<div id="editTermsModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Terms</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.settings.update.terms') }}">
					{{ csrf_field() }}
					<textarea class="form-control ckeditor" name="terms">{{ App\Settings::terms() }}</textarea>
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="editOtherTermsModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title"></h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.settings.update.other-terms') }}">
					{{ csrf_field() }}
					<textarea class="form-control ckeditor" name="terms"></textarea>
					<input type="hidden" name="terms_type">
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="addPageMetaModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Page Meta</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="{{ route('admin.page_meta.store') }}">
					{{ csrf_field() }}
					<div class="form-group">
						<label>Page Url</label>
						<input type="text" name="url" class="form-control" required>
					</div>
					<div class="form-group">
						<label>Meta Title</label>
						<input type="text" name="meta_title" class="form-control" required>
					</div>
					<div class="form-group">
						<label>Meta Description</label>
						<textarea class="form-control" name="meta_description" rows="6" maxlength="350"
								  onkeyup="countChar(this)" required></textarea>
						<div class="charNum">350 characters left</div>
					</div>
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="editPageMetaModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Edit Page Meta</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="">
					{{ csrf_field() }}
					{{ method_field('PUT') }}
					<div class="form-group">
						<label>Page Url</label>
						<input type="text" name="url" class="form-control" required>
					</div>
					<div class="form-group">
						<label>Meta Title</label>
						<input type="text" name="meta_title" class="form-control" required>
					</div>
					<div class="form-group">
						<label>Meta Description</label>
						<textarea class="form-control" name="meta_description" rows="6" maxlength="350"
								  onkeyup="countChar(this)" required></textarea>
						<div class="charNum">350 characters left</div>
					</div>
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-primary">Save</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="deletePageMetaModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Delete Page Meta</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="">
					{{ csrf_field() }}
					{{method_field('DELETE')}}
					Are you sure to delete this page meta?
					<div class="text-right margin-top">
						<button type="submit" class="btn btn-danger">Delete</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>
@stop

@section('scripts')
	<script type="text/javascript" src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
<script>

	let other_terms_tab = '{{ Session::has('terms_tab') ? Session::get('terms_tab'): 'course' }}';

    // tinymce
    var editor_config = {
        path_absolute: "{{ URL::to('/') }}",
        height: '25em',
        selector: '.ckeditor',
        plugins: ['advlist autolink lists link image charmap print preview hr anchor pagebreak',
            'searchreplace wordcount visualblocks visualchars code fullscreen',
            'insertdatetime media nonbreaking save table contextmenu directionality',
            'emoticons template paste textcolor colorpicker textpattern'],
        toolbar1: 'formatselect fontselect fontsizeselect | bold italic underline strikethrough subscript superscript | forecolor backcolor | link | alignleft aligncenter alignright ' +
        'alignjustify  | removeformat',
        toolbar2: 'undo redo | bullist numlist | outdent indent blockquote | link unlink anchor image media code | print fullscreen',
        relative_urls: false,
        file_browser_callback : function(field_name, url, type, win) {
            var x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
            var y = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;

            var cmsURL = editor_config.path_absolute + '/laravel-filemanager?field_name=' + field_name;
            if (type == 'image') {
                cmsURL = cmsURL + '&type=Images';
            } else {
                cmsURL = cmsURL + '&type=Files';
            }

            tinyMCE.activeEditor.windowManager.open({
                file : cmsURL,
                title : 'Filemanager',
                width : x * 0.8,
                height : y * 0.8,
                resizable : 'yes',
                close_previous : 'no'
            });
        }
    };
    tinymce.init(editor_config);

	$('.editAdminBtn').click(function(){
		var form = $('#editAdminModal form');
		var action = $(this).data('action');
		var fields = $(this).data('fields');
		form.attr('action', action);
		form.find('input[name=first_name]').val(fields.first_name);
		form.find('input[name=last_name]').val(fields.last_name);
		form.find('input[name=email]').val(fields.email);

		/*if (fields.minimal_access) {
            form.find('input[name=minimal_access]').attr('checked', true);
		}*/

        if (fields.is_editor) {
            form.find('input[name=is_editor]').attr('checked', true);
        }
	});

    $('.editAdminAccessPageBtn').click(function(){
        var form = $('#editAdminAccessPageModal').find('form');
        var action = $(this).data('action');
        var fields = $(this).data('fields');
        var pages = $(this).data('pages');
        form.attr('action', action);
        form.find('input[name="pages[]"]').attr('checked', false);

        // check if admin has selected pages
        if (pages.length) {
            $.each(pages, function(k ,v) {
                // check the admin access page by value
                form.find('input[name="pages[]"][value='+v.page_id+']').attr('checked', true);
			});
		}
    });


	$('.deleteAdminBtn').click(function(){
		var form = $('#deleteAdminModal form');
		var action = $(this).data('action');
		form.attr('action', action);
	});

    $(".editPageMetaBtn").click(function(){
        var fields = $(this).data('fields');
        var modal = $('#editPageMetaModal');
        var action = $(this).data('action');
        modal.find('form').attr('action', action);
        modal.find('input[name=url]').val(fields.url);
        modal.find('input[name=meta_title]').val(fields.meta_title);
        modal.find('textarea[name=meta_description]').text(fields.meta_description);
    });

    $("#deletePageMetaBtn").click(function(){
        var modal = $('#deletePageMetaModal');
        var action = $(this).data('action');
        modal.find('form').attr('action', action);
    });

    function countChar(val) {
        var len = val.value.length;
        if (len >= 350) {
            val.value = val.value.substring(0, 350);
            $('.charNum').text(0 + " character left");
        } else {
            var charText = "characters left";
            if (350 - len === 1) {
                charText = "character left";
            }
            $('.charNum').text(350 - len + " "+charText);
        }
    }

    $('#editPageMetaModal').on('show.bs.modal', function () {
        var len = $(this).find('textarea').val().length;
        var charText = "characters left";
        if (350 - len === 1) {
            charText = "character left";
        }
        $(this).find('.charNum').text(350 - len + " "+charText);
    });

    $(".otherTermsBtn").click(function(){
       let terms = $(this).data('terms');
       let modal = $("#editOtherTermsModal");
       let form = modal.find('form');
       let terms_type = $(this).data('terms-type');
       modal.find('.modal-title').text(ucFirst(terms_type !== 'coaching' ? terms_type : 'coaching Timer')+' Terms');
       form.find('textarea').text(terms);
        form.find('[name=terms_type]').val(terms_type);

        // set the value for textarea editor
        tinyMCE.activeEditor.setContent(terms);
	});

    if (other_terms_tab) {
        $("#other-terms-tab").find('[href="#nav-'+ other_terms_tab + '"]').trigger('click');
        $("#nav-"+other_terms_tab).addClass('active in');
	}

    // capitalize the first letter
    function ucFirst(string)
    {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }
</script>
@stop
@extends('backend.layout')

@section('title')
<title>Faq &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3><i class="fa fa-question-circle"></i> FAQs</h3>
</div>

<br />
<div class="col-sm-8 col-sm-offset-2">
	<button class="btn btn-primary margin-bottom" data-toggle="modal" data-target="#addFaqModal">Add FAQ</button>
	<a href="{{ route('admin.competition.index') }}" class="btn btn-primary margin-bottom">Konkurranser</a>
	<a href="{{ route('admin.writing-group.index') }}" class="btn btn-primary margin-bottom">Skrivegrupper</a>
	@foreach( $faqs as $faq )
	<div class="panel panel-default">
		<div class="panel-body">
			<div class="pull-right">
				<button class="btn btn-xs btn-primary editFaqBtn" data-fields="{{ json_encode($faq) }}" data-action="{{ route('admin.faq.update', $faq->id) }}" data-toggle="modal" data-target="#editFaqModal"><i class="fa fa-pencil"></i></button>
				<button class="btn btn-xs btn-danger deleteFaqBtn" data-action="{{ route('admin.faq.destroy', $faq->id) }}" data-toggle="modal" data-target="#deleteFaqModal"><i class="fa fa-trash"></i></button>
			</div>
			<h4>{{ $faq->title }}</h4>
			<p style="margin-bottom: 0; margin-top: 7px">{!! nl2br($faq->description) !!}</p>
		</div>
	</div>
	@endforeach
</div>


<div id="addFaqModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">Add FAQ</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="{{ route('admin.faq.store') }}">
		      {{ csrf_field() }}
		      <div class="form-group">
		      	<label>Title</label>
		      	<input type="text" class="form-control" name="title" placeholder="Title" required>
		      </div>
		      <div class="form-group">
		      	<label>Description</label>
		      	<textarea class="form-control editor" name="description" placeholder="Title" rows="8"></textarea>
		      </div>
		      <button type="submit" class="btn btn-primary pull-right margin-top">Save</button>
		      <div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>

<div id="editFaqModal" class="modal fade" role="dialog">
	<div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">Edit FAQ</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="">
		      {{ csrf_field() }}
		      {{ method_field('PUT') }}
		      <div class="form-group">
		      	<label>Title</label>
		      	<input type="text" class="form-control" name="title" placeholder="Title" required>
		      </div>
		      <div class="form-group">
		      	<label>Description</label>
		      	<textarea class="form-control editor" name="description" placeholder="Title" rows="8"></textarea>
		      </div>
		      <button type="submit" class="btn btn-primary pull-right margin-top">Save</button>
		      <div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>

<div id="deleteFaqModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
		  <div class="modal-header">
		    <button type="button" class="close" data-dismiss="modal">&times;</button>
		    <h4 class="modal-title">Delete FAQ</h4>
		  </div>
		  <div class="modal-body">
		    <form method="POST" action="{{ route('admin.faq.store') }}">
		      {{ csrf_field() }}
		      {{ method_field('DELETE') }}
		      Are you sure to delete this FAQ?
		      <br />
		      <button type="submit" class="btn btn-danger pull-right margin-top">Delete</button>
		      <div class="clearfix"></div>
		    </form>
		  </div>
		</div>
	</div>
</div>
@stop

@section('scripts')
	<script type="text/javascript" src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
<script>

    // tinymce
    let editor_config = {
        path_absolute: "{{ URL::to('/') }}",
        height: '15em',
        selector: '.editor',
        plugins: ['advlist autolink lists link image charmap print preview hr anchor pagebreak',
            'searchreplace wordcount visualblocks visualchars code fullscreen',
            'insertdatetime media nonbreaking save table contextmenu directionality',
            'emoticons template paste textcolor colorpicker textpattern'],
        toolbar1: 'formatselect fontselect fontsizeselect | bold italic underline strikethrough subscript superscript | forecolor backcolor | link | alignleft aligncenter alignright ' +
        'alignjustify  | removeformat',
        toolbar2: 'undo redo | bullist numlist | outdent indent blockquote | link unlink anchor image media code | print fullscreen',
        relative_urls: false,
        file_browser_callback : function(field_name, url, type, win) {
            let x = window.innerWidth || document.documentElement.clientWidth || document.getElementsByTagName('body')[0].clientWidth;
            let y = window.innerHeight || document.documentElement.clientHeight || document.getElementsByTagName('body')[0].clientHeight;

            let cmsURL = editor_config.path_absolute + '/laravel-filemanager?field_name=' + field_name;
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

    $('.deleteFaqBtn').click(function(){
        var form = $('#deleteFaqModal form');
        var action = $(this).data('action');
        form.attr('action', action);
    });

    $('.editFaqBtn').click(function(){
        var form = $('#editFaqModal form');
        var fields = $(this).data('fields');
        var action = $(this).data('action');
        form.attr('action', action);
        form.find('input[name=title]').val(fields.title);
        // set content to the active editor
        tinyMCE.activeEditor.setContent(fields.description);
    });
</script>
@stop
@extends('backend.layout')

@section('title')
<title>Tinymce Images &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3><i class="fa fa-file"></i> Tinymce Images</h3>
</div>

<div class="col-md-12">
    <div class="table-users table-responsive margin-top">
		<table class="table dt2-table">
			<thead>
		    	<tr>
			        <th>Image</th>
			        <th>Filepath</th>
		      	</tr>
		    </thead>
            <tbody>
                @foreach ($images as $image)
                    <tr>
                        <td>
                            <img src="{{ $image['path'] }}" alt="{{ $image['name'] }}" 
                            style="width: 300px; max-height: 200px; object-fit:scale-down">
                        </td>
                        <td>
                            {{ $image['path'] }}
                            <input type="text" value="{{ $image['path'] }}"
                                   style="position: absolute; left: -10000px;">
                            <button type="button" class="btn btn-success btn-xs copyToClipboard">
                                <i class="fa fa-clipboard"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@stop

@section('scripts')
<script>
    $(".dt2-table").DataTable({
        "lengthMenu": [[25, 50, 75, 100, -1], [25, 50, 75, 100, "All"]],
        pageLength: 25,
        "aaSorting": []
    });

    $(".copyToClipboard").click(function(){
        let copyText = $(this).closest('td').find('[type=text]');
        /* Select the text field */
        copyText.select();
        /* Copy the text inside the text field */
        document.execCommand("copy");

        toastr.success('Copied to clipboard.', "Success");
        if (window.getSelection) {
            if (window.getSelection().empty) {  // Chrome
                window.getSelection().empty();
            } else if (window.getSelection().removeAllRanges) {  // Firefox
                window.getSelection().removeAllRanges();
            }
        } else if (document.selection) {  // IE?
            document.selection.empty();
        }
    });
</script>
@stop
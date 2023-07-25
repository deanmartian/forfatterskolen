@extends('backend.layout')

@section('title')
<title>Books For Sale &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3><i class="fa fa-book"></i> Books For Sale</h3>

    <button class="btn btn-success pull-right bookForSaleBtn" data-toggle="modal"
            data-action=""
            data-target="#booksForSaleModal">
        + Add Books for Sale
    </button>
	<div class="clearfix"></div>
</div>

<div class="col-md-12">
    <div class="table-users table-responsive">
		<table class="table">
			<thead>
		    	<tr>
			        <th>Project</th>
                    <th>Sales</th>
                    <th>Learner</th>
                    {{-- <th>ISBN</th>
                    <th>Ebook ISBN</th> --}}
                    <th>Title</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th></th>
		      	</tr>
		    </thead>
            <tbody>
                @foreach($books as $bookForSale)
                    <tr>
                        <td>
                            @if ($bookForSale->project)
                                <a href="/project/{{ $bookForSale->project_id }}">
                                    {{ $bookForSale->project->name }}
                                </a>
                            @endif
                        </td>
                        <td>
                            {{ FrontendHelpers::currencyFormat($bookForSale->sales()->sum('amount')) }}
                        </td>
                        <td>
                            <a href="{{ route('admin.learner.show', $bookForSale->user_id) }}">
                                {{ $bookForSale->user->full_name }}
                            </a>
                        </td>
                        {{-- <td>{{ $bookForSale->isbn }}</td>
                        <td>{{ $bookForSale->ebook_isbn }}</td> --}}
                        <td>{{ $bookForSale->project ? $bookForSale->project->book_name : '' }}</td>
                        <td>{{ $bookForSale->description }}</td>
                        <td>{{ $bookForSale->price_formatted }}</td>
                        <td>
                            <a href="{{ route('admin.book-for-sale.show', $bookForSale->id) }}" 
                                class="btn btn-info btn-xs">
                                <i class="fa fa-eye"></i>
                            </a>

                            <button class="btn btn-primary btn-xs bookForSaleBtn" data-toggle="modal" 
                            data-target="#booksForSaleModal" data-record="{{ json_encode($bookForSale) }}">
                                <i class="fa fa-edit"></i>
                            </button>

                            <button class="btn btn-danger btn-xs deleteRecordBtn" data-toggle="modal" data-target="#deleteRecordModal"
                            data-action="{{ route('admin.learner.delete-for-sale-books',
												 [$bookForSale->user_id, $bookForSale->id]) }}">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="pull-right">
		{{ $books->render() }}
	</div>
	<div class="clearfix"></div>
</div>

<div id="booksForSaleModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Books for sale</h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
				{{ csrf_field() }}
					<input type="hidden" name="id">

                    <div class="form-group">
						<label>Learner</label>
						<select name="user_id" class="form-control select2" required>
							<option value="">- Select Learner -</option>
                            @foreach ($learners as $learner)
                                <option value="{{ $learner->id }}" data-record="{{ json_encode($learner) }}">
                                    {{ $learner->fullname }}
                                </option>
                            @endforeach
						</select>
					</div>

					<div class="form-group">
						<label>Project</label>
						<select name="project_id" class="form-control" required onchange="projectChanged(this)">
                            <option value="">- Select Project -</option>
						</select>
					</div>

					<div class="form-group">
						<label>ISBN</label>
						<div class="isbn-container"></div>
					</div>

                    <div class="form-group">
						<label>Title</label>
						<input value='' class='form-control book-title-container' disabled>
					</div>

					{{-- <div class="form-group">
						<label>Ebook ISBN</label>
						<input type="text" class="form-control" name="ebook_isbn">
					</div>

					<div class="form-group">
						<label>Title</label>
						<input type="text" class="form-control" name="title" required>
					</div> --}}

					<div class="form-group">
						<label>Description</label>
						<textarea class="form-control" name="description" rows="10" cols="30"></textarea>
					</div>

					<div class="form-group">
						<label>Price</label>
						<input type="number" class="form-control" name="price" required>
					</div>

					<button class="btn btn-primary pull-right" type="submit">
						{{ trans('site.save') }}
					</button>
					<div class="clearfix"></div>
				</form>
			</div>
		</div>
	</div>
</div>

<div id="deleteRecordModal" class="modal fade" role="dialog" data-backdrop="static">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">
                    Delete Record
                </h4>
			</div>
			<div class="modal-body">
				<form method="POST" action="" onsubmit="disableSubmit(this)">
					{{ csrf_field() }}
					{{ method_field('DELETE') }}

					<p>{{ trans('site.delete-item-question') }}</p>

					<div class="text-right margin-top">
						<button type="submit" class="btn btn-danger">{{ trans('site.delete') }}</button>
					</div>
				</form>
			</div>

		</div>

	</div>
</div>
@stop

@section('scripts')
<script>
    $(".select2").change(function(self) {
        let selectedOption = $(this).select2('data')[0];
        let selectedValue = selectedOption.id;
        let selectedText = selectedOption.text;
        let record = selectedOption.element.dataset.record ? JSON.parse(selectedOption.element.dataset.record) : [];
        let projects = record.projects;
        let project_selector = $("[name=project_id]");
        let modal = $("#booksForSaleModal");
        let action = '/learner/' + selectedValue + '/save-for-sale-books';

        modal.find("form").attr('action', action);
        
        project_selector.empty();
        project_selector.append('<option value="">- Select Project -</option>');

        let option = "";

        if (projects) {
            $.each(projects, function(key, data) {
                let registrations = JSON.stringify(data.registrations);
                option += "<option value='" + data.id + "' data-registrations='" + registrations + "' data-book_name='" + data.book_name + "'>" 
                    + data.name + "</option>";
            });

            project_selector.append(option);
        }
    });

    $(".bookForSaleBtn").click(function() {
        let modal = $("#booksForSaleModal");
        let record = $(this).data('record');

        modal.find('[name=id]').val('');
        modal.find("[name=user_id]").val(null).trigger('change');
        modal.find("[name=project_id]").val('');
        modal.find('[name=isbn]').val('');
		modal.find('[name=ebook_isbn]').val('');
		modal.find('[name=title]').val('');
		modal.find('[name=description]').text('');
		modal.find('[name=price]').val('');

        if (record) {
            modal.find('[name=id]').val(record.id);
            modal.find("[name=user_id]").val(record.user_id).trigger('change');
            modal.find("[name=project_id]").val(record.project_id);
            modal.find('[name=isbn]').val(record.isbn);
            modal.find('[name=ebook_isbn]').val(record.ebook_isbn);
            modal.find('[name=title]').val(record.title);
            modal.find('[name=description]').text(record.description);
            modal.find('[name=price]').val(record.price);
        }

    });

    $(".deleteRecordBtn").click(function() {
        let modal = $("#deleteRecordModal");
        let action = $(this).data('action');
        modal.find('form').attr('action', action);
	});

    function projectChanged(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];

        // Get the value and data-info attribute of the selected option
        const selectedValue = selectedOption.value;
        const selectedDataRegistrations = selectedOption.getAttribute('data-registrations');
        const selectedDataBookname = selectedOption.getAttribute('data-book_name');

        let isbnContainer = $(".isbn-container");
        let bookTitleContainer = $(".book-title-container");
        let list = "<ul>";
            
        isbnContainer.empty();
        bookTitleContainer.val(selectedDataBookname);

        $.each(JSON.parse(selectedDataRegistrations), function(k, registration) {
            list += "<li>" + registration.value + "</li>";
        });

        list += "</ul>";
        isbnContainer.append(list);

    }
</script>
@stop
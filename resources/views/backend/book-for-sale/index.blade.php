@extends('backend.layout')

@section('title')
<title>Books For Sale &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
<div class="page-toolbar">
	<h3><i class="fa fa-book"></i> Books For Sale</h3>
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
                    <th>ISBN</th>
                    <th>Ebook ISBN</th>
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
                        <td>{{ $bookForSale->isbn }}</td>
                        <td>{{ $bookForSale->ebook_isbn }}</td>
                        <td>{{ $bookForSale->title }}</td>
                        <td>{{ $bookForSale->description }}</td>
                        <td>{{ $bookForSale->price_formatted }}</td>
                        <td>
                            <a href="{{ route('admin.book-for-sale.show', $bookForSale->id) }}" 
                                class="btn btn-info btn-xs">
                                <i class="fa fa-eye"></i>
                            </a>
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
@stop
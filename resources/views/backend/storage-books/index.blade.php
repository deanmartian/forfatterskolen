@extends('backend.layout')

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> Storage Books</h3>
    </div>

    <div class="col-sm-12 margin-top">
        <table class="table table-striped table-border">
            <thead>
                <tr>
                    <th>ISBN</th>
                    <th>Type</th>
                    <th>Book Price</th>
                    <th>Book Name</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($projectCentralDistributions as $projectCentralDistribution)
                    <tr>
                        <td>
                            <a href="{{ route('admin.project.storage-details', 
                                [$projectCentralDistribution->project_id, $projectCentralDistribution->id]) }}">
                                {{ $projectCentralDistribution->value }}
                            </a>
                        </td>
                        <td>
                            {{ $isbnTypes[$projectCentralDistribution->type_of_isbn] ?? NULL }}
                        </td>
                        <td>
                            {{ $projectCentralDistribution->isbn_book_price 
                                ? FrontendHelpers::currencyFormat($projectCentralDistribution->isbn_book_price) 
                                : NULL }}
                        </td>
                        <td>
                            {{ $projectCentralDistribution->book_name }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
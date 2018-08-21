@extends('frontend.layout')

@section('title')
    <title>Sent Queries &rsaquo; Forfatterskolen</title>
@stop

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css" integrity="sha384-EkHEUZ6lErauT712zSr0DZ2uuCmi3DoQj6ecNdHQXpMpFNGAQ48WjfXCE5n20W+R" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('js/toastr/toastr.min.css') }}">
@stop

@section('content')
    <div class="account-container">

        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content reader-directory-search white-background">
            <div class="col-sm-12">
                <div class="col-md-8 col-sm-offset-2 col-sm-12 margin-top">

                    @include('frontend.learner.pilot-reader.partials.reader-directory-nav')

                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title with-border-b pb-2">Sent Queries</h4>
                            <table id="sent-queries-table" class="table table-striped table-bordered" width="100%">
                                <thead>
                                <tr>
                                    <th>To</th>
                                    <th>Book</th>
                                    <th>Received</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                </div><!-- end col-md-8 col-sm-offset-2 col-sm-12 margin-top -->
            </div> <!-- end col-sm-12 -->
        </div> <!-- end col-sm-12 col-md-10 sub-right-content reader-directory-search white-background -->

        <div class="clearfix"></div>
    </div>

    @include('frontend.learner.pilot-reader.reader-directory.modal.query_reader_detail')
@stop

@section('scripts')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js" integrity="sha384-rgWRqC0OFPisxlUvl332tiM/qmaNxnlY46eksSZD84t+s2vZlqGeHrncwIRX7CGp" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js" integrity="sha384-uiSTMvD1kcI19sAHJDVf68medP9HA2E2PzGis9Efmfsdb8p9+mvbQNgFhzii1MEX" crossorigin="anonymous"></script>
    <script src="{{ asset('js/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('js/pilot-reader/reader-directory.js') }}"></script>
    <script>
        methods.listQuery('sent');
    </script>
@stop
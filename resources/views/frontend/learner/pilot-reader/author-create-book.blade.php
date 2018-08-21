@extends('frontend.layout')

@section('title')
    <title>New Book &rsaquo; Forfatterskolen</title>
@stop

@section('heading') New Book @stop

@section('content')
    <div class="account-container">

        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content author-create-book">
            <div class="col-sm-12">

                <div class="row">
                    <div class="col-sm-4">
                        <h3 class="no-margin-top">@yield('heading')</h3>
                    </div>
                </div>

                <div class="col-sm-12 margin-top">
                    <div class="col-xs-6">
                        <form action="" method="POST">
                            {{ csrf_field() }}

                            <div class="form-group">
                                <label>Title</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>

                            <div class="form-group margin-top">
                                <label>Displayed Author Name</label>
                                <input type="text" class="form-control" name="display_name" value="{{ old('display_name') }}"
                                placeholder="{{ Auth::user()->full_name }}">
                                <div class="hint">
                                    Use this to show a different author name for the book, eg. a use a different penname,
                                    credit a coauthor, etc. If this field is empty your name will be used.
                                </div>
                            </div>

                                <button class="beta-button color success" type="submit">Create</button>
                                <a href="{{ route('learner.book-author') }}" class="beta-button">Cancel</a>

                        </form>

                        @if ($errors->any())
                            <div class="alert alert-danger margin-top">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>
    </div>
@stop
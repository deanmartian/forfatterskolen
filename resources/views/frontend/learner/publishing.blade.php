@extends('frontend.layout')

@section('title')
    <title>Publishers House &rsaquo; Forfatterskolen</title>
@stop

@section('heading') Forlagsliste @stop

@section('styles')
    <style>
        .table-users .table {
            margin-top: 12px;
            margin-bottom: 12px;
            background-color: #fff;
            border: solid 1px #ccc;
        }

        .table thead {
            background-color: #eee;
        }
    </style>
@stop

@section('content')
    <div class="account-container">

        @include('frontend.partials.learner-menu')

        <div class="col-sm-12 col-md-10 sub-right-content">
            <div class="col-sm-12">

                <div class="col-sm-12">


                    <form role="search" class="row" method="get" action="{{ route('learner.publishing') }}">
                        <div class="col-sm-4">
                            <h3 class="no-margin-top">@yield('heading')</h3>
                        </div>
                        <div class="col-sm-3 pull-right">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" value="{{ Request::input('search') }}" placeholder="Filter genre" required>
                                <span class="input-group-btn">
		    	                    <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
		                        </span>
                            </div>
                        </div>
                    </form>

                </div>

                <div class="col-md-12">
                    <div class="table-users table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Forlagsnavn</th>
                                <th>Post adresse</th>
                                <th>Telefon nummer</th>
                                <th>Sjanger</th>
                                <th>Manus sendes via nettside/post</th>
                                <th>Epost adresse</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($publishingHouses as $publishingHouse)
                                <tr>
                                    <td>
                                        @if ($publishingHouse['home_link'])
                                            <a href="{{ $publishingHouse['home_link'] }}" target="_blank">
                                                {{ $publishingHouse['publishing'] }}
                                            </a>
                                        @else
                                            {{ $publishingHouse['publishing'] }}
                                        @endif
                                    </td>
                                    <td>{{ $publishingHouse['mail_address'] }}</td>
                                    <td>{{ $publishingHouse['phone'] }}</td>
                                    <td>{{ $publishingHouse['genre'] ? \App\Http\FrontendHelpers::formatAssignmentType($publishingHouse['genre']) : ''}}</td>
                                    <td>
                                        @if ($publishingHouse['send_manuscript_link'])
                                            <a href="{{ $publishingHouse['send_manuscript_link'] }}" target="_blank">
                                                JA
                                            </a>
                                        @else
                                            Nei
                                        @endif
                                    </td>
                                    <td><a href="mailto:{{ $publishingHouse['email'] }}">{{ $publishingHouse['email'] }}</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="pull-right">
                        {{$publishingHouses->render()}}
                    </div>

                </div>

            </div>
        </div>
        <div class="clearfix"></div>
    </div>

@stop


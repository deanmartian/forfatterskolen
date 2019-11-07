@extends('backend.layout')

@section('title')
    <title>Show Applicant &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="container padding-top">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{ route('admin.personal-trainer.index') }}" class="btn btn-default">
                    <i class="fa fa-chevron-left"></i> Back
                </a>

                <h3><em>{{ $applicant->user->fullname }}</em></h3>

                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label>{{ trans('site.front.form.full-name') }}</label>
                                <em class="d-block font-16">
                                    {{ $applicant->user->fullname }}
                                </em>
                            </div>
                            <div class="col-sm-6">
                                <label>{{ trans('site.front.form.email-address') }}</label>
                                <em class="d-block font-16">
                                    {{ $applicant->user->email }}
                                </em>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-6">
                                <label for="phone" class="control-label">
                                    {{ trans('site.front.form.phone-number') }}
                                </label>
                                <em class="d-block font-16">
                                    {{ $applicant->user->address->phone }}
                                </em>
                            </div>
                            <div class="col-sm-6">
                                <label for="phone" class="control-label">
                                    {{ trans('site.front.form.age') }}
                                </label>
                                <em class="d-block font-16">
                                    {{ $applicant->age }}
                                </em>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="mb-4">
                                Skriv en valgfri tekst på 1000 ord (innenfor hvilken som helst sjanger, unntatt sakprosa)
                            </label>
                            <div class="pl-3">
                                {!! $applicant->optional_words !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="mb-4">
                                Hva er årsaken til at du søker dette kurset (kort begrunnelse)
                            </label>
                            <div class="pl-3">
                                {!! $applicant->reason_for_applying !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="mb-4">
                                Hva skal til for at du fullfører dette kurset?
                            </label>
                            <div class="pl-3">
                                {!! $applicant->need_in_course !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="mb-4">
                                Hvilke forventninger har du til deg selv – og oss?
                            </label>
                            <div class="pl-3">
                                {!! $applicant->expectations !!}
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="mb-4">
                                Hvor gira er du på å klare målet om ferdig manusutkast innen ett år (sett kryss ved det som er mest riktig):
                            </label>

                            <?php
                                $how_ready = \App\Http\FrontendHelpers::howReadyOptions($applicant->how_ready);
                            ?>
                            <div class="custom-radio px-0">
                                <input type="radio" name="how_ready" value="{{ $how_ready['id'] }}"
                                       id="{{ str_slug($how_ready['text']) }}" checked>
                                <label for="{{ str_slug($how_ready['text']) }}" class="font-weight-normal">{{ $how_ready['text'] }}</label>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


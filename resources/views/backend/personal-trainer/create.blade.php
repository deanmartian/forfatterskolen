@extends('backend.layout')

@section('title')
    <title>Create Applicant &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/css/bootstrap-select.min.css">
@stop

@section('content')
    <div class="container padding-top">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{ route('admin.personal-trainer.index') }}" class="btn btn-default">
                    <i class="fa fa-chevron-left"></i> Back
                </a>

                <form method="POST" action="{{ route('admin.personal-trainer.store') }}">
                    {{ csrf_field() }}
                    <h3>Create Applicant</h3>

                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="learner" class="control-label d-block">
                                    {{ trans_choice('site.learners', 1) }}
                                </label>
                                <select name="user_id" class="selectpicker" data-live-search="true">
                                    <option value="" disabled selected> - Select Learner -</option>
                                    @foreach(\App\Http\AdminHelpers::getLearnerList() as $learner)
                                        <option value="{{ $learner->id }}" data-tokens="{{ $learner->fullname }}"
                                                data-first_name="{{ $learner->first_name }}"
                                                data-last_name="{{ $learner->last_name }}"
                                                data-email="{{ $learner->email }}"
                                                data-phone="{{ $learner->address->phone }}">
                                            {{ $learner->fullname }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="email" class="control-label">
                                    {{ trans('site.front.form.email-address') }}
                                </label>
                                <input type="email" id="email" class="form-control large-input" name="email" required
                                       value="{{old('email')}}" placeholder="{{ trans('site.front.form.email-address') }}">
                            </div>

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="first_name" class="control-label">
                                        {{ trans('site.front.form.first-name') }}
                                    </label>
                                    <input type="text" id="first_name" class="form-control large-input" name="first_name" required
                                           value="{{old('first_name')}}"
                                           placeholder="{{ trans('site.front.form.first-name') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="last_name" class="control-label">
                                        {{ trans('site.front.form.last-name') }}
                                    </label>
                                    <input type="text" id="last_name" class="form-control large-input" name="last_name" required
                                           value="{{old('last_name')}}"
                                           placeholder="{{ trans('site.front.form.last-name') }}">
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 mb-4">
                                    <label for="phone" class="control-label">
                                        {{ trans('site.front.form.phone-number') }}
                                    </label>
                                    <input type="text" id="phone" class="form-control large-input" name="phone" required
                                           value="{{old('phone')}}">
                                </div>
                                <div class="col-md-6 mb-4">
                                    <label for="age" class="control-label">
                                        {{ trans('site.front.form.age') }}
                                    </label>
                                    <input type="number" id="age" class="form-control large-input" name="age"
                                           step="1" value="{{ old('age') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="mb-4">
                                    Skriv en valgfri tekst på 1000 ord (innenfor hvilken som helst sjanger, unntatt sakprosa)
                                </label>
                                <textarea class="form-control tinymce" name="optional_words" rows="12"
                                          id="optional_words">{{ old('optional_words') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label class="mb-4">
                                    Hva er årsaken til at du søker dette kurset (kort begrunnelse)
                                </label>
                                <textarea class="form-control tinymce" name="reason_for_applying" rows="12"
                                          id="reason_for_applying">{{ old('reason_for_applying') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label class="mb-4">
                                    Hva skal til for at du fullfører dette kurset?
                                </label>
                                <textarea class="form-control tinymce" name="need_in_course" rows="12"
                                          id="need_in_course">{{ old('need_in_course') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label class="mb-4">
                                    Hvilke forventninger har du til deg selv – og oss?
                                </label>
                                <textarea class="form-control tinymce" name="expectations" rows="12"
                                          id="expectations">{{ old('expectations') }}</textarea>
                            </div>

                            <div class="form-group">
                                <label class="mb-4">
                                    Hvor gira er du på å klare målet om ferdig manusutkast innen ett år (sett kryss ved det som er mest riktig):
                                </label>

                                @foreach(\App\Http\FrontendHelpers::howReadyOptions() as $readyOption)
                                    <div class="custom-radio px-0">
                                        <input type="radio" name="how_ready" value="{{ $readyOption['id'] }}"
                                               id="{{ str_slug($readyOption['text']) }}" required
                                                {{ old('how_ready') && old('how_ready') == $readyOption['id'] ? 'checked' : ''}}>
                                        <label for="{{ str_slug($readyOption['text']) }}">{{ $readyOption['text'] }}</label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary pull-right" id="submitOrder">
                                    Lever søknad
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.9/dist/js/bootstrap-select.min.js"></script>
    <script>
        let max_words = 1000;

        function getStats(id) {
            let body = tinymce.get(id).getBody(), text = tinymce.trim(body.innerText || body.textContent);

            return {
                chars: text.length,
                words: text.split(/[\w\u2019\'-]+/).length
            };
        }

        $("[name=user_id]").change(function(e){
            let selected = $(this).children("option:selected");
            let first_name = selected.data('first_name'),
                last_name = selected.data('last_name'),
                email = selected.data('email'),
                phone = selected.data('phone');
            $("[name=first_name]").val(first_name);
            $("[name=last_name]").val(last_name);
            $("[name=email]").val(email);
            $("[name=phone]").val(phone);
        })
    </script>
@stop


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="form-group row">
        <div class="col-md-6">
            <label for="first_name" class="control-label">
                {{ trans('site.front.form.first-name') }}
            </label>
            <p>
                {{ $application->user->first_name }}
            </p>
        </div>
        <div class="col-md-6">
            <label for="last_name" class="control-label">
                {{ trans('site.front.form.last-name') }}
            </label>
            <p>
                {{ $application->user->last_name }}
            </p>
        </div>
    </div>
    
    <div class="form-group row mb-0">
        <div class="col-md-6">
            <label for="phone" class="control-label">
                {{ trans('site.front.form.phone-number') }}
            </label>
            <p>
                {{ $application->user->address['phone'] }}
            </p>
        </div>
        <div class="col-md-6">
            <label for="age" class="control-label">
                {{ trans('site.front.form.age') }}
            </label>
            <p>
                {{ $application->age }}
            </p>
        </div>
    </div>
    
    <div class="form-group mt-5">
        <label class="control-label">
            Skriv en valgfri tekst på 1000 ord (innenfor hvilken som helst sjanger, unntatt sakprosa)
        </label>
        <p>
            {!! $application->optional_words !!}
        </p>
    </div>
    
    <div class="form-group">
        <label class="control-label">
            Hva er årsaken til at du søker dette kurset (kort begrunnelse)
        </label>
        <p>
            {!! $application->reason_for_applying !!}
        </p>
    </div>
    
    <div class="form-group">
        <label class="control-label">
            Hva skal til for at du fullfører dette kurset?
        </label>
        <p>
            {!! $application->need_in_course !!}
        </p>
    </div>
    
    <div class="form-group">
        <label class="control-label">
            Hvilke forventninger har du til deg selv – og oss?
        </label>
        <p>
            {!! $application->expectations !!}
        </p>
    </div>
    
    <div class="form-group">
        <label class="mb-4">
            Hvor gira er du på å klare målet om ferdig manusutkast innen ett år (sett kryss ved det som er mest riktig):
        </label>
        <p>
            {{ \App\Http\FrontendHelpers::howReadyOptions($application->how_ready)['text'] }}
        </p>
    </div>
</body>
</html>
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

<div class="form-group">
    <label class="control-label">
        Manuscript
    </label>
    <p>
        {!! $application->file_link !!}
    </p>
</div>
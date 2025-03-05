<div id="topbar">
    <div class="col-md-6">
        <h3>
            {{ trans('site.welcome-to-selfpublishing-portal') }}
            @if ($standardProject)
                - {{ $standardProject->name }}
            @endif
        </h3>
    </div>
</div>
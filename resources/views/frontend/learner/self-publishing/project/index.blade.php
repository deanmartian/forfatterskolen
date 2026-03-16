@extends('frontend.learner.self-publishing.layout')

@section('title')
    <title>Time Register &rsaquo; Forfatterskolen</title>
@stop

@section('content')
    <div class="learner-container">
        <div class="container">
            <div class="row">
                <div class="col-md-12 dashboard-course no-left-padding">
                    <div class="card sp-card">
                        <div class="sp-card-header">
                            <h1 class="d-inline-block">
                                {{ trans('site.author-portal.book-project') }}
                            </h1>

                            <button class="btn btn-brand projectBtn float-end" data-bs-toggle="modal" data-bs-target="#projectModal">
                                {{ trans('site.author-portal.add-book-project') }}
                            </button>
                        </div>
                        <div class="sp-card-body">
                            <table class="sp-table">
                                <thead>
                                <tr>
                                    <th>{{ trans('site.author-portal.project-number') }}</th>
                                    <th>{{ trans('site.author-portal.project-name') }}</th>
                                    <th>{{ trans('site.description') }}</th>
                                    <th>{{ trans('site.status') }}</th>
                                    <th>{{ trans('site.author-portal.standard-project') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($projects as $project)
                                    <tr>
                                        <td>
                                            {{ $project->identifier }}
                                        </td>
                                        <td>
                                            <a href="{{ route('learner.project.show', $project->id) }}">
                                                {{ $project->name }}
                                            </a>
                                        </td>
                                        <td>
                                            {{ $project->description }}
                                        </td>
                                        <td>
                                            {{ $project->start_date}}
                                            @if($project->end_date)
                                                - {{ $project->end_date }}
                                            @endif

                                            <br>

                                            @if($project->status === 'active')
                                                <span class="badge badge-primary">
                                                    {{ trans('site.author-portal.active') }}
                                                </span>
                                            @elseif ($project->status === 'lead')
                                                <span class="badge badge-warning">Prospekt</span>
                                            @elseif($project->status === 'finished')
                                                <span class="badge badge-success">
                                                    Fullført
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($project->is_standard)
                                                <span class="badge badge-primary">
                                                    {{ trans('site.author-portal.current') }}
                                                </span>
                                            @else
                                                <button class="btn btn-brand btn-sm standardProjectBtn" data-bs-toggle="modal"
                                                data-action="{{ route('learner.project.set-standard', $project->id) }}"
                                                data-bs-target="#standardProjectModal">
                                                    {{ trans('site.author-portal.set-standard') }}
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <iframe
src="https://www.chatbase.co/chatbot-iframe/s7nqoF2-3_v5RucONplQE"
width="100%"
height="700"
frameborder="0"
></iframe> --}}
        </div>
    </div>

    <div id="projectModal" class="modal fade" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content sp-modal">
                <div class="sp-modal__header">
                    <h3 class="sp-modal__title">
                        <i class="fas fa-plus-circle" style="color:var(--brand-primary);margin-right:6px" aria-hidden="true"></i>
                        {{ trans('site.author-portal.add-book-project') }}
                    </h3>
                    <button type="button" class="sp-modal__close" data-bs-dismiss="modal" aria-label="Lukk">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="{{ route('learner.save-project') }}" onsubmit="disableSubmit(this)" enctype="multipart/form-data" data-sp-validate>
                    {{ csrf_field() }}
                    <div class="sp-modal__body">
                        <div class="sp-form-group">
                            <label class="sp-label" for="idxProjectName">{{ trans('site.author-portal.project-name') }} <span class="sp-required">*</span></label>
                            <input type="text" class="sp-input" id="idxProjectName" name="name" required>
                            <span class="sp-error"></span>
                        </div>
                        <div class="sp-form-group">
                            <label class="sp-label" for="idxProjectDesc">{{ trans('site.description') }}</label>
                            <textarea class="sp-input sp-textarea" id="idxProjectDesc" name="description" rows="10"></textarea>
                        </div>
                    </div>
                    <div class="sp-modal__footer">
                        <button type="button" class="btn-outline-brand" data-bs-dismiss="modal">Avbryt</button>
                        <button type="submit" class="btn-brand">{{ trans('site.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="standardProjectModal" class="modal fade" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content sp-modal">
                <div class="sp-modal__header">
                    <h3 class="sp-modal__title">
                        <i class="fas fa-star" style="color:var(--brand-primary);margin-right:6px" aria-hidden="true"></i>
                        {{ trans('site.author-portal.standard-project') }}
                    </h3>
                    <button type="button" class="sp-modal__close" data-bs-dismiss="modal" aria-label="Lukk">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" action="" onsubmit="disableSubmit(this)">
                    {{ csrf_field() }}
                    <div class="sp-modal__body">
                        <p style="color:#374151;font-size:14px">
                            Er du sikker på at du vil sette dette prosjektet som <em>standard</em>?
                        </p>
                    </div>
                    <div class="sp-modal__footer">
                        <button type="button" class="btn-outline-brand" data-bs-dismiss="modal">Avbryt</button>
                        <button type="submit" class="btn-brand">{{ trans('site.save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('scripts')
<script>
    $(".standardProjectBtn").click(function() {
        const action = $(this).data('action');

        $("#standardProjectModal").find("form").attr("action", action)
    });
</script>
@stop
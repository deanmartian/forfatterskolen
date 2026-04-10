@extends('frontend.learner.self-publishing.layout')

@section('page_title', 'Faktura &rsaquo; Selvpublisering &rsaquo; Forfatterskolen')
@section('robots', '<meta name="robots" content="noindex, follow">')

@section('content')
    <div class="learner-container">
        <div class="container">
            <div class="row">
                <a href="{{ route('learner.project.show', $project->id) }}"
                   class="btn btn-outline-brand mb-3">
                    <i class="fa fa-arrow-left"></i> Tilbake
                </a>

                <div class="col-md-12 dashboard-course no-left-padding">
                    <div class="card sp-card">
                        <div class="sp-card-body p-0">
                            <table class="sp-table">
                                <thead>
                                <tr>
                                    <th>Fil</th>
                                    <th>Notat</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($invoices as $invoice)
                                    <tr>
                                        <td>
                                            <a href="{{ $invoice->invoice_file }}" download="">
                                                <i class="fa fa-download"></i>
                                            </a>
                                            <a href="{{ $invoice->invoice_file }}">
                                                {{ $invoice->filename }}
                                            </a>
                                        </td>
                                        <td>
                                            {!! $invoice->notes !!}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
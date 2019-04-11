@extends('backend.layout')

@section('title')
    <title>Testimonials &rsaquo; Forfatterskolen Admin</title>
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-comments"></i> Testimonials</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <a class="btn btn-success margin-top" href="{{ route('admin.testimonial.create') }}">Add Testimonial</a>

        <div class="table-users">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th width="700">Testimony</th>
                        <th>Status</th>
                        <th width="100"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($testimonials as $testimonial)
                        <tr>
                            <td>{{ $testimonial->id }}</td>
                            <td>{{ $testimonial->name }}</td>
                            <td>{{ $testimonial->description }}</td>
                            <td>{{ $testimonial->testimony }}</td>
                            <td>
                                <span class="{{ $testimonial->status ? 'text-primary' : 'text-danger' }}">
                                    {{ $testimonial->statusText }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.testimonial.edit', $testimonial->id) }}" class="btn btn-xs btn-primary">
                                    <i class="fa fa-pencil"></i>
                                </a>

                                <button class="btn btn-xs btn-danger" data-toggle="modal" data-target="#deleteTestimonialModal"
                                data-action="{{ route('admin.testimonial.destroy', $testimonial->id) }}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="pull-right">
                {{ $testimonials->render() }}
            </div>
        </div>
    </div>

    @include('backend.testimonials.partials.delete')
@stop

@section('scripts')
    <script>
        $(".btn-danger").click(function(){
            let action = $(this).data('action');
            $("#deleteTestimonialModal").find('form').attr('action', action);
        });
    </script>
@stop
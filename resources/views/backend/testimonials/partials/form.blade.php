<form method="POST" action="@if(Request::is('testimonial/*/edit')){{route('admin.testimonial.update', $testimonial['id'])}}@else{{route('admin.testimonial.store')}}@endif"
enctype="multipart/form-data">
    {{ csrf_field() }}
    @if(Request::is('testimonial/*/edit'))
        {{ method_field('PUT') }}
    @endif

    <div class="col-sm-12">
        @if(Request::is('testimonial/*/edit'))
            <h3>{{ trans('site.edit') }} Testimonial</h3>
        @else
            <h3>Create Testimonial</h3>
        @endif
    </div>

    <div class="col-sm-12 col-md-8">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" value="{{ $testimonial['name'] ?: old('name') }}" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <input type="text" class="form-control" name="description" value="{{ $testimonial['description'] ?: old('description') }}" required>
                </div>
                <div class="form-group">
                    <label>Testimony</label>
                    <textarea name="testimony" cols="30" rows="10" class="form-control">{{ $testimonial['testimony'] ?: old('testimony') }}</textarea>
                </div>

            </div>
        </div>
    </div>

    <div class="col-sm-12 col-md-4">
        <div class="panel panel-default">
            <div class="panel-body">

                <div class="form-group">
                    <label>Author Image</label>
                    <input type="file" name="author_image" accept="image/*" class="form-control">
                    <p class="text-center">
                        <small class="text-muted">90*90</small>
                        <br>
                        <small class="text-muted">
                            <a href="{{ asset($testimonial['author_image']) }}" target="_blank">
                                {{ \App\Http\AdminHelpers::extractFileName($testimonial['author_image']) }}
                            </a>
                        </small>
                    </p>
                </div>

                <div class="form-group">
                    <label>Book Image</label>
                    <input type="file" name="book_image" accept="image/*" class="form-control">
                    <p class="text-center">
                        <small class="text-muted">85*60</small>
                        <br>
                        <small class="text-muted">
                            <a href="{{ asset($testimonial['book_image']) }}" target="_blank">
                                {{ \App\Http\AdminHelpers::extractFileName($testimonial['book_image']) }}
                            </a>
                        </small>
                    </p>
                </div>

                <div class="form-group">
                    <label>Status</label> <br>
                    <input type="checkbox" data-toggle="toggle" data-on="Enabled" data-off="Disabled"
                           name="status">
                </div>

                @if(Request::is('testimonial/*/edit'))
                    <button type="submit" class="btn btn-primary">Update Testimonial</button>
                    <button type="button" class="btn btn-danger deletePoemBtn" data-toggle="modal" data-target="#deleteTestimonialModal"
                            data-action="{{ route('admin.testimonial.destroy', $testimonial['id']) }}">Delete Testimonial</button>
                @else
                    <button type="submit" class="btn btn-primary btn-block btn-lg">Create Testimonial</button>
                @endif
            </div>
        </div>
    </div>

</form>

@include('backend.testimonials.partials.delete')


@section('scripts')
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script>
        let status = parseInt('{{ $testimonial['status'] }}');
        if (status) {
            $("input[name=status]").bootstrapToggle('on');
        }

        $(".btn-danger").click(function(){
            let action = $(this).data('action');
            $("#deleteTestimonialModal").find('form').attr('action', action);
        });
    </script>
@stop
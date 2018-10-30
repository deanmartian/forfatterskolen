<div id="cloneTestimonialModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('site.clone') }} <em>{{$testimonial['name']}}</em></h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{route('admin.course-testimonial.clone', $testimonial['id'])}}">
          {{csrf_field()}}
          <p>
            {{ trans('site.clone-testimonial-question') }}
          </p>
          <button type="submit" class="btn btn-primary pull-right">{{ trans('site.clone') }}</button>
          <div class="clearfix"></div>
        </form>
      </div>
    </div>

  </div>
</div>
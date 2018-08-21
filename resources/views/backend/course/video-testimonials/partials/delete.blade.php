<div id="deleteEditorModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Delete <em>{{$testimonial['name']}}</em></h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{route('admin.course-testimonial.destroy', $testimonial['id'])}}">
          {{csrf_field()}}
          {{ method_field('DELETE') }}
          <p>
            Are you sure to delete this testimonial?
          </p>
          <button type="submit" class="btn btn-danger pull-right">Delete Testimonial</button>
          <div class="clearfix"></div>
        </form>
      </div>
    </div>

  </div>
</div>
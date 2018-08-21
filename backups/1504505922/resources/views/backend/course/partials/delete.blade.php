<div id="deleteCourseModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Delete <em>{{$course['title']}}</em></h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{route('admin.course.destroy', $course['id'])}}">
          {{csrf_field()}}
          {{ method_field('DELETE') }}
          <input type="hidden" name="variation_id">
          <p>
            WARNING: Deleting a course will also delete the learners who took this course.<br /><br />
            Are you sure to delete this course?
          </p>
          <button type="submit" class="btn btn-danger pull-right">Delete Course</button>
          <div class="clearfix"></div>
        </form>
      </div>
    </div>

  </div>
</div>
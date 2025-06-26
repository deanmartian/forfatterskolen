<div id="deleteLessonModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Delete <em>{{$lesson['title']}}</em></h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{route('admin.lesson.destroy', ['course_id' => $course->id, 'lesson' => $lesson['id']])}}"
          onsubmit="disableSubmit(this)">
          {{csrf_field()}}
          {{ method_field('DELETE') }}
          <input type="hidden" name="variation_id">
          <p>
            WARNING: This cannot be undone.<br /><br />
            Are you sure to delete this lesson?
          </p>
          <button type="submit" class="btn btn-danger pull-right">Delete Lesson</button>
          <div class="clearfix"></div>
        </form>
      </div>
    </div>

  </div>
</div>
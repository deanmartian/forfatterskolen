<div id="deleteWebinarModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Delete <em>{{$webinar['topic']}}</em></h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{route('admin.zoom.webinar.delete', $webinar['id'])}}">
          {{csrf_field()}}
          {{ method_field('DELETE') }}
          <p>
            Are you sure to delete this webinar?
          </p>
          <button type="submit" class="btn btn-danger pull-right">Delete Webinar</button>
          <div class="clearfix"></div>
        </form>
      </div>
    </div>

  </div>
</div>
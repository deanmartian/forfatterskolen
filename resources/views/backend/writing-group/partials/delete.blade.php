<div id="deleteWritingGroupModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Delete <em>{{$writingGroup['name']}}</em></h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{route('admin.writing-group.destroy', $writingGroup['id'])}}">
          {{csrf_field()}}
          {{ method_field('DELETE') }}
          <p>
            Are you sure to delete this writing group?
          </p>
          <button type="submit" class="btn btn-danger pull-right">Delete Group</button>
          <div class="clearfix"></div>
        </form>
      </div>
    </div>

  </div>
</div>
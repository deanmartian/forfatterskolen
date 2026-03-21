<div id="deleteEditorModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Slett <em>{{$editor['name']}}</em></h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{route('admin.editor.destroy', $editor['id'])}}"
          onsubmit="disableSubmit(this)">
          {{csrf_field()}}
          {{ method_field('DELETE') }}
          <p>
            Er du sikker på at du vil slette denne redaktøren?
          </p>
          <button type="submit" class="btn btn-danger pull-right">Slett redaktør</button>
          <div class="clearfix"></div>
        </form>
      </div>
    </div>

  </div>
</div>
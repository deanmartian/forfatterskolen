<div id="deleteLessonDocumentModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Delete <em></em></h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="">
          {{csrf_field()}}
          {{ method_field('DELETE') }}
          <p>
            WARNING: This cannot be undone.<br /><br />
            Are you sure to delete this lesson document?
          </p>
          <button type="submit" class="btn btn-danger pull-right">Delete Document</button>
          <div class="clearfix"></div>
        </form>
      </div>
    </div>

  </div>
</div>

<div id="deleteLessonFileModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Delete <em></em></h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="">
          {{csrf_field()}}
          {{ method_field('DELETE') }}
          <p>
            WARNING: This cannot be undone.<br /><br />
            Are you sure to delete this lesson file?
          </p>
          <button type="submit" class="btn btn-danger pull-right">Delete File</button>
          <div class="clearfix"></div>
        </form>
      </div>
    </div>

  </div>
</div>
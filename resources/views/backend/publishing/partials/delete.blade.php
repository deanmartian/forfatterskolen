<div id="deletePublishingModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('site.delete') }} <em>{{$publishingHouse['publishing']}}</em></h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{route('admin.publishing.destroy', $publishingHouse['id'])}}">
          {{csrf_field()}}
          {{ method_field('DELETE') }}
          <p>
            {{ trans('site.delete-publisher-house-question') }}
          </p>
          <button type="submit" class="btn btn-danger pull-right">{{ trans('site.delete-publisher-house') }}</button>
          <div class="clearfix"></div>
        </form>
      </div>
    </div>

  </div>
</div>
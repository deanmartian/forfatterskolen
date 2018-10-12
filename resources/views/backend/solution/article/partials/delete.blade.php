<div id="deleteSolutionArticleModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('site.delete') }} <em>{{$article['title']}}</em></h4>
      </div>
      <div class="modal-body">
        <form method="POST" action="{{route('admin.solution-article.destroy', ['solution_id' => $solution->id, 'id' => $article['id']])}}">
          {{csrf_field()}}
          {{ method_field('DELETE') }}
          <p>
            {{ trans('site.delete-article-question') }}
          </p>
          <button type="submit" class="btn btn-danger pull-right">{{ trans('site.delete-article') }}</button>
          <div class="clearfix"></div>
        </form>
      </div>
    </div>

  </div>
</div>
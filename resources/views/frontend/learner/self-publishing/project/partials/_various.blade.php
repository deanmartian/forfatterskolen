<div class="panel">
    <div class="panel-body">
        <div class="col-md-12">
            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">{{ trans('site.author-portal.publisher') }}</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div class="form-group">
                        <input type="text" class="form-control" name="publisher" 
                        value="{{ $projectUserBook->various->publisher ?? '' }}" disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">{{ trans('site.author-portal.minimum-beh') }}</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div class="form-group">
                        <input type="text" class="form-control" name="minimum_stock"
                        value="{{ $projectUserBook->various->minimum_stock ?? '' }}" disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">{{ trans('site.author-portal.weight-in-grams') }}</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div class="form-group">
                        <input type="text" class="form-control" name="weight"
                        value="{{ $projectUserBook->various->weight ?? '' }}" disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">{{ trans('site.author-portal.height-mm') }}</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div class="form-group">
                        <input type="text" class="form-control" name="height"
                        value="{{ $projectUserBook->various->height ?? '' }}" disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">{{ trans('site.author-portal.width-mm') }}</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div class="form-group">
                        <input type="text" class="form-control" name="width"
                        value="{{ $projectUserBook->various->width ?? '' }}" disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">{{ trans('site.author-portal.thickness-mm') }}</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div class="form-group">
                        <input type="text" class="form-control" name="thickness"
                        value="{{ $projectUserBook->various->thickness ?? '' }}" disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">{{ trans('site.author-portal.self-catering') }}</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div class="form-group">
                        <input type="text" class="form-control" name="cost"
                        value="{{ $projectUserBook->various->cost ?? '' }}" disabled>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-3">
                    <div class="form-group">
                        <label class="control-label">{{ trans('site.author-portal.material-cost') }}</label>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div class="form-group">
                        <input type="text" class="form-control" name="material_cost"
                        value="{{ $projectUserBook->various->material_cost ?? '' }}" disabled>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
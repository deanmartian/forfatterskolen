<div class="panel" id="various-panel" data-record="{{ json_encode($projectUserBook->varuous) }}">
    <div class="panel-body">
        <div class="col-md-6">
            <form method="POST" action="{{ route($saveVariousRoute, $projectUserBook->id) }}"
                onsubmit="disableSubmit(this)">
                @csrf
                <div class="row">
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label class="control-label">Publisher</label>
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
                            <label class="control-label">Minimumsbeh.</label>
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
                            <label class="control-label">Weight in grams</label>
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
                            <label class="control-label">Height mm</label>
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
                            <label class="control-label">Width mm</label>
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
                            <label class="control-label">Thickness mm</label>
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
                            <label class="control-label">Selvkost</label>
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
                            <label class="control-label">Mat.kost</label>
                        </div>
                    </div>
                    <div class="col-xs-9">
                        <div class="form-group">
                            <input type="text" class="form-control" name="material_cost"
                            value="{{ $projectUserBook->various->material_cost ?? '' }}" disabled>
                        </div>
                    </div>
                </div>

                <div class="pull-right">
                    <button type="button" class="btn btn-primary btn-sm" id="editVariousBtn">
                        Edit
                    </button>
        
                    <div class="save-various-container hidden">
                        <button type="submit" class="btn btn-primary btn-sm">
                            Save
                        </button>
        
                        <button type="button" class="btn btn-default btn-sm" id="cancelVariousBtn">
                            Cancel
                        </button>
                    </div>
                </div>
            </form>
        </div> <!-- end col-md-6 -->
    </div>
</div>
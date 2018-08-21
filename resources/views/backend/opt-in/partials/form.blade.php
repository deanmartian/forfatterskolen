<form method="POST" action="@if(Request::is('opt-in/*/edit')){{route('admin.opt-in.update', $optIn['id'])}}@else{{route('admin.opt-in.store')}}@endif"
      enctype="multipart/form-data">
    {{ csrf_field() }}
    @if(Request::is('opt-in/*/edit'))
        {{ method_field('PUT') }}
    @endif

    <div class="col-sm-12">
        @if(Request::is('opt-in/*/edit'))
            <h3>Edit <em>{{$optIn['title']}}</em></h3>
        @else
            <h3>Add Opt-in</h3>
        @endif
    </div>

    <div class="col-sm-12 col-md-8">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" value="{{ $optIn['name'] }}" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" name="email" value="{{ $optIn['email'] }}" required>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-12 col-md-4">
        <div class="panel panel-default">
            <div class="panel-body">
                @if(Request::is('opt-in/*/edit'))
                    <button type="submit" class="btn btn-primary">Update Opt-in</button>
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteOptInModal">Delete Opt-in</button>
                @else
                    <button type="submit" class="btn btn-primary btn-block btn-lg">Create Opt-in</button>
                @endif
            </div>
        </div>
    </div>
</form>
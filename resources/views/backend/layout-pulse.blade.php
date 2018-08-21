<!DOCTYPE html>
<html lang="en">
    <head>
        @yield('title')
        @include('backend.partials.backend-css')
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0 maximum-scale=1.0, user-scalable=no">
        @yield('styles')
    </head>
    <body class="dapulse-body">
    @include('backend.partials.navbar')

    <div class="fixed-sidebar">
        <div class="list-group">
            <a href="{{ route('admin.pulse.index') }}" class="list-group-item">
                <i class="fa fa-clipboard"></i> Boards
            </a>
                <div class="list-group-submenu">

                    @foreach (\Session::get('pulses') as $board)
                        <a href="{{ route('admin.board.show',$board->id) }}"
                           class="list-group-item {{ Session::has('board_id') && $board->id == Session::get('board_id') ? 'active' : '' }}">{{ $board->name }}</a>
                    @endforeach
                        <a href="#addBoardModal" class="list-group-item" data-toggle="modal"
                           data-backdrop="static" data-keyboard="false">
                            <i class="fa fa-plus-circle" style="color:#66ccf5; margin-right: 5px"></i>New
                        </a>
                </div>
        </div>
    </div>
    <div class="fixed-sidebar-content">
        <!-- Custom Content -->
        <div class="row">
            <div class="col-xs-12 panel">
                @yield('content')
            </div>
        </div>
        <!-- End Custom Content -->
    </div>

    <div id="addBoardModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">New Board</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.board.store') }}">
                        <?php echo e(csrf_field()); ?>
                        <div class="form-group">
                            <label for="">Owner</label>
                            <select name="owner" id="" class="form-control" required>
                                <option value="" disabled selected>Select Owner...</option>
                                @foreach(\App\Helpers\DapulseRepository::getUsers() as $user)
                                    <option value="{{ $user->id }}">
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="">Name</label>
                            <input type="text" class="form-control" name="board_name" required>
                        </div>

                        <div class="form-group">
                            <label for="">Description</label>
                            <input type="text" class="form-control" name="description">
                        </div>

                        <button class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    @include('backend.partials.scripts')
    @yield('scripts')

    </body>
</html>

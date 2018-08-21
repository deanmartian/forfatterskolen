<!DOCTYPE html>
<html lang="en">
    <head>
        @yield('title')
        @include('backend.partials.backend-css')
        <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0 maximum-scale=1.0, user-scalable=no">
        @yield('styles')
    </head>
    <body>
        @include('backend.partials.navbar')
        @yield('content')
        <div id="changePasswordModal" class="modal fade" role="dialog" data-backdrop="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Change Password</h4>
                    </div>
                    <div class="modal-body">
                        <form id="form-change-password" role="form" method="POST" action="{{ route('backend.change-password') }}" novalidate class="form-horizontal">
                            {{ csrf_field() }}
                            <div class="col-md-9">
                                <label for="current-password" class="col-sm-4 control-label">Current Password</label>
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <input type="password" class="form-control" id="current-password" name="current-password" placeholder="Password">
                                    </div>
                                </div>
                                <label for="password" class="col-sm-4 control-label">New Password</label>
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                                    </div>
                                </div>
                                <label for="password_confirmation" class="col-sm-4 control-label">Re-enter Password</label>
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Re-enter Password">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-offset-5 col-sm-6">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

        @if($errors->count())
            <?php
                $alert_type = session('alert_type');
                if(!Session::has('alert_type')) {
                    $alert_type = 'danger';
                }
            ?>
            <div class="alert alert-{{ $alert_type }} global-alert-box">
                <a href="#" class="close" data-dismiss="alert" aria-label="close" title="close">×</a>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @include('backend.partials.scripts')
        @yield('scripts')
    </body>
</html>

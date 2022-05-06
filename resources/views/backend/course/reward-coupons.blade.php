@extends('backend.layout')

@section('title')
    <title>Reward Coupons &rsaquo; {{$course->title}} &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
    <style>
        .global-alert-box {
            z-index: 9;
        }
    </style>
@stop

@section('content')

    @include('backend.course.partials.toolbar')

    <div class="course-container">
        @include('backend.partials.course_submenu')

        <div class="col-sm-12 col-md-10 sub-right-content">
            <div class="col-sm-12">
                <button type="button" class="btn btn-primary margin-bottom btn-add-reward" data-toggle="modal"
                        data-target="#rewardModal"
                        data-action="{{ route('admin.reward-coupons.store', $course->id) }}"
                data-title="Add Reward Coupon">+ Add Reward Coupon</button>

                <button type="button" class="btn btn-success margin-bottom btn-add-multiple-reward" data-toggle="modal"
                        data-target="#multipleRewardModal">+ Add Multiple Coupon Codes</button>

                <a href="{{ route('admin.reward-coupons.export-to-text', $course->id) }}" class="btn btn-info margin-bottom">
                    Export as Txt File
                </a>
            </div>

            <div class="col-sm-12 col-md-12">
                <div class="table-responsive">
                    <table class="table table-side-bordered table-white">
                        <thead>
                            <tr>
                                <th>Coupon</th>
                                <th>Is Used</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($course->rewardCoupons()->paginate(15) as $reward)
                                <tr>
                                    <td>{{ $reward['coupon'] }}</td>
                                    <td>{{ $reward['is_used'] ? 'Yes' : 'No' }}</td>
                                    <td>
                                        <button type="button" data-target="#rewardModal" data-toggle="modal" class="btn btn-info btn-xs btn-edit-reward"
                                                data-action="{{route('admin.reward-coupons.update', ['course_id' => $course->id, 'id' => $reward->id])}}"
                                            data-title="Edit Reward" data-coupon="{{ $reward['coupon'] }}">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        <button type="button" data-target="#deleteRewardModal" data-toggle="modal"
                                                class="btn btn-danger btn-xs btn-delete-reward"
                                                data-action="{{route('admin.reward-coupons.destroy', ['course_id' => $course->id, 'id' => $reward->id])}}"
                                        data-coupon="{{ $reward['coupon'] }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="pull-right">
                        {{ $course->rewardCoupons()->paginate(15)->appends(Request::except('page'))->render() }}
                    </div>
                </div>
            </div>

        </div> <!-- end sub-right-content -->

        <div class="clearfix"></div>
    </div> <!-- end course-container -->

    <div id="rewardModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>

                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{csrf_field()}}

                        <div class="form-group">
                            <label>Coupon</label>
                            <input type="text" name="coupon" placeholder="Coupon" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success pull-right">Submit</button>
                        <button type="button" class="btn btn-primary pull-right" style="margin-right: 5px"
                        onclick="generateCoupon()">Generate</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div> <!-- end rewardModal -->

    <div id="multipleRewardModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add Multiple Reward Modal</h4>
                </div>

                <div class="modal-body">
                    <form method="POST" action="{{route('admin.reward-coupons.multiple-store', ['course_id' => $course->id])}}" onsubmit="disableSubmit(this)">
                        {{csrf_field()}}

                        <div class="form-group">
                            <label>Reward Coupon Count</label>
                            <input type="number" name="coupon_count" placeholder="Coupon Count" class="form-control"
                                   step="1" min="1" required>
                        </div>
                        <button type="submit" class="btn btn-success pull-right">Submit</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div> <!-- end multiple reward modal -->

    <div id="deleteRewardModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Delete <em></em> Coupon</h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" onsubmit="disableSubmit(this)">
                        {{csrf_field()}}
                        {{ method_field('DELETE') }}
                        <p>
                            Are you sure to delete this coupon?
                        </p>
                        <button type="submit" class="btn btn-danger pull-right">Delete Coupon</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>

        </div>
    </div>

@stop

@section('scripts')
    <script>
        let reward_modal = $('#rewardModal');
        let delete_reward_modal = $('#deleteRewardModal');
        $(".btn-add-reward").click(function(){
            let action = $(this).data('action');
            let title = $(this).data('title');

            reward_modal.find('.modal-title').empty().text(title);
            reward_modal.find('form').attr('action', action);
            reward_modal.find('[name=_method]').remove();
            $('[name=coupon]').val('');
        });

        $(".btn-edit-reward").click(function(){
            let action = $(this).data('action');
            let title = $(this).data('title');
            let coupon = $(this).data('coupon');

            reward_modal.find('.modal-title').empty().text(title);
            reward_modal.find('form').attr('action', action);
            reward_modal.find('form').prepend('<input name="_method" value="PUT" type="hidden">');
            $('[name=coupon]').val(coupon);
        });

        $(".btn-delete-reward").click(function(){
            let action = $(this).data('action');
            let coupon = $(this).data('coupon');

            delete_reward_modal.find('.modal-title').find('em').text(coupon);
            delete_reward_modal.find('form').attr('action', action);
        });

        function generateCoupon() {
            let text = "";
            let possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

            for (let i = 0; i < 6; i++)
                text += possible.charAt(Math.floor(Math.random() * possible.length));

            $('[name=coupon]').val(text);
        }
    </script>
@stop
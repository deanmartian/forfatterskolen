@extends('backend.layout')

@section('title')
    <title>Course Discounts &rsaquo; Forfatterskolen Admin</title>
@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('js/toastr/toastr.min.css') }}">
@stop

@section('content')
    <div class="page-toolbar">
        <h3><i class="fa fa-file-text-o"></i> {{ $course->title }} {{ trans_choice('site.discounts', 2) }}</h3>
        <div class="clearfix"></div>
    </div>

    <div class="col-md-12">
        <a class="btn btn-success margin-top" data-target="#discountModal" data-toggle="modal"
           data-action="{{ route('admin.course-discount.store', $course->id) }}" id="add-discount">{{ trans('site.add-discount') }}</a>
        <div class="table-users table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ trans('site.id') }}</th>
                    <th>{{ trans('site.coupon') }}</th>
                    <th>{{ trans_choice('site.discounts', 1) }}</th>
                    <th>Valid From</th>
                    <th>Valid To</th>
                    <th>Type</th>
                    <th width="200"></th>
                </tr>
                </thead>

                <tbody>
                @foreach($discounts as $discount)
                    <tr>
                        <td> {{ $discount->id }} </td>
                        <td> {{ $discount->coupon }} </td>
                        <td> {{ $discount->discount }} </td>
                        <td> {{ $discount->valid_from ? \App\Http\FrontendHelpers::formatDate($discount->valid_from) : ''}} </td>
                        <td> {{ $discount->valid_to ? \App\Http\FrontendHelpers::formatDate($discount->valid_to) : ''}} </td>
                        <td>
                            {{ $typeList[$discount->type] }}
                        </td>
                        <td>
                            <?php
                                $discountUrl = config('app.live_url')."/course/".$discount->course_id
                                    ."/checkout?c=".$discount->coupon;
                            ?>
                            <input type="text" value="{{ $discountUrl }}"
                                   style="position: absolute; left: -10000px;">
                            <button type="button" class="btn btn-success btn-xs copyToClipboard">
                                <i class="fa fa-clipboard"></i>
                            </button>
                            <button type="button" class="btn btn-primary btn-xs editDiscountBtn" data-toggle="modal" data-target="#discountModal" data-action="{{ route('admin.course-discount.update', [$discount->course_id, $discount->id]) }}" data-fields="{{ json_encode($discount) }}"><i class="fa fa-pencil"></i></button>
                            <button type="button" class="btn btn-danger btn-xs deleteDiscountBtn" data-toggle="modal" data-target="#deleteDiscountModal" data-action="{{ route('admin.course-discount.destroy', [$discount->course_id, $discount->id]) }}"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="pull-right">
            {{$discounts->render()}}
        </div>
        <div class="clearfix"></div>
    </div>

    <div id="discountModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        {{ csrf_field() }}
                        <div class="form-group">
                            <label>{{ trans('site.coupon') }}</label>
                            <input type="text" class="form-control" name="coupon" required>
                        </div>
                        <div class="form-group">
                            <label>{{ trans_choice('site.discounts', 1) }}</label>
                            <input type="number" class="form-control" name="discount" required>
                        </div>
                        <div class="form-group">
                            <label>Valid From</label>
                            <input type="date" name="valid_from" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Valid To</label>
                            <input type="date" name="valid_to" class="form-control">
                        </div>
                        
                        <div class="form-grou">
                            <label>Type</label>
                            <select name="type" class="form-control">
                                @foreach ($typeList as $k => $type)
                                    <option value="{{ $k }}">
                                        {{ $type }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary pull-right margin-top"></button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteDiscountModal" class="modal fade" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Delete discount</h4>
                </div>
                <div class="modal-body">
                    Are you sure to delete this discount? <br>
                    Warning: This cannot be undone.
                    <form method="POST" action="">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <button type="submit" class="btn btn-danger pull-right margin-top">Delete</button>
                        <div class="clearfix"></div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script src="{{ asset('js/toastr/toastr.min.js') }}"></script>
    <script>
        var generated = [],
            possible  = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        let add_discount = '{{ trans("site.add-discount") }}';
        let edit_discount = '{{ trans("site.edit-discount") }}';
        let add_text = '{{ trans("site.add") }}';
        let edit_text = '{{ trans("site.edit") }}';

        function generateCodes(number, length) {
            generated = []; // empty the generated coupon
            for ( var i=0; i < number; i++ ) {
                generateCode(length);
            }
            $("input[name=coupon]").val(generated.join("\n"));
            $(".generator").addClass("generated");
        }
        function generateCode(length) {
            var text = "";

            for ( var i=0; i < length; i++ ) {
                text += possible.charAt(Math.floor(Math.random() * possible.length));
            }

            if ( generated.indexOf(text) == -1 ) {
                generated.push(text);
            }else {
                generateCode();
            }
        }
        $("#add-discount").on("click", function(e) {
            var num = 6,
                len = 1;

            possible  = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
            $(".modal-title").text(add_discount);

            var form = $("#discountModal form");
            var action = $(this).data('action');
            form.attr('action', action);
            form.find('[type=submit]').text(add_text);

            generateCodes(num, len);
        });

        $(function() {
            setTimeout(function () {
                $(".alert").fadeOut();
            }, 3000);

            $('.deleteDiscountBtn').click(function(){
                var form = $('#deleteDiscountModal form');
                var action = $(this).data('action');
                form.attr('action', action)
            });

            $(".editDiscountBtn").click(function(){
                var form = $("#discountModal form");
                var action = $(this).data('action');
                var fields = $(this).data('fields');

                form.find('input[name=coupon]').val(fields.coupon);
                form.find('input[name=discount]').val(fields.discount);
                form.find('input[name=valid_from]').val(fields.valid_from);
                form.find('input[name=valid_to]').val(fields.valid_to);
                form.find('[name=type]').val(fields.type);
                form.attr('action', action);
                form.prepend('<input type="hidden" name="_method" value="PUT">');
                form.find('[type=submit]').text(edit_text);

                $(".modal-title").text(edit_discount);
            });
        });

        // not working on hidden fields
        $(".copyToClipboard").click(function(){
            let copyText = $(this).closest('td').find('[type=text]');
            /* Select the text field */
            copyText.select();
            /* Copy the text inside the text field */
            document.execCommand("copy");

            toastr.success('Copied to clipboard.', "Success");
            if (window.getSelection) {
                if (window.getSelection().empty) {  // Chrome
                    window.getSelection().empty();
                } else if (window.getSelection().removeAllRanges) {  // Firefox
                    window.getSelection().removeAllRanges();
                }
            } else if (document.selection) {  // IE?
                document.selection.empty();
            }
        });
    </script>
@stop
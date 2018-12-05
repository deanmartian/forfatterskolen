<div class="form-container">
    <?php
        $slugList = ['children', 'crime'];
    ?>
    @if (in_array($slug, $slugList) && !Request::input('ref_id'))
        <i class="img-icon gift-icon"></i> <br>
    @endif

    <div class="form-section">
    Sign up to get a <b>referral</b> code!

        <form name='upviralForm{{$data['camp_id']}}' id='' method='post' action='https://app.upviral.com/site/parse_new_users/call/ajax/campId/{{$data['camp_id']}}'
        onsubmit="disableSubmitOrigText(this)">
            <div class="d-flex">
                <div class="col-md-9 col-sm-12">
                    <div class='form-group'>
                        <input type='text' name='name'  class='form-control' value='' placeholder="Name" required>
                    </div>
                    <div class='form-group'>
                        <input type='email' name='email' class='form-control' value='' placeholder="Email" required>
                    </div>
                </div>

                <div class="col-md-3 col-sm-12">
                    <div class='form-group'>
                        <button class="btn bg-site-red" type="submit" name="upviralsubmit">
                            <i class="img-icon coupon-icon"></i>
                            Get Code
                        </button>
                        {{--<input type='submit' name='upviralsubmit'  value="Get Code" class="btn bg-site-red">--}}
                        <input type='hidden' name='reflink' value='{{ Request::input('ref_id') }}'>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div> <!-- form-container -->
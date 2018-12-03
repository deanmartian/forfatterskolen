<div class="form-container">
    Sign up to get a <b>referral</b> code!

    <form name='upviralForm61832' id='' method='post' action='https://app.upviral.com/site/parse_new_users/call/ajax/campId/61832'
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
</div> <!-- form-container -->
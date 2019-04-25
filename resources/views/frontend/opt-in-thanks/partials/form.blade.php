<div class="form-container">
    <?php
        $slugIdList = [4, 5]; //gratis-krimkurs (crime), aldersgrupper (children)
    ?>
    @if (in_array($optIn->id, $slugIdList) && !Request::input('ref_id'))
        <i class="img-icon gift-icon"></i> <br>
    @endif

    <div class="form-section">
        {!! strtr(trans('site.front.opt-in-thanks.form.title'),
            ['_start_bold_' => '<b>', '_end_bold_' => '</b>']) !!}

        <form name='upviralForm{{$data['camp_id']}}' id='' method='post' action='https://app.upviral.com/site/parse_new_users/call/ajax/campId/{{$data['camp_id']}}'
        onsubmit="disableSubmitOrigText(this)">
            <div class="d-flex">
                <div class="col-md-9 col-sm-12">
                    <div class='form-group'>
                        <input type='text' name='name'  class='form-control' value=''
                               placeholder="{{ trans('site.front.form.name') }}" required>
                    </div>
                    <div class='form-group'>
                        <input type='email' name='email' class='form-control' value=''
                               placeholder="{{ trans('site.front.form.email-address') }}" required>
                    </div>
                </div>

                <div class="col-md-3 col-sm-12">
                    <div class='form-group'>
                        <button class="btn bg-site-red" type="submit" name="upviralsubmit">
                            <i class="img-icon coupon-icon"></i>
                            {!! strtr(trans('site.front.opt-in-thanks.form.button-text'),
                                ['_span_' => '<span></span>']) !!}
                        </button>
                        {{--<input type='submit' name='upviralsubmit'  value="Get Code" class="btn bg-site-red">--}}
                        <input type='hidden' name='reflink' value='{{ Request::input('ref_id') }}'>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div> <!-- form-container -->
<div class="description-container text-center">
    <?php
        $slugList = ['fiction'];
        $slugIdList = [3]; //dikt
    ?>
    @if (in_array($optIn->id, $slugIdList) || in_array($slug, $slugList))
        <i class="img-icon gift-icon"></i>
    @endif
    <h2>{{ trans('site.front.opt-in-thanks.description.title') }}</h2>
    <p>
        {{ trans('site.front.opt-in-thanks.description.details') }}:
    </p>

    <div class="point-details-container">
        <div class="col-md-2 col-sm-12 left-container">
            {{ trans('site.front.opt-in-thanks.description.left-container-first-number') }}
        </div>
        <div class="col-md-10 col-sm-12 text-left right-container">
            {{ trans('site.front.opt-in-thanks.description.right-container-first-text') }}
        </div>
    </div> <!-- end point-details-container -->

    <div class="point-details-container">
        <div class="col-md-2 col-sm-12  left-container">
            {{ trans('site.front.opt-in-thanks.description.left-container-second-number') }}
        </div>
        <div class="col-md-10 col-sm-12 text-left right-container">
            {{ trans('site.front.opt-in-thanks.description.right-container-second-text') }}
            <?php
                $displayText = '';
                switch($optIn->id) {
                    case 3:
                        $displayText = 'diktkurs';
                        break;
                    case 5:
                        $displayText = 'barnebokkurs';
                        break;
                    default:
                        $displayText = 'krimkurs';
                        break;
                }
            ?>
            {{ $displayText }}.
        </div>
    </div> <!-- end point-details-container -->
</div> <!-- end description-container -->
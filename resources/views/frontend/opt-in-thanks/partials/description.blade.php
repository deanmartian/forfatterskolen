<div class="description-container text-center">
    <?php
        $slugList = ['fiction'];
        $slugIdList = [3]; //dikt
    ?>
    @if (in_array($optIn->id, $slugIdList) || in_array($slug, $slugList))
        <i class="img-icon gift-icon"></i>
    @endif
    <h2>Vet du hva?</h2>
    <p>
        Hvis du deler referanselenken med dine venner og bekjente, ønsker vi å belønne deg med en unik bonus:
    </p>

    <div class="point-details-container">
        <div class="col-md-2 col-sm-12 left-container">
            5
        </div>
        <div class="col-md-10 col-sm-12 text-left right-container">
            Hvis du deler lenken, og får 5 av dine venner til å skrive seg på, får du en måneds gratis tilgang til
            webinarpakken (se kommende gjester lenger ned på siden)
        </div>
    </div> <!-- end point-details-container -->

    <div class="point-details-container">
        <div class="col-md-2 col-sm-12  left-container">
            10
        </div>
        <div class="col-md-10 col-sm-12 text-left right-container">
            Hvis du deler lenken, og får 10 av dine venner til å skrive seg på, får du hele 1000 kroner i rabatt på vårt
            kommende
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
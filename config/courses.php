<?php

return [
    'romankurs' => [
        'id' => 121,
        // Opprinnelig var dette "earlybird" med deadline 1. april og 5500 kr
        // rabatt. Fra og med Motor-webinar-kampanjen (april 2026) er dette
        // omdøpt internt til "webinar-pris" med 5000 kr rabatt, gyldig til
        // kursstart 20. april. Vi beholder config-nøklene for å unngå å
        // måtte endre på alle blade-views som bruker 'earlybird_*'.
        'earlybird_deadline' => '2026-04-20',
        'earlybird_discount' => 5000,
    ],
];

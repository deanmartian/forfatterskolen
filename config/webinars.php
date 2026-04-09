<?php

/**
 * Webinar-spesifikk konfigurasjon.
 *
 * motor_webinar_ids: liste over FreeWebinar-IDer som skal trigge
 * 'motor_webinar_registration' email-sekvensen i stedet for den
 * generiske 'webinar_registration'. Brukes av HomeController::
 * freeWebinarRegister og FetchFacebookLeads for å bestemme hvilken
 * sekvens som skal kjøres når noen melder seg på.
 *
 * Legg til flere ID-er her hvis du kjører flere Motor-type webinarer
 * senere, eller flytt til en annen trigger-event hvis du vil ha
 * separate sekvenser per webinar.
 */

return [
    'motor_webinar_ids' => [95],
];

<?php

/**
 * Konfig for AI-verktøy-systemet. Bestemmer hvilke verktøy som er
 * aktive, grenser per verktøy, og hvor lenge forslag lever før de
 * markeres som utløpte.
 *
 * Bruk: config('ai_tools.enabled.send_login_link') etc.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Enabled/disabled per tool
    |--------------------------------------------------------------------------
    | Hvis en verdi er false, blir verktøyet skjult fra AI-en (sendes ikke
    | med til Anthropic) og kan heller ikke kjøres via execute-tool-route.
    | Brukes hvis et verktøy må skrus av midlertidig pga. en bug.
    */
    'enabled' => [
        // Lookup
        'get_user_courses' => true,
        'get_invoice_status' => true,
        'get_assignment_status' => true,
        'get_upcoming_webinars' => true,

        // Action
        'add_internal_note' => true,
        'send_login_link' => true,
        'send_password_reset' => true,
        'extend_assignment_deadline' => true,
        'approve_extension_request' => true,
        'register_for_webinar' => true,
        'assign_editor_to_manuscript' => true,
        'mark_conversation_done' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Forslag-levetid
    |--------------------------------------------------------------------------
    | Hvor mange dager et AI-forslag er gyldig før en cron-jobb markerer
    | det som utløpt. Gamle forslag skal ikke kunne utføres fordi
    | konteksten rundt samtalen kan ha endret seg.
    */
    'suggestion_ttl_days' => 7,

    /*
    |--------------------------------------------------------------------------
    | Hard limits per tool
    |--------------------------------------------------------------------------
    | Brukes av tool-klassene for å begrense hva AI kan foreslå.
    | Disse må holdes i synk med konstantene i tool-klassene (f.eks.
    | ExtendAssignmentDeadlineTool::MAX_DAYS).
    */
    'limits' => [
        'extend_assignment_deadline_max_days' => 60,
        'max_suggestions_per_draft' => 5,
    ],

];

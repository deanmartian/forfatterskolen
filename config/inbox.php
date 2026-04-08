<?php

/*
|--------------------------------------------------------------------------
| Inbox-konfigurasjon — IMAP-kontoer som skal pollet
|--------------------------------------------------------------------------
|
| Hver konto i 'accounts'-arrayet beskriver en separat IMAP-mailbox som
| polleren skal hente nye e-poster fra. Hvert konto-element har:
|
|   - host:        IMAP-host i PHP-format, f.eks.
|                  '{imap.domeneshop.no:993/imap/ssl}INBOX'
|   - username:    IMAP-brukernavnet hos hosting-leverandør
|   - password:    IMAP-passordet (legg det i .env, ALDRI hardkode her)
|   - inbox_email: Hvilken e-post-adresse denne mailboksen er for.
|                  Brukes som 'inbox'-verdien på samtaler. Hvis denne
|                  e-posten matcher en aktiv admin-bruker sin e-post,
|                  blir samtalene automatisk PRIVATE for den brukeren.
|   - label:       Visnings-navn i admin-UI (valgfritt, default = inbox_email)
|
| For å legge til en ny inbox: opprett en ny IMAP-mailbox hos Domeneshop,
| legg til IMAP_USERNAME_X og IMAP_PASSWORD_X i .env, og legg til en
| ny entry i accounts-listen under.
|
| Tomme username/password gjør at kontoen blir hoppet over (trygt fallback).
|
*/

return [

    'accounts' => [

        // Hovedkonto — felles support-inbox (post@forfatterskolen.no)
        [
            'host' => env('IMAP_HOST', '{imap.domeneshop.no:993/imap/ssl}INBOX'),
            'username' => env('IMAP_USERNAME', 'forfatterskolen3'),
            'password' => env('IMAP_PASSWORD', ''),
            'inbox_email' => 'post@forfatterskolen.no',
            'label' => 'Felles support',
        ],

        // Sven Inges private inbox (sven.inge@forfatterskolen.no)
        // Krever IMAP_USERNAME_SVEN og IMAP_PASSWORD_SVEN i .env
        [
            'host' => env('IMAP_HOST_SVEN', '{imap.domeneshop.no:993/imap/ssl}INBOX'),
            'username' => env('IMAP_USERNAME_SVEN', ''),
            'password' => env('IMAP_PASSWORD_SVEN', ''),
            'inbox_email' => 'sven.inge@forfatterskolen.no',
            'label' => 'Sven Inge (privat)',
        ],

    ],

];

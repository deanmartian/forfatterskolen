<?php
    $pageMeta = \App\PageMeta::where('url', url()->current())->first();

    $checkoutTitle = 'Forfatterskolens utsjekksside der brukerne kan legge inn bestillinger';
    $checkoutDescription = 'Utsjekkssiden viser alle nødvendige felt og betalingsalternativer som gjør det enklere for brukeren å bestille varen';
    $genericTitle = 'Forfatterskolens side for forfattere';
    $genericDescription = 'Denne siden tilhører Forfatterskolen og viser innhold som hjelper forfattere å øke sin kunnskap';

    $meta_title = $pageMeta ? $pageMeta->meta_title :
        (strpos(url()->current(), 'checkout') !== false ? $checkoutTitle : $genericTitle);
    $meta_description = $pageMeta ? $pageMeta->meta_description :
        (strpos(url()->current(), 'checkout') !== false ? $checkoutDescription : $genericDescription);

    $defaultKeywords = 'forfatterskolen, forfatterkurs, manusutvikling, manuskript, dikt, sakprosa, serieroman, krim, roman';
    $meta_keywords = $pageMeta && $pageMeta->meta_keywords ? $pageMeta->meta_keywords : $defaultKeywords;
?>

<meta property="og:title" content="{{ $meta_title }}">
<meta property="og:description" content="{{ $meta_description }}">
<meta name="description" content="{{ $meta_description }}">
<meta property="og:site_name" content="Forfatterskolen">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:type" content="website" />
@if ($pageMeta && $pageMeta->meta_image)
    <meta property="og:image" content="{{ url($pageMeta->meta_image) }}">
    <meta property="twitter:image" content="{{ url($pageMeta->meta_image) }}">
@endif

<meta property="twitter:title" content="{{ $meta_title }}">
<meta property="twitter:description" content="{{ $meta_description }}">
<meta name="twitter:site" content="@forfatterskolen" />
<meta name="twitter:card" content="summary" />
<meta name="twitter:title" content="{{ $meta_title }}" />
<meta name="twitter:description" content="{{ $meta_description }}" />
<meta property="fb:app_id" content="300010277156315" />

<title>
    {{ $meta_title }}
</title>

<meta name="keywords" content="{{ $meta_keywords }}">
<meta name="nosnippets">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0 maximum-scale=1.0, user-scalable=no">
<meta name="csrf-token" content="{{ csrf_token() }}" />
<meta name="p:domain_verify" content="eca72f9965922b1f82c80a1ef6e62743"/>

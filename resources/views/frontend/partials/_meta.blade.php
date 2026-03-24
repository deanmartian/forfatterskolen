<?php
    $pageMeta = \App\PageMeta::where('url', url()->current())->first();

    $defaultTitle = 'Forfatterskolen — for deg som vil gjøre alvor av skrivedrømmen';
    $defaultDescription = 'Skrivekurs på nett med erfarne forfattere og redaktører. Fra idé til ferdig manus — roman, barnebok, sakprosa. 5000+ kursdeltagere siden 2015.';
    $defaultImage = asset('images-new/forfatterskolen-og.jpg');

    $meta_title = $pageMeta ? $pageMeta->meta_title : $defaultTitle;
    $meta_description = $pageMeta ? $pageMeta->meta_description : $defaultDescription;
    $meta_image = ($pageMeta && $pageMeta->meta_image) ? url($pageMeta->meta_image) : $defaultImage;

    $defaultKeywords = 'skrivekurs, forfatterkurs, romankurs, skriveverksted, manusutvikling, forfatterskolen, lær å skrive bok, skrivekurs på nett';
    $meta_keywords = $pageMeta && $pageMeta->meta_keywords ? $pageMeta->meta_keywords : $defaultKeywords;
?>

<meta property="og:title" content="{{ $meta_title }}">
<meta property="og:description" content="{{ $meta_description }}">
<meta name="description" content="{{ $meta_description }}">
<meta property="og:site_name" content="Forfatterskolen">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:type" content="website">
<meta property="og:image" content="{{ $meta_image }}">
<meta property="og:locale" content="nb_NO">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@forfatterskolen">
<meta name="twitter:title" content="{{ $meta_title }}">
<meta name="twitter:description" content="{{ $meta_description }}">
<meta name="twitter:image" content="{{ $meta_image }}">
<meta property="fb:app_id" content="300010277156315">

<title>{{ $meta_title }}</title>

<meta name="keywords" content="{{ $meta_keywords }}">
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=5.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="p:domain_verify" content="eca72f9965922b1f82c80a1ef6e62743">

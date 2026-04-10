{{-- Admin beholder FA4 + FA5 for å unngå manglende ikoner.
     FA5 v5.2.0 sin v4-shim dekker ikke alle FA4-ikoner (noen ble
     fjernet/omdøpt). Frontend bruker kun FA5 (der det fungerte).
     Admin kan migreres til kun FA5 gradvis over tid. --}}
<link rel="stylesheet" href="{{asset('css/font-awesome/css/font-awesome.min.css')}}">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css"
      integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
{{-- <link rel="stylesheet" href="{{asset('select2/dist/css/select2.min.css')}}"> --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="{{asset('css/vendor.css')}}">
<link rel="stylesheet" href="{{asset('css/backend.css')}}">
<link rel="stylesheet" href="{{asset('css/backend-v2.css')}}">
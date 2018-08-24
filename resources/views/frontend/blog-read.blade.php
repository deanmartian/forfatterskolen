@extends('frontend.layout')

@section('title')
    <title>{{ $blog->title }}</title>
@stop

@section('metas')
    <meta property="og:url"           content="{{ route('front.read-blog', $blog->id) }}" />
    <meta property="og:type"          content="website" />
    <meta property="og:title"         content="{{ $blog->title }}" />
    <meta property="og:description"   content="{{ substr(trim(strip_tags($blog->description)),0 , 100) }}" />
    <meta property="og:image"         content="{{ asset($blog->image) }}" />
@stop

@section('styles')
    <style>
        /* Fixed/sticky icon bar (vertically aligned 50% from the top of the screen) */
        .icon-bar-cont {
            position: fixed;
            top: 50%;
            -webkit-transform: translateY(-50%);
            -ms-transform: translateY(-50%);
            transform: translateY(-50%);
        }

        /* Style the icon bar links */
        .icon-bar-cont a {
            display: block;
            text-align: center;
            padding: 16px;
            transition: all 0.3s ease;
            color: white;
            font-size: 20px;
        }

        /* Style the social media icons with color, if you want */
        .icon-bar-cont a:hover {
            background-color: #000;
        }

        .facebook {
            background: #3B5998;
            color: white;
        }

        .twitter {
            background: #55ACEE;
            color: white;
        }

        .google {
            background: #dd4b39;
            color: white;
        }

        .linkedin {
            background: #007bb5;
            color: white;
        }

        .youtube {
            background: #bb0000;
            color: white;
        }

    </style>
@stop

@section('content')
<?php
$config     = config('services.facebook');
$client_id  = $config['client_id'];
$secret     = $config['client_secret'];
?>
    <div class="container blog-read-container">
        <h1 class="text-center">{{ $blog->title }}</h1>
        {!! $blog->description !!}
    </div>

<div class="icon-bar-cont">
    <a href="http://www.facebook.com/sharer.php?u={{ route('front.read-blog', $blog->id) }}" class="facebook" target="_new">
        <i class="fa fa-facebook"></i>
    </a>

    <a href="https://twitter.com/share?url={{ route('front.read-blog', $blog->id) }};text={{ $blog->title }}" class="twitter" target="_new">
        <i class="fa fa-twitter"></i>
    </a>
    {{--<a href="#" class="google"><i class="fa fa-pinterest"></i></a>
    <a href="#" class="linkedin"><i class="fa fa-linkedin"></i></a>
    <a href="#" class="youtube"><i class="fa fa-youtube"></i></a>--}}
</div>

@stop

@section('scripts')
    <script>
        let site_url = '{{ route('front.read-blog', $blog->id) }}';

        $('a[target^="_new"]').click(function() {
            return openWindow(this.href);
        });

        // for positioning and resizing the new window
        function openWindow(url) {

            if (window.innerWidth <= 640) {
                // if width is smaller then 640px, create a temporary a elm that will open the link in new tab
                let a = document.createElement('a');
                a.setAttribute("href", url);
                a.setAttribute("target", "_blank");

                let dispatch = document.createEvent("HTMLEvents");
                dispatch.initEvent("click", true, true);

                a.dispatchEvent(dispatch);
                window.open(url);
            }
            else {
                let width = window.innerWidth * 0.66 ;
                // define the height in
                let height = width * window.innerHeight / window.innerWidth ;
                // Ratio the hight to the width as the user screen ratio
                window.open(url , 'newwindow', 'width=' + width + ', height=' + height + ', top=' + ((window.innerHeight - height) / 2) + ', left=' + ((window.innerWidth - width) / 2));
            }
            return false;
        }

        // for getting the share count of facebook
        jQuery(function($) {
            let token = '{{ $client_id }}|{{ $secret }}';
            $.ajax({
                url: 'https://graph.facebook.com/v3.0/',
                dataType: 'jsonp',
                type: 'GET',
                data: {fields:'engagement', access_token: token, id: site_url },
                success: function(data){
                    //$('#results').html('<strong>Number of shares:</strong> ' + data.engagement.share_count);
                }
            });
        });
    </script>
@stop
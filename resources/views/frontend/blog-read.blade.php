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

        /*.facebook {
            background: #3B5998;
            color: white;
        }

        .twitter {
            background: #55ACEE;
            color: white;
        }*/

        .smGlobalBtn { /* global button class */
            display: inline-block;
            position: relative;
            cursor: pointer;
            width: 50px;
            height: 50px;
            border:2px solid #ddd; /* add border to the buttons */
            box-shadow: 0 3px 3px #999;
            padding: 0px;
            text-decoration: none;
            text-align: center;
            color: #fff;
            font-size: 25px;
            font-weight: normal;
            line-height: 2em;
            border-radius: 27px;
            -moz-border-radius:27px;
            -webkit-border-radius:27px;
        }

        /* facebook button class*/
        .facebookBtn{
            background: #4060A5;
        }

        .facebookBtn:before{ /* use :before to add the relevant icons */
            font-family: "FontAwesome";
            content: "\f09a"; /* add facebook icon */
        }

        .facebookBtn:hover{
            color: #4060A5;
            background: #fff;
            border-color: #4060A5; /* change the border color on mouse hover */
        }

        /* twitter button class*/
        .twitterBtn{
            background: #00ABE3;
        }

        .twitterBtn:before{
            font-family: "FontAwesome";
            content: "\f099"; /* add twitter icon */

        }

        .twitterBtn:hover{
            color: #00ABE3;
            background: #fff;
            border-color: #00ABE3;
        }

        /* google plus button class*/
        .googleplusBtn{
            background: #e64522;
        }

        .googleplusBtn:before{
            font-family: "FontAwesome";
            content: "\f0d5"; /* add googleplus icon */
        }

        .googleplusBtn:hover{
            color: #e64522;
            background: #fff;
            border-color: #e64522;
        }

        /* linkedin button class*/
        .linkedinBtn{
            background: #0094BC;
        }

        .linkedinBtn:before{
            font-family: "FontAwesome";
            content: "\f0e1"; /* add linkedin icon */
        }

        .linkedinBtn:hover{
            color: #0094BC;
            background: #fff;
            border-color: #0094BC;
        }

        /* pinterest button class*/
        .pinterestBtn{
            background: #cb2027;
        }

        .pinterestBtn:before{
            font-family: "FontAwesome";
            content: "\f0d2"; /* add pinterest icon */
        }

        .pinterestBtn:hover{
            color: #cb2027;
            background: #fff;
            border-color: #cb2027;
        }

        /* tumblr button class*/
        .tumblrBtn{
            background: #3a5876;
        }

        .tumblrBtn:before{
            font-family: "FontAwesome";
            content: "\f173"; /* add tumblr icon */
        }

        .tumblrBtn:hover{
            color: #3a5876;
            background: #fff;
            border-color: #3a5876;
        }

        /* rss button class*/
        .rssBtn{
            background: #e88845;
        }

        .rssBtn:before{
            font-family: "FontAwesome";
            content: "\f09e"; /* add rss icon */
        }

        .rssBtn:hover{
            color: #e88845;
            background: #fff;
            border-color: #e88845;
        }

        #social-container {
            margin: 20px 0;
        }

        #social-container a:hover {
            text-decoration: none;
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
        <div class="col-md-8 col-sm-offset-2">
            {!! $blog->description !!}

            <div id="social-container">
                <a href="http://www.facebook.com/sharer.php?u={{ route('front.read-blog', $blog->id) }}"
                   class="facebookBtn smGlobalBtn" target="_new">
                </a>

                <a href="https://twitter.com/share?url={{ route('front.read-blog', $blog->id) }};text={{ $blog->title }}"
                   class="twitterBtn smGlobalBtn" target="_new">
                </a>
                {{--<a href="#" class="google"><i class="fa fa-pinterest"></i></a>
                <a href="#" class="linkedin"><i class="fa fa-linkedin"></i></a>
                <a href="#" class="youtube"><i class="fa fa-youtube"></i></a>--}}
            </div>
        </div>
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
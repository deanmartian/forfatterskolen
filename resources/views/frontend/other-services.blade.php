@extends('frontend.layout')

@section('title')
    <title>Forfatterskolen</title>
@stop

@section('styles')
    <style>
        body, html {
            height: 100%;


        }

        div.bg {
            /* The image used */
            background-image: url("images/background.jpg");

            /* Full height */
            height: 1000px;
            padding: 28px;


            /* Center and scale the image nicely */
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
            margin-bottom: 20px;
        }

        p.title {
            position: relative;
            top: 320px;
            left: 28px;
            text-transform: uppercase;
            font-family: "Lato Black";
            font-size: 60pt;
            color: #ffffff;
        }
        .container {
            position: relative;
        }
        div.image1 {
            width: 347px;
            height: 121px;
            position: absolute;
            top: 976px;
            left: 20px;
            transition: box-shadow .3s;
        }
        div.image2 {
            width: 307px;
            height: 121px;
            position: absolute;
            top: 976px;
            left: 371px;
        }
        div.image3 {
            width: 307px;
            height: 121px;
            position: absolute;
            top: 976px;
            left: 682px;
        }
        div.space {
            position: absolute;
            bottom: -250px;
            visibility: hidden;
        }
        .highlight {
            box-sizing: border-box;
            padding-bottom: 1px;
            border-bottom: solid 3px #E83945;
        }
        .book {
            position: absolute;
            top: 27px;
            left: 35px;
        }
        .correct {
            position: absolute;
            top: 27px;
            left: 35px;
        }
        .error {
            position: absolute;
            top: 27px;
            left: 35px;
        }
        p.sub {
            position: absolute;
            color: #ffffff;
            font-family: Lato;
            font-size: 17pt;
            text-transform: uppercase;
            top: 30px;
            left: 120px;
            text-decoration: solid;
        }
        p.link {
            position: absolute;
            color: #e61d41;
            font-family: Lato;
            font-size: 10pt;
            text-transform: uppercase;
            top: 65px;
            left: 120px;
        }
        #priority a {
            color: #e61d41;
        }
        #priority a:hover {
            color: #ffffff;
            text-decoration: none;
        }
        #priority a:link {
            color: #ffffff;
            text-decoration: none;
        }
        .box:hover {
            top: 966px;
            transition: opacity 0.3s ease-in-out;;
        }
    </style>
@stop

@section('content')
    <div class="container">
        <div class="bg">

            <p class="title"><span class="highlight">an</span>dre<br>tjenester</p>
            <div class="image1 container box"><img src="images/backimage1.png"><img src="images/book.png" class="book">
                <p class="sub">COACHING TIMER</p>
                <p class="link" id="priority"><a href="#">view more ></a></p>

            </div>&nbsp;
            <div class="image2 container box"><img src="images/back2.png"><img src="images/correct.png" class="correct">
                <p class="sub">KORREKTUR</p>
                <p class="link" id="priority"><a href="#">view more ></a></p>

            </div>&nbsp;
            <div class="image3 container box"><img src="images/back2.png"><img src="images/error.png" class="error">
                <p class="sub">SPRÅKVASK</p>
                <p class="link" id="priority"><a href="#">view more ></a></p>

            </div>
            <div class="space">s</div>
        </div>
    </div>
@stop
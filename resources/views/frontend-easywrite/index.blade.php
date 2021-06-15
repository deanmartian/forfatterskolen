@extends('frontend-easywrite.layouts.layout')

@section('content')
    <div class="main">

        <div class="section-1">
            <div>
                <div class="div-1">
                    <div>
                        <img class="logo" src="{{ URL::asset('images/front-end/logo-white.png') }}" alt="">
                    </div>
                    <div>
                        <p>Tempus imperdiet nulla malesuada</p>
                    </div>
                </div>
                <div class="div-2">
                    <div><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation.</p></div>
                </div>
            </div>
            <div class="div-3">
                <button class="btn button-blue">Book a consult</button>
                <button class="btn button-red">For Publishers</button>
            </div>
        </div>

        <div class="section-2">
            <div class="div-1">
                <div class="bullets font-blue">Choose the right package for you</div>
                <div class="packages">
                    <div class="slider" id="package-slide">
                        <div class="slide">
                            <div class="package-data">
                                <div class="price font-red">$000</div>&nbsp;
                                <div class="title font-blue">Express</div>
                                <div class="sub-title font-gray">For authors with print ready files</div>&nbsp;
                                <div class="desc">25 softcover books eBook conversion & distribution Print On Demand distribution Two ISBN barcodes</div>&nbsp;
                                <div class="more font-red">Find out more</div>
                            </div>
                        </div>
                        <div class="slide">
                            <div class="package-data">
                                <div class="price font-red">$0000</div>&nbsp;
                                <div class="title font-blue">COMPLETE</div>
                                <div class="sub-title font-gray">The essentials for a professional book release.</div>&nbsp;
                                <div class="desc">25 softcover books eBook conversion & distribution Print On Demand distribution Two ISBN barcodes</div>&nbsp;
                                <div class="more font-red">Find out more</div>
                            </div>
                        </div>
                        <div class="slide">
                            <div class="package-data">
                                <div class="price font-red">$000</div>&nbsp;
                                <div class="title font-blue">DELUXE</div>
                                <div class="sub-title font-gray">The best way to print, publish, and promote your book.</div>&nbsp;
                                <div class="desc">25 softcover books eBook conversion & distribution Print On Demand distribution Two ISBN barcodes</div>&nbsp;
                                <div class="more font-red">Find out more</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rates">
                    <div class="title font-blue">RATES</div>
                    <div class="desc font-blue">Senectus et netus et malesuada fames ac. Platea dictumst vestibulum rhoncus est pellentesque elit ullamcorper. </div>
                </div>
            </div>
            <div class="div-2">
                <div class="all_features">
                    <div class="features box">
                        <div>Features</div>&nbsp;
                        <div>Price for Full-Length Book</div>
                        <div>Why Choose this Package?</div>
                        <div>Pre-Planning and Research</div>
                        <div>Intro Questionnaire and Comprehensive Publishing Checklist</div>
                        <div>Strategy Session Team</div>
                        <div>Strategy Session Location</div>
                    </div>
                    <div class="features-data">
                        <div class="slider" id="features-slide">
                            <div class="slide">
                                <div class="express box">
                                    <div>Express</div>
                                    <div class="price font-red">$000 - $0000</div>
                                    <div>Comprehensive book-writing and publishing navigation services performed by a team of talented industry insiders.</div>&nbsp;
                                    <div><i class="fa fa-check font-red"></i></div>
                                    <div><i class="fa fa-check font-red"></i></div>
                                    <div><i class="fa fa-check font-red"></i></div>
                                    <div><i class="fa fa-check font-red"></i></div>
                                </div>
                            </div>
                            <div class="slide">
                                <div class="complete box">
                                    <div>Complete</div>
                                    <div class="price font-red">$000 - $0000</div>
                                    <div>Comprehensive book-writing package with detailed market research, beta testing, and tailored publishing navigation.</div>
                                    <div><i class="fa fa-check font-red"></i></div>
                                    <div><i class="fa fa-check font-red"></i></div>
                                    <div><i class="fa fa-check font-red"></i></div>
                                    <div><i class="fa fa-check font-red"></i></div>
                                </div>
                            </div>
                            <div class="slide">
                                <div class="delux box">
                                    <div>Delux</div>
                                    <div class="price font-red">$000 - $0000</div>
                                    <div>For exceptional authors who demand the highest level of service and talent in the industry, our Black Card package includes every available amenity.<br><br><p>Limited availability.</p></div>
                                    <div><i class="fa fa-check font-red"></i></div>
                                    <div><i class="fa fa-check font-red"></i></div>
                                    <div><i class="fa fa-check font-red"></i></div>
                                    <div><i class="fa fa-check font-red"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="see-all-rates">
                    <button class="btn button-red">See all rates</button>
                </div>
            </div>
        </div>

        <div class="section-3">
                <div class="what-we-do">
                    <div class="div-1">
                        <div class="bullets font-blue">What we do</div>
                    </div>
                    <div class="div-2">
                        <div class="custom-printed-books">
                            <div class="image right"> 
                                <img src="{{ URL::asset('images/front-end/magazines-wooden-chair.jpg') }}" alt="">
                            </div>
                            <div class="text left">
                                <div class="title">
                                    Custom Printed Books
                                </div><br>
                                <div class="desc">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div><br>
                                <div class="learn-more">Learn more</div>
                            </div>
                        </div>
                        <div class="professional-ghost-writing">
                            <div class="image left"> 
                                <img src="{{ URL::asset('images/front-end/calligraphy-artist-writing-with-pen.jpg') }}" alt="">
                            </div>
                            <div class="text right">
                                <div class="title">
                                    PROFESSIONAL GHOSTWRITING
                                </div><br>
                                <div class="desc">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div><br>
                                <div class="learn-more">Learn more</div>
                            </div>
                        </div>
                        <div class="book-editing">
                            <div class="image right"> 
                                <img src="{{ URL::asset('images/front-end/stacked-books-min.jpg') }}" alt="">
                            </div>
                            <div class="text left">
                                <div class="title">
                                    BOOK EDITING
                                </div><br>
                                <div class="desc">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div><br>
                                <div class="learn-more">Learn more</div>
                            </div>
                        </div>
                        <div class="publishing-consulting">
                            <div class="image left"> 
                                <img src="{{ URL::asset('images/front-end/business-women-talking.jpg') }}" alt="">
                            </div>
                            <div class="text right">
                                <div class="title">
                                    BOOK EDITING
                                </div><br>
                                <div class="desc">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div><br>
                                <div class="learn-more">Learn more</div>
                            </div>
                        </div>
                    </div>
                    <div class="buttons">
                        <button class="btn button-white">See more service</button>
                        <button class="btn button-red">Contact us</button>
                    </div>
                </div>
        </div>

        <div class="section-4">
            <div class="div-1">
                <div class="comprehensive-writing-service-include">
                    <div class="bullets font-blue">Our comprehensive writing services inlcude</div>
                    <div class="icons">
                        <div>
                            <div><img src="{{ URL::asset('images/front-end/section4-icon1.png') }}" alt=""></div>
                            <div>Book-Planning</div>
                        </div>
                        <div>
                            <div><img src="{{ URL::asset('images/front-end/section4-icon2.png') }}" alt=""></div>
                            <div>Ghostwriting</div>       
                        </div>
                        <div>
                            <div><img src="{{ URL::asset('images/front-end/section4-icon3.png') }}" alt=""></div>
                            <div>Editors</div>
                        </div>
                        <div>
                            <div><img src="{{ URL::asset('images/front-end/section4-icon4.png') }}" alt=""></div>
                            <div>Printing</div>
                        </div>
                        <div>
                            <div><img src="{{ URL::asset('images/front-end/section4-icon5.png') }}" alt=""></div>
                            <div>Project Assistance</div>
                        </div>
                        <div>
                            <div><img src="{{ URL::asset('images/front-end/section4-icon6.png') }}" alt=""></div>
                            <div>illustration (Cover Design)</div>
                        </div>       
                        <div>
                            <div><img src="{{ URL::asset('images/front-end/section4-icon7.png') }}" alt=""></div>
                            <div>Book Proposals and Queries</div>
                        </div>
                        <div>
                            <div><img src="{{ URL::asset('images/front-end/section4-icon8.png') }}" alt=""></div>
                            <div>Beta Reader Testing</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="div-2"></div>
            <div class="div-3"></div>
        </div>

        <div class="section-4b">
            <div class="some-of-our-works">
                <div class="head">
                    <div class="bullets font-blue">Some of our works</div>
                    <div><button class="btn button-red">See all our works</button></div>
                </div>
                <div class="slider" id="some-of-our-works-books">
                    <div class="slide">
                        <img src="{{ URL::asset('images/front-end/books/1.jpg') }}" alt="">
                    </div>
                    <div class="slide">
                        <img src="{{ URL::asset('images/front-end/books/2.jpg') }}" alt="">
                    </div>
                    <div class="slide">
                        <img src="{{ URL::asset('images/front-end/books/3.jpg') }}" alt="">
                    </div>
                    <div class="slide">
                        <img src="{{ URL::asset('images/front-end/books/4.jpg') }}" alt="">
                    </div>
                    <div class="slide">
                        <img src="{{ URL::asset('images/front-end/books/5.jpg') }}" alt="">
                    </div>
                    <div class="slide">
                        <img src="{{ URL::asset('images/front-end/books/6.jpg') }}" alt="">
                    </div>
                    <div class="slide">
                        <img src="{{ URL::asset('images/front-end/books/7.png') }}" alt="">
                    </div>
                    <div class="slide">
                        <img src="{{ URL::asset('images/front-end/books/8.png') }}" alt="">
                    </div>
                    <div class="slide">
                        <img src="{{ URL::asset('images/front-end/books/9.png') }}" alt="">
                    </div>
                    <div class="slide">
                        <img src="{{ URL::asset('images/front-end/books/10.png') }}" alt="">
                    </div>
                </div>
            </div>
            <hr>
            <div class="what-our-customer-say">
                <div>
                    <div class="title">What our customers say</div>
                    <div class="content">
                        <div class="slider" id="what-our-customer-say">
                            <div class="slide">
                                <div class="qoute"><img src="{{ URL::asset('images/front-end/qoute-icon.png') }}" alt=""></div>
                                <div class="content">Nunc consequat interdum varius sit amet mattis. Nascetur ridiculus mus mauris vitae. Gravida neque convallis a cras semper auctor. Volutpat blandit aliquam etiam erat velit scelerisque in dictum. Porta non pulvinar neque laoreet suspendisse interdum consectetur libero. Sed vulputate odio ut enim. Ut porttitor leo a diam sollicitudin tempor id eu.</div>
                                <div class="author">
                                    <div class="img">
                                        <img src="{{ URL::asset('images/front-end/section-1-bg.jpg') }}" alt="">
                                    </div>
                                    <div class="authorname">Author 1</div>
                                    <div class="desc2">Cursus eget nunc scelerisque <br> viverra mauris in</div>
                                </div>
                            </div>
                            <div class="slide">
                                <div class="qoute"><img src="{{ URL::asset('images/front-end/qoute-icon.png') }}" alt=""></div>
                                <div class="content">Nunc consequat interdum varius sit amet mattis. Nascetur ridiculus mus mauris vitae. Gravida neque convallis a cras semper auctor. Volutpat blandit aliquam etiam erat velit scelerisque in dictum. Porta non pulvinar neque laoreet suspendisse interdum consectetur libero. Sed vulputate odio ut enim. Ut porttitor leo a diam sollicitudin tempor id eu.</div>
                                <div class="author">
                                    <div class="img">
                                        <img src="{{ URL::asset('images/front-end/section-1-bg.jpg') }}" alt="">
                                    </div>
                                    <div class="authorname">Author 2</div>
                                    <div class="desc2">Cursus eget nunc scelerisque <br> viverra mauris in</div>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>

        <div class="section-5">
            <div class="why-choose">
                <div>
                    <img src="{{ URL::asset('images/front-end/book-icon.png') }}" alt="">
                </div>
                <div>
                    <img class="logo" src="{{ URL::asset('images/front-end/logo-blue.png') }}" alt=""><br><br>
                    <div>
                        <div class="title">Why Choose Easywrite Publishing</div><br>
                        <div class="desc">Volutpat ac tincidunt vitae semper. Nisi lacus sed viverra tellus in hac. Aliquet risus feugiat in ante metus dictum at tempor. Elementum facilisis leo vel fringilla. Aliquam ut porttitor leo a diam sollicitudin tempor. Tincidunt arcu non sodales neque sodales ut. Volutpat ac tincidunt vitae semper quis lectus nulla at. </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="book-a-consultant">
                    <div>
                        <div class="title">Volutpat ac tincidunt vitae?</div><br>
                        <div class="desc">Volutpat ac tincidunt vitae semper. Nisi lacus sed viverra tellus in hac. Aliquet risus feugiat in ante metus dictum at tempor. </div>
                    </div><br><br>
                    <div>
                        <button class="btn button-blue">BOOK A CONSULT</button>
                        <button class="btn button-white">FOR PUBLISHERS</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="section-6 footer">
            <div>
                <div>
                    <div>
                        <img class="logo" src="{{ URL::asset('images/front-end/logo-blue.png') }}" alt=""><br><br>
                    </div>
                    <div>
                        <div class="title">About</div><br>
                        <div class="desc">
                            Volutpat ac tincidunt vitae semper. Nisi lacus sed viverra tellus in hac. Aliquet risus feugiat in ante metus dictum at tempor. 
                        </div>
                    </div>
                    <div class="footer-1">
                        <div class="title">Services</div><br>
                        <ul>
                            <li>Book-Planning</li>
                            <li>Project Assistance</li>
                            <li>Ghostwriting</li>
                            <li>Illustration</li>
                            <li>Editors</li>
                            <li>Book Proposals</li>
                            <li>Printing</li>
                            <li>Beta Reader Testing</li>
                        </ul>
                    </div>
                </div>
                <hr>
                <div class="footer-2">
                    <div>© 2021 EasyWrite Publishing. All rights reserved.</div>
                    <div>Privacy Policy  |  Terms of Use</div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('/components/slick-master/slick/slick.css')  }}">
<link rel="stylesheet" href="{{ asset('/components/slick-master/slick/slick-theme.css')  }}">
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="{{ asset('/components/slick-master/slick/slick.min.js')  }}"></script>
<script type="text/javascript">
var $jq = jQuery.noConflict();
$jq(document).ready(function() { 
    $jq('#some-of-our-works-books').slick({
		dots: true,
		arrows: false,

		speed: 1000,

		slidesToShow: 6,
		slidesToScroll: 1,

		autoplay: true,
		autoplaySpeed: 2000,

		// Pauses autoplay on focus
		pauseOnFocus: true,

		// Pauses autoplay when a dot is hovered
		pauseOnDotsHover: false,
		
		// Breakpoint triggered settings
		responsive: [
		{
		    breakpoint: 1599,
		    settings: {
		        slidesToShow: 5,
		        slidesToScroll: 1,
		        infinite: true,
		        dots: true
		    }
		},
		{
		    breakpoint: 1000,
		    settings: {
		        slidesToShow: 4,
		        slidesToScroll: 1
		    }
		},
        {
		    breakpoint: 800,
		    settings: {
		        slidesToShow: 3,
		        slidesToScroll: 1
		    }
		},
        {
		    breakpoint: 600,
		    settings: {
		        slidesToShow: 2,
		        slidesToScroll: 1
		    }
		},
		{
		    breakpoint: 400,
		    settings: {
		        slidesToShow: 1,
		        slidesToScroll: 1
		    }
		}
		],

		zIndex: 1000,
	});
    $jq('#what-our-customer-say').slick({
		dots: true,
		arrows: false,

		speed: 1000,

		slidesToShow: 2,
		slidesToScroll: 1,

		autoplay: true,
		autoplaySpeed: 2000,

		// Pauses autoplay on focus
		pauseOnFocus: true,

		// Pauses autoplay when a dot is hovered
		pauseOnDotsHover: false,

		zIndex: 1000,

        // break point
        responsive: [
		{
		    breakpoint: 800,
		    settings: {
		        slidesToShow: 1,
		        slidesToScroll: 1,
		        infinite: true,
		        dots: true
		    }
		},
        ]
	});
    $jq('#features-slide').slick({
		dots: true,
		arrows: false,

		speed: 1000,

		slidesToShow: 3,
		slidesToScroll: 1,

		autoplay: true,
		autoplaySpeed: 2000,

		// Pauses autoplay on focus
		pauseOnFocus: true,

		// Pauses autoplay when a dot is hovered
		pauseOnDotsHover: false,

		zIndex: 1000,
        responsive: [
		{
		    breakpoint: 1000,
		    settings: {
		        slidesToShow: 2,
		        slidesToScroll: 1,
		        infinite: true,
		        dots: true
		    }
		},
        {
		    breakpoint: 800,
		    settings: {
		        slidesToShow: 1,
		        slidesToScroll: 1,
		        infinite: true,
		        dots: true
		    }
		},
        ]
        // break point
	});
    $jq('#package-slide').slick({
		dots: true,
		arrows: false,

		speed: 1000,

		slidesToShow: 3,
		slidesToScroll: 1,

		autoplay: true,
		autoplaySpeed: 2000,

		// Pauses autoplay on focus
		pauseOnFocus: true,

		// Pauses autoplay when a dot is hovered
		pauseOnDotsHover: false,

		zIndex: 1000,

        // break point
        responsive: [
		{
		    breakpoint: 1000,
		    settings: {
		        slidesToShow: 2,
		        slidesToScroll: 1,
		        infinite: true,
		        dots: true
		    }
		},
        {
		    breakpoint: 800,
		    settings: {
		        slidesToShow: 1,
		        slidesToScroll: 1,
		        infinite: true,
		        dots: true
		    }
		},
        ]
	});
});
</script>
@endsection
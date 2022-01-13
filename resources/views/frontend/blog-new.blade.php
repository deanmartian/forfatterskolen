@extends('frontend.layout')

@section('title')
    <title>Forfatterskolen Blog</title>
@stop

@section('content')

    <div class="blog-page" data-bg="https://www.forfatterskolen.no/images-new/blog-bg.png">
        <div class="container main-container">
            <div class="row">
                <div class="col-sm-12">
                    <h1 class="text-center mb-5 mt-0">
                        {{ trans('site.front.blog.title') }}
                    </h1>
                </div>
                <div class="col-sm-12 top-page-container">
                    <div class="main-blog" data-bg="https://www.forfatterskolen.no/{{ $mainBlog->image }}">
                        <div class="details text-center">
                            <div class="title h1 mt-0">
                                {{ $mainBlog->title }}
                            </div>

                            <div class="date-author-cont color-b4 my-4">
                                <span class="date mr-5">
                                    <i class="img-icon calendar"></i>
                                    {{ \App\Http\FrontendHelpers::formatDate($mainBlog->created_at) }}
                                </span>
                                <span class="author">
                                    <i class="img-icon author-image"></i>
                                    {{ $mainBlog->author_name ?: $mainBlog->user->full_name }}
                                </span>
                            </div>

                            <div class="description color-b4">
                                {!! strlen($mainBlog->description) > 200
                                ? substr(strip_tags(html_entity_decode($mainBlog->description)),0,200).'....'
                                : $mainBlog->description !!}
                            </div>

                            <a href="{{ route('front.read-blog', $mainBlog->id) }}" class="btn buy-btn">
                                {{ ucwords(trans('site.front.view')) }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="row">
                <div class="col-sm-8 blog-container">
                    @include('frontend.blog-post')
                </div>
                <div class="col-sm-4 sidebar">
                    <div class="row course-container">
                        <img data-src="https://www.forfatterskolen.no/images-new/girl-with-computer.png" alt="">
                        <div class="card">
                            <span>
                                {{ trans('site.front.blog.view-our-course') }}
                            </span>
                            <a href="{{ route('front.course.index') }}" class="btn float-right site-btn-global-w-arrow">
                                {{ ucwords(trans('site.front.view')) }}
                            </a>
                        </div>
                    </div> <!-- end course-container -->

                    {{--<div class="row poem-container">
                        <div class="card">
                            <div class="header">
                                <a href="" class="btn buy-btn">Poem from our students</a>
                            </div>
                            <div class="card-body text-center">
                                <p>
                                    There are no #strangers here, only #friends that have not yet met.
                                </p>
                                <small class="color-b4">John Doe</small>
                            </div>
                        </div>
                    </div> <!-- end poem-container -->

                    <div class="row testimonial-container">
                        <div class="card">
                            <div class="header">
                                <a href="" class="btn buy-btn">Testimonials</a>
                            </div>
                            <div class="card-body">
                                <div id="testimonials-carousel" class="carousel slide" data-ride="carousel" data-interval="false">

                                    <!-- Indicators -->
                                    <ul class="carousel-indicators">
                                        <li data-target="#testimonials-carousel" data-slide-to="0" class="active"></li>
                                        <li data-target="#testimonials-carousel" data-slide-to="1"></li>
                                        <li data-target="#testimonials-carousel" data-slide-to="2"></li>
                                    </ul>

                                    <!-- The slideshow -->
                                    <div class="container carousel-inner no-padding text-center">
                                        <div class="carousel-item active">
                                            <p>
                                                There are no #strangers here, only #friends that have not yet met.
                                            </p>
                                            <small class="color-b4">John Doe</small>
                                        </div>
                                        <div class="carousel-item">
                                            <p>
                                                There are no #strangers here, only #friends that have not yet met.
                                            </p>
                                            <small class="color-b4">John Doe</small>
                                        </div>
                                        <div class="carousel-item">
                                            <p>
                                                There are no #strangers here, only #friends that have not yet met.
                                            </p>
                                            <small class="color-b4">John Doe</small>
                                        </div>
                                    </div>
                                </div> <!-- end testimonials-carouse -->
                            </div> <!-- end card-body -->
                        </div> <!-- end card -->
                    </div> <!-- end testimonial-container -->--}}

                    <div class="row workshop-container">
                        <div class="card">
                            <div class="header">
                                <a href="" class="btn buy-btn">
                                    {{ trans('site.front.blog.next-writing-workshop') }}
                                </a>
                            </div>
                            <div class="card-body text-center">
                                <?php
                                    $latestWorkshops = \App\Workshop::where('is_free','=',0)
                                        ->orderBy('faktura_date', 'asc')->limit(3)->get();
                                ?>
                                <ul>
                                    @foreach($latestWorkshops as $workshop)
                                        <li>
                                            <a href="{{ route('front.workshop.show', $workshop->id) }}">
                                                {{ $workshop->title }} <br>
                                                <span class="color-b4">{{ \Carbon\Carbon::parse($workshop->date)->format('d.F Y') }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                </div> <!-- end sidebar -->
            </div> <!-- end row -->
        </div> <!-- end container-->
    </div> <!-- end blog-page -->

@stop

@section('scripts')
    <script type="text/javascript">

        $(document).ready(function() {
            $(document).on('click', '.pagination a', function (e) {
                e.preventDefault();
                getPosts($(this).attr('href').split('page=')[1]);
            });

            $('a[target^="_new"]').click(function() {
                return openWindow(this.href);
            });

            // check if hash is present
            /*if (window.location.hash) {
                let page = window.location.hash.replace('#', '');
                if (page === Number.NaN || page <= 0) {
                    return false;
                } else {
                    getPosts(page);
                }
            }*/

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
        });

        function getPosts(page) {
            $.ajax({
                url : '?page=' + page,
                dataType: 'json',
            }).done(function (data) {
                $('.blog-container').html(data);
                //location.hash = page;
            }).fail(function () {
                alert('Posts could not be loaded.');
            });
        }
    </script>
@stop
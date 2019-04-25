<div class="col-sm-12">
    <div class="testimonial-container text-center">
        <h1 class="title">
            {{ ucwords(trans('site.front.next-webinar')) }}
        </h1>

        <div id="testimonial-carousel" class="carousel slide global-carousel"
             data-ride="carousel" data-interval="false">

            <?php
            $webinars_chunk = $next_webinars->chunk(3);
            ?>
            <!-- Indicators -->
            <ul class="carousel-indicators">
                @for($i=0; $i<=$webinars_chunk->count() - 1;$i++)
                    <li data-target="#testimonial-carousel" data-slide-to="{{$i}}"
                        @if($i == 0) class="active" @endif></li>
                @endfor
            </ul>

            <!-- The slideshow -->
            <div class="container carousel-inner">
                @foreach($webinars_chunk as $k => $webinars)
                    <div class="carousel-item {{ $k==0 ? 'active' : '' }}">
                        @foreach($webinars as $webinar)
                            <div class="col-md-4 col-sm-12">
                                <div class="card">
                                    <div class="card-body text-center">
                                        <div class="editor-circle">
                                            <img src="{{ $webinar->image ?: asset('/images/no_image.png')}}" alt="" class="rounded-circle">
                                        </div>
                                        <h2>
                                            {{ $webinar->title }}
                                        </h2>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>

        </div> <!-- end testimonials-carouse -->
    </div>
</div>
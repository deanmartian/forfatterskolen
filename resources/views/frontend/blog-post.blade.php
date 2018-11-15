@foreach($blogs->chunk(2) as $blog_chunk)
    <div class="row">
        @foreach($blog_chunk as $blog)
            <div class="col-sm-6 blog-column">
                <div class="blog-row">
                    <img src="{{ asset($blog->image) }}" alt="" width="auto">

                    <div class="details">
                        <div class="date-author-cont color-b4 mb-4">
                            <span class="date mr-5">
                                <i class="img-icon calendar"></i>
                                {{ \App\Http\FrontendHelpers::formatDate($blog->created_at) }}
                            </span>
                            <span class="author">
                                <i class="img-icon author-image"></i>
                                {{ $blog->author_name ?: $blog->user->full_name }}
                            </span>
                        </div> <!-- end date-author-cont color-b4 -->

                        <h1 class="title mb-4">
                            {{ $blog->title }}
                        </h1>

                        <div class="description color-b4 mb-5">
                            {!! strlen($blog->description) > 200
                            ? substr(strip_tags(html_entity_decode($blog->description)),0,200).'....'
                            : $blog->description !!}
                        </div>

                        <div class="button-container">
                            <a href="{{ route('front.read-blog', $blog->id) }}" class="btn buy-btn">
                                Les Mer
                            </a>
                            <span class="social-container">
                                <a href="http://www.facebook.com/sharer.php?u={{ route('front.read-blog', $blog->id) }}"
                                   target="_new">
                                    <img src="{{asset('images-new/social-icons/facebook.png')}}" class="social-image mr-2">
                                </a>
                                <a href="https://twitter.com/share?url={{ route('front.read-blog', $blog->id) }};text={{ $blog->title }}"
                                   target="_new">
                                    <img src="{{asset('images-new/social-icons/twitter.png')}}" class="social-image">
                                </a>
                            </span>
                        </div>

                    </div> <!-- end details -->
                </div> <!-- end blog-row -->
            </div>
        @endforeach
    </div>
@endforeach

<div class="row d-block">
    @if ($blogs->hasPages())
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($blogs->onFirstPage())
                <li class="disabled"><span>Older Posts</span></li>
            @else
                <li><a href="{{ $blogs->previousPageUrl() }}" rel="prev">Older Posts</a></li>
            @endif

            {{-- Next Page Link --}}
            @if ($blogs->hasMorePages())
                <li><a href="{{ $blogs->nextPageUrl() }}" rel="next">Newer Posts</a></li>
            @else
                <li class="disabled"><span>Newer Posts</span></li>
            @endif
        </ul>
    @endif
</div>
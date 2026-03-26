@foreach($blogs as $blog)
    <a href="{{ route('front.read-blog', $blog->id) }}" class="bl-card">
        <div class="bl-card-img">
            @if($blog->image)
                <img src="{{ asset($blog->image) }}" alt="{{ $blog->title }}">
            @else
                <div class="bl-avatar-sm" style="width:44px;height:44px;font-size:14px;">
                    {{ strtoupper(substr($blog->author_name ?: ($blog->user->full_name ?? '?'), 0, 2)) }}
                </div>
            @endif
        </div>
        <div class="bl-card-body">
            <span class="bl-tag">{{ $blog->category ?? 'Blogg' }}</span>
            <h3>{{ $blog->title }}</h3>
            <p class="bl-excerpt">
                {!! strlen($blog->description) > 150
                    ? substr(strip_tags(html_entity_decode($blog->description)), 0, 150) . '...'
                    : strip_tags($blog->description) !!}
            </p>
            <div class="bl-card-footer">
                <span class="bl-author">{{ $blog->author_name ?: ($blog->user->full_name ?? '') }}</span>
                <span class="bl-date">{{ \App\Http\FrontendHelpers::formatDate($blog->created_at) }}</span>
            </div>
        </div>
    </a>
@endforeach

@if ($paginator->hasPages())
    <div class="custom-center-pagination">
        <nav>
            <ul class="pagination">
                {{-- Previous Page Link --}}
                @if (!$paginator->onFirstPage())
                    <li class="page-item first-child">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                            <i class="fa fa-arrow-left"></i>
                        </a>
                    </li>
                @endif

                {{-- Page Numbers --}}
                @for ($i = max(1, $paginator->currentPage() - 4); $i <= min($paginator->lastPage(), $paginator->currentPage() + 4); $i++)
                    <li class="page-item {{ ($paginator->currentPage() == $i) ? 'active' : '' }}">
                        <a class="page-link" href="{{ $paginator->url($i) }}">{{ $i }}</a>
                    </li>
                @endfor

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item last-child">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                            <i class="fa fa-arrow-right"></i>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
@endif



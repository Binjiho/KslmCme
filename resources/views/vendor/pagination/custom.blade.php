@if ($paginator->hasPages())
    <div class="paging-wrap">
        <ul class="paging">
            {{-- Previous Page Link --}}
            @if (!$paginator->onFirstPage())
                <li class="first">
                    <a href="{{ $paginator->url(1) }}" rel="prev" aria-label="@lang('pagination.previous')">
                        <span class="hide">처음</span>
                    </a>
                </li>

                <li class="prev">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">
                        <img src="/assets/image/board/ic_prev.png" alt="prev">
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="disabled num" aria-disabled="true"><span>{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="num on" aria-current="page"><a href="javascript:void(0);">{{ $page }}</a></li>
                        @else
                            <li class="num"><a href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="next">
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">
                        <span class="hide">다음</span>
                    </a>
                </li>

                <li class="last" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <a href="{{ $paginator->url($paginator->lastPage()) }}">
                        <span class="hide">마지막</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
@endif

@if($paginator->hasPages())
    <nav class="pagination" aria-label="">
        <ul class="pagination__links">
            @if(! $paginator->onFirstPage())
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" class="btn btn--sm btn--outline-primary" rel="prev">
                        {{ __('Previous') }}
                    </a>
                </li>
            @endif
            @foreach($elements as $element)
                @if(is_string($element))
                    <li>
                        <span class="btn btn--sm btn--outline-primary" aria-disabled="true">
                            {{ $element }}
                        </span>
                    </li>
                @elseif(is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if($page === $paginator->currentPage())
                                <span class="btn btn--sm btn--outline-primary" aria-disabled="true" aria-current="page">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="btn btn--sm btn--outline-primary">
                                    {{ $page }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach
            @if($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" class="btn btn--sm btn--outline-primary" rel="next">
                        {{ __('Next') }}
                    </a>
                </li>
            @endif
        </ul>
    </nav>
@endif

@if ($paginator->hasPages())
  {{-- TODO: FIX PADDING HERE --}}
  <div class="pagination-wrapper">
    <ul class="pagination pagination-no-border" >
        <!-- Previous Page Link -->
        <div>
        @if ($paginator->onFirstPage())
            <li class="page-item disabled"><span class="page-link">&laquo;</span></li>
        @else
            <li class="page-item arrow-left" ><a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">&laquo;</a></li>
        @endif
        </div>

        <!-- Pagination Elements -->
        <div>
        @foreach ($elements as $element)
            <!-- "Three Dots" Separator -->
            @if (is_string($element))
                <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
            @endif

            <!-- Array Of Links -->
            <span >
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active center"><span class="page-link">{{ $page }}</span></li>
                    @else
                        <li class="page-item center"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
            <span>
        @endforeach
        </div>

        <!-- Next Page Link -->
        <div>
        @if ($paginator->hasMorePages())
            <li class="page-item arrow-right" ><a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">&raquo;</a></li>
        @else
            <li class="page-item disabled" ><span class="page-link">&raquo;</span></li>
        @endif
        </div>
    </ul>
  </div>
@endif

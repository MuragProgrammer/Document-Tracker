@if ($paginator->hasPages())
<div class="pagination-container">

    <!-- Prev / Info / Next Row -->
    <div class="pagination-top-row">
        <!-- Previous -->
        <div class="pagination-prev">
            @if ($paginator->onFirstPage())
                <span class="pagination-disabled">&laquo; Previous</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="pagination-link">&laquo; Previous</a>
            @endif
        </div>

        <!-- Info -->
        <div class="pagination-info">
            Showing {{ $paginator->firstItem() }} to {{ $paginator->lastItem() }} of {{ $paginator->total() }} results
        </div>

        <!-- Next -->
        <div class="pagination-next">
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="pagination-link">Next &raquo;</a>
            @else
                <span class="pagination-disabled">Next &raquo;</span>
            @endif
        </div>
    </div>

    <!-- Page Numbers Row -->
    <div class="pagination-numbers">
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="pagination-ellipsis">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="pagination-current">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pagination-page">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach
    </div>

</div>
@endif

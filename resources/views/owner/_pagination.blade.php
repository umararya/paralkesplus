@php
    $current  = $paginator->currentPage();
    $last     = $paginator->lastPage();
    $window   = 2;
    $pages    = [];
    for ($p = 1; $p <= $last; $p++) {
        if ($p === 1 || $p === $last || ($p >= $current - $window && $p <= $current + $window)) {
            $pages[] = $p;
        }
    }
    $rendered = [];
    $prev = null;
    foreach ($pages as $p) {
        if ($prev !== null && $p - $prev > 1) $rendered[] = '...';
        $rendered[] = $p;
        $prev = $p;
    }
    $pageName = $pageName ?? 'page';
@endphp

<div class="table-footer">
    <div class="pagination-meta">
        Menampilkan <strong>{{ $paginator->firstItem() }} – {{ $paginator->lastItem() }}</strong>
        dari <strong>{{ $paginator->total() }}</strong>
    </div>
    @if($paginator->hasPages())
    <nav class="pagination-nav">
        @if($paginator->onFirstPage())
            <span class="page-btn disabled"><i class="ri-arrow-left-s-line"></i></span>
        @else
            <a class="page-btn" href="{{ $paginator->previousPageUrl() }}"><i class="ri-arrow-left-s-line"></i></a>
        @endif

        @foreach($rendered as $item)
            @if($item === '...')
                <span class="page-ellipsis">…</span>
            @elseif($item == $current)
                <span class="page-btn active">{{ $item }}</span>
            @else
                <a class="page-btn" href="{{ $paginator->url($item) }}">{{ $item }}</a>
            @endif
        @endforeach

        @if($paginator->hasMorePages())
            <a class="page-btn" href="{{ $paginator->nextPageUrl() }}"><i class="ri-arrow-right-s-line"></i></a>
        @else
            <span class="page-btn disabled"><i class="ri-arrow-right-s-line"></i></span>
        @endif
    </nav>
    @endif
</div>
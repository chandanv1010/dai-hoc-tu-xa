@if ($model->hasPages())
    <ul class="pagination">
        {{-- Previous Page Link --}}
        @php
            $prevPageUrl = null;
            if ($model->currentPage() > 1) {
                $prevUrl = $model->previousPageUrl();
                if ($prevUrl) {
                    $parsed = parse_url($prevUrl);
                    $query = [];
                    if (isset($parsed['query'])) {
                        parse_str($parsed['query'], $query);
                    }
                    $pageNum = $query['page'] ?? ($model->currentPage() - 1);
                    $prevPageUrl = formatPaginationUrl($prevUrl, $pageNum);
                }
            }
        @endphp
        @if ($prevPageUrl)
            <li class="page-item"><a class="page-link" href="{{ $prevPageUrl }}">Previous</a></li>
        @else
            <li class="page-item disabled"><span class="page-link">Previous</span></li>
        @endif

        {{-- Pagination Links --}}
        @foreach ($model->getUrlRange(max(1, $model->currentPage() - 2), min($model->lastPage(), $model->currentPage() + 2)) as $page => $url)
            @php
                $paginationUrl = formatPaginationUrl($url, $page);
            @endphp
            <li class="page-item {{ ($page == $model->currentPage()) ? 'active' : '' }}"><a class="page-link" href="{{ $paginationUrl }}">{{ $page }}</a></li>
        @endforeach

        {{-- Next Page Link --}}
        @php
            $nextPageUrl = null;
            if ($model->hasMorePages()) {
                $nextUrl = $model->nextPageUrl();
                if ($nextUrl) {
                    $parsed = parse_url($nextUrl);
                    $query = [];
                    if (isset($parsed['query'])) {
                        parse_str($parsed['query'], $query);
                    }
                    $pageNum = $query['page'] ?? ($model->currentPage() + 1);
                    $nextPageUrl = formatPaginationUrl($nextUrl, $pageNum);
                }
            }
        @endphp
        @if ($nextPageUrl)
            <li class="page-item"><a class="page-link" href="{{ $nextPageUrl }}">Next</a></li>
        @else
            <li class="page-item disabled"><span class="page-link">Next</span></li>
        @endif
    </ul>
@endif

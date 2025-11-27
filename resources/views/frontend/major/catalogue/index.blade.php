@extends('frontend.homepage.layout')
@section('content')
    <!-- Breadcrumb Section -->
    <div class="page-breadcrumb-large">
        <div class="breadcrumb-overlay"></div>
        <div class="uk-container uk-container-center">
            <div class="breadcrumb-content">
                <h1 class="breadcrumb-title">Các ngành đào tạo từ xa</h1>
                <div class="breadcrumb-description">
                    <p>Danh sách đầy đủ các ngành đào tạo từ xa uy tín, chất lượng. Tìm hiểu thông tin chi tiết về các chương trình đào tạo từ xa tại các trường đại học hàng đầu.</p>
                </div>
                <nav class="breadcrumb-nav">
                    <ul class="breadcrumb-list">
                        <li><a href="{{ config('app.url') }}">Trang chủ</a></li>
                        <li>
                            <span class="breadcrumb-separator">/</span>
                            <span>Các ngành đào tạo từ xa</span>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Majors Catalogue Content -->
    <div class="panel-majors-catalogue">
        <div class="uk-container uk-container-center">
            @if($majors && $majors->count() > 0)
                <div class="majors-catalogue-grid">
                    <div class="uk-grid uk-grid-medium" data-uk-grid-match>
                        @foreach($majors as $major)
                            @php
                                // Lấy thông tin từ languages relationship
                                $majorLanguage = $major->languages->first() ?? null;
                                $majorName = '';
                                $majorCanonical = '';
                                $majorDescription = '';
                                
                                if ($majorLanguage) {
                                    $pivot = $majorLanguage->pivot ?? null;
                                    if ($pivot) {
                                        $majorName = $pivot->name ?? '';
                                        $majorCanonical = $pivot->canonical ?? '';
                                        $majorDescription = $pivot->description ?? '';
                                    }
                                }
                                
                                // Lấy ảnh
                                $majorImage = $major->image ?? '';
                                $majorImageUrl = $majorImage ? asset($majorImage) : asset('frontend/resources/img/major-default.png');
                                
                                // Tạo URL
                                $majorUrl = $majorCanonical ? write_url($majorCanonical) : '#';
                                
                                // Lấy thông tin từ JSON nếu có
                                $majorData = [];
                                if ($majorLanguage && $majorLanguage->pivot) {
                                    // Có thể có thông tin về số tín chỉ, thời gian đào tạo trong JSON
                                    // Tạm thời để trống, có thể bổ sung sau
                                }
                                
                                // Delay cho animation
                                $majorDelay = (0.1 + ($loop->index * 0.1)) . 's';
                            @endphp
                            <div class="uk-width-medium-1-2 uk-width-large-1-3">
                                <div class="major-card wow fadeInUp" data-wow-delay="{{ $majorDelay }}">
                                    @if(!empty($majorImage))
                                        <div class="major-card-image">
                                            <a href="{{ $majorUrl }}">
                                                <span class="image img-cover">
                                                    <img src="{{ $majorImageUrl }}" alt="{{ $majorName }}" onerror="this.style.display='none'; this.parentElement.parentElement.classList.add('no-image');">
                                                </span>
                                            </a>
                                        </div>
                                    @endif
                                    <div class="major-card-content">
                                        @if(!empty($majorName))
                                            <h3 class="major-card-title">
                                                <a href="{{ $majorUrl }}" title="{{ $majorName }}">{{ $majorName }}</a>
                                            </h3>
                                        @endif
                                        @if(!empty($majorDescription))
                                            @php
                                                $cleanDescription = strip_tags($majorDescription);
                                                $shortDescription = mb_strlen($cleanDescription) > 120 ? mb_substr($cleanDescription, 0, 120) . '...' : $cleanDescription;
                                            @endphp
                                            <p class="major-card-description">{{ $shortDescription }}</p>
                                        @endif
                                        <a href="{{ $majorUrl }}" class="major-card-button">Xem chi tiết ngành học</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                @if($majors->hasPages())
                    <div class="majors-pagination">
                        <ul class="pagination">
                            {{-- Previous Page Link --}}
                            @php
                                $prevPageUrl = ($majors->currentPage() > 1) ? str_replace('?page=', '/trang-', $majors->previousPageUrl()).config('apps.general.suffix') : null;
                                if ($prevPageUrl && $majors->currentPage() == 2) {
                                    $prevPageUrl = str_replace('/trang-1', '', $prevPageUrl);
                                }
                            @endphp
                            @if ($prevPageUrl)
                                <li class="page-item"><a class="page-link" href="{{ $prevPageUrl }}">‹ Trước</a></li>
                            @else
                                <li class="page-item disabled"><span class="page-link">‹ Trước</span></li>
                            @endif

                            {{-- Pagination Links --}}
                            @foreach ($majors->getUrlRange(max(1, $majors->currentPage() - 2), min($majors->lastPage(), $majors->currentPage() + 2)) as $page => $url)
                                @php
                                    $paginationUrl = str_replace('?page=', '/trang-', $url).config('apps.general.suffix');
                                    $paginationUrl = ($page == 1) ? str_replace('/trang-'.$page, '', $paginationUrl) : $paginationUrl;
                                @endphp
                                <li class="page-item {{ ($page == $majors->currentPage()) ? 'active' : '' }}"><a class="page-link" href="{{ $paginationUrl }}">{{ $page }}</a></li>
                            @endforeach

                            {{-- Next Page Link --}}
                            @php
                                $nextPageUrl = ($majors->hasMorePages()) ? str_replace('?page=', '/trang-', $majors->nextPageUrl()).config('apps.general.suffix') : null;
                            @endphp
                            @if ($nextPageUrl)
                                <li class="page-item"><a class="page-link" href="{{ $nextPageUrl }}">Sau ›</a></li>
                            @else
                                <li class="page-item disabled"><span class="page-link">Sau ›</span></li>
                            @endif
                        </ul>
                    </div>
                @endif
            @else
                <div class="majors-empty">
                    <p>Hiện tại chưa có ngành đào tạo từ xa nào.</p>
                </div>
            @endif
        </div>
    </div>
@endsection


@extends('frontend.homepage.layout')
@section('content')
    <!-- Breadcrumb Section -->
    @php
        // Lấy dữ liệu từ widget
        $widget = $widgets['schools-list'] ?? null;
        $widgetDescription = '';
        $currentLanguage = $config['language'] ?? 1;
        
        if ($widget) {
            // Lấy description từ widget description (JSON)
            $widgetDescriptionData = $widget->description ?? [];
            if (is_array($widgetDescriptionData) && isset($widgetDescriptionData[$currentLanguage])) {
                $widgetDescription = $widgetDescriptionData[$currentLanguage] ?? '';
            } elseif (is_array($widgetDescriptionData) && isset($widgetDescriptionData[1])) {
                $widgetDescription = $widgetDescriptionData[1] ?? '';
            }
        }
    @endphp
    <div class="page-breadcrumb-large">
        <div class="breadcrumb-overlay"></div>
        <div class="uk-container uk-container-center">
            <div class="breadcrumb-content">
                <h1 class="breadcrumb-title">Các trường đào tạo từ xa</h1>
                <div class="breadcrumb-description">
                    @if($widgetDescription)
                        <p>{!! $widgetDescription !!}</p>
                    @else
                        <p>Danh sách đầy đủ các trường đào tạo từ xa uy tín, chất lượng. Tìm hiểu thông tin chi tiết về các chương trình đào tạo từ xa tại các trường đại học hàng đầu.</p>
                    @endif
                </div>
                <nav class="breadcrumb-nav">
                    <ul class="breadcrumb-list">
                        <li><a href="{{ config('app.url') }}">Trang chủ</a></li>
                        <li>
                            <span class="breadcrumb-separator">/</span>
                            <span>Các trường đào tạo từ xa</span>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Schools Catalogue Content -->
    <div class="panel-schools-catalogue">
        <div class="uk-container uk-container-center">
            <div class="uk-grid uk-grid-medium" data-uk-grid-match>
                {{-- Main Content - 3/4 --}}
                <div class="uk-width-medium-3-4" id="schools-content-wrapper">
                    @if($schools && $schools->count() > 0)
                        <div class="schools-catalogue-grid">
                            <div class="uk-grid uk-grid-medium" data-uk-grid-match>
                        @foreach($schools as $school)
                            @php
                                // Lấy thông tin từ languages relationship
                                $schoolLanguage = $school->languages->first() ?? null;
                                $schoolName = '';
                                $schoolCanonical = '';
                                $majorsCount = 0;
                                
                                if ($schoolLanguage) {
                                    $pivot = $schoolLanguage->pivot ?? null;
                                    if ($pivot) {
                                        $schoolName = $pivot->name ?? '';
                                        $schoolCanonical = $pivot->canonical ?? '';
                                        
                                        // Đếm số ngành từ majors JSON
                                        if (isset($pivot->majors) && is_array($pivot->majors)) {
                                            $majorsCount = count($pivot->majors);
                                        } elseif (isset($pivot->majors) && is_string($pivot->majors)) {
                                            $majorsData = json_decode($pivot->majors, true);
                                            if (is_array($majorsData)) {
                                                $majorsCount = count($majorsData);
                                            }
                                        }
                                    }
                                }
                                
                                // Lấy ảnh
                                $schoolImage = $school->image ?? '';
                                $schoolImageUrl = $schoolImage ? asset($schoolImage) : asset('frontend/resources/img/school-default.png');
                                
                                // Tạo URL
                                $schoolUrl = $schoolCanonical ? write_url($schoolCanonical) : '#';
                                
                                // Icon mặc định
                                $schoolIcon = $schoolImageUrl;
                            @endphp
                            <div class="uk-width-medium-1-2 uk-width-large-1-3">
                                <div class="school-card">
                                    <div class="school-card-icon">
                                        <img src="{{ $schoolIcon }}" alt="{{ $schoolName }}">
                                    </div>
                                    <div class="school-card-content">
                                        <h3 class="school-card-name">{{ $schoolName }}</h3>
                                        <div class="school-card-info">
                                            <div class="school-card-info-item">
                                                <span class="info-label">Hệ Đào Tạo Từ Xa</span>
                                            </div>
                                            <div class="school-card-info-item">
                                                <span class="info-label">Số ngành đào tạo: <strong>{{ $majorsCount }}</strong> ngành</span>
                                            </div>
                                        </div>
                                        <a href="{{ $schoolUrl }}" class="school-card-button">Xem chi tiết chương trình</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                            </div>
                        </div>

                        <!-- Pagination -->
                        @if($schools->hasPages())
                            <div class="schools-pagination">
                                <ul class="pagination">
                                    {{-- Previous Page Link --}}
                                    @php
                                        $prevPageUrl = null;
                                        if ($schools->currentPage() > 1) {
                                            $prevUrl = $schools->previousPageUrl();
                                            if ($prevUrl) {
                                                $parsed = parse_url($prevUrl);
                                                $query = [];
                                                if (isset($parsed['query'])) {
                                                    parse_str($parsed['query'], $query);
                                                }
                                                $pageNum = $query['page'] ?? ($schools->currentPage() - 1);
                                                $prevPageUrl = formatPaginationUrl($prevUrl, $pageNum);
                                            }
                                        }
                                    @endphp
                                    @if ($prevPageUrl)
                                        <li class="page-item"><a class="page-link" href="{{ $prevPageUrl }}">‹ Trước</a></li>
                                    @else
                                        <li class="page-item disabled"><span class="page-link">‹ Trước</span></li>
                                    @endif

                                    {{-- Pagination Links --}}
                                    @foreach ($schools->getUrlRange(max(1, $schools->currentPage() - 2), min($schools->lastPage(), $schools->currentPage() + 2)) as $page => $url)
                                        @php
                                            $paginationUrl = formatPaginationUrl($url, $page);
                                        @endphp
                                        <li class="page-item {{ ($page == $schools->currentPage()) ? 'active' : '' }}"><a class="page-link" href="{{ $paginationUrl }}">{{ $page }}</a></li>
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    @php
                                        $nextPageUrl = null;
                                        if ($schools->hasMorePages()) {
                                            $nextUrl = $schools->nextPageUrl();
                                            if ($nextUrl) {
                                                $parsed = parse_url($nextUrl);
                                                $query = [];
                                                if (isset($parsed['query'])) {
                                                    parse_str($parsed['query'], $query);
                                                }
                                                $pageNum = $query['page'] ?? ($schools->currentPage() + 1);
                                                $nextPageUrl = formatPaginationUrl($nextUrl, $pageNum);
                                            }
                                        }
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
                        <div class="schools-empty">
                            <p>Hiện tại chưa có trường đào tạo từ xa nào.</p>
                        </div>
                    @endif
                </div>
                
                {{-- Sidebar Filter - 1/4 --}}
                <div class="uk-width-medium-1-4">
                    @include('frontend.school.catalogue.filter-sidebar')
                </div>
            </div>
        </div>
    </div>

    {{-- CTA Section: Nhận tư vấn miễn phí --}}
    <div class="panel-schools-cta wow fadeInUp" data-wow-delay="0.3s">
        <div class="uk-container uk-container-center">
            <div class="schools-cta-content">
                <h2 class="schools-cta-title">Bạn vẫn còn băn khoăn và có nhiều thắc mắc?</h2>
                <p class="schools-cta-description">Hơn 50,000+ học viên đã lựa chọn Đại Học Từ Xa thay đổi cuộc sống</p>
                <a href="#consultation-modal" class="schools-cta-button" data-uk-modal>
                    NHẬN TƯ VẤN MIỄN PHÍ
                </a>
            </div>
        </div>
    </div>
@endsection


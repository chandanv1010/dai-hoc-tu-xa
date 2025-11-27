@extends('frontend.homepage.layout')
@section('content')
    <!-- Breadcrumb Section -->
    <div class="page-breadcrumb-large">
        <div class="breadcrumb-overlay"></div>
        <div class="uk-container uk-container-center">
            <div class="breadcrumb-content">
                <h1 class="breadcrumb-title">Các trường đào tạo từ xa</h1>
                <div class="breadcrumb-description">
                    <p>Danh sách đầy đủ các trường đào tạo từ xa uy tín, chất lượng. Tìm hiểu thông tin chi tiết về các chương trình đào tạo từ xa tại các trường đại học hàng đầu.</p>
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
                                $prevPageUrl = ($schools->currentPage() > 1) ? str_replace('?page=', '/trang-', $schools->previousPageUrl()).config('apps.general.suffix') : null;
                                if ($prevPageUrl && $schools->currentPage() == 2) {
                                    $prevPageUrl = str_replace('/trang-1', '', $prevPageUrl);
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
                                    $paginationUrl = str_replace('?page=', '/trang-', $url).config('apps.general.suffix');
                                    $paginationUrl = ($page == 1) ? str_replace('/trang-'.$page, '', $paginationUrl) : $paginationUrl;
                                @endphp
                                <li class="page-item {{ ($page == $schools->currentPage()) ? 'active' : '' }}"><a class="page-link" href="{{ $paginationUrl }}">{{ $page }}</a></li>
                            @endforeach

                            {{-- Next Page Link --}}
                            @php
                                $nextPageUrl = ($schools->hasMorePages()) ? str_replace('?page=', '/trang-', $schools->nextPageUrl()).config('apps.general.suffix') : null;
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
    </div>
@endsection


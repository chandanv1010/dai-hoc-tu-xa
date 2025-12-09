@extends('frontend.homepage.layout')
@section('content')
    <div class="search-results page-wrapper">
        <div class="uk-container uk-container-center mt40">
            <div class="panel-body">
                <h2 class="heading-1 mb20">
                    <span>Kết quả tìm kiếm cho: "{{ $keyword }}"</span>
                </h2>

                @php
                    $hasResults = false;
                    if (($posts && $posts->count() > 0) || 
                        ($products && $products->count() > 0) ||
                        ($schools && $schools->count() > 0) || 
                        ($majors && $majors->count() > 0)) {
                        $hasResults = true;
                    }
                @endphp

                @if($hasResults)
                    {{-- 1. Tin tức (Posts) --}}
                    @if($posts && $posts->count() > 0)
                        <div class="mb40">
                            <h3 class="mb20" style="font-size: 24px; font-weight: 600; color: #253d4e; margin-bottom: 20px;">
                                Tin tức ({{ $posts->total() }})
                            </h3>
                            <div class="panel-post-catalogue">
                                <div class="post-catalogue-grid">
                                    <div class="uk-grid uk-grid-medium" data-uk-grid-match>
                                        @foreach($posts as $post)
                                            @php
                                                // Lấy dữ liệu từ pivot
                                                $postLanguage = $post->languages->first();
                                                $postPivot = $postLanguage->pivot ?? null;
                                                
                                                $postName = $postPivot->name ?? '';
                                                $postDescription = $postPivot->description ?? '';
                                                $postCanonical = $postPivot->canonical ?? '';
                                                $postUrl = $postCanonical ? write_url($postCanonical) : '#';
                                                $postImage = $post->image ?? '';
                                                $postImageUrl = $postImage ? (function_exists('thumb') ? thumb($postImage, 400, 300) : asset($postImage)) : asset('frontend/resources/img/default-news.jpg');
                                                
                                                // Lấy ngày tháng
                                                $postDate = $post->created_at ?? now();
                                                $formattedDate = $postDate ? date('d/m/Y', strtotime($postDate)) : '';
                                            @endphp
                                            <div class="uk-width-medium-1-2 uk-width-large-1-3">
                                                <div class="news-item wow fadeInUp">
                                                    <a href="{{ $postUrl }}" class="news-image img-cover">
                                                        <img src="{{ $postImageUrl }}" alt="{{ $postName }}">
                                                    </a>
                                                    <div class="news-content">
                                                        <h3 class="news-title">
                                                            <a href="{{ $postUrl }}" title="{{ $postName }}">{{ $postName }}</a>
                                                        </h3>
                                                        @if($postDescription)
                                                            @php
                                                                $cleanDescription = strip_tags($postDescription);
                                                                $shortDescription = mb_strlen($cleanDescription) > 100 ? mb_substr($cleanDescription, 0, 100) . '...' : $cleanDescription;
                                                            @endphp
                                                            <p class="news-description">{!! $shortDescription !!}</p>
                                                        @endif
                                                        <div class="news-meta">
                                                            <span class="news-date">
                                                                <i class="fa fa-calendar"></i>
                                                                {{ $formattedDate }}
                                                            </span>
                                                            <a href="{{ $postUrl }}" class="news-detail-button">Xem chi tiết</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @if($posts->hasPages())
                                    <div class="post-catalogue-pagination mt30">
                                        @include('frontend.component.pagination', ['model' => $posts])
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- 2. Khóa học (Products/Courses) --}}
                    @if($products && $products->count() > 0)
                        <div class="mb40">
                            <h3 class="mb20" style="font-size: 24px; font-weight: 600; color: #253d4e; margin-bottom: 20px;">
                                Khóa học ({{ $products->total() }})
                            </h3>
                            <div class="course-catalogue-page">
                                <div class="course-list-wrapper">
                                    <div class="course-grid">
                                        @foreach($products as $product)
                                            <div class="course-grid-item">
                                                @include('frontend.component.p-item', ['product' => $product])
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($products->hasPages())
                                        <div class="course-pagination mt30">
                                            @include('frontend.component.pagination', ['model' => $products])
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- 3. Ngành học (Majors) --}}
                    @if($majors && $majors->count() > 0)
                        <div class="mb40">
                            <h3 class="mb20" style="font-size: 24px; font-weight: 600; color: #253d4e; margin-bottom: 20px;">
                                Ngành học ({{ $majors->total() }})
                            </h3>
                            <div class="panel-majors-list">
                                <div class="majors-list-grid">
                                    <div class="uk-grid uk-grid-medium" data-uk-grid-match>
                                        @foreach($majors as $major)
                                            @include('frontend.component.major-item', ['major' => $major])
                                        @endforeach
                                    </div>
                                    @if($majors->hasPages())
                                        <div class="major-catalogue-pagination mt30" style="text-align: center;">
                                            {{ $majors->links('pagination::bootstrap-4') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- 4. Trường học (Schools) --}}
                    @if($schools && $schools->count() > 0)
                        <div class="mb40">
                            <h3 class="mb20" style="font-size: 24px; font-weight: 600; color: #253d4e; margin-bottom: 20px;">
                                Trường học ({{ $schools->total() }})
                            </h3>
                            <div class="panel-schools-catalogue">
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
                                                            @if($majorsCount > 0)
                                                                <div class="school-card-info-item">
                                                                    <span class="info-label">Số ngành đào tạo: <strong>{{ $majorsCount }}</strong> ngành</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <a href="{{ $schoolUrl }}" class="school-card-button">Xem chi tiết chương trình</a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @if($schools->hasPages())
                                        <div class="schools-pagination mt30">
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
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="search-empty pt20 pb20" style="text-align: center; padding: 40px 20px;">
                        <p style="font-size: 18px; color: #666; margin-bottom: 10px;">Không có kết quả phù hợp với từ khóa "{{ $keyword }}".</p>
                        <p style="font-size: 14px; color: #999;">Vui lòng thử lại với từ khóa khác.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

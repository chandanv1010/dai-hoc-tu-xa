@extends('frontend.homepage.layout')
@section('content')
    @php
        // Lấy dữ liệu từ introduces
        $introTitle = $introduce['block_1_title'] ?? 'Về Chúng Tôi';
        $introDescription = $introduce['block_1_description'] ?? '';
        $introImage = $introduce['block_1_image'] ?? '';
        
        $visionTitle = $introduce['block_2_title'] ?? 'Tầm Nhìn';
        $visionDescription = $introduce['block_2_description'] ?? '';
        
        $missionTitle = $introduce['block_3_title'] ?? 'Sứ Mệnh';
        $missionDescription = $introduce['block_3_description'] ?? '';
        
        $historyTitle = $introduce['block_4_title'] ?? 'Lịch Sử Hình Thành';
        $historyDescription = $introduce['block_4_description'] ?? '';
        
        $contactTitle = $introduce['block_5_title'] ?? 'Thông Tin Liên Hệ';
        $contactDescription = $introduce['block_5_description'] ?? '';
    @endphp

    {{-- Breadcrumb --}}
    <div class="page-breadcrumb-large">
        <div class="breadcrumb-overlay"></div>
        <div class="uk-container uk-container-center">
            <div class="breadcrumb-content">
                @php
                    $postCatalogueLanguage = $postCatalogue->languages->first();
                    $postCatalogueName = ($postCatalogueLanguage && $postCatalogueLanguage->pivot) ? ($postCatalogueLanguage->pivot->name ?? 'Giới Thiệu') : 'Giới Thiệu';
                @endphp
                <h1 class="breadcrumb-title">{{ $postCatalogueName }}</h1>
                <nav class="breadcrumb-nav">
                    <ul class="breadcrumb-list">
                        <li><a href="{{ config('app.url') }}">Trang chủ</a></li>
                        @if(!is_null($breadcrumb) && $breadcrumb->count() > 0)
                            @foreach($breadcrumb as $key => $val)
                                @php
                                    $breadcrumbLanguage = $val->languages->first();
                                    $breadcrumbName = ($breadcrumbLanguage && $breadcrumbLanguage->pivot) ? ($breadcrumbLanguage->pivot->name ?? '') : '';
                                    $breadcrumbCanonical = ($breadcrumbLanguage && $breadcrumbLanguage->pivot) ? ($breadcrumbLanguage->pivot->canonical ?? '') : '';
                                    $breadcrumbUrl = $breadcrumbCanonical ? write_url($breadcrumbCanonical) : '#';
                                @endphp
                                @if(!empty($breadcrumbName))
                                    <li>
                                        <span class="breadcrumb-separator">/</span>
                                        <a href="{{ $breadcrumbUrl }}">{{ $breadcrumbName }}</a>
                                    </li>
                                @endif
                            @endforeach
                        @endif
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    {{-- Khối 1: Giới thiệu --}}
    @if(!empty($introTitle) || !empty($introDescription))
        <div class="panel-intro-block-1 wow fadeInUp" data-wow-delay="0.1s">
            <div class="uk-container uk-container-center uk-container-1260">
                <div class="intro-block-1-content">
                    <div class="intro-block-1-text">
                        @if(!empty($introTitle))
                            <h2 class="intro-block-1-title">{{ $introTitle }}</h2>
                        @endif
                        @if(!empty($introDescription))
                            <div class="intro-block-1-description">
                                {!! $introDescription !!}
                            </div>
                        @endif
                    </div>
                    <div class="intro-block-1-image">
                        @if(!empty($introImage))
                            <span class="image img-cover">
                                <img src="{{ image($introImage) }}" alt="{{ $introTitle }}">
                            </span>
                        @else
                            <div class="intro-block-1-image-placeholder">
                                <i class="fa fa-image"></i>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Khối 2, 3, 4: Tầm nhìn, Sứ Mệnh, Lịch sử hình thành --}}
    <div class="panel-intro-vision-mission-history">
        <div class="uk-container uk-container-center uk-container-1260">
            <div class="intro-vision-mission-grid">
                {{-- Khối 2: Tầm nhìn --}}
                @if(!empty($visionTitle) || !empty($visionDescription))
                    <div class="intro-vision-card wow fadeInUp" data-wow-delay="0.2s">
                        <div class="intro-vision-card-header">
                            <div class="intro-vision-badge">TẦM NHÌN</div>
                            @if(!empty($visionTitle))
                                <h2 class="intro-vision-title">{{ $visionTitle }}</h2>
                            @endif
                        </div>
                        @if(!empty($visionDescription))
                            <div class="intro-vision-description">
                                {!! $visionDescription !!}
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Khối 3: Sứ Mệnh --}}
                @if(!empty($missionTitle) || !empty($missionDescription))
                    <div class="intro-mission-card wow fadeInUp" data-wow-delay="0.3s">
                        <div class="intro-mission-card-header">
                            <div class="intro-mission-badge">SỨ MỆNH</div>
                            @if(!empty($missionTitle))
                                <h2 class="intro-mission-title">{{ $missionTitle }}</h2>
                            @endif
                        </div>
                        @if(!empty($missionDescription))
                            <div class="intro-mission-description">
                                {!! $missionDescription !!}
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Khối 4: Lịch sử hình thành --}}
                @if(!empty($historyTitle) || !empty($historyDescription))
                    <div class="intro-history-card wow fadeInUp" data-wow-delay="0.4s">
                        <div class="intro-history-card-header">
                            <div class="intro-history-badge">LỊCH SỬ HÌNH THÀNH</div>
                            @if(!empty($historyTitle))
                                <h2 class="intro-history-title">{{ $historyTitle }}</h2>
                            @endif
                        </div>
                        @if(!empty($historyDescription))
                            <div class="intro-history-description">
                                {!! $historyDescription !!}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Khối 6: Học viên feedback --}}
    @if(isset($widgets['students']->object) && !is_null($widgets['students']->object))
        @foreach($widgets['students']->object as $key => $val)
            @php
                $widgetName = $val->languages->name ?? 'Cảm Nhận Học Viên';
                $widgetDescription = $val->languages->description ?? '';
                $feedbackPosts = $val->posts ?? collect();
            @endphp
            @if($feedbackPosts->count() > 0)
                <div class="panel-intro-block-6 wow fadeInUp" data-wow-delay="0.6s">
                    <div class="uk-container uk-container-center uk-container-1260">
                        <div class="intro-block-6-header">
                            <h2 class="intro-block-6-title">{{ $widgetName }}</h2>
                            @if(!empty($widgetDescription))
                                <div class="intro-block-6-description">
                                    {!! $widgetDescription !!}
                                </div>
                            @endif
                        </div>
                        
                        <div class="intro-block-6-slider">
                            <div class="swiper-container intro-feedback-swiper">
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                                <div class="swiper-wrapper">
                                    @foreach($feedbackPosts as $post)
                                        @php
                                            $postLanguage = $post->languages->first();
                                            $postName = $postLanguage->name ?? '';
                                            $postDescription = $postLanguage->description ?? '';
                                            $postImage = $post->image ?? '';
                                            
                                            // Tạo chữ cái đầu từ tên
                                            $initials = '';
                                            if (!empty($postName)) {
                                                $words = explode(' ', trim($postName));
                                                if (count($words) > 0) {
                                                    $initials = mb_strtoupper(mb_substr($words[0], 0, 1));
                                                    if (count($words) > 1) {
                                                        $initials .= mb_strtoupper(mb_substr($words[count($words) - 1], 0, 1));
                                                    }
                                                }
                                            }
                                            $hasImage = !empty($postImage);
                                            $delay = (0.7 + ($loop->index * 0.15)) . 's';
                                        @endphp
                                        <div class="swiper-slide">
                                            <div class="intro-feedback-item wow fadeInUp" data-wow-delay="{{ $delay }}">
                                                <div class="intro-feedback-header">
                                                    <div class="intro-feedback-avatar {{ $hasImage ? 'has-image' : 'no-image' }}">
                                                        @if($hasImage)
                                                            <img src="{{ image($postImage) }}" alt="{{ $postName }}">
                                                        @else
                                                            <span class="avatar-initials">{{ $initials }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="intro-feedback-name-wrapper">
                                                        <h3 class="intro-feedback-name">{{ $postName }}</h3>
                                                    </div>
                                                </div>
                                                <div class="intro-feedback-rating">
                                                    <div class="star-rating">
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                    </div>
                                                </div>
                                                <div class="intro-feedback-content">
                                                    <div class="intro-feedback-description">
                                                        {!! $postDescription !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-pagination"></div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    @endif

    {{-- Khối: Các trường đại học đang hợp tác tuyển sinh --}}
    @php
        // Debug: Kiểm tra dữ liệu schools
        $schoolsList = $schools ?? collect();
    @endphp
    
    @if($schoolsList->count() > 0)
        <div class="panel-intro-schools wow fadeInUp" data-wow-delay="0.7s">
            <div class="uk-container uk-container-center uk-container-1260">
                <div class="intro-schools-header">
                    <h2 class="intro-schools-title">Các Trường Đại Học Đang Hợp Tác Tuyển Sinh</h2>
                </div>
                
                <div class="intro-schools-grid">
                    <div class="uk-grid uk-grid-small uk-grid-match" data-uk-grid-match>
                        @foreach($schoolsList as $school)
                            @php
                                $schoolLanguage = $school->languages->first() ?? null;
                                $schoolName = '';
                                $schoolShortName = $school->short_name ?? '';
                                $schoolImage = $school->image ?? '';
                                $schoolCanonical = '';
                                $schoolLogoUrl = $schoolImage ? asset($schoolImage) : asset('frontend/resources/img/school-default.png');
                                
                                if ($schoolLanguage) {
                                    $pivot = $schoolLanguage->pivot ?? null;
                                    if ($pivot) {
                                        $schoolName = $pivot->name ?? '';
                                        $schoolCanonical = $pivot->canonical ?? '';
                                        // Nếu không có short_name, dùng name
                                        if (empty($schoolShortName)) {
                                            $schoolShortName = $schoolName;
                                        }
                                    }
                                }
                                
                                $schoolUrl = $schoolCanonical ? write_url($schoolCanonical) : '#';
                            @endphp
                            <div class="uk-width-small-1-2 uk-width-medium-1-2 uk-width-large-1-4">
                                <a href="{{ $schoolUrl }}" class="intro-school-link">
                                    <div class="intro-school-item">
                                        <div class="intro-school-logo">
                                            <img src="{{ $schoolLogoUrl }}" alt="{{ $schoolName }}">
                                        </div>
                                        <div class="intro-school-info">
                                            <h3 class="intro-school-name">{{ $schoolName }}</h3>
                                            @if(!empty($schoolShortName) && $schoolShortName !== $schoolName)
                                                <p class="intro-school-short-name">{{ $schoolShortName }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @else
        {{-- Debug: Hiển thị thông báo nếu không có dữ liệu --}}
        <div class="panel-intro-schools wow fadeInUp" data-wow-delay="0.7s">
            <div class="uk-container uk-container-center uk-container-1260">
                <div class="intro-schools-header">
                    <h2 class="intro-schools-title">Các Trường Đại Học Đang Hợp Tác Tuyển Sinh</h2>
                </div>
                <p style="text-align: center; color: #999;">Chưa có dữ liệu trường học</p>
            </div>
        </div>
    @endif

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Khởi tạo Swiper cho feedback (nếu có)
    var feedbackSlideContainer = document.querySelector(".intro-feedback-swiper");
    if (feedbackSlideContainer) {
        var feedbackSlides = feedbackSlideContainer.querySelectorAll('.swiper-slide');
        var feedbackSlideCount = feedbackSlides.length;
        var enableFeedbackLoop = feedbackSlideCount >= 2;

        var feedbackSwiper = new Swiper(".intro-feedback-swiper", {
            loop: enableFeedbackLoop,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            autoplay: enableFeedbackLoop ? {
                delay: 3000,
                disableOnInteraction: false,
            } : false,
            spaceBetween: 30,
            slidesPerView: 1,
            breakpoints: {
                768: {
                    slidesPerView: 2,
                },
                1024: {
                    slidesPerView: 3,
                }
            }
        });
    }
});
</script>
@endpush

@extends('frontend.homepage.layout')
@section('content')
    {{-- Hidden H1 for SEO - lấy từ systems homepage_h1, fallback về homepage_slogan --}}
    @php
        $h1Text = $system['homepage_h1'] ?? $system['homepage_slogan'] ?? '';
    @endphp
    @if($h1Text)
        <h1 style="display: none;">{{ $h1Text }}</h1>
    @endif
    
    @include('frontend.component.slide')
    
    @include('frontend.component.statistics')
    @include('frontend.component.distance-learning')
    
    {{-- Training Program Section --}}
    @if(isset($widgets['training-program']) && $widgets['training-program']->object && $widgets['training-program']->object->isNotEmpty())
        @php
            $trainingCatalogue = $widgets['training-program']->object->first();
            $catalogueName = $trainingCatalogue->languages->name ?? '';
            $catalogueDescription = $trainingCatalogue->languages->description ?? '';
            $posts = $trainingCatalogue->posts ?? collect();
            
            // Icon mapping theo canonical - Font Awesome class names
            $iconMap = [
                'hoc-dai-hoc-tu-xa' => 'fa-graduation-cap',
                'van-bang-2-online' => 'fa-star',
                'dai-hoc-truc-tuyen-cho-nguoi-di-lam' => 'fa-briefcase',
                'nang-bang-tu-trung-cap-cao-dang-len-dai-hoc' => 'fa-line-chart'
            ];
        @endphp

        <div class="panel-training-program wow fadeInUp" data-wow-delay="0.1s">
            <div class="uk-container uk-container-center">
                <div class="training-program-wrapper">
                    <div class="training-program-header">
                        <h2 class="training-program-title">{{ $catalogueName }}</h2>
                        @if($catalogueDescription)
                            <p class="training-program-description">{!! $catalogueDescription !!}</p>
                        @endif
                    </div>
                    
                    @if($posts->isNotEmpty())
                        <div class="training-program-cards">
                            <div class="uk-grid uk-grid-medium" data-uk-grid-match>
                                @foreach($posts as $post)
                                    @php
                                        // Handle languages - could be Collection or object
                                        $postLanguage = ($post->languages instanceof \Illuminate\Support\Collection) 
                                            ? $post->languages->first() 
                                            : (is_array($post->languages) ? (object)($post->languages[0] ?? []) : ($post->languages ?? (object)[]));
                                        
                                        $postName = $postLanguage->name ?? '';
                                        $postDescription = $postLanguage->description ?? '';
                                        $postCanonical = $postLanguage->canonical ?? '';
                                        $postUrl = $postCanonical ? write_url($postCanonical) : '#';
                                        
                                        // Get icon từ canonical mapping
                                        $iconClass = $iconMap[$postCanonical] ?? 'fa-graduation-cap';
                                    @endphp
                                    <div class="uk-width-medium-1-2">
                                        <div class="training-program-card">
                                            <div class="card-icon">
                                                <i class="fa {{ $iconClass }}"></i>
                                            </div>
                                            <div class="card-content">
                                                <h3 class="card-title">{{ $postName }}</h3>
                                                <p class="card-description">{!! $postDescription !!}</p>
                                                <a href="{{ $postUrl }}" class="card-link">Tìm hiểu thêm →</a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
    
    {{-- Why Distance Learning Section --}}
    @if(isset($widgets['why-distance-learning']) && $widgets['why-distance-learning']->object && $widgets['why-distance-learning']->object->isNotEmpty())
        @php
            $whyCatalogue = $widgets['why-distance-learning']->object->first();
            $catalogueName = $whyCatalogue->languages->name ?? '';
            $catalogueDescription = $whyCatalogue->languages->description ?? '';
            $posts = $whyCatalogue->posts ?? collect();
            
            // Lấy ảnh từ PostCatalogue
            $catalogueImage = $whyCatalogue->image ?? '';
            $catalogueImageUrl = $catalogueImage ? asset($catalogueImage) : asset('frontend/resources/img/why-distance-learning/main-image.png');
        @endphp

        <div class="panel-why-distance-learning wow fadeInUp" data-wow-delay="0.2s">
            <div class="uk-container uk-container-center">
                <div class="why-distance-learning-wrapper">
                    <!-- Header -->
                    <div class="why-distance-learning-header">
                        <h2 class="why-distance-learning-title">{{ $catalogueName }}</h2>
                        @if($catalogueDescription)
                            <p class="why-distance-learning-subtitle" style="color:#fff;">{!! strip_tags($catalogueDescription) !!}</p>
                        @endif
                    </div>

                    <!-- Main Content -->
                    <div class="why-distance-learning-content">
                        <div class="uk-grid uk-grid-medium uk-flex uk-flex-middle" data-uk-grid-match>
                            <!-- Left: Image -->
                            <div class="uk-width-medium-1-2">
                                <div class="why-distance-learning-image">
                                    <div class="main-image">
                                        <img src="{{ $catalogueImageUrl }}" alt="{{ $catalogueName }}" loading="lazy">
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Feature List -->
                            <div class="uk-width-medium-1-2">
                                <div class="why-distance-learning-features">
                                    @if($posts->isNotEmpty())
                                        @foreach($posts as $index => $post)
                                            @php
                                                // Handle languages
                                                $postLanguage = ($post->languages instanceof \Illuminate\Support\Collection) 
                                                    ? $post->languages->first() 
                                                    : (is_array($post->languages) ? (object)($post->languages[0] ?? []) : ($post->languages ?? (object)[]));
                                                
                                                $postName = $postLanguage->name ?? '';
                                                $postCanonical = $postLanguage->canonical ?? '';
                                                
                                                // Lấy ảnh từ Post
                                                $postImage = $post->image ?? '';
                                                $postImageUrl = $postImage ? asset($postImage) : asset('frontend/resources/img/why-distance-learning/icon-default.png');
                                                
                                                // Xác định item number (1-based)
                                                $itemNumber = $index + 1;
                                            @endphp
                                            <div class="why-feature-item why-feature-item-{{ $itemNumber }}">
                                                <div class="why-feature-icon">
                                                    <img src="{{ $postImageUrl }}" alt="{{ $postName }}" loading="lazy">
                                                </div>
                                                <div class="why-feature-text">
                                                    <p>{{ $postName }}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Text -->
                    <div class="why-distance-learning-footer">
                        <h3 class="why-distance-learning-footer-title">{{ $system['homepage_why_distance_learning_footer_title'] ?? 'Chương trình này sẽ giải quyết tất cả!' }}</h3>
                        <p class="why-distance-learning-footer-subtitle">{{ $system['homepage_why_distance_learning_footer_subtitle'] ?? 'Hệ Đào Tạo Đại Học Từ Xa – Cơ hội học tập linh hoạt, bằng cấp chính quy, mở ra tương lai' }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    {{-- Value We Bring Section --}}
    @include('frontend.component.value-we-bring')
    
    {{-- Register Banner Section --}}
    @include('frontend.component.register-banner')
    
    {{-- Schools List Section --}}
    @php
        // Lấy dữ liệu từ widget
        $widget = $widgets['schools-list'] ?? null;
        $widgetDescription = '';
        
        if ($widget) {
            // Lấy description từ widget description (JSON) - mặc định language_id = 1
            $widgetDescriptionData = $widget->description ?? [];
            if (is_array($widgetDescriptionData) && isset($widgetDescriptionData[1])) {
                $widgetDescription = $widgetDescriptionData[1] ?? '';
            }
        }
        
        // Lấy danh sách schools từ controller
        $schoolsList = $schools ?? collect();
    @endphp

    @if($widget || $schoolsList->isNotEmpty())
        <div class="panel-schools-list wow fadeInUp" data-wow-delay="0.3s">
            <div class="uk-container uk-container-center">
                <div class="schools-list-wrapper">
                    <!-- Header Section -->
                    <div class="schools-list-header">
                        <h2 class="schools-list-title">Các Trường Đào Tạo Từ Xa</h2>
                        @if($widgetDescription)
                            <div class="schools-list-description">
                                {!! $widgetDescription !!}
                            </div>
                        @else
                            <p class="schools-list-description">Chúng tôi hợp tác cùng các trường đại học công lập hàng đầu được Bộ Giáo dục & Đào tạo cho phép triển khai chương trình đại học từ xa, đảm bảo chất lượng - chuẩn đầu ra - bằng cấp hợp pháp, giá trị toàn quốc.</p>
                        @endif
                    </div>

                    <!-- Schools Swiper -->
                    @if($schoolsList->isNotEmpty())
                        <div class="schools-list-grid">
                            <div class="swiper-container schools-list-swiper">
                                <div class="swiper-wrapper">
                                 
                                    <div class="swiper-slide">
                                        @foreach($schoolsList as $key => $school)
                                            <div class="school-card">
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
                                                <div class="school-card-icon">
                                                    <img 
                                                        src="{{ $schoolIcon }}" 
                                                        alt="{{ $schoolName }}"
                                                        width="116"
                                                        height="116"
                                                        loading="lazy"
                                                    >
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
                                            @if(($key + 1) % 2 === 0 && ($key + 1) < $schoolsList->count())
                                                </div>
                                            <div class="swiper-slide">
                                            @endif
                                        @endforeach
                                    </div>
                                    
                                </div>
                                <div class="swiper-button-prev"></div>
                                <div class="swiper-button-next"></div>
                            </div>
                        </div>
                    @endif

                    <!-- Call to Action Button -->
                    <div class="schools-list-footer">
                        <a href="{{ write_url('cac-truong-dao-tao-tu-xa') }}" class="schools-list-cta-button">Xem các trường đào tạo từ xa</a>
                    </div>
                </div>
            </div>
        </div>
    @endif

     {{-- Consultation Section --}}
     @include('frontend.component.consultation')
    
    {{-- Majors List Section --}}
    @php
        // Lấy dữ liệu từ widget
        $widget = $widgets['majors-list'] ?? null;
        $widgetDescription = '';
        
        if ($widget) {
            // Lấy description từ widget description (JSON) - mặc định language_id = 1
            $widgetDescriptionData = $widget->description ?? [];
            if (is_array($widgetDescriptionData) && isset($widgetDescriptionData[1])) {
                $widgetDescription = $widgetDescriptionData[1] ?? '';
            }
        }
        
        // Lấy danh sách majors từ controller
        $majorsList = $majors ?? collect();
        
        // Lấy major_catalogues trực tiếp từ Model (không dùng Service/Widget)
        $majorCataloguesForFilter = \App\Models\MajorCatalogue::select([
                'major_catalogues.id',
                'major_catalogues.publish',
                'tb2.name',
                'tb2.canonical',
            ])
            ->join('major_catalogue_language as tb2', 'tb2.major_catalogue_id', '=', 'major_catalogues.id')
            ->where('tb2.language_id', '=', 1)
            ->where('major_catalogues.publish', '=', 2)
            ->orderBy('major_catalogues.order', 'asc')
            ->orderBy('tb2.name', 'asc')
            ->get();
    @endphp

    @if($widget || $majorsList->isNotEmpty())
        <div class="panel-majors-list wow fadeInUp" data-wow-delay="0.4s">
            <div class="uk-container uk-container-center">
                <div class="majors-list-wrapper">
                    <!-- Header Section -->
                    <div class="majors-list-header">
                        <h2 class="majors-list-title">Các Ngành Đào Tạo Từ Xa</h2>
                        @if($widgetDescription)
                            <div class="majors-list-description">
                                {!! $widgetDescription !!}
                            </div>
                        @else
                            <p class="majors-list-description">Chương trình đại học từ xa mang đến nhiều lựa chọn để đáp ứng nhu cầu của người đi làm, học viên và công chức. Mỗi chương trình được thiết kế theo tiêu chuẩn, cập nhật kiến thức thực tế, giúp người học dễ dàng thăng tiến và phát triển sự nghiệp.</p>
                        @endif
                    </div>

                    <!-- Filter Tabs (dưới mô tả) -->
                    @if($majorCataloguesForFilter->count() > 0)
                        <div class="major-catalogue-filter">
                            <a href="javascript:void(0)" class="filter-tab active" data-catalogue-id="">Tất cả ngành</a>
                            @foreach($majorCataloguesForFilter as $catalogue)
                                <a href="javascript:void(0)" class="filter-tab" data-catalogue-id="{{ $catalogue->id }}" data-canonical="{{ $catalogue->canonical }}">{{ $catalogue->name }}</a>
                            @endforeach
                        </div>
                    @endif

                    <!-- Majors Grid/Swiper -->
                    <div class="majors-list-grid">
                        @if($majorsList->isNotEmpty())
                            <!-- Swiper cho tab "Tất cả ngành" -->
                            <div class="majors-list-swiper-wrapper" id="majors-swiper-wrapper">
                                <div class="swiper-container majors-list-swiper">
                                    <div class="swiper-wrapper">
                                        @foreach($majorsList as $major)
                                            <div class="swiper-slide">
                                                <div class="major-card-wrapper">
                                                    @php
                                                        $majorLanguage = $major->languages->first() ?? null;
                                                        $majorName = '';
                                                        $majorCanonical = '';
                                                        $trainingDuration = '';
                                                        $schoolsList = [];
                                                        
                                                        if ($majorLanguage) {
                                                            $pivot = $majorLanguage->pivot ?? null;
                                                            if ($pivot) {
                                                                $majorName = $pivot->name ?? '';
                                                                $majorCanonical = $pivot->canonical ?? '';
                                                                $trainingDuration = $pivot->training_duration ?? '';
                                                            }
                                                        }
                                                        
                                                        if ($major->schools && $major->schools->count() > 0) {
                                                            foreach ($major->schools as $school) {
                                                                $shortName = $school->short_name ?? '';
                                                                if (empty($shortName)) {
                                                                    $schoolLanguage = $school->languages->first() ?? null;
                                                                    if ($schoolLanguage && $schoolLanguage->pivot) {
                                                                        $shortName = $schoolLanguage->pivot->name ?? '';
                                                                    }
                                                                }
                                                                if (!empty($shortName)) {
                                                                    $schoolsList[] = $shortName;
                                                                }
                                                            }
                                                        }
                                                        
                                                        $majorImage = $major->image ?? '';
                                                        $majorImageUrl = $majorImage ? asset($majorImage) : asset('frontend/resources/img/major-default.png');
                                                        $majorUrl = $majorCanonical ? write_url($majorCanonical) : '#';
                                                        $schoolsText = !empty($schoolsList) ? implode(', ', $schoolsList) : '';
                                                    @endphp
                                                    <div class="major-card">
                                                        <div class="major-card-image">
                                                            <img 
                                                                src="{{ $majorImageUrl }}" 
                                                                alt="{{ $majorName }}"
                                                                loading="lazy"
                                                            >
                                                        </div>
                                                        <div class="major-card-content">
                                                            <h3 class="major-card-name">{{ $majorName }}</h3>
                                                            <div class="major-card-info">
                                                                @if($schoolsText)
                                                                    <div class="major-card-info-item">
                                                                        <i class="fa fa-graduation-cap"></i>
                                                                        <div class="info-content">
                                                                            <span class="info-label">Trường Đào Tạo:</span>
                                                                            <strong class="info-value">{{ $schoolsText }}</strong>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                                @if($trainingDuration)
                                                                    <div class="major-card-info-item">
                                                                        <i class="fa fa-clock-o"></i>
                                                                        <div class="info-content">
                                                                            <span class="info-label">Thời Gian Đào Tạo:</span>
                                                                            <strong class="info-value">{{ $trainingDuration }}</strong>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <a href="{{ $majorUrl }}" class="major-card-button">Xem chi tiết chương trình</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="swiper-button-prev"></div>
                                    <div class="swiper-button-next"></div>
                                </div>
                            </div>
                            
                            <!-- Grid cho các tab khác -->
                            <div class="majors-list-grid-wrapper" id="majors-grid-wrapper" style="display: none;">
                                <div class="uk-grid uk-grid-medium" data-uk-grid-match>
                                    @foreach($majorsList as $major)
                                        @include('frontend.component.major-item', ['major' => $major])
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <!-- Không có dữ liệu -->
                            <div class="no-majors-message" style="text-align: center; padding: 60px 20px;">
                                <p style="font-size: 18px; color: #666;">Không có ngành học nào để hiển thị.</p>
                            </div>
                        @endif
                        
                        <!-- Call to Action Button (bên trong majors-list-grid) -->
                        <div class="majors-list-footer">
                            <a href="{{ write_url('cac-nganh-dao-tao-tu-xa') }}" class="majors-list-cta-button" id="majors-list-cta-button">Xem các ngành đào tạo</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    {{-- Value Bring Degree Section --}}
    @php
        // Lấy dữ liệu từ system
        $valueBringName = $system['homepage_value-bring_name'] ?? '';
        $valueBringDescription = $system['homepage_value-bring_description'] ?? '';
        $valueBringVideo = $system['homepage_value-bring_video'] ?? '';
        $valueBringImage = $system['homepage_value-bring_image'] ?? '';
        $valueBringImage2 = $system['homepage_value-bring_image_2'] ?? '';
        
        // Xử lý image - có thể là string hoặc array
        $valueBringImageUrl = '';
        if (!empty($valueBringImage)) {
            if (is_array($valueBringImage)) {
                $valueBringImageUrl = !empty($valueBringImage[0]) ? asset($valueBringImage[0]) : '';
            } else {
                $valueBringImageUrl = asset($valueBringImage);
            }
        }
        
        // Xử lý image 2
        $valueBringImage2Url = '';
        if (!empty($valueBringImage2)) {
            if (is_array($valueBringImage2)) {
                $valueBringImage2Url = !empty($valueBringImage2[0]) ? asset($valueBringImage2[0]) : '';
            } else {
                $valueBringImage2Url = asset($valueBringImage2);
            }
        }
        
        // Xử lý video - có thể là URL hoặc embed code
        $videoEmbed = '';
        $videoThumbnail = '';
        if (!empty($valueBringVideo)) {
            // Nếu là URL YouTube, convert sang embed
            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]+)/', $valueBringVideo, $matches)) {
                $videoId = $matches[1];
                $videoEmbed = 'https://www.youtube.com/embed/' . $videoId;
                $videoThumbnail = 'https://img.youtube.com/vi/' . $videoId . '/maxresdefault.jpg';
            } elseif (strpos($valueBringVideo, '<iframe') !== false) {
                // Nếu đã là embed code, extract video ID
                if (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $valueBringVideo, $matches)) {
                    $videoId = $matches[1];
                    $videoThumbnail = 'https://img.youtube.com/vi/' . $videoId . '/maxresdefault.jpg';
                }
                $videoEmbed = $valueBringVideo;
            } else {
                $videoEmbed = $valueBringVideo;
            }
        }
    @endphp

    @if($valueBringName || $valueBringDescription || $valueBringVideo || $valueBringImageUrl)
        <div class="panel-value-bring-degree wow fadeInUp" data-wow-delay="0.5s">
            <div class="uk-container uk-container-center">
                <div class="value-bring-wrapper">
                    <div class="uk-grid uk-grid-medium" data-uk-grid-match>
                        <!-- Left Column: Content -->
                        <div class="uk-width-medium-1-2 uk-width-large-1-2">
                            <div class="value-bring-content">
                                @if($valueBringName)
                                    <h2 class="value-bring-title">{{ $valueBringName }}</h2>
                                @endif
                                
                                @if($valueBringDescription)
                                    <div class="value-bring-description">
                                        {!! $valueBringDescription !!}
                                    </div>
                                @endif
                                
                                <!-- Video Thumbnail (Bottom Left) -->
                                @if($videoEmbed && $videoThumbnail)
                                    <div class="value-bring-video-thumbnail">
                                        <div class="video-thumbnail-wrapper" data-video-embed="{{ htmlspecialchars($videoEmbed, ENT_QUOTES, 'UTF-8') }}">
                                            <img src="{{ $videoThumbnail }}" alt="Video" class="video-thumbnail-image" loading="lazy">
                                            <div class="video-play-overlay">
                                                <i class="fa fa-play-circle"></i>
                                            </div>
                                            <p class="video-label">Video VTV về bằng đại học từ xa</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Right Column: Images -->
                        <div class="uk-width-medium-1-2 uk-width-large-1-2">
                            <div class="value-bring-media">
                                <!-- Top Right: Graduate Image 2 -->
                                @if($valueBringImage2Url)
                                    <div class="value-bring-graduate-image image img-scaledown">
                                        <img src="{{ $valueBringImage2Url }}" alt="Graduate Image 2" loading="lazy">
                                    </div>
                                @endif
                                
                                <!-- Bottom Right: Graduate Image 1 -->
                                @if($valueBringImageUrl)
                                    <div class="value-bring-graduate-image-2 image img-cover">
                                        <img src="{{ $valueBringImageUrl }}" alt="Graduate Image" loading="lazy">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    
    {{-- Student Feedback Section --}}
    @include('frontend.component.student-feedback')
    
    {{-- News Outstanding Section --}}
    @include('frontend.component.news-outstanding')
    
    <script>
        $(document).ready(function() {
            // Xử lý click vào video thumbnail
            $('.value-bring-video-thumbnail .video-thumbnail-wrapper').on('click', function(e) {
                e.preventDefault();
                let $wrapper = $(this);
                let videoEmbed = $wrapper.attr('data-video-embed');
                
                // Nếu đã có iframe rồi thì không làm gì
                if ($wrapper.find('iframe').length > 0) {
                    return;
                }
                
                let iframeHtml = '';
                
                // Kiểm tra xem là URL hay iframe code
                if (videoEmbed.includes('<iframe')) {
                    // Nếu là iframe code, parse và thêm autoplay
                    let $temp = $('<div>').html(videoEmbed);
                    let $iframe = $temp.find('iframe');
                    if ($iframe.length > 0) {
                        let src = $iframe.attr('src') || '';
                        if (src) {
                            // Thêm autoplay vào src
                            let separator = src.includes('?') ? '&' : '?';
                            src = src + separator + 'autoplay=1&mute=0';
                            $iframe.attr('src', src);
                        }
                        // Lấy các attributes khác
                        let width = $iframe.attr('width') || '100%';
                        let allow = $iframe.attr('allow') || 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture';
                        let allowfullscreen = $iframe.attr('allowfullscreen') !== undefined;
                        
                        iframeHtml = '<iframe src="' + ($iframe.attr('src') || src) + '" width="' + width + '" frameborder="0" allow="' + allow + '"' + (allowfullscreen ? ' allowfullscreen' : '') + ' style="width: 100%; height: 100%; border-radius: 12px;"></iframe>';
                    } else {
                        iframeHtml = videoEmbed;
                    }
                } else {
                    // Nếu là URL, tạo iframe mới
                    let src = videoEmbed;
                    if (src.includes('youtube.com/embed/') || src.includes('youtu.be/') || src.includes('youtube.com/watch')) {
                        // Extract video ID từ YouTube URL
                        let videoId = '';
                        if (src.includes('youtube.com/embed/')) {
                            videoId = src.split('youtube.com/embed/')[1].split('?')[0].split('&')[0];
                        } else if (src.includes('youtu.be/')) {
                            videoId = src.split('youtu.be/')[1].split('?')[0].split('&')[0];
                        } else if (src.includes('youtube.com/watch')) {
                            let match = src.match(/[?&]v=([^&]+)/);
                            if (match) videoId = match[1];
                        }
                        
                        if (videoId) {
                            src = 'https://www.youtube.com/embed/' + videoId;
                        }
                    }
                    
                    // Thêm autoplay
                    let separator = src.includes('?') ? '&' : '?';
                    src = src + separator + 'autoplay=1&mute=0';
                    
                    iframeHtml = '<iframe src="' + src + '" width="100%" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen style="width: 100%; height: 100%; border-radius: 12px;"></iframe>';
                }
                
                // Thay thế thumbnail bằng iframe và thêm class has-video
                $wrapper.addClass('has-video');
                $wrapper.html(iframeHtml);
            });
        });

        // AJAX filter cho major catalogues
        var majorsSwiper = null;
        
        // Khởi tạo swiper cho tab "Tất cả ngành"
        function initMajorsSwiper() {
            var $swiperContainer = $('.majors-list-swiper');
            if (typeof Swiper !== 'undefined' && $swiperContainer.length > 0) {
                // Destroy swiper cũ nếu có
                if (majorsSwiper) {
                    majorsSwiper.destroy(true, true);
                    majorsSwiper = null;
                }
                
                majorsSwiper = new Swiper('.majors-list-swiper', {
                    slidesPerView: 3,
                    spaceBetween: 30,
                    navigation: {
                        nextEl: '.majors-list-swiper .swiper-button-next',
                        prevEl: '.majors-list-swiper .swiper-button-prev',
                    },
                    watchOverflow: true, // Chỉ enable navigation khi có nhiều slide
                    breakpoints: {
                        0: {
                            slidesPerView: 1,
                            spaceBetween: 15,
                        },
                        768: {
                            slidesPerView: 2,
                            spaceBetween: 20,
                        },
                        1024: {
                            slidesPerView: 3,
                            spaceBetween: 30,
                        }
                    }
                });
            }
        }
        
        // Function để load majors qua AJAX
        function loadMajorsByCatalogue(catalogueId, canonical, isActiveTab) {
            var isAllMajors = catalogueId === '';
            
            // Hiển thị loading
            var $swiperWrapper = $('#majors-swiper-wrapper');
            var $gridWrapper = $('#majors-grid-wrapper');
            var $gridContent = $gridWrapper.find('.uk-grid');
            var $swiperContainer = $swiperWrapper.find('.majors-list-swiper');
            var $swiperWrapperInner = $swiperContainer.find('.swiper-wrapper');
            
            // Ẩn cả 2 wrapper trước
            $swiperWrapper.hide();
            $gridWrapper.hide();
            
            // Nếu không có grid container, tạo mới
            if ($gridContent.length === 0) {
                $gridWrapper.prepend('<div class="uk-grid uk-grid-medium" data-uk-grid-match></div>');
                $gridContent = $gridWrapper.find('.uk-grid');
            }
            
            // Hiển thị loading
            if (isAllMajors) {
                $swiperWrapperInner.html('<div class="swiper-slide" style="text-align: center; padding: 40px;"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
                $swiperWrapper.show();
            } else {
                $gridContent.html('<div class="uk-width-1-1" style="text-align: center; padding: 40px;"><i class="fa fa-spinner fa-spin fa-2x"></i></div>');
                $gridWrapper.show();
            }
            
            // Gọi AJAX
            $.ajax({
                url: '{{ route("ajax.major.getMajorsByCatalogue") }}',
                type: 'GET',
                data: {
                    catalogue_id: catalogueId,
                    limit: isAllMajors ? 0 : 6 // 0 = không giới hạn cho tab "Tất cả ngành"
                },
                success: function(response) {
                        if (response.success && response.html) {
                            if (isAllMajors) {
                                // Tab "Tất cả ngành" - dùng swiper
                                // Parse HTML và wrap mỗi major-card vào swiper-slide
                                var wrappedHtml = '';
                                var $tempDiv = $('<div>').html(response.html);
                                
                                // Tìm tất cả major-card (có thể nằm trong uk-width-medium-1-3 hoặc trực tiếp)
                                $tempDiv.find('.major-card').each(function() {
                                    var $card = $(this);
                                    var cardHtml = $card[0].outerHTML;
                                    wrappedHtml += '<div class="swiper-slide">' + cardHtml + '</div>';
                                });
                                
                                $swiperWrapperInner.html(wrappedHtml);
                                $swiperWrapper.show();
                                $gridWrapper.hide();
                                
                                // Khởi tạo hoặc update swiper
                                if (majorsSwiper) {
                                    majorsSwiper.destroy(true, true);
                                    majorsSwiper = null;
                                }
                                setTimeout(function() {
                                    initMajorsSwiper();
                                }, 100);
                            } else {
                                // Các tab khác - dùng grid
                                $gridContent.html(response.html);
                                $swiperWrapper.hide();
                                $gridWrapper.show();
                                
                                // Destroy swiper nếu có
                                if (majorsSwiper) {
                                    majorsSwiper.destroy(true, true);
                                    majorsSwiper = null;
                                }
                                
                                // Re-init UIkit grid match
                                if (typeof UIkit !== 'undefined' && UIkit.gridMatch) {
                                    UIkit.gridMatch($gridContent);
                                }
                            }
                            
                            // Cập nhật link button
                            if (response.canonical) {
                                $('#majors-list-cta-button').attr('href', response.canonical);
                            }
                        } else {
                            if (isAllMajors) {
                                $swiperWrapperInner.html('<div class="swiper-slide" style="text-align: center; padding: 40px;"><p>Không có dữ liệu</p></div>');
                                $swiperWrapper.show();
                            } else {
                                $gridContent.html('<div class="uk-width-1-1" style="text-align: center; padding: 40px;"><p>Không có dữ liệu</p></div>');
                                $gridWrapper.show();
                            }
                        }
                    },
                    error: function() {
                        if (isAllMajors) {
                            $swiperWrapperInner.html('<div class="swiper-slide" style="text-align: center; padding: 40px;"><p>Có lỗi xảy ra. Vui lòng thử lại.</p></div>');
                            $swiperWrapper.show();
                        } else {
                            $gridContent.html('<div class="uk-width-1-1" style="text-align: center; padding: 40px;"><p>Có lỗi xảy ra. Vui lòng thử lại.</p></div>');
                            $gridWrapper.show();
                        }
                    }
                });
        }
        
        $(document).ready(function() {
            // Khởi tạo swiper cho dữ liệu ban đầu nếu có
            var $swiperWrapper = $('#majors-swiper-wrapper');
            var $gridWrapper = $('#majors-grid-wrapper');
            
            if ($swiperWrapper.find('.swiper-slide').length > 0) {
                // Có dữ liệu ban đầu trong swiper - khởi tạo swiper
                setTimeout(function() {
                    initMajorsSwiper();
                }, 100);
            }
            
            // Kiểm tra tab active khi trang load
            var $activeTab = $('.major-catalogue-filter .filter-tab.active');
            var isAllMajorsOnLoad = $activeTab.length > 0 && ($activeTab.data('catalogue-id') === '' || $activeTab.data('catalogue-id') === undefined);
            
            if (isAllMajorsOnLoad) {
                // Tab "Tất cả ngành" đang active - hiển thị swiper với dữ liệu ban đầu
                $swiperWrapper.show();
                $gridWrapper.hide();
                // Có thể gọi AJAX để load thêm nếu cần (tất cả majors thay vì chỉ 6)
                // loadMajorsByCatalogue('', '', true);
            } else {
                // Tab khác active - hiển thị grid với dữ liệu hiện có (nếu có)
                if ($gridWrapper.find('.major-card').length > 0) {
                    $swiperWrapper.hide();
                    $gridWrapper.show();
                } else {
                    // Không có dữ liệu, gọi AJAX
                    var catalogueId = $activeTab.data('catalogue-id') || '';
                    var canonical = $activeTab.data('canonical') || '';
                    if (catalogueId) {
                        loadMajorsByCatalogue(catalogueId, canonical, false);
                    }
                }
            }
            
            $('.major-catalogue-filter .filter-tab').on('click', function(e) {
                e.preventDefault();
                
                // Remove active class từ tất cả tabs
                $('.major-catalogue-filter .filter-tab').removeClass('active');
                
                // Add active class cho tab được click
                $(this).addClass('active');
                
                // Lấy catalogue_id và canonical
                var catalogueId = $(this).data('catalogue-id') || '';
                var canonical = $(this).data('canonical') || '';
                
                // Gọi function load majors
                loadMajorsByCatalogue(catalogueId, canonical, false);
            });
        });

    </script>
    
@endsection

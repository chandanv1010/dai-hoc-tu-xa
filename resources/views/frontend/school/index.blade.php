@extends('frontend.homepage.layout')
@section('content')
    @php
        $name = $pivot->name ?? '';
        $description = $pivot->description ?? '';
        $content = $pivot->content ?? '';
        $canonical = write_url($pivot->canonical ?? '');
    @endphp

    {{-- Hidden H1 for SEO - Tên trường --}}
    @if($name)
        <h1 style="position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden;">{{ $name }}</h1>
    @endif

    {{-- Khối Slide từ Album - PC và Mobile --}}
    @if(isset($album) && !empty($album) && isset($album[0]) && !empty($album[0]))
        @php
            // Xác định ảnh cho PC và Mobile
            $pcImage = $album[0];
            $mobileImage = isset($album[1]) && !empty($album[1]) ? $album[1] : $album[0]; // Fallback về album[0] nếu không có album[1]
        @endphp
        
        {{-- Khối Slide PC --}}
        <div class="panel-school-slide pc">
            <div class="swiper-container school-slide-container">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <span class="image img-cover" style="cursor: pointer;" data-uk-modal="{target:'#school-consultation-modal'}">
                            <img src="{{ image($pcImage) }}" alt="{{ $name }}">
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Khối Slide Mobile --}}
        <div class="panel-school-slide mobile">
            <div class="swiper-container school-slide-container">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <span class="image img-cover" style="cursor: pointer;" data-uk-modal="{target:'#school-consultation-modal'}">
                            <img src="{{ image($mobileImage) }}" alt="{{ $name }}">
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Khối Statistics --}}
    @if(isset($school) && $school->is_show_statistics == 2)
        @php
            $stats = [
                [
                    'number' => $school->statistics_majors ?? 0,
                    'label' => 'Ngành Học',
                    'suffix' => ''
                ],
                [
                    'number' => $school->statistics_students ?? 0,
                    'label' => 'Học Viên Theo Học',
                    'suffix' => '+'
                ],
                [
                    'number' => $school->statistics_courses ?? 0,
                    'label' => 'Khóa Khai giảng',
                    'suffix' => '+'
                ],
                [
                    'number' => $school->statistics_satisfaction ?? 0,
                    'label' => 'Học Viên Hài Lòng',
                    'suffix' => '%'
                ],
                [
                    'number' => $school->statistics_employment ?? 0,
                    'label' => 'Có việc sau tốt nghiệp',
                    'suffix' => '%'
                ],
            ];
        @endphp
        <div class="panel-statistics wow fadeInUp" data-wow-delay="0.1s">
            <div class="uk-container uk-container-center">
                <div class="statistics-wrapper">
                    <div class="statistics-list">
                        @foreach($stats as $index => $stat)
                            @if($stat['number'] > 0)
                                @php
                                    $statDelay = (0.1 + ($loop->index * 0.1)) . 's';
                                @endphp
                                <div class="statistics-item wow fadeInUp" data-wow-delay="{{ $statDelay }}" data-target="{{ $stat['number'] }}" data-suffix="{{ $stat['suffix'] }}">
                                    <div class="stat-number">
                                        <span class="counter-value">0</span><span class="counter-suffix">{{ $stat['suffix'] }}</span>
                                    </div>
                                    <div class="stat-label">{{ $stat['label'] }}</div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Khối Giới thiệu --}}
    @if(isset($school) && $school->is_show_intro == 2)
        @php
            $intro = ($pivot && isset($pivot->intro)) ? (is_array($pivot->intro) ? $pivot->intro : json_decode($pivot->intro, true)) : [];
            $introCreated = $intro['created'] ?? '';
            $introTop = $intro['top'] ?? '';
            $introPercent = $intro['percent'] ?? '';
        @endphp
        <div class="panel-school-intro wow fadeInUp" data-wow-delay="0.2s">
            <div class="uk-container uk-container-center uk-container-1260">
                <div class="uk-grid uk-grid-large uk-flex uk-flex-middle" data-uk-grid-match>
                    <div class="uk-width-medium-1-2">
                        <div class="school-intro-content">
                            <h2 class="school-intro-title">Về {{ $name }}</h2>
                            @if(!empty($description))
                                <div class="school-intro-description">
                                    {!! $description !!}
                                </div>
                            @endif
                            @if(!empty($content))
                                <div class="school-intro-detail">
                                    {!! $content !!}
                                </div>
                            @endif
                            
                            {{-- 3 thẻ thống kê --}}
                            @if(!empty($introCreated) || !empty($introTop) || !empty($introPercent))
                                <div class="school-intro-stats">
                                    @if(!empty($introCreated))
                                        <div class="intro-stat-card wow fadeInUp" data-wow-delay="0.3s">
                                            <div class="stat-number">{{ $introCreated }}</div>
                                            <div class="stat-label">Năm thành lập</div>
                                        </div>
                                    @endif
                                    @if(!empty($introTop))
                                        <div class="intro-stat-card wow fadeInUp" data-wow-delay="0.4s">
                                            <div class="stat-number">{{ $introTop }}</div>
                                            <div class="stat-label">Trường ĐH VN</div>
                                        </div>
                                    @endif
                                    @if(!empty($introPercent))
                                        <div class="intro-stat-card wow fadeInUp" data-wow-delay="0.5s">
                                            <div class="stat-number">{{ $introPercent }}</div>
                                            <div class="stat-label">Công nhận</div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            {{-- Nút Tải Lộ Trình Học và Tải Tài Liệu --}}
                            <div class="school-intro-actions">
                                <a href="javascript:void(0)" class="btn-learn-more" data-uk-modal="{target:'#school-tai-lo-trinh-modal'}">
                                    <span>Tải lộ trình học</span>
                                </a>
                                @if(!empty($school->download_file))
                                    <a href="{{ asset($school->download_file) }}" class="btn-download" download>
                                        <span>Tải Tài Liệu</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="uk-width-medium-1-2">
                        @if(!empty($school->intro_image))
                            <div class="school-intro-image">
                                <span class="image img-cover intro-image-animated">
                                    <img src="{{ image($school->intro_image) }}" alt="{{ $name }}">
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Khối Hệ Đào Tạo Từ Xa --}}
    @include('frontend.component.distance-learning')

    {{-- Khối Thông Báo Tuyển Sinh --}}
    @if(isset($school) && $school->is_show_announce == 2)
        @php
            $announce = ($pivot && isset($pivot->announce)) ? (is_array($pivot->announce) ? $pivot->announce : json_decode($pivot->announce, true)) : [];
            $announceDescription = $announce['description'] ?? '';
            $announceContent = $announce['content'] ?? '';
            $announceTarget = $announce['target'] ?? '';
            $announceType = $announce['type'] ?? '';
            $announceRequest = $announce['request'] ?? '';
            $announceAddress = $announce['address'] ?? '';
            $announceValue = $announce['value'] ?? '';
        @endphp
        <div class="panel-school-announce wow fadeInUp" data-wow-delay="0.3s">
            <div class="uk-container uk-container-center uk-container-1260">
                <div class="announce-header">
                    <h2 class="announce-title">{{ !empty($school->announce_title) ? $school->announce_title : 'Thông Báo Tuyển Sinh' }}</h2>
                    @if(!empty($announceDescription))
                        <p class="announce-subtitle">{{ $announceDescription }}</p>
                    @endif
                </div>
                <div class="uk-grid uk-grid-large" data-uk-grid-match>
                    {{-- Left: Image --}}
                    <div class="uk-width-medium-2-5">
                        @if(!empty($school->announce_image))
                            <div class="announce-image">
                                <span class="image img-cover">
                                    <img src="{{ image($school->announce_image) }}" alt="{{ $name }}">
                                </span>
                            </div>
                        @elseif(!empty($school->image))
                            <div class="announce-image">
                                <span class="image img-cover">
                                    <img src="{{ image($school->image) }}" alt="{{ $name }}">
                                </span>
                            </div>
                        @endif
                    </div>
                    {{-- Right: Content --}}
                    <div class="uk-width-medium-3-5">
                        <div class="announce-content">
                            @if(!empty($announceContent))
                                <div class="announce-intro">
                                    {!! $announceContent !!}
                                </div>
                            @endif

                            {{-- Accordion Sections --}}
                            <div class="uk-accordion" data-uk-accordion="{collapse: false}">
                                {{-- Đối tượng tuyển sinh --}}
                                @if(!empty($announceTarget))
                                    <div class="announce-section">
                                        <h3 class="uk-accordion-title announce-section-title">
                                            <span>Đối tượng tuyển sinh</span>
                                            <i class="fa fa-chevron-up"></i>
                                        </h3>
                                        <div class="uk-accordion-content announce-section-content">
                                            {!! $announceTarget !!}
                                        </div>
                                    </div>
                                @endif

                                {{-- Hình thức tuyển sinh --}}
                                @if(!empty($announceType))
                                    <div class="announce-section">
                                        <h3 class="uk-accordion-title announce-section-title">
                                            <span>Hình thức tuyển sinh</span>
                                            <i class="fa fa-chevron-down"></i>
                                        </h3>
                                        <div class="uk-accordion-content announce-section-content">
                                            {!! $announceType !!}
                                        </div>
                                    </div>
                                @endif

                                {{-- Yêu cầu tuyển sinh --}}
                                @if(!empty($announceRequest))
                                    <div class="announce-section">
                                        <h3 class="uk-accordion-title announce-section-title">
                                            <span>Yêu cầu tuyển sinh</span>
                                            <i class="fa fa-chevron-down"></i>
                                        </h3>
                                        <div class="uk-accordion-content announce-section-content">
                                            {!! $announceRequest !!}
                                        </div>
                                    </div>
                                @endif

                                {{-- Nơi tiếp nhận hồ sơ --}}
                                @if(!empty($announceAddress))
                                    <div class="announce-section">
                                        <h3 class="uk-accordion-title announce-section-title">
                                            <span>Nơi tiếp nhận hồ sơ</span>
                                            <i class="fa fa-chevron-down"></i>
                                        </h3>
                                        <div class="uk-accordion-content announce-section-content">
                                            {!! $announceAddress !!}
                                        </div>
                                    </div>
                                @endif

                                {{-- Giá trị văn bằng --}}
                                @if(!empty($announceValue))
                                    <div class="announce-section">
                                        <h3 class="uk-accordion-title announce-section-title">
                                            <span>Giá trị văn bằng</span>
                                            <i class="fa fa-chevron-down"></i>
                                        </h3>
                                        <div class="uk-accordion-content announce-section-content">
                                            {!! $announceValue !!}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Khối Đăng Ký Ngay --}}
    <div class="panel-school-register wow fadeInUp" data-wow-delay="0.4s">
        <div class="uk-container uk-container-center">
            <div class="register-banner-content">
                <h2 class="register-banner-title">Hàng nghìn người đã bắt đầu – Bạn thì sao?</h2>
                <p class="register-banner-subtitle">
                    <i class="fa fa-check-circle"></i>
                    Đăng ký ngay để nhận thông tin tuyển sinh mới nhất!
                </p>
                <button class="register-banner-btn" data-uk-modal="{target:'#school-hoc-thu-modal'}">
                    <i class="fa fa-paper-plane"></i>
                    <span>Đăng Ký Ngay</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Khối Phù Hợp --}}
    @if(isset($school) && $school->is_show_suitable == 2)
        @php
            $suitable = ($pivot && isset($pivot->suitable)) ? (is_array($pivot->suitable) ? $pivot->suitable : json_decode($pivot->suitable, true)) : [];
            $suitableName = $suitable['name'] ?? 'Chương trình Đại học từ xa phù hợp với ai?';
            $suitableDescription = $suitable['description'] ?? 'Những nhóm học viên có thể tận dụng tối đa lợi thế học online linh hoạt, hiện đại';
            $suitableItems = $suitable['items'] ?? [];
        @endphp
        @if(!empty($suitableItems) && count($suitableItems) > 0)
            <div class="panel-school-suitable wow fadeInUp" data-wow-delay="0.5s">
                <div class="uk-container uk-container-center uk-container-1260">
                    <div class="suitable-header">
                        <h2 class="suitable-title">{{ $suitableName }}</h2>
                        @if(!empty($suitableDescription))
                            <p class="suitable-subtitle">{{ $suitableDescription }}</p>
                        @endif
                    </div>
                    <div class="suitable-grid">
                        @foreach($suitableItems as $item)
                            @php
                                $suitableDelay = (0.6 + ($loop->index * 0.15)) . 's';
                            @endphp
                            <div class="suitable-card wow fadeInUp" data-wow-delay="{{ $suitableDelay }}">
                                <div class="suitable-card-icon">
                                    @if(!empty($item['image']))
                                        <span class="image img-cover">
                                            <img src="{{ image($item['image']) }}" alt="{{ $item['name'] ?? '' }}">
                                        </span>
                                    @endif
                                </div>
                                <div class="suitable-card-content">
                                    @if(!empty($item['name']))
                                        <h3 class="suitable-card-title">{{ $item['name'] }}</h3>
                                    @endif
                                    @if(!empty($item['description']))
                                        <p class="suitable-card-description">{{ $item['description'] }}</p>
                                    @endif
                                </div>
                                <div class="suitable-card-decoration"></div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- Khối Ưu Điểm --}}
    @if(isset($school) && $school->is_show_advantage == 2)
        @php
            $advantage = ($pivot && isset($pivot->advantage)) ? (is_array($pivot->advantage) ? $pivot->advantage : json_decode($pivot->advantage, true)) : [];
            $advantageTitle = $advantage['title'] ?? 'Ưu Điểm của Hệ Đào Tạo Từ Xa NEU';
            $advantageDescription = $advantage['description'] ?? '';
            $advantageItems = $advantage['items'] ?? [];
        @endphp
        @if(!empty($advantageItems) && count($advantageItems) > 0)
            <div class="panel-school-advantage wow fadeInUp" data-wow-delay="0.6s">
                <div class="uk-container uk-container-center uk-container-1260">
                    <div class="advantage-header">
                        <h2 class="advantage-title">{{ $advantageTitle }}</h2>
                        @if(!empty($advantageDescription))
                            <p class="advantage-subtitle">{{ $advantageDescription }}</p>
                        @endif
                    </div>
                    <div class="advantage-grid">
                        @foreach($advantageItems as $item)
                            @php
                                $advantageDelay = (0.7 + ($loop->index * 0.15)) . 's';
                            @endphp
                            <div class="advantage-card wow fadeInUp" data-wow-delay="{{ $advantageDelay }}">
                                <div class="advantage-card-header">
                                    <div class="advantage-card-icon-wrapper">
                                        @if(!empty($item['icon']))
                                            <div class="advantage-card-icon">
                                                @if(strpos($item['icon'], 'http') === 0 || strpos($item['icon'], '/') === 0)
                                                    <img src="{{ image($item['icon']) }}" alt="{{ $item['name'] ?? '' }}">
                                                @else
                                                    <i class="{{ $item['icon'] }}"></i>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    @if(!empty($item['name']))
                                        <h3 class="advantage-card-title">{{ $item['name'] }}</h3>
                                    @endif
                                </div>
                                <div class="advantage-card-content">
                                    @if(!empty($item['description']))
                                        <p class="advantage-card-description">{{ $item['description'] }}</p>
                                    @endif
                                    @if(!empty($item['note']))
                                        <a href="javascript:void(0)" class="advantage-card-link">
                                            <span>→</span> {{ $item['note'] }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endif

     {{-- Banner Tư Vấn --}}
     <div class="uk-container uk-container-center uk-container-1260">
        <div class="panel-school-advantage-banner wow fadeInUp" data-wow-delay="0.65s">
        
            <div class="advantage-banner-content">
                <div class="advantage-banner-text">
                    <div class="banner-text-line">
                        <span class="banner-icon">🎓</span>
                        <span>ĐĂNG KÝ TƯ VẤN ĐỢT KHAI GIẢNG MỚI NHẤT</span>
                    </div>
                    <div class="banner-text-line">
                        <span>Nắm lấy cơ hội sở hữu "BẰNG ĐẠI HỌC" ngay hôm nay</span>
                    </div>
                </div>
                <div class="advantage-banner-action">
                    <a href="#school-consultation-modal" class="btn-consultation" data-uk-modal>
                        <i class="fa fa-headphones"></i>
                        <span>NHẬN TƯ VẤN MIỄN PHÍ</span>
                    </a>
                    <div class="banner-features">
                        <span>Tư vấn miễn phí</span>
                        <span class="separator">•</span>
                        <span>Hỗ trợ 24/7</span>
                        <span class="separator">•</span>
                        <span>Giải đáp thắc mắc</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Khối Các Ngành Đào Tạo Từ Xa --}}
    @if(isset($school) && $school->is_show_majors == 2 && !empty($schoolMajors) && count($schoolMajors) > 0)
        <div class="panel-school-majors wow fadeInUp" data-wow-delay="0.7s">
            <div class="uk-container uk-container-center uk-container-1260">
                <div class="majors-header">
                    <h2 class="majors-title">Các Ngành Đào Tạo Từ Xa</h2>
                    <p class="majors-description">
                        Chương trình Đại học từ xa cung cấp nhiều lựa chọn ngành học đáp ứng nhu cầu của người đi làm, sinh viên, cán bộ công chức. Mỗi ngành đều được thiết kế theo chuẩn chương trình, cập nhật thực tiễn, giúp bạn dễ dàng <span class="highlight-text">thăng tiến và phát triển nghề nghiệp</span>
                    </p>
                </div>

                <div class="majors-swiper">
                    <div class="swiper-container majors-swiper-container">
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-wrapper">
                            @foreach($schoolMajors as $item)
                                @php
                                    $major = $item['major'];
                                    $majorPivot = $item['majorPivot'];
                                    $majorData = $item['data'];
                                    $majorName = $majorPivot->name ?? '';
                                    $majorImage = $major->image ?? '';
                                    $majorCredits = $majorData['credits'] ?? '';
                                    $majorDuration = $majorData['duration'] ?? '';
                                    $majorCanonical = $majorPivot->canonical ?? '';
                                    $majorUrl = $majorCanonical ? write_url($majorCanonical) : '#';
                                    $majorDelay = (0.8 + ($loop->index * 0.15)) . 's';
                                    
                                    // Lấy form tải lộ trình từ major
                                    $formTaiLoTrinh = $major->form_tai_lo_trinh_json ?? null;
                                    $hasForm = $formTaiLoTrinh && !empty($formTaiLoTrinh['script']);
                                    $formTitle = $hasForm ? ($formTaiLoTrinh['title'] ?? 'Tải Lộ Trình Học') : '';
                                    $formDescription = $hasForm ? ($formTaiLoTrinh['description'] ?? '') : '';
                                    $formScript = $hasForm ? ($formTaiLoTrinh['script'] ?? '') : '';
                                    $formFooter = $hasForm ? ($formTaiLoTrinh['footer'] ?? '') : '';
                                @endphp
                                <div class="swiper-slide">
                                    <div class="major-card wow fadeInUp" data-wow-delay="{{ $majorDelay }}">
                                        @if(!empty($majorImage))
                                            <div class="major-card-image">
                                                <span class="image img-cover">
                                                    <img src="{{ image($majorImage) }}" alt="{{ $majorName }}">
                                                </span>
                                            </div>
                                        @endif
                                        <div class="major-card-content">
                                            @if(!empty($majorName))
                                                <h3 class="major-card-title">
                                                    <a href="{{ $majorUrl }}" title="{{ $majorName }}">{{ $majorName }}</a>
                                                </h3>
                                            @endif
                                            <div class="major-card-info">
                                                @if(!empty($majorCredits))
                                                    <div class="major-info-item">
                                                        <i class="fa fa-book"></i>
                                                        <span class="info-label">Số tín chỉ:</span>
                                                        <span class="info-value">{{ $majorCredits }}</span>
                                                    </div>
                                                @endif
                                                @if(!empty($majorDuration))
                                                    <div class="major-info-item">
                                                        <i class="fa fa-clock-o"></i>
                                                        <span class="info-label">Thời Gian Đào Tạo:</span>
                                                        <span class="info-value">{{ $majorDuration }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            @if($hasForm)
                                                <button type="button" 
                                                    class="major-card-btn major-roadmap-btn" 
                                                    data-major-form-title="{{ htmlspecialchars($formTitle, ENT_QUOTES, 'UTF-8') }}"
                                                    data-major-form-description="{{ htmlspecialchars($formDescription, ENT_QUOTES, 'UTF-8') }}"
                                                    data-major-form-script="{{ htmlspecialchars($formScript, ENT_QUOTES, 'UTF-8') }}"
                                                    data-major-form-footer="{{ htmlspecialchars($formFooter, ENT_QUOTES, 'UTF-8') }}"
                                                    onclick="if(typeof window.openMajorRoadmapModal === 'function') { window.openMajorRoadmapModal(this); } else { console.error('openMajorRoadmapModal not found'); }">
                                                    Nhận lộ trình chi tiết
                                                </button>
                                            @else
                                                <a href="{{ $majorUrl }}" class="major-card-btn">
                                                    Xem chi tiết
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>

                <div class="majors-footer">
                    <a href="{{ write_url('cac-nganh-dao-tao-tu-xa') }}" class="majors-view-all-btn">
                        Xem các ngành đào tạo từ xa
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Form Tải Lộ Trình Học cho Major (Dynamic) --}}
    <div id="major-roadmap-modal" class="uk-modal download-roadmap-modal">
        <div class="uk-modal-dialog download-roadmap-modal-dialog">
            <a class="uk-modal-close uk-close"></a>
            
            <!-- Header -->
            <div class="download-roadmap-header">
                <h2 class="download-roadmap-title" id="major-roadmap-title"></h2>
                <div class="download-roadmap-description" id="major-roadmap-description"></div>
            </div>
            
            <!-- Wrapper cho script nhúng -->
            <div class="download-roadmap-form-wrapper">
                <div class="download-roadmap-script-wrapper" id="major-roadmap-script"></div>
            </div>
            
            <!-- Footer -->
            <div class="download-roadmap-footer" id="major-roadmap-footer"></div>
        </div>
    </div>

    {{-- Script inline để đảm bảo function sẵn sàng ngay lập tức --}}
    <script>
    (function() {
        // Function để decode HTML entities
        function decodeHtmlEntities(str) {
            if (!str) return '';
            const textarea = document.createElement('textarea');
            textarea.innerHTML = str;
            return textarea.value;
        }

        // Function để mở modal tải lộ trình học cho major - ĐỊNH NGHĨA GLOBAL NGAY
        window.openMajorRoadmapModal = function(button) {
            console.log('=== openMajorRoadmapModal called ===', button);
            
            try {
                // Lấy và decode HTML entities từ data attributes
                let title = button.getAttribute('data-major-form-title') || 'Tải Lộ Trình Học';
                let description = button.getAttribute('data-major-form-description') || '';
                let script = button.getAttribute('data-major-form-script') || '';
                let footer = button.getAttribute('data-major-form-footer') || '';
                
                console.log('Raw data:', { title, description, script: script.substring(0, 100), footer });
                
                // Decode HTML entities
                title = decodeHtmlEntities(title);
                description = decodeHtmlEntities(description);
                script = decodeHtmlEntities(script);
                footer = decodeHtmlEntities(footer);
                
                console.log('Decoded data:', { title, description, script: script.substring(0, 100), footer });
                
                // Kiểm tra modal có tồn tại không
                const modal = document.getElementById('major-roadmap-modal');
                if (!modal) {
                    console.error('Major roadmap modal not found');
                    alert('Modal không tìm thấy. Vui lòng tải lại trang.');
                    return;
                }
                console.log('Modal found:', modal);
                
                // Cập nhật nội dung modal
                const titleEl = document.getElementById('major-roadmap-title');
                const descEl = document.getElementById('major-roadmap-description');
                const scriptEl = document.getElementById('major-roadmap-script');
                const footerEl = document.getElementById('major-roadmap-footer');
                
                console.log('Elements found:', { titleEl, descEl, scriptEl, footerEl });
                
                if (titleEl) {
                    titleEl.textContent = title;
                    console.log('Title set:', title);
                } else {
                    console.error('major-roadmap-title element not found');
                }
                
                if (descEl) {
                    descEl.innerHTML = description;
                    console.log('Description set');
                } else {
                    console.error('major-roadmap-description element not found');
                }
                
                if (scriptEl) {
                    // Clear previous content
                    scriptEl.innerHTML = '';
                    
                    console.log('Script content length:', script.length);
                    console.log('Script content preview:', script.substring(0, 200));
                    
                    // Inject script HTML
                    if (script && script.trim() !== '') {
                        scriptEl.innerHTML = script;
                        
                        // Re-execute all script tags found in the injected HTML
                        const scripts = scriptEl.getElementsByTagName('script');
                        const scriptsArray = Array.from(scripts); // Convert to array to avoid live NodeList issues
                        
                        console.log('Found scripts to execute:', scriptsArray.length);
                        
                        scriptsArray.forEach(function(oldScript) {
                            const newScript = document.createElement('script');
                            
                            // Copy all attributes
                            Array.from(oldScript.attributes).forEach(function(attr) {
                                newScript.setAttribute(attr.name, attr.value);
                            });
                            
                            // Copy script content
                            if (oldScript.src) {
                                newScript.src = oldScript.src;
                            } else {
                                newScript.text = oldScript.text || oldScript.innerHTML;
                            }
                            
                            // Append new script to body (scripts execute when appended)
                            document.body.appendChild(newScript);
                            
                            // Remove old script
                            oldScript.parentNode.removeChild(oldScript);
                            
                            console.log('Script executed:', oldScript.src || 'inline script');
                        });
                        
                        // Handle iframes (like Form.io)
                        const iframes = scriptEl.getElementsByTagName('iframe');
                        console.log('Found iframes:', iframes.length);
                        Array.from(iframes).forEach(function(iframe) {
                            console.log('Iframe found:', iframe.src || iframe.id);
                        });
                        
                        console.log('Script set and executed successfully');
                    } else {
                        console.warn('Script is empty or not provided');
                        scriptEl.innerHTML = '<p style="text-align: center; color: #999; padding: 20px;">Form đang được cập nhật...</p>';
                    }
                } else {
                    console.error('major-roadmap-script element not found');
                }
                
                if (footerEl) {
                    footerEl.innerHTML = footer;
                    console.log('Footer set');
                } else {
                    console.error('major-roadmap-footer element not found');
                }
                
                // Mở modal bằng UIkit - mở TRƯỚC để đảm bảo modal được render
                console.log('Checking UIkit:', typeof UIkit, UIkit);
                
                if (typeof UIkit !== 'undefined' && UIkit.modal) {
                    const modalInstance = UIkit.modal('#major-roadmap-modal');
                    console.log('Modal instance:', modalInstance);
                    if (modalInstance) {
                        modalInstance.show();
                        console.log('Modal.show() called via UIkit.modal()');
                    }
                } else {
                    console.error('UIkit is not loaded, trying alternative method...');
                    // Fallback: thêm class trực tiếp
                    modal.classList.add('uk-open');
                    document.body.classList.add('uk-modal-page');
                    console.log('Modal opened by adding classes');
                }
                
                // Re-check và re-execute scripts sau khi modal đã mở (delay để DOM được render)
                setTimeout(function() {
                    console.log('Re-checking scripts after modal opened...');
                    const scriptEl = document.getElementById('major-roadmap-script');
                    if (scriptEl) {
                        const scripts = scriptEl.getElementsByTagName('script');
                        const scriptsArray = Array.from(scripts);
                        console.log('Re-found scripts after modal opened:', scriptsArray.length);
                        
                        // Re-execute nếu có script tags mới
                        if (scriptsArray.length > 0) {
                            scriptsArray.forEach(function(oldScript) {
                                // Kiểm tra xem script đã được execute chưa (có src hoặc đã có parent khác)
                                if (!oldScript.src || oldScript.parentNode === scriptEl) {
                                    const newScript = document.createElement('script');
                                    Array.from(oldScript.attributes).forEach(function(attr) {
                                        newScript.setAttribute(attr.name, attr.value);
                                    });
                                    if (oldScript.src) {
                                        newScript.src = oldScript.src;
                                    } else {
                                        newScript.text = oldScript.text || oldScript.innerHTML;
                                    }
                                    document.body.appendChild(newScript);
                                    oldScript.parentNode.removeChild(oldScript);
                                    console.log('Re-executed script after modal opened');
                                }
                            });
                        }
                    }
                    
                    // Kiểm tra modal có mở không
                    if (modal.classList.contains('uk-open') || modal.style.display !== 'none') {
                        console.log('Modal is open and scripts should be executed!');
                    } else {
                        console.warn('Modal might not be visible');
                    }
                }, 300);
            } catch (error) {
                console.error('Error opening major roadmap modal:', error);
                console.error('Error stack:', error.stack);
                alert('Có lỗi xảy ra khi mở form: ' + error.message);
            }
        };
        
        console.log('openMajorRoadmapModal function defined:', typeof window.openMajorRoadmapModal);
    })();
    </script>

    {{-- Khối Đăng Ký Tuyển Sinh --}}
    <div class="panel-school-enrollment wow fadeInUp" data-wow-delay="0.8s">
        <div class="uk-container uk-container-center uk-container-1260">
            <div class="enrollment-header">
                <h2 class="enrollment-title">Đăng ký tuyển sinh kỳ tới – Chỉ tiêu có hạn!</h2>
            </div>
            <div class="enrollment-content">
                <div class="enrollment-left">
                    <div class="enrollment-card">
                        <h3 class="enrollment-card-title">Thiếu bằng Đại học bằng Mất</h3>
                        <p class="enrollment-card-subtitle"><span class="highlight-red">40%</span> cơ hội thăng tiến</p>
                        <p class="enrollment-card-text">Theo khảo sát VietnamWorks 2024: 78% vị trí quản lý yêu cầu bằng Đại học. Đừng để tấm bằng là rào cản giữa bạn và ước mơ!</p>
                        
                        <div class="enrollment-stats">
                            <div class="stat-item">
                                <span class="stat-number">50.000 +</span>
                                <span class="stat-label">học viên đã tốt nghiệp thành công</span>
                            </div>
                            <div class="stat-item">
                                <i class="fa fa-star"></i>
                                <span>96% hài lòng với chương trình</span>
                            </div>
                        </div>

                        <div class="enrollment-benefits">
                            <div class="benefit-item">
                                <i class="fa fa-check"></i>
                                <span>Tiết kiệm <strong>60% chi phí</strong> so với học chính quy</span>
                            </div>
                            <div class="benefit-item">
                                <i class="fa fa-check"></i>
                                <span>Linh hoạt <strong>100%</strong> - Học mọi lúc, mọi nơi</span>
                            </div>
                            <div class="benefit-item">
                                <i class="fa fa-check"></i>
                                <span><strong>Bằng được công nhận</strong> bởi Bộ GD&ĐT</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="enrollment-middle">
                    <h3 class="enrollment-steps-title">
                        <span class="title-red">3 BƯỚC ĐĂNG KÝ</span>
                        <span class="title-white">HỌC ĐẠI HỌC ONLINE</span>
                    </h3>
                    <p class="enrollment-steps-subtitle">Quy trình tuyển sinh đơn giản</p>
                    
                    <div class="enrollment-steps">
                        <div class="step-item wow fadeInUp" data-wow-delay="0.9s">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <h4>Đăng ký nhận tư vấn</h4>
                                <p>Điền thông tin hoàn thiện Form đăng ký</p>
                            </div>
                        </div>
                        <div class="step-item wow fadeInUp" data-wow-delay="1.0s">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <h4>Nhận tư vấn</h4>
                                <p>Cán bộ tư vấn liên hệ trao đổi về chương trình học, thủ tục hồ sơ</p>
                            </div>
                        </div>
                        <div class="step-item wow fadeInUp" data-wow-delay="1.1s">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <h4>Học và lấy bằng cử nhân đại học</h4>
                                <p>Tham gia học trực tuyến và tích lũy đủ số tín chỉ, hoàn thành chương trình học</p>
                            </div>
                        </div>
                    </div>

                    @if(!empty($school->enrollment_quota))
                        <div class="enrollment-quota-box">
                            <i class="fa fa-bolt"></i>
                            <span> <strong>{{ $school->enrollment_quota }}</strong></span>
                        </div>
                    @endif
                </div>
                <div class="enrollment-right">
                    <div class="enrollment-form-card">
                        @php
                            // Sử dụng form_tu_van_mien_phi
                            $formTuVan = $school->form_tu_van_mien_phi ?? [];
                            $formTitle = $formTuVan['title'] ?? 'Đăng Ký Học Trực Tuyến';
                            $formDescription = $formTuVan['description'] ?? 'Hoàn thành thông tin để nhận tư vấn';
                            $formFooter = $formTuVan['footer'] ?? '';
                            $formScript = trim($formTuVan['script'] ?? '');
                        @endphp
                        
                        @if(!empty($formTitle))
                            <h3 class="form-title">{{ $formTitle }}</h3>
                        @endif
                        
                        @if(!empty($formDescription))
                            <p class="form-subtitle">{{ $formDescription }}</p>
                        @endif
                        
                        @if(!empty($formScript))
                            <div class="form-script">
                                <div id="enrollment-form">
                                    {!! $formScript !!}
                                </div>
                            </div>
                        @else
                            {{-- Fallback form nếu không có script --}}
                            <form id="enrollment-form" action="{{ route('contact.save') }}" method="POST">
                                @csrf
                                <input type="hidden" name="type" value="enrollment">
                                <input type="hidden" name="school_id" value="{{ $school->id ?? '' }}">
                                
                                <div class="form-group">
                                    <label for="enrollment-name">Họ và tên <span class="required">*</span></label>
                                    <input type="text" name="name" id="enrollment-name" class="form-control" placeholder="Nhập họ và tên của bạn" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="enrollment-email">Email <span class="required">*</span></label>
                                    <input type="email" name="email" id="enrollment-email" class="form-control" placeholder="example@email.com" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="enrollment-phone">Số điện thoại <span class="required">*</span></label>
                                    <input type="tel" name="phone" id="enrollment-phone" class="form-control" placeholder="0123 456 789" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="enrollment-major">Chọn ngành học quan tâm <span class="required">*</span></label>
                                    <select name="major_id" id="enrollment-major" class="form-control" required>
                                        <option value="">Chọn ngành học</option>
                                        @if(!empty($schoolMajors) && count($schoolMajors) > 0)
                                            @foreach($schoolMajors as $item)
                                                @php
                                                    $majorPivot = $item['majorPivot'];
                                                    $majorName = $majorPivot->name ?? '';
                                                    $majorId = $item['major']->id ?? '';
                                                @endphp
                                                @if(!empty($majorName) && !empty($majorId))
                                                    <option value="{{ $majorId }}">{{ $majorName }}</option>
                                                @endif
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                
                                <button type="submit" class="btn-submit">
                                    <i class="fa fa-paper-plane"></i>
                                    <span>Gửi Đăng Ký</span>
                                </button>
                                
                                <div class="form-privacy">
                                    <i class="fa fa-check-circle"></i>
                                    <span>Thông tin của bạn được bảo mật tuyệt đối.</span>
                                </div>
                            </form>
                        @endif
                        
                        @if(!empty($formFooter))
                            <div class="form-footer">
                                {!! $formFooter !!}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Khối Hình Thức Học --}}
    @if(isset($school) && $school->is_show_study_method == 2)
        @php
            $studyMethod = ($pivot && isset($pivot->study_method)) ? (is_array($pivot->study_method) ? $pivot->study_method : json_decode($pivot->study_method, true)) : [];
            $studyMethodName = $studyMethod['name'] ?? 'Hình Thức Học';
            $studyMethodDescription = $studyMethod['description'] ?? 'Chương trình học linh hoạt, hiện đại – tối ưu cho người đi làm và học viên bận rộn.';
            $studyMethodContent = $studyMethod['content'] ?? '';
            $studyMethodItems = $studyMethod['items'] ?? [];
            $studyMethodImage = $studyMethod['image'] ?? '';
        @endphp
        <div class="panel-school-study-method wow fadeInUp" data-wow-delay="0.9s">
            <div class="uk-container uk-container-center uk-container-1260">
                <div class="study-method-header">
                    <h2 class="study-method-title">{{ $studyMethodName }}</h2>
                    @if(!empty($studyMethodDescription))
                        <p class="study-method-subtitle">{{ $studyMethodDescription }}</p>
                    @endif
                </div>

                @if(!empty($studyMethodItems) && count($studyMethodItems) > 0)
                    <div class="study-method-cards">
                        @foreach($studyMethodItems as $item)
                            @php
                                $itemImage = $item['image'] ?? '';
                                $itemName = $item['name'] ?? '';
                                $itemDescription = $item['description'] ?? '';
                                $studyMethodDelay = (1.0 + ($loop->index * 0.15)) . 's';
                            @endphp
                            <div class="study-method-card wow fadeInUp" data-wow-delay="{{ $studyMethodDelay }}">
                                @if(!empty($itemImage))
                                    <div class="study-method-card-icon">
                                        <span class="image img-cover">
                                            <img src="{{ image($itemImage) }}" alt="{{ $itemName }}">
                                        </span>
                                    </div>
                                @endif
                                <h3 class="study-method-card-title">{{ $itemName }}</h3>
                                @if(!empty($itemDescription))
                                    <p class="study-method-card-description">{{ $itemDescription }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                @if(!empty($studyMethodImage) || !empty($studyMethodContent))
                    <div class="study-method-bottom">
                        @if(!empty($studyMethodImage))
                            <div class="study-method-illustration">
                                <span class="image img-cover">
                                    <img src="{{ image($studyMethodImage) }}" alt="Hình thức học">
                                </span>
                            </div>
                        @endif
                        @if(!empty($studyMethodContent))
                            <div class="study-method-experience">
                                <h3 class="experience-title">Trải Nghiệm Học Online Toàn Diện</h3>
                                <div class="experience-content">
                                    {!! $studyMethodContent !!}
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Khối Giá Trị Văn Bằng --}}
    @if(isset($school) && $school->is_show_value == 2)
        @php
            $value = ($pivot && isset($pivot->value)) ? (is_array($pivot->value) ? $pivot->value : json_decode($pivot->value, true)) : [];
            $valueName = $value['name'] ?? 'Giá Trị Văn Bằng Đại Học Từ Xa NEU';
            $valueDescription = $value['description'] ?? 'Bằng Đại Học Từ Xa - Được Bộ GD&ĐT Công Nhận, Giá Trị Sử Dụng Toàn Quốc';
            $valueItems = $value['items'] ?? [];
            $valueImage = $value['image'] ?? '';
        @endphp
        <div class="panel-school-degree-value wow fadeInUp" data-wow-delay="1.0s">
            <div class="uk-container uk-container-center uk-container-1260">
                <div class="degree-value-content">
                    <div class="degree-value-left">
                        <h2 class="degree-value-title">{{ $valueName }}</h2>
                        @if(!empty($valueDescription))
                            <p class="degree-value-subtitle">{{ $valueDescription }}</p>
                        @endif
                        
                        @if(!empty($valueItems) && count($valueItems) > 0)
                            <div class="degree-value-list">
                                @foreach($valueItems as $item)
                                    @php
                                        $itemIcon = $item['icon'] ?? '';
                                        $itemName = $item['name'] ?? '';
                                        $valueDelay = (1.1 + ($loop->index * 0.15)) . 's';
                                    @endphp
                                    <div class="degree-value-item wow fadeInLeft" data-wow-delay="{{ $valueDelay }}">
                                        @if(!empty($itemIcon))
                                            <div class="degree-value-icon">
                                                <span class="image img-cover">
                                                    <img src="{{ image($itemIcon) }}" alt="{{ $itemName }}">
                                                </span>
                                            </div>
                                        @endif
                                        <span class="degree-value-text">{{ $itemName }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    @if(!empty($valueImage))
                        <div class="degree-value-right">
                            <div class="degree-value-image">
                                <span class="image img-cover">
                                    <img src="{{ image($valueImage) }}" alt="Bằng cấp">
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Khối Không Ghi Hình Thức Đào Tạo --}}
    <div class="panel-school-degree-notice wow fadeInUp" data-wow-delay="1.1s">
        <div class="uk-container uk-container-center uk-container-1260">
            <div class="degree-notice-banner">
                <div class="degree-notice-main-button">
                    <span class="degree-notice-text">KHÔNG GHI HÌNH THỨC ĐÀO TẠO TRÊN BẰNG TỐT NGHIỆP</span>
                    @php
                        $formHocThu = $school->form_hoc_thu ?? [];
                        $formHocThuTitle = $formHocThu['title'] ?? '';
                        $formHocThuScript = trim($formHocThu['script'] ?? '');
                        $hasFormHocThu = !empty($formHocThuScript) || !empty($formHocThuTitle);
                        $modalTarget = $hasFormHocThu ? '#school-hoc-thu-modal' : '#register-modal';
                    @endphp
                    <a href="{{ $modalTarget }}" class="degree-notice-trial-button" data-uk-modal>
                        Học thử miễn phí
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Khối Cảm Nhận Học Viên --}}
    @if(isset($school) && $school->is_show_feedback == 2 && isset($feedback) && !empty($feedback))
        @php
            $feedbackName = $feedback['name'] ?? 'Cảm nhận của học viên về Hệ Từ Xa';
            $feedbackDescription = $feedback['description'] ?? '';
            $feedbackItems = $feedback['items'] ?? [];
        @endphp
        @if(!empty($feedbackItems) && count($feedbackItems) > 0)
            <div class="panel-student-feedback wow fadeInUp" data-wow-delay="0.6s">
                <div class="uk-container uk-container-center">
                    <div class="student-feedback-wrapper">
                        <!-- Header -->
                        <div class="student-feedback-header">
                            <h2 class="student-feedback-title">{{ $feedbackName }}</h2>
                            @if(!empty($feedbackDescription))
                                <div class="student-feedback-description">
                                    {!! $feedbackDescription !!}
                                </div>
                            @endif
                        </div>

                        <!-- Swiper Container -->
                        <div class="student-feedback-slide">
                            <div class="swiper-container feedback-swiper">
                                <div class="swiper-button-next"></div>
                                <div class="swiper-button-prev"></div>
                                <div class="swiper-wrapper">
                                    @foreach($feedbackItems as $item)
                                        @php
                                            $itemName = $item['name'] ?? '';
                                            $itemPosition = $item['position'] ?? '';
                                            $itemDescription = $item['description'] ?? '';
                                            $itemImage = $item['image'] ?? '';
                                            
                                            // Tạo chữ cái đầu từ tên
                                            $initials = '';
                                            if (!empty($itemName)) {
                                                $words = explode(' ', trim($itemName));
                                                if (count($words) > 0) {
                                                    $initials = mb_strtoupper(mb_substr($words[0], 0, 1));
                                                    if (count($words) > 1) {
                                                        $initials .= mb_strtoupper(mb_substr($words[count($words) - 1], 0, 1));
                                                    }
                                                }
                                            }
                                            $hasImage = !empty($itemImage);
                                            $delay = (0.7 + ($loop->index * 0.15)) . 's';
                                        @endphp
                                        <div class="swiper-slide">
                                            <div class="feedback-item wow fadeInUp" data-wow-delay="{{ $delay }}">
                                                <div class="feedback-header-info">
                                                    <div class="feedback-avatar {{ $hasImage ? 'has-image' : 'no-image' }}">
                                                        @if($hasImage)
                                                            <img src="{{ image($itemImage) }}" alt="{{ $itemName }}">
                                                        @else
                                                            <span class="avatar-initials">{{ $initials }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="feedback-name-wrapper">
                                                        <h3 class="feedback-name">{{ $itemName }}</h3>
                                                        @if($itemPosition)
                                                            <p class="feedback-position">{!! $itemPosition !!}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="feedback-rating">
                                                    <div class="star-rating">
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                        <i class="fa fa-star"></i>
                                                    </div>
                                                </div>
                                                <div class="feedback-content">
                                                    <div class="feedback-description">
                                                        {!! $itemDescription !!}
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
            </div>
        @endif
    @endif

    {{-- Khối Sự Kiện Hoạt Động --}}
    @if(isset($school) && $school->is_show_event == 2 && isset($eventPosts) && $eventPosts->isNotEmpty())
        @php
            $eventName = 'Sự Kiện Hoạt Động';
        @endphp
        <div class="panel-news-outstanding wow fadeInUp" data-wow-delay="0.6s">
            <div class="uk-container uk-container-center">
                <div class="news-outstanding-wrapper">
                    <!-- Header -->
                    <div class="news-outstanding-header">
                        <h2 class="news-outstanding-title">{{ $eventName }}</h2>
                    </div>

                    <!-- News Swiper -->
                    <div class="news-outstanding-swiper">
                        <div class="swiper-container event-swiper">
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                            <div class="swiper-wrapper">
                                @foreach($eventPosts as $index => $post)
                                    @php
                                        $postName = $post->name ?? '';
                                        $postDescription = $post->description ?? '';
                                        $postCanonical = $post->canonical ?? '';
                                        $postUrl = $postCanonical ? write_url($postCanonical) : '#';
                                        $postImage = $post->image ?? '';
                                        $hasPostImage = !empty($postImage);
                                        $postImageUrl = $hasPostImage ? (function_exists('thumb') ? thumb($postImage, 400, 300) : asset($postImage)) : '';
                                        
                                        // Lấy ngày tháng
                                        $postDate = $post->created_at ?? now();
                                        $formattedDate = $postDate ? date('d/m/Y', strtotime($postDate)) : '';
                                    @endphp
                                    <div class="swiper-slide">
                                        <div class="news-item wow fadeInUp" data-wow-delay="{{ (0.7 + ($index * 0.15)) . 's' }}">
                                            <a href="{{ $postUrl }}" class="news-image img-cover {{ !$hasPostImage ? 'no-image' : '' }}">
                                                @if($hasPostImage)
                                                    <img src="{{ $postImageUrl }}" alt="{{ $postName }}">
                                                @endif
                                            </a>
                                            <div class="news-content">
                                                <span class="news-category-label">{{ $eventName }}</span>
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
                                                    <a href="{{ $postUrl }}" class="news-detail-link">Xem chi tiết →</a>
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
        </div>
    @endif

    <!-- School Enrollment Modal (from form_script) -->
    <div id="school-enrollment-modal" class="uk-modal school-enrollment-modal">
        <div class="uk-modal-dialog school-enrollment-modal-dialog">
            <a class="uk-modal-close uk-close"></a>
            @php
                // Sử dụng form_tu_van_mien_phi
                $formTuVan = $school->form_tu_van_mien_phi ?? [];
                $formTitle = $formTuVan['title'] ?? 'Đăng Ký Học Trực Tuyến';
                $formDescription = $formTuVan['description'] ?? 'Hoàn thành thông tin để nhận tư vấn';
                $formFooter = $formTuVan['footer'] ?? '';
                $formScript = trim($formTuVan['script'] ?? '');
            @endphp
            
            <!-- Header với màu cam và đỏ -->
            <div class="school-enrollment-header">
                <div class="school-enrollment-description">{{ $formDescription }}</div>
                <h2 class="school-enrollment-title">{{ $formTitle }}</h2>
            </div>
            
            <!-- Wrapper cho script nhúng (khung màu trắng) -->
            <div class="school-enrollment-form-wrapper">
                @if(!empty($formScript))
                    <div class="school-enrollment-script-wrapper">
                        {!! $formScript !!}
                    </div>
                @else
                    {{-- Fallback form nếu không có script --}}
                    <form id="school-enrollment-form" action="{{ route('contact.save') }}" method="POST">
                        @csrf
                        <input type="hidden" name="type" value="enrollment">
                        <input type="hidden" name="school_id" value="{{ $school->id ?? '' }}">
                        
                        <div class="form-group">
                            <label for="enrollment-name-modal">Họ và tên <span class="required">*</span></label>
                            <input type="text" name="name" id="enrollment-name-modal" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="enrollment-email-modal">Email <span class="required">*</span></label>
                            <input type="email" name="email" id="enrollment-email-modal" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="enrollment-phone-modal">Số điện thoại <span class="required">*</span></label>
                            <input type="tel" name="phone" id="enrollment-phone-modal" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="enrollment-address-modal">Địa chỉ</label>
                            <input type="text" name="address" id="enrollment-address-modal">
                        </div>
                        
                        <div class="form-group">
                            <label for="enrollment-message-modal">Lời nhắn</label>
                            <textarea name="message" id="enrollment-message-modal" rows="4"></textarea>
                        </div>
                        
                        <button type="submit" class="btn-submit">Gửi Đăng Ký</button>
                    </form>
                @endif
            </div>
            
            <!-- Footer -->
            @if(!empty($formFooter))
                <div class="school-enrollment-footer">
                    {!! $formFooter !!}
                </div>
            @endif
        </div>
    </div>

    <!-- Register Modal -->
    <div id="register-modal" class="uk-modal">
        <div class="uk-modal-dialog register-modal-dialog">
            <a class="uk-modal-close uk-close"></a>
            <div class="uk-modal-header">
                <h2 class="uk-modal-title">Đăng Ký Tư Vấn</h2>
            </div>
            <form id="register-form" action="{{ route('contact.save') }}" method="POST">
                @csrf
                <div class="uk-modal-body">
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="register-name">Họ và tên <span class="required">*</span></label>
                        <div class="uk-form-controls">
                            <input type="text" name="name" id="register-name" class="uk-width-1-1" required>
                        </div>
                    </div>
                    
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="register-phone">Số điện thoại <span class="required">*</span></label>
                        <div class="uk-form-controls">
                            <input type="tel" name="phone" id="register-phone" class="uk-width-1-1" required>
                        </div>
                    </div>
                    
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="register-email">Email <span class="required">*</span></label>
                        <div class="uk-form-controls">
                            <input type="email" name="email" id="register-email" class="uk-width-1-1" required>
                        </div>
                    </div>
                    
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="register-address">Địa chỉ</label>
                        <div class="uk-form-controls">
                            <input type="text" name="address" id="register-address" class="uk-width-1-1">
                        </div>
                    </div>
                    
                    <div class="uk-form-row">
                        <label class="uk-form-label" for="register-message">Lời nhắn</label>
                        <div class="uk-form-controls">
                            <textarea name="message" id="register-message" class="uk-width-1-1" rows="4"></textarea>
                        </div>
                    </div>
                </div>
                <div class="uk-modal-footer uk-text-right">
                    <button type="button" class="uk-button uk-button-default uk-modal-close">Hủy</button>
                    <button type="submit" class="uk-button uk-button-primary">Gửi</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Form Tư Vấn Miễn Phí --}}
    @php
        // Lấy title, description, footer, script từ form_tu_van_mien_phi
        $formTuVan = $school->form_tu_van_mien_phi ?? [];
        $formTuVanTitle = $formTuVan['title'] ?? 'Tư vấn miễn phí';
        $formTuVanDescription = $formTuVan['description'] ?? '';
        $formTuVanFooter = $formTuVan['footer'] ?? '';
        $formTuVanScript = trim($formTuVan['script'] ?? '');
    @endphp
    
    @if(!empty($formTuVanScript) || !empty($formTuVanTitle))
        <x-form-modal
            modalId="school-consultation-modal"
            modalClass="download-roadmap-modal"
            :title="$formTuVanTitle"
            :description="$formTuVanDescription"
            :script="$formTuVanScript"
            :footer="$formTuVanFooter"
        />
    @endif

    {{-- Modal Form Tải Lộ Trình Học --}}
    @php
        // Lấy title, description, footer, script từ form_tai_lo_trinh_hoc
        $formTaiLoTrinhHoc = $school->form_tai_lo_trinh_hoc ?? [];
        $formTaiLoTrinhHocTitle = $formTaiLoTrinhHoc['title'] ?? 'NHẬN LỘ TRÌNH ĐÀO TẠO CHI TIẾT';
        $formTaiLoTrinhHocDescription = $formTaiLoTrinhHoc['description'] ?? '';
        $formTaiLoTrinhHocFooter = $formTaiLoTrinhHoc['footer'] ?? '';
        $formTaiLoTrinhHocScript = trim($formTaiLoTrinhHoc['script'] ?? '');
    @endphp
    
    @if(!empty($formTaiLoTrinhHocScript) || !empty($formTaiLoTrinhHocTitle))
        <x-form-modal
            modalId="school-tai-lo-trinh-modal"
            modalClass="download-roadmap-modal"
            :title="$formTaiLoTrinhHocTitle"
            :description="$formTaiLoTrinhHocDescription"
            :script="$formTaiLoTrinhHocScript"
            :footer="$formTaiLoTrinhHocFooter"
        />
    @endif

    {{-- Modal Form Học Thử Miễn Phí --}}
    @php
        // Lấy title, description, footer, script từ form_hoc_thu
        $formHocThu = $school->form_hoc_thu ?? [];
        $formHocThuTitle = $formHocThu['title'] ?? 'Học thử miễn phí';
        $formHocThuDescription = $formHocThu['description'] ?? '';
        $formHocThuFooter = $formHocThu['footer'] ?? '';
        $formHocThuScript = trim($formHocThu['script'] ?? '');
    @endphp
    
    @if(!empty($formHocThuScript) || !empty($formHocThuTitle))
        <x-form-modal
            modalId="school-hoc-thu-modal"
            modalClass="download-roadmap-modal"
            :title="$formHocThuTitle"
            :description="$formHocThuDescription"
            :script="$formHocThuScript"
            :footer="$formHocThuFooter"
        />
    @endif
@endsection

@section('script')
<script>
    // Function để decode HTML entities
    function decodeHtmlEntities(str) {
        if (!str) return '';
        const textarea = document.createElement('textarea');
        textarea.innerHTML = str;
        return textarea.value;
    }

    // Function để decode HTML entities
    function decodeHtmlEntities(str) {
        if (!str) return '';
        const textarea = document.createElement('textarea');
        textarea.innerHTML = str;
        return textarea.value;
    }

    // Function đã được định nghĩa inline ngay sau modal, không cần định nghĩa lại
    // Chỉ giữ lại comment này để tham khảo
    if (typeof window.openMajorRoadmapModal === 'undefined') {
        // Fallback nếu inline script chưa load
        window.openMajorRoadmapModal = function(button) {
            alert('Function chưa được load. Vui lòng tải lại trang.');
        };
    }
        console.log('=== openMajorRoadmapModal called ===', button);
        
        try {
            // Lấy và decode HTML entities từ data attributes
            let title = button.getAttribute('data-major-form-title') || 'Tải Lộ Trình Học';
            let description = button.getAttribute('data-major-form-description') || '';
            let script = button.getAttribute('data-major-form-script') || '';
            let footer = button.getAttribute('data-major-form-footer') || '';
            
            console.log('Raw data:', { title, description, script: script.substring(0, 100), footer });
            
            // Decode HTML entities
            title = decodeHtmlEntities(title);
            description = decodeHtmlEntities(description);
            script = decodeHtmlEntities(script);
            footer = decodeHtmlEntities(footer);
            
            console.log('Decoded data:', { title, description, script: script.substring(0, 100), footer });
            
            // Kiểm tra modal có tồn tại không
            const modal = document.getElementById('major-roadmap-modal');
            if (!modal) {
                console.error('Major roadmap modal not found');
                alert('Modal không tìm thấy. Vui lòng tải lại trang.');
                return;
            }
            console.log('Modal found:', modal);
            
            // Cập nhật nội dung modal
            const titleEl = document.getElementById('major-roadmap-title');
            const descEl = document.getElementById('major-roadmap-description');
            const scriptEl = document.getElementById('major-roadmap-script');
            const footerEl = document.getElementById('major-roadmap-footer');
            
            console.log('Elements found:', { titleEl, descEl, scriptEl, footerEl });
            
            if (titleEl) {
                titleEl.textContent = title;
                console.log('Title set:', title);
            } else {
                console.error('major-roadmap-title element not found');
            }
            
            if (descEl) {
                descEl.innerHTML = description;
                console.log('Description set');
            } else {
                console.error('major-roadmap-description element not found');
            }
            
            if (scriptEl) {
                scriptEl.innerHTML = script;
                // Re-execute scripts if any
                const scripts = scriptEl.getElementsByTagName('script');
                for (let i = 0; i < scripts.length; i++) {
                    const newScript = document.createElement('script');
                    newScript.text = scripts[i].text;
                    scriptEl.appendChild(newScript);
                    scriptEl.removeChild(scripts[i]);
                }
                console.log('Script set and executed');
            } else {
                console.error('major-roadmap-script element not found');
            }
            
            if (footerEl) {
                footerEl.innerHTML = footer;
                console.log('Footer set');
            } else {
                console.error('major-roadmap-footer element not found');
            }
            
            // Mở modal bằng UIkit - thử nhiều cách
            console.log('Checking UIkit:', typeof UIkit, UIkit);
            console.log('Checking jQuery:', typeof $, $);
            
            let modalOpened = false;
            
            // Cách 1: UIkit.modal().show()
            if (typeof UIkit !== 'undefined' && UIkit.modal) {
                try {
                    const modalInstance = UIkit.modal('#major-roadmap-modal');
                    console.log('Modal instance:', modalInstance);
                    if (modalInstance) {
                        modalInstance.show();
                        console.log('Modal.show() called via UIkit.modal()');
                        modalOpened = true;
                    }
                } catch (e) {
                    console.error('Error with UIkit.modal().show():', e);
                }
            }
            
            // Cách 2: UIkit.modal.alert() hoặc trigger event
            if (!modalOpened && typeof UIkit !== 'undefined') {
                try {
                    UIkit.util.on('#major-roadmap-modal', 'show', function() {
                        console.log('Modal show event triggered');
                    });
                    // Trigger bằng cách thêm class
                    modal.classList.add('uk-open');
                    document.body.classList.add('uk-modal-page');
                    console.log('Modal opened by adding classes');
                    modalOpened = true;
                } catch (e) {
                    console.error('Error with UIkit event:', e);
                }
            }
            
            // Cách 3: jQuery nếu có
            if (!modalOpened && typeof $ !== 'undefined') {
                try {
                    $('#major-roadmap-modal').modal('show');
                    console.log('Modal.show() called via jQuery');
                    modalOpened = true;
                } catch (e) {
                    console.error('Error with jQuery modal:', e);
                }
            }
            
            // Cách 4: Trigger click vào element có data-uk-modal
            if (!modalOpened) {
                try {
                    // Tạo một element tạm để trigger
                    const tempTrigger = document.createElement('a');
                    tempTrigger.setAttribute('data-uk-modal', '');
                    tempTrigger.setAttribute('href', '#major-roadmap-modal');
                    document.body.appendChild(tempTrigger);
                    tempTrigger.click();
                    document.body.removeChild(tempTrigger);
                    console.log('Modal triggered via data-uk-modal');
                    modalOpened = true;
                } catch (e) {
                    console.error('Error with data-uk-modal trigger:', e);
                }
            }
            
            if (!modalOpened) {
                console.error('All methods failed to open modal');
                alert('Không thể mở modal. Vui lòng kiểm tra console để xem chi tiết lỗi.');
            } else {
                // Kiểm tra lại sau 200ms
                setTimeout(function() {
                    if (modal.classList.contains('uk-open') || modal.style.display !== 'none') {
                        console.log('Modal is open!');
                    } else {
                        console.warn('Modal might not be visible');
                    }
                }, 200);
            }
        } catch (error) {
            console.error('Error opening major roadmap modal:', error);
            console.error('Error stack:', error.stack);
            alert('Có lỗi xảy ra khi mở form: ' + error.message);
        }
    };
    
    console.log('openMajorRoadmapModal function defined:', typeof window.openMajorRoadmapModal);
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if(isset($album) && !empty($album) && count($album) > 0)
        var schoolSwiper = new Swiper('.school-slide-container', {
            loop: {{ count($album) > 1 ? 'true' : 'false' }},
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            autoplay: {{ count($album) > 1 ? '{ delay: 3000, disableOnInteraction: false }' : 'false' }},
            spaceBetween: 0,
            slidesPerView: 1,
        });
    @endif

    // Khởi tạo counter animation cho statistics
    if (typeof HT !== 'undefined' && typeof HT.statisticsCounter === 'function') {
        HT.statisticsCounter();
    }

    // Khởi tạo Swiper cho feedback
    @if(isset($school) && $school->is_show_feedback == 2 && isset($feedback) && !empty($feedback) && isset($feedback['items']) && count($feedback['items']) > 0)
        var feedbackSlideContainer = document.querySelector(".panel-student-feedback .feedback-swiper");
        if (feedbackSlideContainer) {
            var feedbackSlides = feedbackSlideContainer.querySelectorAll('.swiper-slide');
            var feedbackSlideCount = feedbackSlides.length;
            var enableFeedbackLoop = feedbackSlideCount >= 2;
            
            var feedbackSwiper = new Swiper(".panel-student-feedback .feedback-swiper", {
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
    @endif



    // Xử lý form enrollment
    const enrollmentForm = document.getElementById('enrollment-form');
    if (enrollmentForm) {
        enrollmentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(enrollmentForm);
            const submitBtn = enrollmentForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> <span>Đang gửi...</span>';
            
            fetch(enrollmentForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.message === 'success') {
                    alert('Đăng ký thành công! Chúng tôi sẽ liên hệ với bạn sớm nhất.');
                    enrollmentForm.reset();
                } else {
                    alert('Có lỗi xảy ra. Vui lòng thử lại.');
                }
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }

    // Xử lý form đăng ký (fallback form)
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(registerForm);
            const submitBtn = registerForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Đang gửi...';
            
            fetch(registerForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.message === 'success') {
                    if (typeof UIkit !== 'undefined' && UIkit.modal) {
                        UIkit.modal('#register-modal').hide();
                    }
                    alert('Đăng ký thành công! Chúng tôi sẽ liên hệ lại với bạn trong thời gian sớm nhất.');
                    registerForm.reset();
                } else {
                    alert('Có lỗi xảy ra. Vui lòng thử lại.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        });
    }

    // Xử lý form enrollment trong modal (fallback form)
    const schoolEnrollmentForm = document.getElementById('school-enrollment-form');
    if (schoolEnrollmentForm) {
        schoolEnrollmentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(schoolEnrollmentForm);
            const submitBtn = schoolEnrollmentForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Đang gửi...';
            
            fetch(schoolEnrollmentForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.message === 'success') {
                    if (typeof UIkit !== 'undefined' && UIkit.modal) {
                        UIkit.modal('#school-enrollment-modal').hide();
                    }
                    window.location.href = '{{ route("contact.thankyou") }}';
                } else {
                    alert('Có lỗi xảy ra. Vui lòng thử lại.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        });
    }

    // Highlight số trong footer của school enrollment modal (màu cam)
    const schoolEnrollmentFooter = document.querySelector('#school-enrollment-modal .school-enrollment-footer');
    if (schoolEnrollmentFooter) {
        const text = schoolEnrollmentFooter.innerHTML;
        schoolEnrollmentFooter.innerHTML = text.replace(/(\d+)/g, '<span style="color: #FF8C00; font-weight: 700;">$1</span>');
    }

    // Highlight số trong footer của consultation modal (màu cam)
    const consultationFooter = document.querySelector('#school-consultation-modal .download-roadmap-footer');
    if (consultationFooter) {
        const text = consultationFooter.innerHTML;
        consultationFooter.innerHTML = text.replace(/(\d+)/g, '<span style="color: #FF8C00; font-weight: 700;">$1</span>');
    }

    // Highlight số trong footer của tải lộ trình học modal (màu cam)
    const taiLoTrinhFooter = document.querySelector('#school-tai-lo-trinh-modal .download-roadmap-footer');
    if (taiLoTrinhFooter) {
        const text = taiLoTrinhFooter.innerHTML;
        taiLoTrinhFooter.innerHTML = text.replace(/(\d+)/g, '<span style="color: #FF8C00; font-weight: 700;">$1</span>');
    }

    // Highlight số trong footer của học thử miễn phí modal (màu cam)
    const hocThuFooter = document.querySelector('#school-hoc-thu-modal .download-roadmap-footer');
    if (hocThuFooter) {
        const text = hocThuFooter.innerHTML;
        hocThuFooter.innerHTML = text.replace(/(\d+)/g, '<span style="color: #FF8C00; font-weight: 700;">$1</span>');
    }
});
</script>
@endsection


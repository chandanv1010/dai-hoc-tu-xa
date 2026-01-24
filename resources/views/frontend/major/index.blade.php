@extends('frontend.homepage.layout')
@section('content')
    @php
        $pivot = $major->languages->first()->pivot ?? null;
        $name = $pivot->name ?? '';
        $description = $pivot->description ?? '';
        $content = $pivot->content ?? '';
        $canonical = write_url($pivot->canonical ?? '');
        $subtitle = $major->subtitle ?? '';
        
        // Lấy dữ liệu feature từ pivot
        $feature = ($pivot && isset($pivot->feature)) ? (is_array($pivot->feature) ? $pivot->feature : json_decode($pivot->feature, true)) : [];
        
        // Lấy các trường text từ pivot
        $trainingSystem = $pivot->training_system ?? '';
        $studyMethod = $pivot->study_method ?? '';
        $admissionMethod = $pivot->admission_method ?? '';
        $admissionType = $pivot->admission_type ?? '';
        $trainingDuration = $pivot->training_duration ?? '';
        $degreeType = $pivot->degree_type ?? '';
        $enrollmentQuota = $pivot->enrollment_quota ?? '';
        $enrollmentPeriod = $pivot->enrollment_period ?? '';
        // Tổng số tín chỉ tích lũy - lấy từ pivot.admission_method (field trong major_language table)
        // Kiểm tra cả isset() và !empty() để tránh NULL hoặc empty string
        $totalCredits = (isset($pivot->admission_method) && !empty(trim($pivot->admission_method))) ? trim($pivot->admission_method) : '75-131 Tín Chỉ Tùy Trường';
        
        // Lấy dữ liệu JSON từ pivot
        $overview = ($pivot && isset($pivot->overview)) ? (is_array($pivot->overview) ? $pivot->overview : json_decode($pivot->overview, true)) : [];
        $target = ($pivot && isset($pivot->target)) ? (is_array($pivot->target) ? $pivot->target : json_decode($pivot->target, true)) : [];
        $who = ($pivot && isset($pivot->who)) ? (is_array($pivot->who) ? $pivot->who : json_decode($pivot->who, true)) : [];
        $priority = ($pivot && isset($pivot->priority)) ? (is_array($pivot->priority) ? $pivot->priority : json_decode($pivot->priority, true)) : [];
        $learn = ($pivot && isset($pivot->learn)) ? (is_array($pivot->learn) ? $pivot->learn : json_decode($pivot->learn, true)) : [];
        $chance = ($pivot && isset($pivot->chance)) ? (is_array($pivot->chance) ? $pivot->chance : json_decode($pivot->chance, true)) : [];
        $school = ($pivot && isset($pivot->school)) ? (is_array($pivot->school) ? $pivot->school : json_decode($pivot->school, true)) : [];
        $address = ($pivot && isset($pivot->address)) ? (is_array($pivot->address) ? $pivot->address : json_decode($pivot->address, true)) : [];
        $value = ($pivot && isset($pivot->value)) ? (is_array($pivot->value) ? $pivot->value : json_decode($pivot->value, true)) : [];
        
        // Lấy dữ liệu form từ major (chỉ dùng cho modal riêng của major, không override system)
        $formTaiLoTrinh = $major->form_tai_lo_trinh_json ?? null;
        $formTuVan = $major->form_tu_van_mien_phi_json ?? null;
        $formHocThu = $major->form_hoc_thu_json ?? null;
        
        // KHÔNG override system data cho form_tai_lo_trinh vì nút trên header cần dùng dữ liệu từ systems
        // Chỉ override cho form_tu_van_mien_phi vì nó dùng chung modal consultation-modal
        if ($formTuVan && !empty($formTuVan['script'])) {
            $system['form_tu_van_mien_phi_title'] = $formTuVan['title'] ?? $system['form_tu_van_mien_phi_title'] ?? '';
            $system['form_tu_van_mien_phi_description'] = $formTuVan['description'] ?? $system['form_tu_van_mien_phi_description'] ?? '';
            $system['form_tu_van_mien_phi_script'] = $formTuVan['script'] ?? $system['form_tu_van_mien_phi_script'] ?? '';
            $system['form_tu_van_mien_phi_footer'] = $formTuVan['footer'] ?? $system['form_tu_van_mien_phi_footer'] ?? '';
        }
    @endphp

    {{-- Khối Banner Hero --}}
    @if(!empty($major->banner))
        <div class="panel-major-hero">
            <div class="major-hero-background">
                <span class="image img-cover">
                    <img src="{{ image($major->banner) }}" alt="{{ $name }}">
                </span>
                <div class="major-hero-overlay"></div>
            </div>
            <div class="major-hero-content">
                <div class="uk-container uk-container-center">
                    {{-- Badge --}}
                    <div class="major-hero-badge wow fadeInDown" data-wow-delay="0.1s">
                        <i class="fa fa-graduation-cap"></i>
                        <span>Tuyển sinh</span>
                    </div>

                    {{-- Tiêu đề chính --}}
                    <h1 class="major-hero-title wow fadeInUp" data-wow-delay="0.2s">
                        Tuyển Sinh ĐẠI HỌC TỪ XA VĂN BẰNG T2
                    </h1>

                    {{-- Tiêu đề phụ (màu vàng) --}}
                    @if(!empty($subtitle))
                        <h2 class="major-hero-subtitle wow fadeInUp" data-wow-delay="0.3s">
                            {{ $subtitle }}
                        </h2>
                    @endif

                    {{-- Description --}}
                    @if(!empty($description))
                        <p class="major-hero-description wow fadeInUp" data-wow-delay="0.4s">
                            {{ strip_tags($description) }}
                        </p>
                    @endif

                    {{-- Khối tính năng --}}
                    @if(!empty($feature) && count($feature) > 0)
                        <div class="major-hero-features wow fadeInUp" data-wow-delay="0.5s">
                            <div class="uk-grid uk-grid-small" data-uk-grid-match>
                                @foreach(array_slice($feature, 0, 6) as $index => $item)
                                    <div class="uk-width-medium-1-3 uk-width-small-1-2">
                                        <div class="major-feature-card wow fadeInUp" data-wow-delay="{{ (0.5 + ($index * 0.1)) }}s">
                                            @if(!empty($item['image']))
                                                <div class="major-feature-icon">
                                                    <img src="{{ image($item['image']) }}" alt="{{ $item['name'] ?? '' }}">
                                                </div>
                                            @endif
                                            @if(!empty($item['name']))
                                                <div class="major-feature-text">
                                                    {{ $item['name'] }}
                                                </div>
                                            @endif
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

    {{-- Khối Thông Tin Chi Tiết --}}
    <div class="panel-major-detail wow fadeInUp" data-wow-delay="0.1s">
        <div class="uk-container uk-container-center uk-container-1260">
            <div class="uk-grid uk-grid-large" data-uk-grid-match>
                {{-- Cột trái: Nội dung chính --}}
                <div class="uk-width-large-3-4 major-detail-left">
                    <div class="major-detail-content-wrapper">
                        {{-- Header với ảnh và badges --}}
                        <div class="major-detail-header wow fadeInUp" data-wow-delay="0.2s">
                            <div class="major-detail-header-content">
                                @if(!empty($major->image))
                                    <div class="major-detail-image">
                                        <span class="image img-cover">
                                            <img src="{{ image($major->image) }}" alt="{{ $name }}">
                                        </span>
                                    </div>
                                @endif
                                <div class="major-detail-header-text">
                                    <h2 class="major-detail-title">{{ $name }} - Hệ Đào Tạo Từ Xa</h2>
                                </div>
                            </div>
                            <div class="major-detail-badges">
                                <span class="badge-item">Bằng TĐ Chính Quy</span>
                                <span class="badge-item">Công nhận toàn quốc</span>
                                <span class="badge-item">Học 100% online</span>
                            </div>
                        </div>

                        {{-- 4 boxes thông tin --}}
                        <div class="major-info-boxes wow fadeInUp" data-wow-delay="0.3s">
                            <div class="uk-grid uk-grid-small">
                            <div class="uk-width-medium-1-4 uk-width-small-1-2">
                                <div class="info-box">
                                    <div class="info-box-label">Hình thức học</div>
                                    <div class="info-box-value">{{ $studyMethod ?: 'Online 100%' }}</div>
                                </div>
                            </div>
                            <div class="uk-width-medium-1-4 uk-width-small-1-2">
                                <div class="info-box">
                                    <div class="info-box-label">Thời gian đào tạo</div>
                                    <div class="info-box-value">{{ $trainingDuration ?: '1,5 - 4,5 năm' }}</div>
                                </div>
                            </div>
                            <div class="uk-width-medium-1-4 uk-width-small-1-2">
                                <div class="info-box">
                                    <div class="info-box-label">Tổng số tín chỉ</div>
                                    <div class="info-box-value">{{ $totalCredits }}</div>
                                </div>
                            </div>
                            <div class="uk-width-medium-1-4 uk-width-small-1-2">
                                <div class="info-box">
                                    <div class="info-box-label">Bằng cấp</div>
                                    <div class="info-box-value">{{ $degreeType ?: 'Cử nhân' }}</div>
                                </div>
                            </div>
                            </div>
                        </div>

                        {{-- Tổng quan chương trình --}}
                        @if(!empty($overview))
                            @php
                                $overviewName = $overview['name'] ?? 'Tổng quan chương trình';
                                $overviewDescription = $overview['description'] ?? '';
                                $overviewItems = $overview['items'] ?? [];
                            @endphp
                            <div class="major-section major-overview wow fadeInUp" data-wow-delay="0.4s">
                                <h3 class="section-title">Tổng quan chương trình</h3>
                                @if(!empty($content))
                                <div class="major-overview-content">
                                    {!! $content !!}
                                </div>
                                @endif
                            </div>
                        @endif


                        {{-- Đối tượng tuyển sinh, Các Trường Đào Tạo và Nơi tiếp nhận hồ sơ --}}
                        <div class="major-three-columns wow fadeInUp" data-wow-delay="0.6s">
                        <div class="uk-grid uk-grid-medium">
                            {{-- Đối tượng tuyển sinh --}}
                            @if(!empty($who) && is_array($who))
                                @php
                                    $whoItems = isset($who['items']) ? $who['items'] : (is_array($who) && isset($who[0]) ? $who : []);
                                @endphp
                                @if(!empty($whoItems))
                                    <div class="uk-width-medium-1-3">
                                        <div class="major-section major-who">
                                            <h3 class="section-title">Đối tượng tuyển sinh</h3>
                                            <p class="section-subtitle">Từ 18 tuổi trở lên đủ các tiêu chuẩn dưới đây:</p>
                                            <ul class="section-list">
                                                @foreach($whoItems as $item)
                                                    <li class="wow fadeInUp" data-wow-delay="{{ (0.7 + ($loop->index * 0.1)) }}s">
                                                        <i class="fa fa-check"></i>
                                                        <span>{{ is_array($item) ? ($item['text'] ?? $item['name'] ?? '') : $item }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            @endif

                            {{-- Các Trường Đào Tạo --}}
                            @if(isset($major->schools) && $major->schools->count() > 0)
                                <div class="uk-width-medium-1-3">
                                    <div class="major-section major-schools">
                                        <h3 class="section-title">Các Trường Đào Tạo</h3>
                                        <ul class="school-list">
                                            @foreach($major->schools as $school)
                                                @php
                                                    $schoolPivot = $school->languages->first()->pivot ?? null;
                                                    $schoolName = $schoolPivot->name ?? '';
                                                @endphp
                                                @if(!empty($schoolName))
                                                    <li class="wow fadeInUp" data-wow-delay="{{ (0.8 + ($loop->index * 0.1)) }}s">
                                                        <div class="school-icon">
                                                            @if(!empty($school->image))
                                                                <img src="{{ image($school->image) }}" alt="{{ $schoolName }}">
                                                            @endif
                                                        </div>
                                                        <span>{{ $schoolName }}</span>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            {{-- Nơi tiếp nhận hồ sơ --}}
                            @if(!empty($address) && is_array($address))
                                @php
                                    $addressItems = isset($address['items']) ? $address['items'] : (is_array($address) && isset($address[0]) ? $address : []);
                                @endphp
                                @if(!empty($addressItems))
                                    <div class="uk-width-medium-1-3">
                                        <div class="major-section major-address">
                                            <h3 class="section-title">Nơi tiếp nhận hồ sơ</h3>
                                            <ul class="address-list">
                                                @foreach($addressItems as $item)
                                                    <li class="wow fadeInUp" data-wow-delay="{{ (0.8 + ($loop->index * 0.1)) }}s">
                                                        <i class="fa fa-check"></i>
                                                        <div class="address-content">
                                                            <strong>{{ is_array($item) ? ($item['region'] ?? $item['name'] ?? '') : '' }}</strong>
                                                            <span>{{ is_array($item) ? ($item['address'] ?? $item['text'] ?? '') : $item }}</span>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                    </div>
                </div>

                {{-- Cột phải: Sidebar sticky --}}
                <div class="uk-width-large-1-4 major-detail-right">
                    <div class="major-sidebar" id="major-sidebar">
                        {{-- Thông tin ngành học --}}
                        <div class="sidebar-section sidebar-info wow fadeInUp" data-wow-delay="0.3s">
                            <div class="sidebar-header">
                                <h3 class="sidebar-title">{{ strtoupper($name) }}</h3>
                            </div>
                            <div class="sidebar-content">
                                <div class="sidebar-item">
                                    <i class="fa fa-graduation-cap"></i>
                                    <div class="sidebar-item-content">
                                        <span class="sidebar-item-label">Hệ đào tạo</span>
                                        <span class="sidebar-item-value">{{ $trainingSystem ?: 'Từ xa' }}</span>
                                    </div>
                                </div>
                                <div class="sidebar-item">
                                    <i class="fa fa-laptop"></i>
                                    <div class="sidebar-item-content">
                                        <span class="sidebar-item-label">Hình thức học</span>
                                        <span class="sidebar-item-value">{{ $studyMethod ?: 'Trực tuyến' }}</span>
                                    </div>
                                </div>
                                <div class="sidebar-item">
                                    <i class="fa fa-users"></i>
                                    <div class="sidebar-item-content">
                                        <span class="sidebar-item-label">Hình thức tuyển sinh</span>
                                        <span class="sidebar-item-value">{{ $admissionType ?: ($admissionMethod ?: 'Xét tuyển hồ sơ') }}</span>
                                    </div>
                                </div>
                                <div class="sidebar-item">
                                    <i class="fa fa-exclamation-circle"></i>
                                    <div class="sidebar-item-content">
                                        <span class="sidebar-item-label">Chỉ tiêu tuyển sinh</span>
                                        <span class="sidebar-item-value">{{ $enrollmentQuota ?: 'Phụ thuộc từng khoá' }}</span>
                                    </div>
                                </div>
                                <div class="sidebar-item">
                                    <i class="fa fa-calendar"></i>
                                    <div class="sidebar-item-content">
                                        <span class="sidebar-item-label">Thời gian tuyển sinh</span>
                                        <span class="sidebar-item-value">{{ $enrollmentPeriod ?: 'Liên tục tuyển sinh' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Lộ trình chi tiết --}}
                        @if(!empty($major->study_path_file))
                            <div class="sidebar-section sidebar-roadmap wow fadeInUp" data-wow-delay="0.4s">
                                <div class="sidebar-header">
                                    <h3 class="sidebar-title">LỘ TRÌNH CHI TIẾT</h3>
                                </div>
                                <div class="sidebar-content">
                                    <p class="roadmap-description">Tải lộ trình chi tiết ngành Khung chương trình, tiến độ đào tạo, số tín chỉ, học phí...</p>
                                    @php
                                        $formTaiLoTrinh = $major->form_tai_lo_trinh_json ?? null;
                                        $hasFormTaiLoTrinh = $formTaiLoTrinh && !empty($formTaiLoTrinh['script']);
                                    @endphp
                                    @if($hasFormTaiLoTrinh)
                                        <a href="#major-download-roadmap-modal" class="btn-download-roadmap" data-uk-modal>
                                            <i class="fa fa-file-pdf"></i>
                                            <span>Tải xuống lộ trình học</span>
                                        </a>
                                    @elseif(!empty($major->study_path_file))
                                        <a href="{{ asset($major->study_path_file) }}" class="btn-download-roadmap" download>
                                            <i class="fa fa-file-pdf"></i>
                                            <span>Tải xuống lộ trình học</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Toàn Cảnh Ngành --}}
    @if(!empty($overview) && isset($overview['name']))
        @php
            $overviewName = $overview['name'] ?? '';
            $overviewDescription = $overview['description'] ?? '';
            $overviewImage = $overview['image'] ?? '';
            $overviewItems = $overview['items'] ?? [];
        @endphp
        <div class="panel-major-overview wow fadeInUp" data-wow-delay="0.2s">
            <div class="uk-container uk-container-center uk-container-1260">
                <div class="uk-grid uk-grid-large" data-uk-grid-match>
                    {{-- Cột trái: Nội dung --}}
                    <div class="uk-width-large-1-2 major-overview-left">
                        <div class="overview-content">
                            <h2 class="overview-title wow fadeInUp" data-wow-delay="0.3s">{{ $overviewName }}</h2>
                            
                            @if(!empty($overviewDescription))
                                <div class="overview-description wow fadeInUp" data-wow-delay="0.4s">
                                    {!! $overviewDescription !!}
                                </div>
                            @endif

                            @if(!empty($overviewItems) && is_array($overviewItems) && count($overviewItems) > 0)
                                <div class="overview-features">
                                    @foreach($overviewItems as $index => $item)
                                        @php
                                            $itemImage = is_array($item) ? ($item['image'] ?? '') : '';
                                            $itemName = is_array($item) ? ($item['name'] ?? '') : '';
                                            $itemDescription = is_array($item) ? ($item['description'] ?? '') : '';
                                        @endphp
                                        @if(!empty($itemName))
                                            <div class="overview-feature-item wow fadeInUp" data-wow-delay="{{ (0.5 + ($loop->index * 0.1)) }}s">
                                                @if(!empty($itemImage))
                                                    <div class="feature-icon">
                                                        <img src="{{ image($itemImage) }}" alt="{{ $itemName }}">
                                                    </div>
                                                @endif
                                                <div class="feature-content">
                                                    <h4 class="feature-title">{{ $itemName }}</h4>
                                                    @if(!empty($itemDescription))
                                                        <p class="feature-description">{{ $itemDescription }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Cột phải: Ảnh minh họa --}}
                    @if(!empty($overviewImage))
                        <div class="uk-width-large-1-2 major-overview-right">
                            <div class="overview-image-wrapper wow fadeInRight" data-wow-delay="0.4s">
                                <div class="overview-image">
                                    <img src="{{ image($overviewImage) }}" alt="{{ $overviewName }}">
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Ai Phù Hợp Theo Hình Thức Đào Tạo? --}}
    @if(!empty($who) && is_array($who) && count($who) > 0)
        @php
            // Lấy title từ JSON, nếu không có thì dùng giá trị mặc định
            $suitableTitle = isset($who['title']) && !empty($who['title']) ? $who['title'] : 'Ai Phù Hợp Theo Hình Thức Đào Tạo?';
            // Lấy items từ who (có thể là mảng trực tiếp hoặc trong key 'items')
            $whoItems = [];
            if (isset($who['items']) && is_array($who['items'])) {
                $whoItems = $who['items'];
            } elseif (isset($who[0]) && is_array($who[0])) {
                // Fallback: nếu không có 'items', có thể who là mảng trực tiếp các items
                $whoItems = $who;
            }
        @endphp
        <div class="panel-major-suitable wow fadeInUp" data-wow-delay="0.2s">
            <div class="uk-container uk-container-center uk-container-1260">
                <h2 class="suitable-title wow fadeInUp" data-wow-delay="0.3s">{{ $suitableTitle }}</h2>
                <div class="suitable-cards-grid">
                    @foreach($whoItems as $index => $item)
                        @php
                            $itemImage = is_array($item) ? ($item['image'] ?? '') : '';
                            $itemName = is_array($item) ? ($item['name'] ?? '') : '';
                            $itemDescription = is_array($item) ? ($item['description'] ?? '') : '';
                            $itemPerson = is_array($item) ? ($item['person'] ?? '') : '';
                        @endphp
                        @if(!empty($itemName))
                            <div class="suitable-card wow fadeInUp" data-wow-delay="{{ (0.4 + ($loop->index * 0.1)) }}s">
                                @if(!empty($itemImage))
                                    <div class="suitable-card-icon">
                                        <img src="{{ image($itemImage) }}" alt="{{ $itemName }}">
                                    </div>
                                @endif
                                <h3 class="suitable-card-title">{{ $itemName }}</h3>
                                @if(!empty($itemDescription))
                                    <div class="suitable-card-description">
                                        {!! $itemDescription !!}
                                    </div>
                                @endif
                                @if(!empty($itemPerson))
                                    <a href="#register-modal" class="suitable-card-button" data-uk-modal>
                                        {{ $itemPerson }}
                                    </a>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- Ưu điểm --}}
    @if(!empty($priority) && is_array($priority) && count($priority) > 0)
        @php
            // Lấy title từ JSON, nếu không có thì dùng giá trị mặc định
            $priorityTitle = isset($priority['title']) && !empty($priority['title']) ? $priority['title'] : 'Ưu điểm khi học Đại học từ xa Ngành Ngôn Ngữ Anh';
            // Lấy items từ priority (có thể là mảng trực tiếp hoặc trong key 'items')
            $priorityItems = isset($priority['items']) && is_array($priority['items']) ? $priority['items'] : [];
            // Nếu không có 'items', có thể priority là mảng trực tiếp các items
            if (empty($priorityItems) && isset($priority[0])) {
                $priorityItems = $priority;
            }
            $priorityVisible = array_slice($priorityItems, 0, 5);
            $priorityHidden = array_slice($priorityItems, 5);
        @endphp
        <div class="panel-major-advantage wow fadeInUp" data-wow-delay="0.2s">
            <div class="uk-container uk-container-center uk-container-1260">
                <h2 class="advantage-title wow fadeInUp" data-wow-delay="0.3s">{{ $priorityTitle }}</h2>
                <div class="advantage-cards-grid">
                    @foreach($priorityVisible as $index => $item)
                        @php
                            $itemImage = is_array($item) ? ($item['image'] ?? '') : '';
                            $itemName = is_array($item) ? ($item['name'] ?? '') : '';
                            $itemDescription = is_array($item) ? ($item['description'] ?? '') : '';
                        @endphp
                        @if(!empty($itemName))
                            <div class="advantage-card wow fadeInUp" data-wow-delay="{{ (0.4 + ($loop->index * 0.1)) }}s">
                                @if(!empty($itemImage))
                                    <div class="advantage-card-icon">
                                        <img src="{{ image($itemImage) }}" alt="{{ $itemName }}">
                                    </div>
                                @endif
                                <h3 class="advantage-card-title">{{ $itemName }}</h3>
                                @if(!empty($itemDescription))
                                    <p class="advantage-card-description">{{ $itemDescription }}</p>
                                @endif
                            </div>
                        @endif
                    @endforeach

                    @if(count($priorityHidden) > 0)
                        @foreach($priorityHidden as $index => $item)
                            @php
                                $itemImage = is_array($item) ? ($item['image'] ?? '') : '';
                                $itemName = is_array($item) ? ($item['name'] ?? '') : '';
                                $itemDescription = is_array($item) ? ($item['description'] ?? '') : '';
                            @endphp
                            @if(!empty($itemName))
                                <div class="advantage-card advantage-card-hidden wow fadeInUp" data-wow-delay="{{ (0.4 + (($loop->index + 5) * 0.1)) }}s" style="display: none;">
                                    @if(!empty($itemImage))
                                        <div class="advantage-card-icon">
                                            <img src="{{ image($itemImage) }}" alt="{{ $itemName }}">
                                        </div>
                                    @endif
                                    <h3 class="advantage-card-title">{{ $itemName }}</h3>
                                    @if(!empty($itemDescription))
                                        <p class="advantage-card-description">{{ $itemDescription }}</p>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>

                @if(count($priorityHidden) > 0)
                    <div class="advantage-view-more wow fadeInUp" data-wow-delay="0.9s">
                        <a href="javascript:void(0)" class="advantage-view-more-link" id="advantageViewMoreBtn">
                            Xem thêm các ưu điểm của hình thức đào tạo từ xa →
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Banner Tư Vấn --}}
    <div class="panel-major-consultation wow fadeInUp" data-wow-delay="0.2s">
        <div class="uk-container uk-container-center uk-container-1260">
            <div class="consultation-banner">
                <div class="consultation-content wow fadeInUp" data-wow-delay="0.4s">
                    <h2 class="consultation-title">
                        <i class="fa fa-bar-chart consultation-title-icon"></i>
                        Hơn 50.000 học viên đã thay đổi tương lai - Sẵn sàng bứt phá cùng
                        <span class="consultation-title-highlight">hệ Đại học Từ Xa!</span>
                    </h2>
                    @php
                        $formTuVan = $major->form_tu_van_mien_phi_json ?? null;
                        $hasFormTuVan = $formTuVan && !empty($formTuVan['script']);
                    @endphp
                    @if($hasFormTuVan)
                        <div class="consultation-button-wrapper">
                            <a href="#consultation-modal" class="consultation-button" data-uk-modal>
                                <i class="fa fa-headphones"></i>
                                <span>NHẬN TƯ VẤN MIỄN PHÍ</span>
                            </a>
                        </div>
                    @else
                        <div class="consultation-button-wrapper">
                            <a href="#consultation-modal" class="consultation-button" data-uk-modal>
                                <i class="fa fa-headphones"></i>
                                <span>NHẬN TƯ VẤN MIỄN PHÍ</span>
                            </a>
                        </div>
                    @endif
                    <div class="consultation-features">
                        <span class="feature-item">Tư vấn miễn phí</span>
                        <span class="feature-separator">•</span>
                        <span class="feature-item">Hỗ trợ 24/7</span>
                        <span class="feature-separator">•</span>
                        <span class="feature-item">Giải đáp thắc mắc</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Bạn sẽ học được những gì? --}}
    @if(!empty($learn) && is_array($learn) && isset($learn['items']) && is_array($learn['items']) && count($learn['items']) > 0)
        @php
            $learnTitle = isset($learn['title']) && !empty($learn['title']) ? $learn['title'] : 'Bạn sẽ được học những gì?';
            $learnDescription = $learn['description'] ?? '';
            $learnCategories = $learn['items'] ?? [];
        @endphp
        <div class="panel-major-learn wow fadeInUp" data-wow-delay="0.2s">
            <div class="uk-container uk-container-center uk-container-1260">
                <h2 class="learn-title wow fadeInUp" data-wow-delay="0.3s">{{ $learnTitle }}</h2>
                @if(!empty($learnDescription))
                    <p class="learn-subtitle wow fadeInUp" data-wow-delay="0.4s">{{ $learnDescription }}</p>
                @endif

                @if(count($learnCategories) > 0)
                    <ul class="uk-switcher learn-switcher-tabs" data-uk-switcher="{connect:'#learn-switcher-content', animation: 'fade'}">
                        @foreach($learnCategories as $categoryIndex => $category)
                            @php
                                $categoryName = is_array($category) ? ($category['name'] ?? '') : '';
                            @endphp
                            @if(!empty($categoryName))
                                <li>
                                    @if($loop->index == 0)
                                        <i class="fa fa-question-circle"></i>
                                    @elseif($loop->index == 1)
                                        <i class="fa fa-graduation-cap"></i>
                                    @else
                                        <i class="fa fa-star"></i>
                                    @endif
                                    <span>{{ $categoryName }}</span>
                                </li>
                            @endif
                        @endforeach
                    </ul>

                    <ul id="learn-switcher-content" class="uk-switcher learn-switcher-content">
                        @foreach($learnCategories as $categoryIndex => $category)
                            @php
                                $categoryName = is_array($category) ? ($category['name'] ?? '') : '';
                                $categoryItems = is_array($category) && isset($category['items']) ? $category['items'] : [];
                            @endphp
                            @if(!empty($categoryName))
                                <li>
                                    <div class="learn-cards-grid">
                                        @foreach($categoryItems as $itemIndex => $item)
                                            @php
                                                $itemImage = is_array($item) ? ($item['image'] ?? '') : '';
                                                $itemName = is_array($item) ? ($item['name'] ?? '') : '';
                                                $itemDescription = is_array($item) ? ($item['description'] ?? '') : '';
                                            @endphp
                                            @if(!empty($itemName))
                                                <div class="learn-card">
                                                    <div class="learn-card-header">
                                                        @if(!empty($itemImage))
                                                            <div class="learn-card-icon">
                                                                <img src="{{ image($itemImage) }}" alt="{{ $itemName }}">
                                                            </div>
                                                        @endif
                                                        <h3 class="learn-card-title">{{ $itemName }}</h3>
                                                    </div>
                                                    @if(!empty($itemDescription))
                                                        <p class="learn-card-description">{{ $itemDescription }}</p>
                                                    @endif
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    @endif

    {{-- Cơ hội việc làm --}}
    @if(!empty($chance) && is_array($chance) && isset($chance['job']) && is_array($chance['job']) && count($chance['job']) > 0)
        @php
            $chanceName = isset($chance['name']) ? $chance['name'] : 'Cơ hội việc làm sau khi tốt nghiệp';
            $chanceDescription = $chance['description'] ?? '';
            $chanceTags = isset($chance['tags']) && is_array($chance['tags']) ? $chance['tags'] : [];
            $chanceJobs = isset($chance['job']) && is_array($chance['job']) ? $chance['job'] : [];
            $careerImage = $major->career_image ?? '';
        @endphp
        <div class="panel-major-career wow fadeInUp" data-wow-delay="0.2s">
            <div class="uk-container uk-container-center uk-container-1260">
                <h2 class="career-title wow fadeInUp" data-wow-delay="0.3s">{{ $chanceName }}</h2>
                @if(!empty($chanceDescription))
                    <p class="career-subtitle wow fadeInUp" data-wow-delay="0.4s">{{ $chanceDescription }}</p>
                @endif

                @if(count($chanceTags) > 0)
                    <div class="career-tags wow fadeInUp" data-wow-delay="0.5s">
                        @foreach($chanceTags as $tagIndex => $tag)
                            @php
                                $tagIcon = is_array($tag) ? ($tag['icon'] ?? '') : '';
                                $tagName = is_array($tag) ? ($tag['name'] ?? '') : '';
                                $tagColor = is_array($tag) ? ($tag['color'] ?? '#2E7D32') : '#2E7D32';
                                // Map màu hex sang class hoặc dùng inline style
                                $tagColorClass = '';
                                if (strpos($tagColor, '#2E7D32') !== false || strpos($tagColor, '#4CAF50') !== false || strpos($tagColor, '#66BB6A') !== false) {
                                    $tagColorClass = 'career-tag-green';
                                } elseif (strpos($tagColor, '#1976D2') !== false || strpos($tagColor, '#2196F3') !== false || strpos($tagColor, '#42A5F5') !== false) {
                                    $tagColorClass = 'career-tag-blue';
                                } elseif (strpos($tagColor, '#C62828') !== false || strpos($tagColor, '#E31E24') !== false || strpos($tagColor, '#F44336') !== false) {
                                    $tagColorClass = 'career-tag-red';
                                } else {
                                    $tagColorClass = 'career-tag-custom';
                                }
                            @endphp
                            @if(!empty($tagName))
                                <div class="career-tag {{ $tagColorClass }} wow fadeInUp" data-wow-delay="{{ (0.6 + ($loop->index * 0.1)) }}s" @if($tagColorClass == 'career-tag-custom') style="background: {{ $tagColor }}20; color: {{ $tagColor }};" @endif>
                                    @if(!empty($tagIcon))
                                        @if(strpos($tagIcon, '.') !== false || strpos($tagIcon, '/') !== false)
                                            <img src="{{ image($tagIcon) }}" alt="" style="width: 20px; height: 20px; object-fit: contain;">
                                        @else
                                            <i class="{{ $tagIcon }}"></i>
                                        @endif
                                    @endif
                                    <span>{{ $tagName }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif

                <div class="uk-grid" data-uk-grid-match>
                    <div class="uk-width-medium-1-2 uk-width-large-1-2">
                        <div class="career-jobs-grid">
                            @foreach($chanceJobs as $jobIndex => $job)
                                @php
                                    $jobImage = is_array($job) ? ($job['image'] ?? '') : '';
                                    $jobName = is_array($job) ? ($job['name'] ?? '') : '';
                                    $jobDescription = is_array($job) ? ($job['description'] ?? '') : '';
                                    $jobSalary = is_array($job) ? ($job['salary'] ?? '') : '';
                                    $salaryColor = ($loop->index % 2 == 0) ? 'blue' : 'red';
                                @endphp
                                @if(!empty($jobName))
                                    <div class="career-job-card wow fadeInUp" data-wow-delay="{{ (0.7 + ($loop->index * 0.1)) }}s">
                                        <h3 class="career-job-title">{{ $jobName }}</h3>
                                        @if(!empty($jobDescription))
                                            <p class="career-job-description">{{ $jobDescription }}</p>
                                        @endif
                                        @if(!empty($jobSalary))
                                            <p class="career-job-salary career-job-salary-{{ $salaryColor }}">{{ $jobSalary }}</p>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <div class="uk-width-medium-1-2 uk-width-large-1-2">
                        <div class="career-image-wrapper wow fadeInRight" data-wow-delay="0.6s">
                            @if(!empty($careerImage))
                                <div class="career-main-image">
                                    <span class="image img-cover">
                                        <img src="{{ image($careerImage) }}" alt="Cơ hội việc làm">
                                    </span>
                                </div>
                            @endif
                            <div class="career-opportunity-cards">
                                <div class="career-opportunity-card wow fadeInUp" data-wow-delay="0.8s">
                                    <i class="fa fa-line-chart"></i>
                                    <div class="career-opportunity-content">
                                        <h4>Kết nối doanh nghiệp</h4>
                                        <p>Tham gia cộng đồng cựu sinh viên và đối tác tuyển dụng của trường.</p>
                                    </div>
                                </div>
                                <div class="career-opportunity-card wow fadeInUp" data-wow-delay="0.9s">
                                    <i class="fa fa-globe"></i>
                                    <div class="career-opportunity-content">
                                        <h4>Cơ hội quốc tế</h4>
                                        <p>Làm việc tại các tập đoàn đa quốc gia</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Chọn Trường Phù Hợp --}}
    @if(isset($major->schools) && $major->schools->count() > 0)
        @php
            // Lấy school description từ JSON trong pivot (major_language.school->description)
            // Đây là nơi admin nhập trong form major edit
            // Đảm bảo lấy lại $school từ pivot để tránh scope issue
            $schoolData = ($pivot && isset($pivot->school)) ? (is_array($pivot->school) ? $pivot->school : json_decode($pivot->school, true)) : [];
            
            $schoolDescription = '';
            if (is_array($schoolData) && isset($schoolData['description']) && !empty(trim($schoolData['description']))) {
                $schoolDescription = trim($schoolData['description']);
            } else {
                // Default: Mô tả động dựa trên tên ngành
                $majorName = $name ?? 'ngành học';
                $schoolDescription = "Mỗi trường đào tạo ngành {$majorName} hệ từ xa có thể mạnh riêng về hình thức xét tuyển, hỗ trợ sinh viên và mức học phí. Hãy tham khảo nhanh ưu điểm của từng trường để đưa ra lựa chọn phù hợp nhất.";
            }
            $schoolImage = isset($schoolData) && isset($schoolData['image']) ? $schoolData['image'] : '';
            $schoolNote = isset($schoolData) && isset($schoolData['note']) ? $schoolData['note'] : 'Lưu ý: Thông tin chi tiết về học phí và địa điểm thi có thể thay đổi theo từng năm. Vui lòng liên hệ trực tiếp với trường để được tư vấn cụ thể.';
        @endphp
        <div class="panel-major-choose-school wow fadeInUp" data-wow-delay="0.2s">
            <div class="choose-school-background">
                <div class="choose-school-overlay"></div>
            </div>
            <div class="choose-school-content">
                <div class="uk-container uk-container-center uk-container-1260">
                    <h2 class="choose-school-title wow fadeInUp" data-wow-delay="0.3s">Chọn Trường Phù Hợp Với Lộ Trình Của Bạn</h2>
                    <p class="choose-school-subtitle wow fadeInUp" data-wow-delay="0.4s">{{ $schoolDescription }}</p>

                    {{-- Khối 1: Swiper Slide --}}
                    <div class="schools-carousel-wrapper wow fadeInUp" data-wow-delay="0.5s">
                        <div class="swiper-container schools-swiper">
                            <div class="swiper-wrapper">
                                @foreach($major->schools as $schoolItem)
                                    @php
                                        $schoolPivot = $schoolItem->languages->first()->pivot ?? null;
                                        $schoolName = $schoolPivot->name ?? '';
                                        $schoolShortName = $schoolItem->short_name ?? '';
                                        $schoolCanonical = $schoolPivot->canonical ?? '';
                                        $schoolUrl = $schoolCanonical ? write_url($schoolCanonical) : '#';
                                        // Lấy dữ liệu từ school_language.majors (JSON) - là mảng trực tiếp
                                        $schoolMajors = is_array($schoolPivot->majors ?? null) ? $schoolPivot->majors : (is_string($schoolPivot->majors ?? null) ? json_decode($schoolPivot->majors, true) : []);
                                        // Tìm major hiện tại trong danh sách majors của school
                                        $currentMajorData = null;
                                        if (is_array($schoolMajors) && count($schoolMajors) > 0) {
                                            foreach ($schoolMajors as $majorItem) {
                                                if (isset($majorItem['major_id']) && $majorItem['major_id'] == $major->id) {
                                                    $currentMajorData = $majorItem;
                                                    break;
                                                }
                                            }
                                        }
                                    @endphp
                                    @if(!empty($schoolName) && !empty($currentMajorData))
                                        <div class="swiper-slide">
                                            <div class="school-card">
                                                <div class="school-card-header">
                                                    <div class="school-logo">
                                                        @if(!empty($schoolItem->image))
                                                            <img src="{{ image($schoolItem->image) }}" alt="{{ $schoolName }}">
                                                        @endif
                                                    </div>
                                                    <div class="school-header-text">
                                                        <h3 class="school-name">{{ $schoolName }}</h3>
                                                        <p class="school-slogan">Đào tạo từ xa - LMS hiện đại</p>
                                                    </div>
                                                    @if(!empty($schoolShortName))
                                                        <div class="school-badge">{{ $schoolShortName }}</div>
                                                    @endif
                                                </div>
                                                <div class="school-details">
                                                    @if(isset($currentMajorData['admission_method']) && !empty($currentMajorData['admission_method']))
                                                        <div class="school-detail-item">
                                                            <strong>Hình thức xét tuyển:</strong> {{ $currentMajorData['admission_method'] }}
                                                        </div>
                                                    @endif
                                                    @if(isset($currentMajorData['duration']) && !empty($currentMajorData['duration']))
                                                        <div class="school-detail-item">
                                                            <strong>Thời gian học:</strong> {{ $currentMajorData['duration'] }}
                                                        </div>
                                                    @endif
                                                    @if(isset($currentMajorData['tuition_per_credit']) && !empty($currentMajorData['tuition_per_credit']))
                                                        <div class="school-detail-item">
                                                            <strong>Học phí dự kiến:</strong> {{ $currentMajorData['tuition_per_credit'] }}
                                                        </div>
                                                    @endif
                                                    @if(isset($currentMajorData['location']) && !empty($currentMajorData['location']))
                                                        <div class="school-detail-item">
                                                            <strong>Địa điểm thi:</strong> {{ $currentMajorData['location'] }}
                                                        </div>
                                                    @endif
                                                    @if(isset($currentMajorData['annual_tuition']) && !empty($currentMajorData['annual_tuition']))
                                                        <div class="school-detail-item school-tuition-highlight">
                                                            <strong>Học phí:</strong> {{ $currentMajorData['annual_tuition'] }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <a href="{{ $schoolUrl }}" class="school-button">
                                                    Nhận thông tin lộ trình học
                                                </a>
                                                <p class="school-additional-info">Sở hữu hệ thống học tập trực tuyến cập nhật, tài liệu chuẩn quốc tế, lịch khai giảng liên tục trong năm.</p>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            <div class="swiper-button-prev"></div>
                            <div class="swiper-button-next"></div>
                        </div>
                    </div>

                    {{-- Khối 2: Bảng so sánh --}}
                    <div class="schools-comparison-table wow fadeInUp" data-wow-delay="0.6s">
                        <table class="uk-table">
                            <thead>
                                <tr>
                                    <th>TRƯỜNG</th>
                                    <th>ĐỐI TƯỢNG TUYỂN SINH</th>
                                    <th>THỜI GIAN ĐÀO TẠO</th>
                                    <th>SỐ TÍN CHỈ</th>
                                    <th>HỌC PHÍ / 1 TÍN CHỈ</th>
                                    <th>ĐỊA ĐIỂM THI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($major->schools as $schoolItem)
                                    @php
                                        $schoolPivot = $schoolItem->languages->first()->pivot ?? null;
                                        $schoolName = $schoolPivot->name ?? '';
                                        $schoolCanonical = $schoolPivot->canonical ?? '';
                                        $schoolUrl = $schoolCanonical ? write_url($schoolCanonical) : '#';
                                        // Lấy dữ liệu từ school_language.majors (JSON) - là mảng trực tiếp
                                        $schoolMajors = is_array($schoolPivot->majors ?? null) ? $schoolPivot->majors : (is_string($schoolPivot->majors ?? null) ? json_decode($schoolPivot->majors, true) : []);
                                        // Tìm major hiện tại trong danh sách majors của school
                                        $currentMajorData = null;
                                        if (is_array($schoolMajors) && count($schoolMajors) > 0) {
                                            foreach ($schoolMajors as $majorItem) {
                                                if (isset($majorItem['major_id']) && $majorItem['major_id'] == $major->id) {
                                                    $currentMajorData = $majorItem;
                                                    break;
                                                }
                                            }
                                        }
                                    @endphp
                                    @if(!empty($schoolName) && !empty($currentMajorData))
                                        <tr>
                                            <td><strong><a href="{{ $schoolUrl }}" class="school-name-link">{{ $schoolName }}</a></strong></td>
                                            <td>{{ $currentMajorData['admission_method'] ?? '' }}</td>
                                            <td>{{ $currentMajorData['duration'] ?? '' }}</td>
                                            <td>{{ $currentMajorData['credits'] ?? '' }}</td>
                                            <td>{{ $currentMajorData['tuition_per_credit'] ?? '' }}</td>
                                            <td>{{ $currentMajorData['location'] ?? '' }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Lưu ý --}}
                    <div class="school-note wow fadeInUp" data-wow-delay="0.7s">
                        <i class="fa fa-lightbulb-o"></i>
                        <span>{{ $schoolNote }}</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Khối Giá Trị Văn Bằng --}}
    @if(isset($major) && $major->is_show_value == 2 && isset($value) && !empty($value))
        @php
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

    {{-- Khối Banner Học Thử --}}
    <div class="panel-major-trial-banner wow fadeInUp" data-wow-delay="1.1s">
        <div class="uk-container uk-container-center">
            <div class="trial-banner-content">
                <h2 class="trial-banner-title">KHÔNG GHI HÌNH THỨC ĐÀO TẠO TRÊN BẰNG TỐT NGHIỆP</h2>
                @php
                    $formHocThu = $major->form_hoc_thu_json ?? null;
                    $hasFormHocThu = $formHocThu && !empty($formHocThu['script']);
                @endphp
                @if($hasFormHocThu)
                    <button type="button" class="trial-banner-button" onclick="UIkit.modal('#major-form-hoc-thu-modal').show();">
                        Học thử miễn phí
                    </button>
                @else
                    <button type="button" class="trial-banner-button" onclick="UIkit.modal('#register-modal').show();">
                        Học thử miễn phí
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- Khối Cảm Nhận Học Viên --}}
    @if(isset($major) && $major->is_show_feedback == 2 && isset($feedback) && !empty($feedback))
        @php
            $feedbackName = $feedback['name'] ?? 'Cảm nhận của học viên về Hệ Từ Xa';
            $feedbackDescription = $feedback['description'] ?? '';
            $feedbackItems = $feedback['items'] ?? [];
        @endphp
        @if(!empty($feedbackItems) && count($feedbackItems) > 0)
            <div class="panel-student-feedback wow fadeInUp" data-wow-delay="1.2s">
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
                                            $delay = (1.3 + ($loop->index * 0.15)) . 's';
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
    @if(isset($major) && $major->is_show_event == 2 && isset($eventPosts) && $eventPosts->isNotEmpty())
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


    <!-- Register Modal -->
    <div id="register-modal" class="uk-modal">
        <div class="uk-modal-dialog register-modal-dialog">
            <a class="uk-modal-close uk-close"></a>
            <div class="register-form-wrapper">
                <h3 class="form-title">Đăng Ký Học Trực Tuyến</h3>
                <p class="form-subtitle">Hoàn thành thông tin để nhận tư vấn</p>
                
                <form id="register-form" action="{{ route('contact.save') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="enrollment">
                    <input type="hidden" name="major_id" value="{{ $major->id ?? '' }}">
                    
                    <div class="form-group">
                        <label for="register-name">Họ và tên <span class="required">*</span></label>
                        <input type="text" name="name" id="register-name" class="form-control" placeholder="Nhập họ và tên của bạn" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="register-email">Email</label>
                        <input type="email" name="email" id="register-email" class="form-control" placeholder="example@email.com">
                    </div>
                    
                    <div class="form-group">
                        <label for="register-phone">Số điện thoại <span class="required">*</span></label>
                        <input type="tel" name="phone" id="register-phone" class="form-control" placeholder="0123 456 789" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="register-major">Chọn ngành học quan tâm <span class="required">*</span></label>
                        <select name="major_id" id="register-major" class="form-control" required>
                            <option value="">Chọn ngành học</option>
                            @if(isset($allMajors) && !empty($allMajors) && count($allMajors) > 0)
                                @foreach($allMajors as $item)
                                    @php
                                        $majorPivot = $item->languages->first()->pivot ?? null;
                                        $majorName = $majorPivot->name ?? '';
                                        $majorId = $item->id ?? '';
                                    @endphp
                                    @if(!empty($majorName) && !empty($majorId))
                                        <option value="{{ $majorId }}" {{ (isset($major->id) && $majorId == $major->id) ? 'selected' : '' }}>{{ $majorName }}</option>
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
            </div>
        </div>
    </div>

    {{-- Modal riêng cho Major: Tải Lộ Trình Học (dùng dữ liệu từ major) --}}
    @if($formTaiLoTrinh && !empty($formTaiLoTrinh['script']))
        @php
            $formTaiLoTrinhTitle = $formTaiLoTrinh['title'] ?? 'Tải Lộ Trình Học';
            $formTaiLoTrinhDescription = $formTaiLoTrinh['description'] ?? '';
            $formTaiLoTrinhScript = $formTaiLoTrinh['script'] ?? '';
            $formTaiLoTrinhFooter = $formTaiLoTrinh['footer'] ?? '';
        @endphp
        <x-form-modal
            :title="$formTaiLoTrinhTitle"
            :description="$formTaiLoTrinhDescription"
            :script="$formTaiLoTrinhScript"
            :footer="$formTaiLoTrinhFooter"
            modal-id="major-download-roadmap-modal"
            modal-class="download-roadmap-modal"
        />
    @endif
    
    {{-- Override modal consultation-modal với dữ liệu từ major (chỉ khi có dữ liệu từ major) --}}
    @if($formTuVan && !empty($formTuVan['script']))
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Override modal consultation-modal với dữ liệu từ major
            const consultationModal = document.getElementById('consultation-modal');
            if (consultationModal) {
                const titleEl = consultationModal.querySelector('.download-roadmap-title');
                const descEl = consultationModal.querySelector('.download-roadmap-description');
                const scriptEl = consultationModal.querySelector('.download-roadmap-script-wrapper');
                const footerEl = consultationModal.querySelector('.download-roadmap-footer');
                
                // Lưu dữ liệu form từ major
                const formData = {
                    title: {!! json_encode($formTuVan['title'] ?? '') !!},
                    description: {!! json_encode($formTuVan['description'] ?? '') !!},
                    script: {!! json_encode($formTuVan['script'] ?? '') !!},
                    footer: {!! json_encode($formTuVan['footer'] ?? '') !!}
                };
                
                // Function để update modal content và re-execute scripts
                function updateConsultationModal() {
                    if (titleEl) titleEl.textContent = formData.title;
                    if (descEl) descEl.textContent = formData.description;
                    
                    if (footerEl) footerEl.innerHTML = formData.footer;
                    
                    // Clear và inject script
                    if (scriptEl) {
                        scriptEl.innerHTML = formData.script;
                        
                        // Re-execute all script tags
                        const scripts = scriptEl.getElementsByTagName('script');
                        const scriptsArray = Array.from(scripts);
                        
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
                            
                            // Append to body to execute
                            document.body.appendChild(newScript);
                            oldScript.parentNode.removeChild(oldScript);
                        });
                    }
                }
                
                // Update ngay khi DOM ready
                updateConsultationModal();
                
                // Listen for modal show event để update lại khi modal được mở
                if (typeof UIkit !== 'undefined' && UIkit.util) {
                    UIkit.util.on('#consultation-modal', 'show', function() {
                        updateConsultationModal();
                    });
                }
            }
        });
        </script>
    @endif
    
    {{-- Component mới cho Học Thử Miễn Phí (chỉ tạo khi có dữ liệu từ major) - dùng cùng style với các popup khác --}}
    @php
        $formHocThu = $major->form_hoc_thu_json ?? null;
    @endphp
    @if($formHocThu && !empty($formHocThu['script']))
        @php
            $formHocThuTitle = $formHocThu['title'] ?? 'Học Thử Miễn Phí';
            $formHocThuDescription = $formHocThu['description'] ?? '';
            $formHocThuScript = $formHocThu['script'] ?? '';
            $formHocThuFooter = $formHocThu['footer'] ?? '';
        @endphp
        <x-form-modal
            :title="$formHocThuTitle"
            :description="$formHocThuDescription"
            :script="$formHocThuScript"
            :footer="$formHocThuFooter"
            modal-id="major-form-hoc-thu-modal"
            modal-class="download-roadmap-modal"
        />
    @endif

@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form submission
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalHtml = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> <span>Đang gửi...</span>';
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status || data.flag) {
                    alert(data.messages || 'Gửi yêu cầu thành công! Chúng tôi sẽ liên hệ lại trong thời gian sớm nhất.');
                    registerForm.reset();
                    if (typeof UIkit !== 'undefined' && UIkit.modal) {
                        UIkit.modal('#register-modal').hide();
                    }
                } else {
                    let errorMsg = data.messages || 'Có lỗi xảy ra, vui lòng thử lại!';
                    if (typeof data.messages === 'object') {
                        errorMsg = Object.values(data.messages).join('\n');
                    }
                    alert(errorMsg);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra, vui lòng thử lại!');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
            });
        });
    }

    // Sticky sidebar
    const sidebar = document.getElementById('major-sidebar');
    const leftColumn = document.querySelector('.major-detail-left');
    
    if (sidebar && leftColumn && window.innerWidth > 960) {
        const sidebarTop = sidebar.offsetTop;
        const leftColumnHeight = leftColumn.offsetHeight;
        const sidebarHeight = sidebar.offsetHeight;
        
        function handleScroll() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const leftColumnBottom = leftColumn.offsetTop + leftColumnHeight;
            const sidebarBottom = sidebarTop + sidebarHeight;
            
            if (scrollTop + 100 >= sidebarTop) {
                if (scrollTop + sidebarHeight + 100 <= leftColumnBottom) {
                    sidebar.style.position = 'fixed';
                    sidebar.style.top = '100px';
                } else {
                    sidebar.style.position = 'absolute';
                    sidebar.style.top = (leftColumnHeight - sidebarHeight) + 'px';
                }
            } else {
                sidebar.style.position = 'sticky';
                sidebar.style.top = '100px';
            }
        }
        
        window.addEventListener('scroll', handleScroll);
        window.addEventListener('resize', handleScroll);
        handleScroll();
    }

    // Xử lý nút "Xem thêm" cho sự kiện
    const showMoreEventsBtn = document.getElementById('show-more-events-btn');
    if (showMoreEventsBtn) {
        showMoreEventsBtn.addEventListener('click', function() {
            const hiddenItems = document.querySelectorAll('.event-item-hidden');
            hiddenItems.forEach(function(item) {
                item.style.display = '';
                item.classList.remove('event-item-hidden');
            });
            
            // Ẩn nút "Xem thêm" sau khi hiển thị tất cả
            showMoreEventsBtn.style.display = 'none';
        });
    }

    // Khởi tạo Swiper cho event (nếu có)
    @if(isset($major) && isset($major->is_show_event) && $major->is_show_event == 2 && !empty($eventPosts) && $eventPosts->count() > 0)
        var eventSlideContainer = document.querySelector(".panel-news-outstanding .event-swiper");
        if (eventSlideContainer) {
            var eventSlides = eventSlideContainer.querySelectorAll('.swiper-slide');
            var eventSlideCount = eventSlides.length;
            var enableEventLoop = eventSlideCount >= 2;

            var eventSwiper = new Swiper(".panel-news-outstanding .event-swiper", {
                loop: enableEventLoop,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                autoplay: enableEventLoop ? {
                    delay: 4000,
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

    // Khởi tạo Swiper cho feedback (nếu có)
    @if(isset($major) && $major->is_show_feedback == 2 && isset($feedback) && !empty($feedback) && isset($feedback['items']) && count($feedback['items']) > 0)
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

    // Xử lý switcher tabs để hiển thị content ngay khi click
    $(document).on('click', '.learn-switcher-tabs li', function() {
        var $switcher = UIkit.switcher($(this).closest('.learn-switcher-tabs'));
        var activeIndex = $(this).index();
        
        // Đợi animation fade hoàn thành rồi hiển thị content ngay
        setTimeout(function() {
            var $activeContent = $('#learn-switcher-content li').eq(activeIndex);
            if ($activeContent.length) {
                // Hiển thị ngay lập tức, không cần animation
                $activeContent.find('.learn-card').each(function() {
                    var $card = $(this);
                    $card.css({
                        'visibility': 'visible',
                        'opacity': '1',
                        'animation': 'none'
                    });
                    // Remove wow classes để không bị ảnh hưởng
                    $card.removeClass('wow fadeInUp fadeInDown fadeInLeft fadeInRight');
                });
            }
        }, 100);
    });
});
</script>
@endsection


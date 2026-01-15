@extends('frontend.homepage.layout')
@section('content')
    @php
        // Lấy thông tin catalogue
        $catalogueName = $postCatalogue->languages->first()->pivot->name ?? '';
        $catalogueDescription = $postCatalogue->languages->first()->pivot->description ?? '';
    @endphp

    <!-- Breadcrumb Section -->
    <div class="page-breadcrumb-large" style="margin-bottom: 25px;">
        <div class="breadcrumb-overlay"></div>
        <div class="uk-container uk-container-center">
            <div class="breadcrumb-content">
                <h1 class="breadcrumb-title">{{ $catalogueName }}</h1>
                @if($catalogueDescription)
                    <div class="breadcrumb-description">
                        {!! $catalogueDescription !!}
                    </div>
                @endif
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

    <!-- Post Catalogue Content -->
    <div class="panel-post-catalogue">
        <div class="uk-container uk-container-center">
            @if($postCatalogue->canonical === 'tin-tuc-noi-bat')
                <!-- Layout 3/4 - 1/4 cho trang tin tức nổi bật -->
                <div class="uk-grid uk-grid-medium" data-uk-grid-match>
                    <!-- Cột trái: 3/4 -->
                    <div class="uk-width-medium-3-4">
                        <div class="post-catalogue-wrapper">
                            @if($posts->isNotEmpty())
                                <!-- Posts Grid -->
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
                                            <div class="uk-width-medium-1-2 uk-width-large-1-2">
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

                                <!-- Pagination -->
                                <div class="post-catalogue-pagination">
                                    @include('frontend.component.pagination', ['model' => $posts])
                                </div>
                            @else
                                <div class="post-catalogue-empty">
                                    <p>Chưa có bài viết nào trong danh mục này.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Cột phải: 1/4 - Sidebar -->
                    <div class="uk-width-medium-1-4">
                        <div class="post-catalogue-sidebar">
                            <!-- 1. Form đăng ký -->
                            <div class="sidebar-register-form wow fadeInUp">
                                <h3 class="sidebar-title">Đăng ký tư vấn</h3>
                                <form id="sidebar-register-form" class="register-form">
                                    @csrf
                                    <div class="form-group">
                                        <input type="text" name="name" class="form-control" placeholder="Họ và tên *" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="tel" name="phone" class="form-control" placeholder="Số điện thoại *" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="email" name="email" class="form-control" placeholder="Email">
                                    </div>
                                    <div class="form-group">
                                        <textarea name="message" class="form-control" rows="3" placeholder="Ghi chú"></textarea>
                                    </div>
                                    <input type="hidden" name="type" value="sidebar_register">
                                    <button type="submit" class="btn-submit">Gửi đăng ký</button>
                                </form>
                            </div>

                            <!-- 2. Danh sách các trường đào tạo -->
                            @if(isset($schools) && $schools->isNotEmpty())
                                <div class="sidebar-schools-list wow fadeInUp">
                                    <h3 class="sidebar-title">Các trường đào tạo</h3>
                                    <div class="schools-list">
                                        @foreach($schools as $school)
                                            @php
                                                $schoolLanguage = $school->languages->first() ?? null;
                                                $schoolName = '';
                                                $schoolCanonical = '';
                                                $schoolCode = $school->short_name ?? '';
                                                
                                                if ($schoolLanguage) {
                                                    $pivot = $schoolLanguage->pivot ?? null;
                                                    if ($pivot) {
                                                        $schoolName = $pivot->name ?? '';
                                                        $schoolCanonical = $pivot->canonical ?? '';
                                                    }
                                                }
                                                
                                                $schoolImage = $school->image ?? '';
                                                $schoolImageUrl = $schoolImage ? asset($schoolImage) : asset('frontend/resources/img/school-default.png');
                                                $schoolUrl = $schoolCanonical ? write_url($schoolCanonical) : '#';
                                            @endphp
                                            <div class="school-item">
                                                <a href="{{ $schoolUrl }}" class="school-link">
                                                    <div class="school-logo">
                                                        <img src="{{ $schoolImageUrl }}" alt="{{ $schoolName }}">
                                                    </div>
                                                    <div class="school-info">
                                                        <div class="school-name">{{ $schoolName }}</div>
                                                        @if($schoolCode)
                                                            <div class="school-code">Mã: {{ $schoolCode }}</div>
                                                        @endif
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- 3. Khối tải lộ trình học -->
                            @php
                                $downloadFile = $system['homepage_download'] ?? null;
                                $downloadUrl = $downloadFile ? asset($downloadFile) : '#';
                            @endphp
                            <div class="sidebar-download-roadmap wow fadeInUp">
                                <h3 class="sidebar-title">HỖ TRỢ ONLINE</h3>
                                <div class="download-roadmap-content">
                                    <p class="download-description">
                                        Tải lộ trình đào tạo chi tiết. Bao gồm khung chương trình, tiến độ đào tạo, số lượng tín chỉ từng môn học, học phí,...
                                    </p>
                                    <a href="#download-roadmap-modal" class="download-roadmap-banner pdf-icon-box" data-uk-modal>
                                        <i class="fa fa-file-pdf"></i>
                                        <span>Tải xuống lộ trình học</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Layout mặc định cho các trang khác -->
                <div class="post-catalogue-wrapper">
                    @if($posts->isNotEmpty())
                        <!-- Posts Grid -->
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

                        <!-- Pagination -->
                        <div class="post-catalogue-pagination">
                            @include('frontend.component.pagination', ['model' => $posts])
                        </div>
                    @else
                        <div class="post-catalogue-empty">
                            <p>Chưa có bài viết nào trong danh mục này.</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    @if($postCatalogue->canonical === 'tin-tuc-noi-bat')
    <script>
        $(document).ready(function() {
            $('#sidebar-register-form').on('submit', function(e) {
                e.preventDefault();
                
                var form = $(this);
                var formData = form.serialize();
                var submitBtn = form.find('button[type="submit"]');
                var originalText = submitBtn.text();
                
                submitBtn.prop('disabled', true).text('Đang gửi...');
                
                $.ajax({
                    url: '{{ route("contact.save") }}',
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.message === 'success') {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            } else {
                                window.location.href = '{{ route("contact.thankyou") }}';
                            }
                        } else {
                            alert('Có lỗi xảy ra. Vui lòng thử lại.');
                        }
                    },
                    error: function(xhr) {
                        alert('Có lỗi xảy ra. Vui lòng thử lại.');
                        console.error(xhr);
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).text(originalText);
                    }
                });
            });
        });
    </script>
    @endif
@endsection

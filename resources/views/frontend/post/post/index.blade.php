@extends('frontend.homepage.layout')
@section('content')
    @php
        // Lấy thông tin post
        $postPivot = $post->languages->first()->pivot;
        $postName = $postPivot->name ?? '';
        $postDescription = $postPivot->description ?? '';
        $postContent = $contentWithToc ?? '';
        
        // Lấy thông tin danh mục
        $cataloguePivot = $postCatalogue->languages->first()->pivot ?? null;
        $catalogueName = $cataloguePivot->name ?? '';
    @endphp

    <!-- Breadcrumb Section -->
    <div class="page-breadcrumb-large">
        <div class="breadcrumb-overlay"></div>
        <div class="uk-container uk-container-center">
            <div class="breadcrumb-content">
                <h1 class="breadcrumb-title">{{ $catalogueName }}</h1>
                <nav class="breadcrumb-nav">
                    <ul class="breadcrumb-list">
                        <li><a href="{{ config('app.url') }}">Trang chủ</a></li>
                        @if(!is_null($breadcrumb))
                            @foreach($breadcrumb as $key => $val)
                                @php
                                    $breadcrumbName = $val->languages->first()->pivot->name ?? '';
                                    $breadcrumbCanonical = $val->languages->first()->pivot->canonical ?? '';
                                    $breadcrumbUrl = $breadcrumbCanonical ? write_url($breadcrumbCanonical) : '#';
                                @endphp
                                <li>
                                    <span class="breadcrumb-separator">/</span>
                                    <a href="{{ $breadcrumbUrl }}">{{ $breadcrumbName }}</a>
                                </li>
                            @endforeach
                        @endif
                        <li>
                            <span class="breadcrumb-separator">/</span>
                            <span>{{ $postName }}</span>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Post Detail Content -->
    <div class="panel-post-detail {{ $postPivot->canonical === 'lich-khai-giang-du-kien' ? 'no-sidebar' : '' }}">
        @if($postPivot->canonical === 'lich-khai-giang-du-kien')
            <div class="uk-container uk-container-center">
                <div class="post-content-wrapper-full">
        @else
            <div class="uk-container uk-container-center">
                <div class="uk-grid uk-grid-medium" data-uk-grid-match>
                    <!-- Left Column: Post Content -->
                    <div class="uk-width-large-3-4">
                        <div class="post-content-wrapper">
        @endif
                        <article class="post-article">
                            <div class="post-content">
                                <h1 class="post-title">{{ $postName }}</h1>
                                @if($postDescription)
                                    <div class="post-description">
                                        <strong>{!! $postDescription !!}</strong>
                                    </div>
                                @endif
                                @if($postPivot->canonical !== 'lich-khai-giang-du-kien')
                                    <x-table-of-contents :content="$contentWithToc" />
                                @endif
                                @if($postPivot->canonical === 'lich-khai-giang-du-kien')
                                    <div class="table-wrapper">
                                        {!! $postContent !!}
                                    </div>
                                @else
                                    {!! $postContent !!}
                                @endif
                                
                                <!-- Social Share Buttons -->
                                @php
                                    $postCanonical = $postPivot->canonical ?? '';
                                    $postUrl = $postCanonical ? write_url($postCanonical) : url()->current();
                                    $encodedUrl = urlencode($postUrl);
                                    $encodedTitle = urlencode($postName);
                                @endphp
                                <div class="post-social-share">
                                    <div class="social-share-buttons">
                                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedUrl }}" target="_blank" rel="noopener noreferrer" class="social-share-btn social-facebook" title="Chia sẻ trên Facebook">
                                            <i class="fa fa-facebook"></i>
                                        </a>
                                        <a href="https://twitter.com/intent/tweet?url={{ $encodedUrl }}&text={{ $encodedTitle }}" target="_blank" rel="noopener noreferrer" class="social-share-btn social-twitter" title="Chia sẻ trên Twitter">
                                            <i class="fa fa-twitter"></i>
                                        </a>
                                        <a href="mailto:?subject={{ $encodedTitle }}&body={{ $encodedUrl }}" class="social-share-btn social-email" title="Chia sẻ qua Email">
                                            <i class="fa fa-envelope"></i>
                                        </a>
                                        <a href="https://pinterest.com/pin/create/button/?url={{ $encodedUrl }}&description={{ $encodedTitle }}" target="_blank" rel="noopener noreferrer" class="social-share-btn social-pinterest" title="Chia sẻ trên Pinterest">
                                            <i class="fa fa-pinterest"></i>
                                        </a>
                                        <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ $encodedUrl }}&title={{ $encodedTitle }}" target="_blank" rel="noopener noreferrer" class="social-share-btn social-linkedin" title="Chia sẻ trên LinkedIn">
                                            <i class="fa fa-linkedin"></i>
                                        </a>
                                        <a href="https://www.tumblr.com/widgets/share/tool?canonicalUrl={{ $encodedUrl }}&title={{ $encodedTitle }}" target="_blank" rel="noopener noreferrer" class="social-share-btn social-tumblr" title="Chia sẻ trên Tumblr">
                                            <i class="fa fa-tumblr"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </article>

                        <!-- Contact Information Block -->
                        <div class="post-contact-block">
                            <div class="contact-block-content">
                                <p class="contact-text-line-1">50.000+ Học Viên Tin Tưởng và Lựa chọn Hệ Từ Xa Để phát triển bản thân và sự nghiệp</p>
                                <p class="contact-text-line-2">Nắm lấy cơ hội sở hữu <strong>BẰNG ĐẠI HỌC</strong> ngay hôm nay</p>
                                <p class="contact-text-line-3">📩 Liên hệ để được tư vấn miễn phí và chọn Trường, chọn ngành học phù hợp nhất</p>
                            </div>
                        </div>

                        <!-- Center Introduction Block -->
                        <div class="post-center-block">
                            <div class="center-block-content">
                                <div class="center-logo">
                                    @if(isset($system['homepage_logo']) && $system['homepage_logo'])
                                        <img src="{{ asset($system['homepage_logo']) }}" alt="Logo" class="center-logo-img">
                                    @endif
                                </div>
                                <div class="center-info">
                                    <h3 class="center-title">{{ $system['homepage_post_center_title'] ?? 'Trung Tâm luyện thi eVSTEP bậc 4/6' }}</h3>
                                    <div class="center-description">
                                        @if(isset($system['homepage_post_center_description']) && $system['homepage_post_center_description'])
                                            {!! $system['homepage_post_center_description'] !!}
                                        @else
                                            <p>Đang cập nhật</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Related Posts -->
                        @if($relatedPosts && $relatedPosts->isNotEmpty())
                            <div class="post-related">
                                <h2 class="related-title">Bài viết liên quan</h2>
                                <div class="related-posts-grid">
                                    <div class="uk-grid uk-grid-medium">
                                        @foreach($relatedPosts as $relatedPost)
                                            @php
                                                $relatedPostLanguage = $relatedPost->languages->first();
                                                $relatedPostPivot = $relatedPostLanguage->pivot ?? null;
                                                $relatedPostName = $relatedPostPivot->name ?? '';
                                                $relatedPostCanonical = $relatedPostPivot->canonical ?? '';
                                                $relatedPostUrl = $relatedPostCanonical ? write_url($relatedPostCanonical) : '#';
                                                $relatedPostImage = $relatedPost->image ?? '';
                                                $relatedPostImageUrl = $relatedPostImage ? (function_exists('thumb') ? thumb($relatedPostImage, 300, 200) : asset($relatedPostImage)) : asset('frontend/resources/img/default-news.jpg');
                                                $relatedPostDate = $relatedPost->created_at ?? now();
                                                $relatedPostFormattedDate = $relatedPostDate ? date('d/m/Y', strtotime($relatedPostDate)) : '';
                                            @endphp
                                            <div class="uk-width-medium-1-2">
                                                <div class="related-post-item">
                                                    <a href="{{ $relatedPostUrl }}" class="related-post-image">
                                                        <img src="{{ $relatedPostImageUrl }}" alt="{{ $relatedPostName }}">
                                                    </a>
                                                    <div class="related-post-content">
                                                        <h3 class="related-post-title">
                                                            <a href="{{ $relatedPostUrl }}">{{ $relatedPostName }}</a>
                                                        </h3>
                                                        <span class="related-post-date">
                                                            <i class="fa fa-calendar"></i>
                                                            {{ $relatedPostFormattedDate }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column: Aside Posts -->
                <div class="uk-width-large-1-4 {{ $postPivot->canonical === 'lich-khai-giang-du-kien' ? 'uk-hidden' : '' }}">
                    <div class="post-aside">
                        <div class="aside-posts">
                            <h3 class="aside-title">Bài viết mới</h3>
                            <div class="aside-posts-list">
                                @if($asidePosts && $asidePosts->isNotEmpty())
                                    @foreach($asidePosts as $asidePost)
                                        @php
                                            $asidePostLanguage = $asidePost->languages->first();
                                            $asidePostPivot = $asidePostLanguage->pivot ?? null;
                                            $asidePostName = $asidePostPivot->name ?? '';
                                            $asidePostCanonical = $asidePostPivot->canonical ?? '';
                                            $asidePostUrl = $asidePostCanonical ? write_url($asidePostCanonical) : '#';
                                            $asidePostImage = $asidePost->image ?? '';
                                            $asidePostImageUrl = $asidePostImage ? (function_exists('thumb') ? thumb($asidePostImage, 100, 100) : asset($asidePostImage)) : asset('frontend/resources/img/default-news.jpg');
                                            $asidePostDate = $asidePost->created_at ?? now();
                                            $asidePostFormattedDate = $asidePostDate ? date('d/m/Y', strtotime($asidePostDate)) : '';
                                        @endphp
                                        <div class="aside-post-item">
                                            <a href="{{ $asidePostUrl }}" class="aside-post-image">
                                                <img src="{{ $asidePostImageUrl }}" alt="{{ $asidePostName }}">
                                            </a>
                                            <div class="aside-post-content">
                                                <h4 class="aside-post-title">
                                                    <a href="{{ $asidePostUrl }}">{{ $asidePostName }}</a>
                                                </h4>
                                                <span class="aside-post-date">
                                                    <i class="fa fa-calendar"></i>
                                                    {{ $asidePostFormattedDate }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>

                        <!-- Registration Form -->
                        <div class="aside-register-form">
                            <h3 class="aside-title">Đăng ký tư vấn</h3>
                            <form id="aside-register-form" action="{{ route('contact.save') }}" method="POST" class="aside-form-content">
                                @csrf
                                <div class="form-group">
                                    <label for="aside-name">Họ và tên <span class="required">*</span></label>
                                    <input type="text" name="name" id="aside-name" class="form-control" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="aside-phone">Số điện thoại <span class="required">*</span></label>
                                    <input type="tel" name="phone" id="aside-phone" class="form-control" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="aside-email">Email <span class="required">*</span></label>
                                    <input type="email" name="email" id="aside-email" class="form-control" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="aside-address">Địa chỉ</label>
                                    <input type="text" name="address" id="aside-address" class="form-control">
                                </div>
                                
                                <div class="form-group">
                                    <label for="aside-message">Lời nhắn</label>
                                    <textarea name="message" id="aside-message" class="form-control" rows="3"></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <button type="submit" class="form-submit-btn">
                                        <i class="fa fa-paper-plane"></i>
                                        Gửi đăng ký
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('aside-register-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(form);
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang gửi...';
                
                fetch(form.action, {
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
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.href = '{{ route("contact.thankyou") }}';
                        }
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
                    submitBtn.innerHTML = originalText;
                });
            });
        }
    });
    </script>
@endsection

@php
    // Lấy dữ liệu từ widget
    $widget = $widgets['student-feedback'] ?? null;
    $posts = collect();
    
    if ($widget && $widget->object && $widget->object->isNotEmpty()) {
        $postCatalogue = $widget->object->first();
        $posts = $postCatalogue->posts ?? collect();
        
        // Lấy thông tin catalogue
        $catalogueName = $postCatalogue->languages->name ?? 'Học viên nói gì về Hệ Từ Xa';
        $catalogueDescription = $postCatalogue->languages->description ?? '';
    }
@endphp

@if($widget && $posts->isNotEmpty())
    <div class="panel-student-feedback wow fadeInUp" data-wow-delay="0.6s">
        <div class="uk-container uk-container-center">
            <div class="student-feedback-wrapper">
                <!-- Header -->
                <div class="student-feedback-header">
                    <h2 class="student-feedback-title">{{ $catalogueName }}</h2>
                    @if($catalogueDescription)
                        <div class="student-feedback-description">
                            {!! $catalogueDescription !!}
                        </div>
                    @endif
                </div>

                <!-- Swiper Container -->
                <div class="student-feedback-slide">
                    <div class="swiper-container feedback-swiper">
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-wrapper">
                            @foreach($posts as $post)
                                @php
                                    // Handle languages - could be Collection or object
                                    $postLanguage = ($post->languages instanceof \Illuminate\Support\Collection) 
                                        ? $post->languages->first() 
                                        : (is_array($post->languages) ? (object)($post->languages[0] ?? []) : ($post->languages ?? (object)[]));
                                    
                                    $postName = $postLanguage->name ?? '';
                                    $postDescription = $postLanguage->description ?? ''; // Vị trí: "Kế toán - K2022"
                                    $postContent = $postLanguage->content ?? ''; // Cảm nhận
                                    $postCanonical = $postLanguage->canonical ?? '';
                                    $postUrl = $postCanonical ? write_url($postCanonical) : '#';
                                    $postImage = $post->image ?? '';
                                    $postImageUrl = $postImage ? asset($postImage) : '';
                                    
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
                                    $hasImage = !empty($postImageUrl) && file_exists(public_path(str_replace(asset(''), '', $postImageUrl)));
                                @endphp
                                <div class="swiper-slide">
                                    <div class="feedback-item">
                                        <div class="feedback-header-info">
                                            <div class="feedback-avatar {{ $hasImage ? 'has-image' : 'no-image' }}">
                                                @if($hasImage)
                                                    <img src="{{ $postImageUrl }}" alt="{{ $postName }}">
                                                @else
                                                    <span class="avatar-initials">{{ $initials }}</span>
                                                @endif
                                            </div>
                                            <div class="feedback-name-wrapper">
                                                <h3 class="feedback-name">{{ $postName }}</h3>
                                                @if($postDescription)
                                                    <div class="feedback-position">{!! $postDescription !!}</div>
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
                                                {!! $postContent !!}
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

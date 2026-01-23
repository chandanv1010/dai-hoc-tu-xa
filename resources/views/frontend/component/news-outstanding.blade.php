@php
    // Lấy dữ liệu từ HomeController
    $posts = collect();
    $catalogueUrl = '#';
    $catalogueName = 'TIN TỨC NỔI BẬT';
    
    if (isset($newsOutstanding) && $newsOutstanding) {
        $postCatalogue = $newsOutstanding['catalogue'] ?? null;
        $posts = $newsOutstanding['posts'] ?? collect();
        
        if ($postCatalogue) {
            $catalogueName = $postCatalogue->name ?? 'TIN TỨC NỔI BẬT';
            $catalogueCanonical = $postCatalogue->canonical ?? '';
            $catalogueUrl = $catalogueCanonical ? write_url($catalogueCanonical) : '#';
        }
    }
@endphp


@if(isset($newsOutstanding) && $newsOutstanding && $posts->isNotEmpty())
    <div class="panel-news-outstanding wow fadeInUp" data-wow-delay="0.6s">
        <div class="uk-container uk-container-center">
            <div class="news-outstanding-wrapper">
                <!-- Header -->
                <div class="news-outstanding-header">
                    <h2 class="news-outstanding-title">{{ $catalogueName }}</h2>
                </div>

                <!-- News Grid -->
                <div class="news-outstanding-grid">
                    <div class="uk-grid uk-grid-medium" data-uk-grid-match>
                        @foreach($posts as $post)
                            @php
                                // Dữ liệu đã được join sẵn từ Controller
                                $postName = $post->name ?? '';
                                $postDescription = $post->description ?? '';
                                $postCanonical = $post->canonical ?? '';
                                $postUrl = $postCanonical ? write_url($postCanonical) : '#';
                                $postImage = $post->image ?? '';
                                $postImageUrl = $postImage ? (function_exists('thumb') ? thumb($postImage, 400, 300) : asset($postImage)) : asset('frontend/resources/img/default-news.jpg');
                                
                                // Lấy ngày tháng
                                $postDate = $post->created_at ?? now();
                                $formattedDate = $postDate ? date('d/m/Y', strtotime($postDate)) : '';
                                
                                // Lấy tên danh mục (label đỏ)
                                $categoryName = $catalogueName ?? 'Tin tức';
                            @endphp
                            <div class="uk-width-medium-1-2 uk-width-large-1-3">
                                <div class="news-item wow fadeInUp">
                                    <a href="{{ $postUrl }}" class="news-image img-cover">
                                        <img src="{{ $postImageUrl }}" alt="{{ $postName }}" loading="lazy">
                                    </a>
                                    <div class="news-content">
                                        <span class="news-category-label">{{ $categoryName }}</span>
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
                </div>

                <!-- Footer: Xem thêm button -->
                <div class="news-outstanding-footer">
                    <a href="{{ $catalogueUrl }}" class="news-outstanding-cta-button">Xem thêm</a>
                </div>
            </div>
        </div>
    </div>
@endif


@php
    // Lấy dữ liệu từ widget
    $widget = $widgets['news-outstanding'] ?? null;
    $posts = collect();
    $catalogueUrl = '#';
    
    if ($widget && $widget->object && $widget->object->isNotEmpty()) {
        $postCatalogue = $widget->object->first();
        // WidgetService trả về Posts (chữ P hoa) - getObjectKey() trả về "Post" + "s" = "Posts"
        // Thử nhiều cách truy cập
        $posts = collect();
        if (isset($postCatalogue->Posts) && is_iterable($postCatalogue->Posts)) {
            $posts = is_array($postCatalogue->Posts) ? collect($postCatalogue->Posts) : $postCatalogue->Posts;
        } elseif (isset($postCatalogue->posts) && is_iterable($postCatalogue->posts)) {
            $posts = is_array($postCatalogue->posts) ? collect($postCatalogue->posts) : $postCatalogue->posts;
        }
        
        // Lấy thông tin catalogue
        $catalogueName = $postCatalogue->languages->name ?? 'TIN TỨC NỔI BẬT';
        $catalogueCanonical = $postCatalogue->languages->canonical ?? '';
        $catalogueUrl = $catalogueCanonical ? write_url($catalogueCanonical) : '#';
    }
@endphp

@if($widget && $posts->isNotEmpty())
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
                        @foreach($posts->take(3) as $post)
                            @php
                                // Handle languages - could be Collection or object
                                $postLanguage = ($post->languages instanceof \Illuminate\Support\Collection) 
                                    ? $post->languages->first() 
                                    : (is_array($post->languages) ? (object)($post->languages[0] ?? []) : ($post->languages ?? (object)[]));
                                
                                $postName = $postLanguage->name ?? '';
                                $postDescription = $postLanguage->description ?? '';
                                $postCanonical = $postLanguage->canonical ?? '';
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
                                        <img src="{{ $postImageUrl }}" alt="{{ $postName }}">
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


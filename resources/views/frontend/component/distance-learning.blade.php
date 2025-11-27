@php
    // Lấy dữ liệu từ widget
    $widget = $widgets['distance-learning'] ?? null;
    $postCatalogue = null;
    $features = [];
    $description = '';
    $mainContent = '';
    
    if ($widget && isset($widget->object) && $widget->object->isNotEmpty()) {
        // Widget trả về PostCatalogue trong object
        $postCatalogue = $widget->object->first();
        
        // Tất cả dữ liệu nằm trong PostCatalogue->languages
        if (isset($postCatalogue->languages) && is_object($postCatalogue->languages)) {
            // Description: giữ nguyên, không xử lý
            $description = $postCatalogue->languages->description ?? '';
            
            // Content: tách thành mảng các thẻ <p>
            $content = $postCatalogue->languages->content ?? '';
            
            // Tách tất cả thẻ <p> thành mảng
            preg_match_all('/<p[^>]*>(.*?)<\/p>/is', $content, $matches);
            
            if (!empty($matches[0])) {
                $allParagraphs = $matches[0];
                $totalParagraphs = count($allParagraphs);
                
                // Lấy 4 thẻ <p> cuối cùng làm features (4 nút)
                if ($totalParagraphs >= 4) {
                    $features = array_slice($allParagraphs, -4);
                    // Loại bỏ 4 thẻ cuối khỏi content để lấy main description
                    $mainContent = implode('', array_slice($allParagraphs, 0, -4));
                } else {
                    // Nếu không đủ 4 thẻ, lấy tất cả làm features
                    $features = $allParagraphs;
                    $mainContent = '';
                }
                
                // Extract text từ các thẻ p của features (loại bỏ thẻ p)
                $features = array_map(function($pTag) {
                    return strip_tags($pTag);
                }, $features);
            }
        }
    }
@endphp

@if($postCatalogue)
    <div class="panel-distance-learning wow fadeInUp" data-wow-delay="0.25s">
        <div class="uk-container uk-container-center">
            <div class="distance-learning-wrapper">
                <div class="uk-grid uk-grid-medium" data-uk-grid-match>
                    <!-- Left Column: Image (2/5) -->
                    <div class="uk-width-medium-2-5 uk-width-large-2-5">
                        <div class="distance-learning-image-wrapper" style="position: relative;">
                            <div class="distance-learning-image">
                                @if(isset($postCatalogue->image) && $postCatalogue->image)
                                    <img src="{{ $postCatalogue->image }}" alt="{{ $postCatalogue->languages->name ?? '' }}" class="img-responsive">
                                @else
                                    <div class="placeholder-image">
                                        <i class="fa fa-graduation-cap" style="font-size: 120px; color: #0066CC;"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <!-- Right Column: Content (3/5) -->
                    <div class="uk-width-medium-3-5 uk-width-large-3-5">
                        <div class="distance-learning-content">
                            <!-- Title -->
                            <div class="distance-learning-title">
                                <h2>{{ $postCatalogue->languages->name ?? 'Hệ Đào Tạo Từ Xa Là Gì?' }}</h2>
                            </div>

                            <!-- Banner Box (Special) -->
                            @if($description)
                                <div class="distance-learning-banner">
                                    {!! $description !!}
                                </div>
                            @endif

                            <!-- Description Content -->
                            @if($mainContent)
                                <div class="distance-learning-description">
                                    {!! $mainContent !!}
                                </div>
                            @endif
                            
                            <!-- Features Grid -->
                            @if(count($features) > 0)
                                <div class="distance-learning-features">
                                    <div class="uk-grid uk-grid-small" data-uk-grid-match>
                                        @foreach($features as $index => $feature)
                                            <div class="uk-width-medium-1-2">
                                                <div class="feature-item">
                                                    <div class="feature-icon">
                                                        @php
                                                            $iconNumber = ($index % 4) + 1;
                                                            $iconPath = asset("frontend/resources/img/icon/icon-{$iconNumber}.png");
                                                        @endphp
                                                        <img src="{{ $iconPath }}" alt="Icon {{ $iconNumber }}">
                                                    </div>
                                                    <div class="feature-text">{!! $feature !!}</div>
                                                </div>
                                            </div>
                                        @endforeach
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


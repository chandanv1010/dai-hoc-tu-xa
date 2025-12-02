@php
    // Lấy dữ liệu từ widget
    $widget = $widgets['value-we-bring'] ?? null;
    $postCatalogue = null;
    $posts = collect();
    
    if ($widget && isset($widget->object) && $widget->object->isNotEmpty()) {
        $postCatalogue = $widget->object->first();
        $posts = $postCatalogue->posts ?? collect();
    }
@endphp

@if($postCatalogue && $posts->isNotEmpty())
    @php
        $catalogueName = $postCatalogue->languages->name ?? 'Giá trị chúng tôi mang đến bạn';
        $catalogueDescription = $postCatalogue->languages->description ?? 'Tại sao Hệ Đào Tạo Từ Xa là lựa chọn hoàn hảo cho bạn';
    @endphp

    <div class="panel-value-we-bring wow fadeInUp" data-wow-delay="0.15s">
        <div class="uk-container uk-container-center">
            <div class="value-we-bring-wrapper">
                <!-- Header -->
                <div class="value-we-bring-header">
                    <h2 class="value-we-bring-title">{{ $catalogueName }}</h2>
                    @if($catalogueDescription)
                        <p class="value-we-bring-subtitle">{!! strip_tags($catalogueDescription) !!}</p>
                    @endif
                </div>

                <!-- Cards Container -->
                <div class="value-we-bring-cards">
                    <div class="uk-grid uk-grid-medium" data-uk-grid-match>
                        @foreach($posts as $index => $post)
                            @php
                                // Handle languages
                                $postLanguage = ($post->languages instanceof \Illuminate\Support\Collection) 
                                    ? $post->languages->first() 
                                    : (is_array($post->languages) ? (object)($post->languages[0] ?? []) : ($post->languages ?? (object)[]));
                                
                                $postName = $postLanguage->name ?? '';
                                $postDescription = $postLanguage->description ?? '';
                                $postContent = $postLanguage->content ?? '';
                            @endphp
                            <div class="uk-width-medium-1-5 uk-width-small-1-2">
                                <div class="value-card">
                                    <div class="value-card-banner">
                                        <span class="banner-text">{{ $postName ?: 'Linh hoạt' }}</span>
                                    </div>
                                    <div class="value-card-content">
                                        {!! $postContent !!}
                                    </div>
                                    <div class="value-card-icon">
                                        <img src="{{ asset('frontend/resources/img/red-t.png') }}" alt="icon">
                                    </div>
                                    @if($index < $posts->count() - 1)
                                        <div class="value-card-connector">
                                            <div class="connector-line"></div>
                                            <div class="connector-dot"></div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif


@extends('frontend.homepage.layout')
@section('content')
    @php
        // Lấy thông tin catalogue
        $catalogueName = $postCatalogue->languages->first()->pivot->name ?? '';
        $catalogueDescription = $postCatalogue->languages->first()->pivot->description ?? '';
    @endphp

    <!-- Breadcrumb Section -->
    <div class="page-breadcrumb-large">
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
        </div>
    </div>
@endsection

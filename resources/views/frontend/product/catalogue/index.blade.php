@extends('frontend.homepage.layout')
@section('content')
    @php
        // Lấy thông tin catalogue
        $catalogueName = $productCatalogue->languages->first()->pivot->name ?? '';
        $catalogueDescription = $productCatalogue->languages->first()->pivot->description ?? '';
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
    
    <div class="course-catalogue-page">
        <div class="uk-container uk-container-center">
            <!-- Page Header -->
            <div class="course-page-header">
                <h1 class="course-page-title">{{ $productCatalogue->name }}</h1>
                @if(!empty($productCatalogue->description))
                    <div class="course-page-description">
                        {!! $productCatalogue->description !!}
                    </div>
                @endif
            </div>
            
            <!-- Course List -->
            @if (!is_null($products) && $products->count() > 0)
                <div class="course-list-wrapper">
                    <div class="course-results-info">
                        <p>Tìm thấy <strong>{{ $products->total() }}</strong> khóa học</p>
                    </div>
                    <div class="course-grid">
                        @foreach ($products as $product)
                            <div class="course-grid-item">
                                @include('frontend.component.p-item', ['product' => $product])
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    <div class="course-pagination">
                        @include('frontend.component.pagination', ['model' => $products])
                    </div>
                </div>
            @else
                <div class="course-empty">
                    <p>Không tìm thấy khóa học nào.</p>
                </div>
            @endif
            
            <!-- Content -->
            @if(!empty($productCatalogue->content))
                <div class="course-page-content">
                    {!! $productCatalogue->content !!}
                </div>
            @endif
        </div>
    </div>
@endsection

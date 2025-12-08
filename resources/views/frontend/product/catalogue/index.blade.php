@extends('frontend.homepage.layout')
@section('content')
    @php
    $breadcrumbImage = !empty($productCatalogue->album) ? json_decode($productCatalogue->album, true)[0] : asset('userfiles/image/system/breadcrumb.png');
    @endphp
    
    <!-- Breadcrumb -->
    @include('frontend.component.breadcrumb', ['model' => $productCatalogue, 'breadcrumb' => $breadcrumb ?? null])
    
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

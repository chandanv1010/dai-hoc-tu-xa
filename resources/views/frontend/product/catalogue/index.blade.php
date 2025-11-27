@extends('frontend.homepage.layout')
@section('content')
    @php
    $breadcrumbImage = !empty($productCatalogue->album) ? json_decode($productCatalogue->album, true)[0] : asset('userfiles/image/system/breadcrumb.png');
    @endphp
    <div class="product-catalogue page-wrapper">
        <div class="uk-container uk-container-center">
            <div class="mt40 mb40 banner">
                <a href="" class="image img-cover">
                    <img src="{{ $system['background_1'] }}" alt="">
                </a>
                <div class="text-overlay">
                    <h2 class="heading-1"><span>{{ $productCatalogue->name }}</span></h2>
                    <div class="description">
                        {!! $productCatalogue->description !!}
                    </div>
                </div>
            </div>
            <div class="panel-body mb30" id="panel-body">
                <div class="uk-container uk-container-center">
                    <div class="uk-grid uk-grid-medium">
                        <div class="uk-width-medium-1-4">
                            @include('frontend.component.filter')
                        </div>
                        <div class="uk-width-medium-3-4">
                            <div class="browse-items">
                                <div class="browse-tools">
                                    <div class="filter-tools">
                                        <button class="button button-outline button-pill toggle-filters">
                                            <span class="icon icon-filters"></span><span class="caption">Hiển thị <strong>{{ $products->count() }} kết quả</strong></span>
                                        </button>
                                    </div>
                                    <div class="dropdown dropdown-align-right sort-options">
                                        <button class="button button-outline button-pill dropdown-toggle" title="Sort" aria-label="Sort">
                                            <span class="overflow">Phổ Biến Nhất</span>
                                            <span class="icon icon-dropdown indicator"></span>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-large dropdown-menu-gap dropdown-menu-span dropdown-menu-icons">
                                            <div class="menu-items">
                                                <div class="menu-item"><a href="" data-sort="popular"><span class="icon icon-check"></span>Phổ biến nhất</a></div>
                                                <div class="menu-item"><a href="" data-sort="name-asc">Tên A -> Z</a></div>
                                                <div class="menu-item"><a href="" data-sort="name-desc">Tên Z -> A</a></div>
                                                <div class="menu-item"><a href="" data-sort="price-asc">Giá tăng dần</a></div>
                                                <div class="menu-item"><a href="" data-sort="price-desc">Giá giảm dần</a></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if (!is_null($products))
                                <div class="product-catalogue product-list">
                                    <div class="uk-grid uk-grid-medium">
                                        @foreach ($products as $product)
                                            <div class="uk-width-small-1-1 uk-width-medium-1-2 uk-width-large-1-3 mb20">
                                                @include('frontend.component.p-item', ['product' => $product])
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="uk-flex uk-flex-center">
                                        @include('frontend.component.pagination', ['model' => $products])
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="description dc">
                {!! $productCatalogue->content !!}
            </div>
        </div>
    </div>
@endsection

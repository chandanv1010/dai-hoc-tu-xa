@php
    $name = $post->name;
    $canonical = write_url($post->canonical);
    $image = image($post->image);
    $catName = $postCatalogue->name;
    $description = $post->description;
    $content = $post->content;
    $gallery = json_decode($post->album);

@endphp

@extends('frontend.homepage.layout')
@section('content')
<div class="product-container">
    @include('frontend.component.breadcrumb', ['model' => $post, 'breadcrumb' => $breadcrumb])
    <div class="uk-container uk-container-center">
        <div class="panel-head">
            @if(isset($postCatalogue->children) && !is_null($postCatalogue->children) )
                <ul class="children">
                    @foreach($postCatalogue->children as $key => $item)
                        @php
                            $name =  $item->short_name;
                            $canonical = write_url($item->languages->first()->pivot->canonical);
                        @endphp
                        <li>
                            <a href="{{ $canonical }}" title="{{ $name }}" class="{{ $item->languages->first()->pivot->canonical == $postCatalogue->canonical ? 'active' : '' }}">{{ $name }}</a>
                        </li>
                    @endforeach
                </ul>
            @endif
            <h1 class="product-detail-name ">{{ $post->name }}</h1>
            <div class="product-detail-container design-detail-container">
                <div class="product-detail-gallery">
                    @include('frontend.product.product.component.gallery')
                </div> 
                <div class="design-general">
                    <div class="uk-grid uk-grid-small">
                        <div class="uk-width-small-3-5">
                            <div class="product-general-info">
                                <div class="content-info">
                                    <ul data-uk-switcher="{connect:'#my-id'}" class="switcher mb20">
                                        <li><a href="">Mô tả</a></li>
                                        <li><a href="">Thông tin bổ sung</a></li>
                                        <li><a href="">Gợi ý kết hợp</a></li>
                                    </ul>

                                    <ul id="my-id" class="uk-switcher">
                                        <li>
                                            <div class="content-container">
                                                {!! $content !!}
                                            </div>
                                            <button class="view-more-btn">Xem thêm</button>
                                        </li>
                                    </ul>
                                </div>
                                
                            </div>
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    // Lấy tất cả các nút "Xem thêm"
                                    const viewMoreButtons = document.querySelectorAll('.view-more-btn');
                                    
                                    viewMoreButtons.forEach(function(button) {
                                        // Tìm content container liên quan (element anh em liền trước)
                                        const container = button.previousElementSibling;
                                        
                                        if (container && container.classList.contains('content-container')) {
                                            // Thêm sự kiện click cho nút
                                            button.addEventListener('click', function() {
                                                if (container.classList.contains('expanded')) {
                                                    container.classList.remove('expanded');
                                                    button.textContent = 'Xem thêm';
                                                    // Scroll lên đầu của container nếu cần
                                                    container.scrollIntoView({behavior: 'smooth'});
                                                } else {
                                                    container.classList.add('expanded');
                                                    button.textContent = 'Thu gọn';
                                                }
                                            });
                                        }
                                    });
                                });
                            </script>
                        </div>
                        <div class="uk-width-small-2-5">
                            <div class="product-detail-info">
                                <div class="product-info">
                                    <div class="uk-grid uk-grid-medium">
                                        <div class="uk-width-large-1-2">
                                            <a class="button-item suggest" data-uk-modal="{target:'#suggest'}">
                                                <span class="main-text">Yêu cầu tư vấn</span>
                                                <span class="small-text">Thông tin chi tiết nhất</span>
                                            </a>
                                            @include('frontend.product.product.component.suggest', ['payload' => $post, 'field' => 'post_id'])
                                        </div>
                                        <div class="uk-width-large-1-2">
                                            <a class="button-item book" data-uk-modal="{target:'#suggest'}">
                                                <span class="main-text">Hẹn lịch đến xem</span>
                                                <span class="small-text">Được sắp chỗ để xe miễn phí</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="quick-consult">
                                        <div class="quick-consult-title">Tư vấn nhanh</div>
                                        <div class="quick-consult-form">
                                            <input type="number" name="phone" class="phone-input" placeholder="Nhập số điện thoại..." required>
                                            <input type="hidden" name="post_id" value="{{ $post->id }}">
                                            <button type="submit" class="submit-button">Gửi</button>
                                        </div>
                                    </div>
                                    <div class="shopware mb20">
                                        <p>HỆ THỐNG SHOWROOM CHÍNH HÃNG:</p>
                                        <p>Hà Nội: {{ $system['contact_office'] }}</p>
                                        <p>TP. HCM: {{ $system['hcm_office'] }}</p>
                                    </div>
                                </div>
                                <div class="content-aside">
                                    @if(isset($widgets['news-feature']))
                                        <div class="post-featured">
                                            <div class="aside-heading">{{ $widgets['news-feature']->name }}</div>
                                            <div>
                                                @foreach($widgets['news-feature']->object as $key => $val)
                                                @php
                                                    $name = $val->languages->name;
                                                    $canonical = write_url($val->languages->canonical);
                                                    $createdAt = $val->created_at;
                                                @endphp
                                                <div class="post-feature-item">
                                                    <h3 class="title"><a href="{{ $canonical }}" title="{{ $name }}">{{ $name }}</a></h3>
                                                    {{-- <div class="created_at uk-flex uk-flex-middle">
                                                        <div class="time"><i class="fa fa-calendar"></i> {{ $createdAt }} </div>
                                                        <span><i class="fa fa-user"></i>Admin</span>
                                                    </div> --}}
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if(isset($cartSeen))
                                        <div class="product-seen mt30 mb30">
                                            <div class="aside-heading">Sản phẩm đã xem</div>
                                            <div>
                                                @foreach($cartSeen as $key => $val)
                                                @php
                                                    $name = $val->name;
                                                    $canonical = write_url($val->options['canonical']);
                                                    $image = $val->options['image'];
                                                    $price = $val->price;
                                                @endphp
                                                <div class="product-seen-item">
                                                    <a href="{{ $canonical }}" class="image img-cover"><img src="{{ $image }}" alt="{{ $name }}"></a>
                                                    <div class="info">
                                                        <h3 class="title"><a href="{{ $canonical }}" title="{{ $name }}">{{ $name }}</a></h3>
                                                        <div class="uk-flex uk-flex-middle uk-flex-space-between">
                                                            <div class="price">
                                                                Giá: <span>{{ number_format($price, 0, ',', '.') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif

                                    @if(isset($widgets['projects-feature']))
                                        <div class="post-featured project-featured mt40" data-uk-sticky="{boundary: true}">
                                            <div class="aside-heading">{{ $widgets['projects-feature']->name }}</div>
                                            <div>
                                                @foreach($widgets['projects-feature']->object as $key => $val)
                                                @php
                                                    $name = $val->languages->name;
                                                    $canonical = write_url($val->languages->canonical);
                                                    $createdAt = $val->created_at;
                                                    $image = thumb($val->image, 600, 400);
                                                @endphp
                                                <div class="post-feature-item">
                                                    <a href="{{ $canonical }}" class="image img-cover"><img src="{{ $image }}" alt="{{ $name }}"></a>
                                                    <h3 class="title"><a href="{{ $canonical }}" title="{{ $name }}">{{ $name }}</a></h3>
                                                    {{-- <div class="created_at uk-flex uk-flex-middle">
                                                        <div class="time"><i class="fa fa-calendar"></i> {{ $createdAt }} </div>
                                                        <span><i class="fa fa-user"></i>Admin</span>
                                                    </div> --}}
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
        </div>
    </div>
    @include('frontend.component.news')
</div>
@endsection

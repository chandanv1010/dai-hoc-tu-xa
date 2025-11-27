@php
    $name = $product->name;
    $canonical = write_url($product->canonical);
    $image = image($product->image);
    $price = getPrice($product);
    $catName = $productCatalogue->name;
    $review = getReview($product);
    $description = $product->description;
    $attributeCatalogue = $product->attributeCatalogue;
    $gallery = json_decode($product->album);
    $iframe = $product->iframe;
    $total_lesson = !is_null($product->chapter) ? calculateCourses($product)['durationText'] : '';
    $student = $product->student;
    $rate = $product->rate;
@endphp
<div class="info">
    <div class="popup">
        <div class="uk-grid uk-grid-large">
            <div class="uk-width-large-1-2">
                <div class="popup-product">
                    <div class="badge">
                        <span>Top bán chạy</span>
                    </div>
                    <h1 class="title product-main-title"><span>{{ $name }}</span></h1>
                    <div class="description">
                        {!! $description !!}
                    </div>
                    <div class="stats">
                        <div class="stat-item rating">
                            <img src="/frontend/resources/img/star1.svg" alt="">
                            {{-- <span>{{ $review['totalRate'] != 0 ? $review['totalRate'] : 5 }} ({{ $review['count'] }} đánh giá)</span> --}}
                            <span>{{ $rate }} đánh giá</span>
                        </div>
                        <div class="stat-item students">
                            <img src="/frontend/resources/img/user.svg" alt="">
                            <span>{{ $student ?? 0 }} học viên</span>
                        </div>
                        <div class="stat-item duration">
                            <img src="/frontend/resources/img/time1.svg" alt="">
                            <span>{{ $total_lesson }}</span>
                        </div>
                    </div>
                    <div class="buttons">
                        <a href="#modal-register" title="" class="btn btn-register" data-uk-modal><span>Đăng ký ngay</span></a>
                        <a href="" class="preview-video" data-video="{{ json_encode($product->iframe) }}" title="" class="btn btn-demo"><span>Xem demo</span></a>
                    </div>
                </div>
            </div>
            <div class="uk-width-large-1-2">
                @php
                    $qrcode = $product->qrcode;
                @endphp
                @if(!empty($product->iframe))
                    <div class="video-feature product-video-feature p-r">
                        <div class="bg">
                            {!! $qrcode !!}
                        </div>
                        <a href="" data-video="{{ json_encode($product->iframe) }}" class="image img-cover wow fadeInUp video preview-video" data-wow-delay="0.2s" target="_blank" style="visibility: visible; animation-delay: 0.2s; animation-name: fadeInUp;">
                            <img src="{{ $product->image }}" alt="{{ $product->name }}">
                            <button class="btn-play">
                                <img src="/frontend/resources/img/play.svg" alt="">
                            </button>
                        </a>
                    </div>
                @else 
                    <span class="image img-cover product-preview-image p-r">
                        <div class="bg">
                            {!! $qrcode !!}
                        </div>
                        <img src="{{ $product->image }}" alt="{{ $product->name }}">
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>

<input type="hidden" class="productName" value="{{ $product->name }}">
<input type="hidden" class="attributeCatalogue" value="{{ json_encode($attributeCatalogue) }}">
<input type="hidden" class="productCanonical" value="{{ write_url($product->canonical) }}">

@include('frontend.component.modal-register', ['name' => $name ?? ''])
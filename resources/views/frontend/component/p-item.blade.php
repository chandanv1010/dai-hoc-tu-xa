@php
    $productLanguage = $product->languages->first();
    $name = $productLanguage ? $productLanguage->pivot->name : 'Khóa học';
    $canonical = $productLanguage ? write_url($productLanguage->pivot->canonical) : '#';
    $image = image($product->image);
    $price = getPrice($product);
    $description = $productLanguage ? ($productLanguage->pivot->description ?? '') : '';
    // Cắt mô tả ngắn
    $shortDescription = strip_tags($description);
    $shortDescription = mb_strlen($shortDescription) > 100 ? mb_substr($shortDescription, 0, 100) . '...' : $shortDescription;
@endphp

<div class="course-card">
    <div class="course-card-image">
        <a href="{{ $canonical }}" title="{{ $name }}">
            <img src="{{ $image }}" alt="{{ $name }}" class="img-responsive">
        </a>
    </div>
    <div class="course-card-content">
        <h3 class="course-card-title">
            <a href="{{ $canonical }}" title="{{ $name }}">{{ $name }}</a>
        </h3>
        @if(!empty($shortDescription))
            <p class="course-card-description">{{ $shortDescription }}</p>
        @endif
        <div class="course-card-price">
            {!! $price['html'] !!}
        </div>
        <div class="course-card-actions">
            <a href="{{ $canonical }}" class="btn-view-detail">Xem chi tiết</a>
            <a href="{{ $canonical }}" class="btn-buy-now">Mua ngay</a>
        </div>
    </div>
</div>


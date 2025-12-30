@php
    $productLanguage = $product->languages->first();
    $name = $productLanguage ? $productLanguage->pivot->name : 'Khóa học';
    $canonical = $productLanguage ? write_url($productLanguage->pivot->canonical) : '#';
    $image = image($product->image);
    $price = getPrice($product);
    $description = $productLanguage ? ($productLanguage->pivot->description ?? '') : '';
    // Cắt mô tả ngắn
    $shortDescription = html_entity_decode(strip_tags($description), ENT_QUOTES, 'UTF-8');
    $shortDescription = trim($shortDescription);
    if (mb_strlen($shortDescription, 'UTF-8') > 100) {
        $shortDescription = mb_substr($shortDescription, 0, 100, 'UTF-8');
        // Tìm vị trí khoảng trắng cuối cùng để không cắt giữa từ
        $lastSpace = mb_strrpos($shortDescription, ' ', 0, 'UTF-8');
        if ($lastSpace !== false && $lastSpace > 80) {
            $shortDescription = mb_substr($shortDescription, 0, $lastSpace, 'UTF-8');
        }
        $shortDescription .= '...';
    }
    
    // Tính giá hiển thị
    $originalPrice = $product->price ?? 0;
    $salePrice = ($price['priceSale'] > 0) ? $price['priceSale'] : $originalPrice;
    $hasDiscount = ($price['percent'] > 0 && $price['priceSale'] > 0);
@endphp

<div class="course-card{{ $hasDiscount ? ' has-discount' : '' }}">
    <div class="course-card-image">
        <a href="{{ $canonical }}" title="{{ $name }}">
            <img src="{{ $image }}" alt="{{ $name }}" class="img-responsive">
        </a>
        @if($hasDiscount)
            <div class="course-discount-badge">
                <span>-{{ $price['percent'] }}%</span>
            </div>
        @endif
    </div>
    <div class="course-card-content">
        <h3 class="course-card-title">
            <a href="{{ $canonical }}" title="{{ $name }}">{{ $name }}</a>
        </h3>
        @if(!empty($shortDescription))
            <p class="course-card-description">{{ $shortDescription }}</p>
        @endif
        <div class="course-card-price">
            @if($originalPrice == 0)
                <div class="price-contact">Liên Hệ</div>
            @elseif($hasDiscount)
                <div class="price-wrapper">
                    <div class="price-original">{{ number_format($originalPrice, 0, ',', '.') }}₫</div>
                    <div class="price-sale">{{ number_format($salePrice, 0, ',', '.') }}₫</div>
                </div>
                <div class="price-save-tag">
                    <i class="fa fa-tag"></i> Tiết kiệm {{ number_format($originalPrice - $salePrice, 0, ',', '.') }}₫
                </div>
            @else
                <div class="price-current">{{ number_format($originalPrice, 0, ',', '.') }}₫</div>
            @endif
        </div>
        <div class="course-card-actions">
            <a href="{{ $canonical }}" class="btn-view-detail">Xem chi tiết</a>
            <a href="#product-form-modal" class="btn-buy-now" data-uk-modal>Nhận tư vấn</a>
        </div>
    </div>
</div>


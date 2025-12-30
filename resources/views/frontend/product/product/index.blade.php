@php
    $productLanguage = $product->languages->first();
    $name = $productLanguage ? $productLanguage->pivot->name : 'Khóa học';
    $canonical = $productLanguage ? write_url($productLanguage->pivot->canonical) : '#';
    $image = $product->image;
    $price = getPrice($product);
    $description = $productLanguage ? ($productLanguage->pivot->description ?? '') : '';
    $content = $productLanguage ? ($productLanguage->pivot->content ?? '') : '';
    $gallery = json_decode($product->album, true) ?? [];
    $iframe = $product->iframe;
    $qrcode = $product->qrcode;
    $totalLesson = $product->total_lesson ?? 0;
    $duration = $product->duration ?? 0;
    $rate = $product->rate ?? '';
    $lessionContent = !is_null($product->lession_content) ? explode(',', $product->lession_content) : null;
    $total_time = !is_null($product->chapter) ? calculateCourses($product)['durationText'] : '';
    $totalLessons = !is_null($product->chapter) ? collect($product->chapter)
        ->flatMap(fn ($chapter) => $chapter['content'] ?? [])
        ->count() : 0;
    $modelId = $product->id;
    
    // Lecturer info
    $lecturer = $product->lecturers ?? null;
    
    // Format price
    $productPrice = $product->price ?? 0;
    $priceSale = ($price['priceSale'] > 0) ? $price['priceSale'] : $productPrice;
    $priceFormatted = number_format($priceSale, 0, ',', '.');
    $priceOld = ($price['priceSale'] > 0) ? $price['price'] : null;
    $priceOldFormatted = $priceOld ? number_format($priceOld, 0, ',', '.') : null;
@endphp
@extends('frontend.homepage.layout')
@section('content')
    {{-- Breadcrumb --}}
    @include('frontend.component.breadcrumb', ['model' => $productCatalogue, 'breadcrumb' => $breadcrumb ?? null])
    
    <div class="product-detail-page page-wrapper">
        <div class="uk-container uk-container-center">
            {{-- Row 1: Gallery + Product Info --}}
            <div class="product-detail-row-1">
                <div class="uk-grid uk-grid-medium" data-uk-grid-match>
                    {{-- Left: Gallery --}}
                    <div class="uk-width-medium-1-2">
                        @php
                            $galleryItems = [];
                            
                            // Add main image first
                            if($image) {
                                $galleryItems[] = ['type' => 'image', 'content' => $image];
                            }
                            
                            // Add gallery images
                            if(!empty($gallery) && is_array($gallery)) {
                                foreach($gallery as $galleryImage) {
                                    $galleryItems[] = ['type' => 'image', 'content' => $galleryImage];
                                }
                            }
                            
                            // Add QR code last (chỉ thêm QR nếu có)
                            if($qrcode) {
                                $galleryItems[] = ['type' => 'qrcode', 'content' => $qrcode];
                            }
                        @endphp
                        <div class="product-gallery wow fadeInLeft" data-wow-duration="0.8s" data-wow-delay="0.1s">
                            @if($image)
                                <div class="product-main-image">
                                    <img src="{{ asset($image) }}" alt="{{ $name }}" style="width: 100%; height: auto; border-radius: 12px;">
                                </div>
                            @endif
                        </div>
                        {{-- Testimonials Marquee --}}
                        @php
                            $productReviews = $product->reviews()->where('status', 1)->get();
                        @endphp
                        @if($productReviews->isNotEmpty())
                            <div class="testimonials-marquee-section">
                                <div class="marquee-wrapper">
                                    <div class="marquee-content">
                                        @foreach($productReviews as $review)
                                            <div class="testimonial-card">
                                                <div class="quote-icon">"</div>
                                                <div class="testimonial-header">
                                                    @if($review->image)
                                                        <div class="avatar">
                                                            <img src="{{ $review->image }}" alt="{{ $review->fullname }}">
                                                        </div>
                                                    @else
                                                        <div class="avatar avatar-placeholder">
                                                            <span>{{ strtoupper(substr($review->fullname, 0, 1)) }}</span>
                                                        </div>
                                                    @endif
                                                    <div class="rating">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= floor($review->score))
                                                                <i class="fa fa-star"></i>
                                                            @elseif($i - 0.5 <= $review->score)
                                                                <i class="fa fa-star-half-o"></i>
                                                            @else
                                                                <i class="fa fa-star-o"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                </div>
                                                <div class="testimonial-content">
                                                    <h4 class="reviewer-name">{{ $review->fullname }}</h4>
                                                    <p class="testimonial-text">{!! $review->description !!}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                        {{-- Duplicate for seamless loop --}}
                                        @foreach($productReviews as $review)
                                            <div class="testimonial-card">
                                                <div class="quote-icon">"</div>
                                                <div class="testimonial-header">
                                                    @if($review->image)
                                                        <div class="avatar">
                                                            <img src="{{ $review->image }}" alt="{{ $review->fullname }}">
                                                        </div>
                                                    @else
                                                        <div class="avatar avatar-placeholder">
                                                            <span>{{ strtoupper(substr($review->fullname, 0, 1)) }}</span>
                                                        </div>
                                                    @endif
                                                    <div class="rating">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= floor($review->score))
                                                                <i class="fa fa-star"></i>
                                                            @elseif($i - 0.5 <= $review->score)
                                                                <i class="fa fa-star-half-o"></i>
                                                            @else
                                                                <i class="fa fa-star-o"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                </div>
                                                <div class="testimonial-content">
                                                    <h4 class="reviewer-name">{{ $review->fullname }}</h4>
                                                    <p class="testimonial-text">{!! $review->description !!}</p>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Right: Product Info --}}
                    <div class="uk-width-medium-1-2">
                        <div class="product-info wow fadeInRight" data-wow-duration="0.8s" data-wow-delay="0.2s">
                            <h1 class="product-title">{{ $name }}</h1>
                            
                            {{-- Course Info --}}
                            <div class="product-course-info">
                                <ul class="info-list">
                                    @if($totalLesson > 0)
                                        <li>
                                            <i class="fa fa-book"></i>
                                            <span>{{ $totalLesson }} bài học</span>
                                        </li>
                                    @endif
                                    @if($duration > 0)
                                        <li>
                                            <i class="fa fa-calendar"></i>
                                            <span>{{ $duration }} tuần</span>
                                        </li>
                                    @endif
                                    @if($total_time)
                                        <li>
                                            <i class="fa fa-clock-o"></i>
                                            <span>Thời gian: {{ $total_time }}</span>
                                        </li>
                                    @endif
                                    @if($totalLessons > 0)
                                        <li>
                                            <i class="fa fa-list"></i>
                                            <span>{{ $totalLessons }} bài giảng</span>
                                        </li>
                                    @endif
                                    @if($rate)
                                        <li>
                                            <i class="fa fa-star"></i>
                                            <span>Trình độ: {{ $rate }}</span>
                                        </li>
                                    @endif
                                    @if(!is_null($lessionContent) && is_array($lessionContent) && count($lessionContent))
                                        @foreach($lessionContent as $contentItem)
                                            <li>
                                                <i class="fa fa-check"></i>
                                                <span>{{ $contentItem }}</span>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                            
                            @if($description)
                                <div class="product-description">
                                    {!! $description !!}
                                </div>
                            @endif
                            
                            <div class="product-price-section">
                                @if($priceOldFormatted && $priceOld > $priceSale)
                                    <div class="price-old">{{ $priceOldFormatted }}₫</div>
                                    <div class="price-new">{{ $priceFormatted }}₫</div>
                                    @if($price['percent'] > 0)
                                        <div class="price-save">Tiết kiệm: {{ number_format($priceOld - $priceSale, 0, ',', '.') }}₫</div>
                                    @endif
                                @else
                                    <div class="price-new">{{ $priceFormatted }}₫</div>
                                @endif
                            </div>
                            
                            {{-- Action Buttons --}}
                            <div class="product-detail-actions">
                                <button type="button" class="btn btn-consult btn-register-red open-product-form-modal" style="width: 100%;">
                                    <i class="fa fa-phone"></i>
                                    Nhận tư vấn
                                </button>
                            </div>
                            
                            @if($price['percent'] > 0 && isset($promotionLeft))
                                <div class="promotion-notice">
                                    ⏰ Ưu đãi kết thúc sau {{ $promotionLeft }} ngày
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Row 2: Content (3/4) + Sidebar (1/4) --}}
            <div class="product-detail-row-2" id="product-detail-container">
                <div class="uk-grid uk-grid-medium" data-uk-grid-match>
                    {{-- Left: Content 3/4 --}}
                    <div class="uk-width-medium-3-4">
                        {{-- Content --}}
                        @if($content)
                            <div class="product-content wow fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.3s">
                                <h2 class="content-title">Nội dung khóa học</h2>
                                <div class="content-body">
                                    {!! $content !!}
                                </div>
                            </div>
                        @endif
                        {{-- Lecturer Section --}}
                        @if($lecturer)
                            <div class="product-lecturer wow fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.35s">
                                <h2 class="lecturer-section-title">Giới thiệu về giáo viên</h2>
                                <div class="lecturer-info-box">
                                    <div class="lecturer-text">
                                        <h3 class="lecturer-name-title">{{ $lecturer->name }}</h3>
                                        @if(!empty($lecturer->position))
                                            <p class="lecturer-position">{{ $lecturer->position }}</p>
                                        @endif
                                        @if(!empty($lecturer->description))
                                            <div class="lecturer-description">
                                                {!! $lecturer->description !!}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="lecturer-image-box">
                                        @if(!empty($lecturer->image))
                                            <img src="{{ image($lecturer->image) }}" alt="{{ $lecturer->name }}" class="lecturer-photo">
                                        @else
                                            <div class="lecturer-photo-placeholder">
                                                <span>{{ strtoupper(substr($lecturer->name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        {{-- Related Products --}}
                        @if(isset($productRelated) && $productRelated->isNotEmpty())
                            <div class="product-related wow fadeInUp" data-wow-duration="0.8s" data-wow-delay="0.4s">
                                <h2 class="related-title">Khóa học liên quan</h2>
                                <div class="related-grid">
                                    @foreach($productRelated as $relatedProduct)
                                        @php
                                            $relatedLanguage = $relatedProduct->languages->first();
                                            $relatedName = $relatedLanguage->pivot->name ?? '';
                                            $relatedDescription = $relatedLanguage->pivot->description ?? '';
                                            // Fix HTML entities - decode trước khi strip_tags
                                            $relatedDescriptionClean = html_entity_decode(strip_tags($relatedDescription), ENT_QUOTES, 'UTF-8');
                                            $relatedDescriptionClean = trim($relatedDescriptionClean);
                                            $relatedCanonical = write_url($relatedLanguage->pivot->canonical ?? '');
                                            $relatedImage = $relatedProduct->image ?? '';
                                            
                                            // Lấy giá khuyến mãi
                                            $relatedPriceData = getPrice($relatedProduct);
                                            $relatedOriginalPrice = $relatedProduct->price ?? 0;
                                            $relatedSalePrice = ($relatedPriceData['priceSale'] > 0) ? $relatedPriceData['priceSale'] : $relatedOriginalPrice;
                                            $relatedHasDiscount = ($relatedPriceData['percent'] > 0 && $relatedPriceData['priceSale'] > 0);
                                        @endphp
                                        <div class="related-card{{ $relatedHasDiscount ? ' has-discount' : '' }} wow fadeInUp" data-wow-duration="0.6s" data-wow-delay="{{ ($loop->index * 0.1) + 0.5 }}s">
                                            @if($relatedHasDiscount)
                                                <div class="related-discount-badge">-{{ $relatedPriceData['percent'] }}%</div>
                                            @endif
                                            <a href="{{ $relatedCanonical }}" class="related-link">
                                                <div class="related-image">
                                                    @if($relatedImage)
                                                        <img src="{{ asset($relatedImage) }}" alt="{{ $relatedName }}">
                                                    @else
                                                        <div class="image-placeholder">
                                                            <div class="vstep-logo">VSTEP</div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="related-content">
                                                    <h3 class="related-name">{{ $relatedName }}</h3>
                                                    @if($relatedDescriptionClean)
                                                        <p class="related-description">{{ \Illuminate\Support\Str::limit($relatedDescriptionClean, 80) }}</p>
                                                    @endif
                                                    <div class="related-price-wrapper">
                                                        @if($relatedOriginalPrice == 0)
                                                            <div class="related-price contact">Liên Hệ</div>
                                                        @elseif($relatedHasDiscount)
                                                            <div class="related-price-original">{{ number_format($relatedOriginalPrice, 0, ',', '.') }}₫</div>
                                                            <div class="related-price sale">{{ number_format($relatedSalePrice, 0, ',', '.') }}₫</div>
                                                        @else
                                                            <div class="related-price">{{ number_format($relatedOriginalPrice, 0, ',', '.') }}₫</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                    {{-- Right: Sidebar 1/4 --}}
                    <div class="uk-width-medium-1-4">
                        <div class="product-sidebar" data-uk-sticky="{boundary: true,top:20}" style="height:100%;">
                            {{-- CTA Box --}}
                            <div class="sidebar-cta-box wow fadeInRight" data-wow-duration="0.8s" data-wow-delay="0.3s">
                                {{-- CTA Button --}}
                                <a href="{{ $system['product_cta_cta_button_link'] ?? '#' }}" class="cta-button" target="_blank">
                                    <i class="fa fa-comment"></i>
                                    {{ $system['product_cta_cta_button_text'] ?? 'Nhận cho Chúng tôi ngay' }}
                                </a>
                                {{-- Overview Box --}}
                                <div class="overview-box">
                                    <h3 class="overview-title">{{ $system['product_cta_overview_title'] ?? 'Tổng quan khóa Xây dựng nền tảng' }}</h3>
                                    
                                    <ul class="overview-list">
                                        {{-- Item 1 --}}
                                        <li class="overview-item">
                                            <i class="fa fa-graduation-cap"></i>
                                            <span>{{ $system['product_cta_overview_item_1'] ?? 'Đầu ra: Lấy lại gốc ngữ pháp tiếng Anh cơ bản;' }}</span>
                                        </li>
                                        
                                        {{-- Item 2 --}}
                                        <li class="overview-item">
                                            <i class="fa fa-calendar"></i>
                                            <span>{{ $system['product_cta_overview_item_2'] ?? 'Học online bất cứ khi nào có thiết bị kết nối internet;' }}</span>
                                        </li>
                                        
                                        {{-- Item 3 --}}
                                        <li class="overview-item">
                                            <i class="fa fa-gift"></i>
                                            <span>{{ $system['product_cta_overview_item_3'] ?? 'Nội dung khoa học được thu sẵn và chia thành 12 video chủ đề;' }}</span>
                                        </li>
                                        
                                        {{-- Price --}}
                                        <li class="overview-item">
                                            <i class="fa fa-star"></i>
                                            <span><strong>Học phí:</strong> {{ $priceFormatted }}₫</span>
                                        </li>
                                    </ul>
                                    
                                    {{-- Special Note --}}
                                    @if(!empty($system['product_cta_overview_item_4']))
                                        <div class="special-note">
                                            <strong>Đặc biệt:</strong> {!! $system['product_cta_overview_item_4'] !!}
                                        </div>
                                    @else
                                        <div class="special-note">
                                            <strong>Đặc biệt:</strong> Tặng miễn phí khóa Xây dựng nền tảng cho những học viên đăng ký khóa Chinh phục B1 và Bứt phá B2;
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

    {{-- Form Sản Phẩm Modal --}}
    @php
        // Lấy dữ liệu từ System config - form sản phẩm
        $formSanPhamTitle = $system['form_san_pham_title'] ?? 'ĐĂNG KÝ NHẬN TƯ VẤN';
        $formSanPhamDescription = $system['form_san_pham_description'] ?? '';
        $formSanPhamFooter = $system['form_san_pham_footer'] ?? '';
        $formSanPhamScript = trim($system['form_san_pham_script'] ?? '');
    @endphp
    
    @if(!empty($formSanPhamScript) || !empty($formSanPhamTitle))
        <x-form-modal
            modalId="product-form-modal"
            modalClass="download-roadmap-modal"
            :title="$formSanPhamTitle"
            :description="$formSanPhamDescription"
            :script="$formSanPhamScript"
            :footer="$formSanPhamFooter"
        />
    @endif

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // Open product form modal
        const productFormBtn = document.querySelector('.open-product-form-modal');
        if (productFormBtn) {
            productFormBtn.addEventListener('click', function() {
                const modal = document.querySelector('#product-form-modal');
                if (modal) {
                    UIkit.modal(modal).show();
                }
            });
        }
    });
    </script>
@endsection

{{-- PC Slide - hiện trên màn hình >= 768px --}}
@if(isset($slides[App\Enums\SlideEnum::MAIN]) && isset($slides[App\Enums\SlideEnum::MAIN]['item']) && count($slides[App\Enums\SlideEnum::MAIN]['item']))
    <div class="panel-slide panel-slide-pc page-setup">
        <div class="slide-nav">
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
        <div class="swiper-container">
            <div class="swiper-wrapper">
                @foreach($slides[App\Enums\SlideEnum::MAIN]['item'] as $key => $val )
                    <div class="swiper-slide">
                        <a href="#consultation-modal" class="slide-link" data-uk-modal>
                            <span class="image img-cover">
                                <img 
                                    src="{{ $val['image'] }}" 
                                    class="img-ab img-1 wow fadeInDown" 
                                    data-wow-delay="0.8s" 
                                    alt="Slide"
                                    @if($key === 0) fetchpriority="high" @endif
                                    loading="{{ $key === 0 ? 'eager' : 'lazy' }}"
                                >
                            </span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

{{-- Mobile Slide - hiện trên màn hình < 768px --}}
@if(isset($slides['mobile-slide']) && isset($slides['mobile-slide']['item']) && count($slides['mobile-slide']['item']))
    <div class="panel-slide panel-slide-mobile page-setup">
        <div class="slide-nav">
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>
        <div class="swiper-container">
            <div class="swiper-wrapper">
                @foreach($slides['mobile-slide']['item'] as $key => $val )
                    <div class="swiper-slide">
                        <a href="#consultation-modal" class="slide-link" data-uk-modal>
                            <span class="image img-cover">
                                <img 
                                    src="{{ $val['image'] }}" 
                                    class="img-ab img-1 wow fadeInDown" 
                                    data-wow-delay="0.8s" 
                                    alt="Slide"
                                    @if($key === 0) fetchpriority="high" @endif
                                    loading="{{ $key === 0 ? 'eager' : 'lazy' }}"
                                >
                            </span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

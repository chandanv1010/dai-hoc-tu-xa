{{-- Fixed Buttons Left Side --}}
@php
    $zaloUrl = $system['social_zalo'] ?? '';
    $facebookUrl = $system['social_messenger'] ?? $system['social_facebook'] ?? '';
    
    // Format Zalo URL nếu chưa có https://zalo.me/
    if ($zaloUrl && !preg_match('/^https?:\/\//', $zaloUrl)) {
        // Nếu là số điện thoại, thêm zalo.me
        if (preg_match('/^[0-9+\-\s()]+$/', $zaloUrl)) {
            $zaloUrl = 'https://zalo.me/' . preg_replace('/[^0-9+]/', '', $zaloUrl);
        } else {
            $zaloUrl = 'https://zalo.me/' . $zaloUrl;
        }
    }
    
    // Format Facebook Messenger URL nếu chưa có https://
    if ($facebookUrl && !preg_match('/^https?:\/\//', $facebookUrl)) {
        $facebookUrl = 'https://m.me/' . $facebookUrl;
    }
@endphp
<div class="fixed-buttons-left">
    @if($zaloUrl)
    <a href="{{ $zaloUrl }}" target="_blank" class="fixed-btn fixed-btn-zalo" title="Chat qua Zalo">
        <div class="fixed-btn-icon">
            <img src="{{ asset('frontend/resources/img/zalo-icon.png') }}" alt="Zalo" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
            <span style="display:none;">Z</span>
        </div>
    </a>
    @endif
    
    @if($facebookUrl)
    <a href="{{ $facebookUrl }}" target="_blank" class="fixed-btn fixed-btn-facebook" title="Chat qua Facebook">
        <div class="fixed-btn-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2C6.48 2 2 6.48 2 12c0 5.01 3.66 9.15 8.44 9.9v-7H7.9v-2.9h2.54V9.85c0-2.51 1.49-3.89 3.78-3.89 1.09 0 2.23.19 2.23.19v2.47h-1.26c-1.24 0-1.63.77-1.63 1.56v1.88h2.78l-.45 2.9h-2.33v7C18.34 21.15 22 17.01 22 12c0-5.52-4.48-10-10-10z"/>
            </svg>
        </div>
    </a>
    @endif
    
    <button type="button" class="fixed-btn fixed-btn-form" id="open-consultation-form" title="Điền form nhận tư vấn">
        <div class="fixed-btn-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 3h18v18H3zM7 8h10M7 12h10M7 16h7"/>
            </svg>
        </div>
    </button>
    
    {{-- Scroll Top Button (inside fixed-buttons-left on mobile) --}}
    <button type="button" class="fixed-btn fixed-btn-scroll-top scroll-top-btn-mobile" id="scroll-top-btn-mobile" title="Lên đầu trang">
        <div class="fixed-btn-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 15l-6-6-6 6"/>
            </svg>
        </div>
    </button>
</div>

{{-- Scroll Top Button Right Side (desktop only) --}}
<button type="button" class="scroll-top-btn scroll-top-btn-desktop" id="scroll-top-btn" title="Lên đầu trang">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M18 15l-6-6-6 6"/>
    </svg>
</button>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Scroll to top - handle both desktop and mobile buttons
        const scrollTopBtn = document.getElementById('scroll-top-btn');
        const scrollTopBtnMobile = document.getElementById('scroll-top-btn-mobile');
        
        function handleScrollTop() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            if (scrollTop > 300) {
                if (scrollTopBtn) scrollTopBtn.classList.add('show');
                if (scrollTopBtnMobile) scrollTopBtnMobile.classList.add('show');
            } else {
                if (scrollTopBtn) scrollTopBtn.classList.remove('show');
                if (scrollTopBtnMobile) scrollTopBtnMobile.classList.remove('show');
            }
        }
        
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
        
        window.addEventListener('scroll', handleScrollTop);
        
        if (scrollTopBtn) {
            scrollTopBtn.addEventListener('click', scrollToTop);
        }
        
        if (scrollTopBtnMobile) {
            scrollTopBtnMobile.addEventListener('click', scrollToTop);
        }

        // Open consultation form
        const openFormBtn = document.getElementById('open-consultation-form');
        if (openFormBtn) {
            openFormBtn.addEventListener('click', function() {
                // Trigger consultation modal (form tư vấn miễn phí)
                if (typeof UIkit !== 'undefined') {
                    UIkit.modal('#consultation-modal').show();
                } else if (typeof $ !== 'undefined') {
                    $('#consultation-modal').modal('show');
                } else {
                    // Fallback: scroll to form
                    const formSection = document.querySelector('#panel-contact, .register-banner-form');
                    if (formSection) {
                        formSection.scrollIntoView({ behavior: 'smooth' });
                    }
                }
            });
        }
    });
</script>


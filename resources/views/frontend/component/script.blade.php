@php
    $coreScript = [
        'backend/js/plugins/toastr/toastr.min.js',
        'frontend/resources/plugins/wow/dist/wow.min.js',
        'frontend/resources/uikit/js/uikit.min.js',
        'frontend/resources/uikit/js/components/sticky.min.js',
        'frontend/resources/uikit/js/components/accordion.min.js',
        'frontend/resources/uikit/js/components/lightbox.min.js',
        'frontend/resources/uikit/js/components/sticky.min.js',
        'frontend/core/plugins/jquery-nice-select-1.1.0/js/jquery.nice-select.min.js',
        // function.js is now imported via Vite in app.js
    ];
    if(isset($config['js'])){
        foreach($config['js'] as $key => $val){
            array_push($coreScript, $val);
        }
    }
@endphp
@if(isset($config['externalJs']))
    @foreach ($config['externalJs'] as $item)
        <script src="{{ $item }}"></script>
    @endforeach
@endif
@foreach ($coreScript as $item)
    <script src="{{ asset($item) }}"></script>
@endforeach



<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

<div id="fb-root"></div>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v17.0&appId=103609027035330&autoLogAppEvents=1" nonce="E1aWx0Pa"></script>

{{-- Script để tự động thêm UTM parameters vào các link frontend --}}
<script>
(function() {
    // Lấy UTM parameters từ URL hiện tại
    function getUtmParams() {
        const urlParams = new URLSearchParams(window.location.search);
        const utmParams = {};
        const utmKeys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'];
        
        utmKeys.forEach(key => {
            if (urlParams.has(key)) {
                utmParams[key] = urlParams.get(key);
            }
        });
        
        return utmParams;
    }
    
    // Lưu UTM vào sessionStorage
    function saveUtmToStorage() {
        const utmParams = getUtmParams();
        if (Object.keys(utmParams).length > 0) {
            sessionStorage.setItem('utm_parameters', JSON.stringify(utmParams));
        }
    }
    
    // Lấy UTM từ sessionStorage
    function getUtmFromStorage() {
        const stored = sessionStorage.getItem('utm_parameters');
        return stored ? JSON.parse(stored) : {};
    }
    
    // Kiểm tra xem URL có phải là backend không
    function isBackendUrl(url) {
        if (!url) return false;
        const backendPatterns = ['/admin', '/dashboard', '/backend', '/api/'];
        return backendPatterns.some(pattern => url.includes(pattern));
    }
    
    // Kiểm tra xem link có phải là modal/hash link không
    function isModalOrHashLink(url) {
        if (!url) return false;
        // Bỏ qua các link anchor (#), javascript:, và các link rỗng
        return url.startsWith('#') || 
               url.startsWith('javascript:') || 
               url === '' || 
               url === 'javascript:void(0)' ||
               url === 'javascript:void(0);';
    }
    
    // Thêm UTM vào URL
    function addUtmToUrl(url) {
        if (!url || isBackendUrl(url) || isModalOrHashLink(url)) {
            return url;
        }
        
        // Bỏ qua external URLs (http/https)
        if (url.startsWith('http://') || url.startsWith('https://')) {
            const urlObj = new URL(url);
            if (urlObj.origin !== window.location.origin) {
                return url;
            }
        }
        
        const utmParams = getUtmFromStorage();
        if (Object.keys(utmParams).length === 0) {
            return url;
        }
        
        try {
            // Xử lý relative URLs
            const baseUrl = url.startsWith('/') ? window.location.origin : window.location.href.split('/').slice(0, -1).join('/');
            const urlObj = new URL(url, baseUrl);
            
            // Chỉ thêm UTM nếu chưa có
            Object.keys(utmParams).forEach(key => {
                if (!urlObj.searchParams.has(key)) {
                    urlObj.searchParams.set(key, utmParams[key]);
                }
            });
            
            // Trả về relative URL nếu input là relative
            if (url.startsWith('/') || !url.includes('://')) {
                return urlObj.pathname + urlObj.search + urlObj.hash;
            }
            
            return urlObj.toString();
        } catch (e) {
            console.error('Error adding UTM to URL:', e);
            return url;
        }
    }
    
    // Lưu UTM khi trang load
    saveUtmToStorage();
    
    // Kiểm tra xem link có phải là modal trigger không
    function isModalTrigger(linkElement) {
        return linkElement.hasAttribute('data-uk-modal') || 
               linkElement.hasAttribute('uk-modal') ||
               linkElement.hasAttribute('data-uk-lightbox') ||
               linkElement.hasAttribute('uk-lightbox') ||
               linkElement.hasAttribute('data-toggle') ||
               linkElement.classList.contains('uk-modal-close');
    }
    
    // Xử lý tất cả các link trong trang
    function processLinks() {
        const links = document.querySelectorAll('a[href]');
        links.forEach(link => {
            const originalHref = link.getAttribute('href');
            // Bỏ qua modal triggers và hash links
            if (originalHref && !isBackendUrl(originalHref) && !isModalOrHashLink(originalHref) && !isModalTrigger(link)) {
                const newHref = addUtmToUrl(originalHref);
                if (newHref !== originalHref) {
                    link.setAttribute('href', newHref);
                }
            }
        });
    }
    
    // Xử lý khi DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', processLinks);
    } else {
        processLinks();
    }
    
    // Xử lý một link element
    function processLinkElement(link) {
        const originalHref = link.getAttribute('href');
        // Bỏ qua modal triggers và hash links
        if (originalHref && !isBackendUrl(originalHref) && !isModalOrHashLink(originalHref) && !isModalTrigger(link)) {
            const newHref = addUtmToUrl(originalHref);
            if (newHref !== originalHref) {
                link.setAttribute('href', newHref);
            }
        }
    }
    
    // Xử lý các link được thêm động (sử dụng MutationObserver)
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        if (node.tagName === 'A' && node.hasAttribute('href')) {
                            processLinkElement(node);
                        } else {
                            // Kiểm tra các link con
                            const links = node.querySelectorAll && node.querySelectorAll('a[href]');
                            if (links) {
                                links.forEach(processLinkElement);
                            }
                        }
                    }
                });
            }
        });
    });
    
    // Bắt đầu quan sát
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
})();
</script>
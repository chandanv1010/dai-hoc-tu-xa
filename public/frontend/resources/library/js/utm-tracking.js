/**
 * UTM Parameter Tracking and Auto-append Script
 * Automatically saves and appends UTM parameters to internal links
 */
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
        
        // Bỏ qua external URLs
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
            const baseUrl = url.startsWith('/') ? window.location.origin : window.location.href.split('/').slice(0, -1).join('/');
            const urlObj = new URL(url, baseUrl);
            
            Object.keys(utmParams).forEach(key => {
                if (!urlObj.searchParams.has(key)) {
                    urlObj.searchParams.set(key, utmParams[key]);
                }
            });
            
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
    
    // Xử lý một link element
    function processLinkElement(link) {
        const originalHref = link.getAttribute('href');
        if (originalHref && !isBackendUrl(originalHref) && !isModalOrHashLink(originalHref) && !isModalTrigger(link)) {
            const newHref = addUtmToUrl(originalHref);
            if (newHref !== originalHref) {
                link.setAttribute('href', newHref);
            }
        }
    }
    
    // Xử lý tất cả các link trong trang
    function processLinks() {
        const links = document.querySelectorAll('a[href]');
        links.forEach(processLinkElement);
    }
    
    // Xử lý khi DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', processLinks);
    } else {
        processLinks();
    }
    
    // Xử lý các link được thêm động
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) {
                        if (node.tagName === 'A' && node.hasAttribute('href')) {
                            processLinkElement(node);
                        } else {
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
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
})();

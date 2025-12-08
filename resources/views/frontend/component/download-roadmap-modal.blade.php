@php
    // Lấy dữ liệu từ System config
    $formTitle = $system['form_tai_lo_trinh_title'] ?? 'ĐĂNG KÝ NHẬN TƯ VẤN MIỄN PHÍ NGAY';
    $formDescription = $system['form_tai_lo_trinh_description'] ?? 'Cơ hội sở hữu bằng ĐH chỉ từ 2-4 năm';
    $formFooter = $system['form_tai_lo_trinh_footer'] ?? 'Còn 10 chỉ tiêu tuyển sinh năm 2025';
    $formScript = $system['form_tai_lo_trinh_script'] ?? '';
@endphp

<!-- Download Roadmap Modal -->
<div id="download-roadmap-modal" class="uk-modal download-roadmap-modal">
    <div class="uk-modal-dialog download-roadmap-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
        
        <!-- Header với màu cam -->
        <div class="download-roadmap-header">
            <div class="download-roadmap-description">{{ $formDescription }}</div>
            <h2 class="download-roadmap-title">{{ $formTitle }}</h2>
        </div>
        
        <!-- Wrapper cho script nhúng (khung màu đỏ) -->
        <div class="download-roadmap-form-wrapper">
            <div class="download-roadmap-script-wrapper">
                {!! $formScript !!}
            </div>
            <!-- TEST BUTTON - XÓA SAU KHI TEST XONG -->
            <div style="padding: 10px; background: #f0f0f0; margin-top: 10px; border-radius: 4px;">
                <button type="button" id="test-ajax-button" style="padding: 10px 20px; background: #ff6b6b; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
                    🧪 TEST AJAX (Click để test lưu dữ liệu)
                </button>
            </div>
        </div>
        
        <!-- Footer -->
        @if(!empty($formFooter))
            <div class="download-roadmap-footer">
                {!! $formFooter !!}
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Highlight số trong footer (màu cam)
    const footer = document.querySelector('.download-roadmap-footer');
    if (footer) {
        const text = footer.innerHTML;
        footer.innerHTML = text.replace(/(\d+)/g, '<span style="color: #FF8C00; font-weight: 700;">$1</span>');
    }

    // Biến lưu dữ liệu form
    let formDataCache = {};
    let isSubmitting = false;
    let ajaxCompleted = false;
    let saveDataPromise = null;
    let formSubmitBlocked = false;
    let globalSubmitBlocked = false;
    
    // BLOCK TẤT CẢ SUBMIT - GLOBAL INTERCEPT
    console.log('=== INITIALIZING GLOBAL FORM SUBMIT BLOCKER ===');
    
    // Block tất cả click events trong modal
    document.addEventListener('click', function(e) {
        const modal = document.getElementById('download-roadmap-modal');
        if (!modal || !modal.classList.contains('uk-open')) {
            return; // Modal chưa mở, không block
        }
        
        // Kiểm tra xem có phải click vào submit button không
        const target = e.target;
        const isSubmitButton = target.type === 'submit' || 
                              target.tagName === 'BUTTON' && 
                              (target.type === 'submit' || target.textContent.toLowerCase().includes('đăng ký') || target.textContent.toLowerCase().includes('submit') || target.textContent.toLowerCase().includes('gửi'));
        
        if (isSubmitButton && !ajaxCompleted && !globalSubmitBlocked) {
            console.log('=== SUBMIT BUTTON CLICKED - BLOCKING ===');
            e.preventDefault();
            e.stopImmediatePropagation();
            e.stopPropagation();
            
            globalSubmitBlocked = true;
            
            // Lấy dữ liệu
            const data = extractFormData();
            
            if (data && Object.keys(data).length > 0) {
                console.log('=== SAVING DATA BEFORE SUBMIT ===');
                saveContactData(data).then(() => {
                    console.log('=== DATA SAVED - ALLOWING SUBMIT ===');
                    ajaxCompleted = true;
                    globalSubmitBlocked = false;
                    // Trigger click lại sau 100ms
                    setTimeout(() => {
                        target.click();
                    }, 100);
                }).catch((error) => {
                    console.error('=== ERROR SAVING - BUT ALLOWING SUBMIT ===', error);
                    ajaxCompleted = true;
                    globalSubmitBlocked = false;
                    setTimeout(() => {
                        target.click();
                    }, 2000);
                });
            } else {
                console.warn('=== NO DATA TO SAVE ===');
                globalSubmitBlocked = false;
            }
            
            return false;
        }
    }, true); // Capture phase
    
    // Block Enter key trong form
    document.addEventListener('keydown', function(e) {
        const modal = document.getElementById('download-roadmap-modal');
        if (!modal || !modal.classList.contains('uk-open')) {
            return;
        }
        
        if (e.key === 'Enter' && !ajaxCompleted && !globalSubmitBlocked) {
            const target = e.target;
            if (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA' || target.tagName === 'SELECT') {
                const form = target.closest('form');
                if (form) {
                    console.log('=== ENTER KEY PRESSED IN FORM - BLOCKING ===');
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    
                    globalSubmitBlocked = true;
                    
                    const data = extractFormData();
                    if (data && Object.keys(data).length > 0) {
                        saveContactData(data).then(() => {
                            ajaxCompleted = true;
                            globalSubmitBlocked = false;
                            // Trigger submit sau khi lưu xong
                            setTimeout(() => {
                                form.submit();
                            }, 100);
                        }).catch(() => {
                            ajaxCompleted = true;
                            globalSubmitBlocked = false;
                            setTimeout(() => {
                                form.submit();
                            }, 2000);
                        });
                    } else {
                        globalSubmitBlocked = false;
                    }
                    
                    return false;
                }
            }
        }
    }, true);
    
    // Function để lấy dữ liệu từ form
    function extractFormData() {
        const scriptWrapper = document.querySelector('.download-roadmap-script-wrapper');
        if (!scriptWrapper) {
            console.log('No script wrapper found');
            return null;
        }
        
        // Tìm form trong wrapper
        let form = scriptWrapper.querySelector('form');
        
        // Nếu không có, tìm trong iframe
        if (!form) {
            const iframe = scriptWrapper.querySelector('iframe');
            if (iframe) {
                try {
                    const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                    if (iframeDoc) {
                        form = iframeDoc.querySelector('form');
                    }
                } catch (e) {
                    console.log('Cannot access iframe:', e);
                }
            }
        }
        
        if (!form) {
            console.log('No form found');
            return null;
        }
        
        const formData = new FormData(form);
        const data = {};
        
        // Lấy tất cả dữ liệu từ form
        for (let [key, value] of formData.entries()) {
            if (!key.includes('_token') && !key.includes('csrf') && !key.includes('__')) {
                data[key] = value;
            }
        }
        
        // Bổ sung từ cache
        Object.keys(formDataCache).forEach(key => {
            if (!data[key] && formDataCache[key]) {
                data[key] = formDataCache[key];
            }
        });
        
        // Cũng thử lấy từ các input trực tiếp
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            const name = input.name || input.id || '';
            const value = input.value;
            if (name && value && !name.includes('_token') && !name.includes('csrf')) {
                if (!data[name]) {
                    data[name] = value;
                }
            }
        });
        
        console.log('Extracted form data:', data);
        return data;
    }
    
    // Intercept window.location để bắt redirect
    const originalLocationAssign = window.location.assign;
    const originalLocationReplace = window.location.replace;
    const originalLocationHref = Object.getOwnPropertyDescriptor(window.location, 'href');
    
    // Override location.href setter
    Object.defineProperty(window.location, 'href', {
        set: function(url) {
            // Kiểm tra xem có phải redirect sau khi submit form không
            if (url.includes('camon') || url.includes('thank') || url.includes('success')) {
                console.log('Detected redirect to success page:', url);
                
                // Nếu chưa lưu dữ liệu, lưu ngay
                if (!ajaxCompleted && !saveDataPromise) {
                    const scriptWrapper = document.querySelector('.download-roadmap-script-wrapper');
                    if (scriptWrapper) {
                        const form = scriptWrapper.querySelector('form');
                        if (form) {
                            const formData = new FormData(form);
                            const data = {};
                            for (let [key, value] of formData.entries()) {
                                if (!key.includes('_token') && !key.includes('csrf')) {
                                    data[key] = value;
                                }
                            }
                            
                            // Bổ sung từ cache
                            Object.keys(formDataCache).forEach(key => {
                                if (!data[key] && formDataCache[key]) {
                                    data[key] = formDataCache[key];
                                }
                            });
                            
                            console.log('Saving data before redirect:', data);
                            
                            saveDataPromise = saveContactData(data).then(() => {
                                console.log('Data saved, allowing redirect');
                                ajaxCompleted = true;
                                // Cho phép redirect
                                originalLocationHref.set.call(window.location, url);
                            }).catch((error) => {
                                console.error('Error saving, but allowing redirect:', error);
                                ajaxCompleted = true;
                                originalLocationHref.set.call(window.location, url);
                            });
                            
                            return; // Không redirect ngay
                        }
                    }
                }
            }
            
            // Cho phép redirect bình thường
            originalLocationHref.set.call(window.location, url);
        },
        get: function() {
            return originalLocationHref.get.call(window.location);
        }
    });
    
    // Override location.assign
    window.location.assign = function(url) {
        if (url.includes('camon') || url.includes('thank') || url.includes('success')) {
            console.log('location.assign to success page detected');
            // Tương tự như trên
            if (!ajaxCompleted && !saveDataPromise) {
                const scriptWrapper = document.querySelector('.download-roadmap-script-wrapper');
                if (scriptWrapper) {
                    const form = scriptWrapper.querySelector('form');
                    if (form) {
                        const formData = new FormData(form);
                        const data = {};
                        for (let [key, value] of formData.entries()) {
                            if (!key.includes('_token') && !key.includes('csrf')) {
                                data[key] = value;
                            }
                        }
                        
                        Object.keys(formDataCache).forEach(key => {
                            if (!data[key] && formDataCache[key]) {
                                data[key] = formDataCache[key];
                            }
                        });
                        
                        saveDataPromise = saveContactData(data).then(() => {
                            ajaxCompleted = true;
                            originalLocationAssign.call(window.location, url);
                        }).catch(() => {
                            ajaxCompleted = true;
                            originalLocationAssign.call(window.location, url);
                        });
                        return;
                    }
                }
            }
        }
        return originalLocationAssign.call(window.location, url);
    };
    
    // Intercept XMLHttpRequest và Fetch để bắt khi Form.io gửi request
    const originalXHROpen = XMLHttpRequest.prototype.open;
    const originalXHRSend = XMLHttpRequest.prototype.send;
    const originalFetch = window.fetch;
    
    // Intercept XMLHttpRequest
    XMLHttpRequest.prototype.open = function(method, url, ...args) {
        this._url = url;
        this._method = method;
        return originalXHROpen.apply(this, [method, url, ...args]);
    };
    
    XMLHttpRequest.prototype.send = function(data) {
        // Kiểm tra xem có phải request từ Form.io không
        if (this._url && (this._url.includes('form.io') || this._url.includes('formio') || this._url.includes('sambala.net'))) {
            console.log('Detected Form.io request:', this._url);
            
            // Lấy dữ liệu từ request
            let formData = null;
            if (data instanceof FormData) {
                formData = data;
            } else if (typeof data === 'string') {
                try {
                    formData = new FormData();
                    const params = new URLSearchParams(data);
                    for (let [key, value] of params.entries()) {
                        formData.append(key, value);
                    }
                } catch (e) {
                    console.error('Error parsing form data:', e);
                }
            }
            
            // Lưu dữ liệu trước khi gửi
            if (formData) {
                const dataObj = {};
                for (let [key, value] of formData.entries()) {
                    if (!key.includes('_token') && !key.includes('csrf')) {
                        dataObj[key] = value;
                    }
                }
                
                console.log('Form data extracted:', dataObj);
                
                // Gửi dữ liệu về server của mình TRƯỚC
                saveContactData(dataObj).then(() => {
                    console.log('Data saved to contacts, allowing Form.io request to proceed');
                    ajaxCompleted = true;
                    // Cho phép request tiếp tục
                    return originalXHRSend.apply(this, [data]);
                }).catch((error) => {
                    console.error('Error saving data, but allowing Form.io request:', error);
                    ajaxCompleted = true;
                    return originalXHRSend.apply(this, [data]);
                });
                
                return; // Không gọi originalXHRSend ở đây, đã gọi trong then/catch
            }
        }
        
        return originalXHRSend.apply(this, [data]);
    };
    
    // Intercept Fetch API
    window.fetch = function(url, options = {}) {
        // Kiểm tra xem có phải request từ Form.io không
        if (typeof url === 'string' && (url.includes('form.io') || url.includes('formio') || url.includes('sambala.net'))) {
            console.log('Detected Form.io fetch request:', url);
            
            // Lấy dữ liệu từ body
            if (options.body) {
                let formData = null;
                if (options.body instanceof FormData) {
                    formData = options.body;
                } else if (typeof options.body === 'string') {
                    try {
                        formData = new FormData();
                        const params = new URLSearchParams(options.body);
                        for (let [key, value] of params.entries()) {
                            formData.append(key, value);
                        }
                    } catch (e) {
                        // Có thể là JSON
                        try {
                            const json = JSON.parse(options.body);
                            formData = new FormData();
                            Object.keys(json).forEach(key => {
                                formData.append(key, json[key]);
                            });
                        } catch (e2) {
                            console.error('Error parsing fetch body:', e2);
                        }
                    }
                }
                
                if (formData) {
                    const dataObj = {};
                    for (let [key, value] of formData.entries()) {
                        if (!key.includes('_token') && !key.includes('csrf')) {
                            dataObj[key] = value;
                        }
                    }
                    
                    console.log('Form data extracted from fetch:', dataObj);
                    
                    // Gửi dữ liệu về server của mình TRƯỚC
                    return saveContactData(dataObj).then(() => {
                        console.log('Data saved to contacts, allowing Form.io fetch to proceed');
                        ajaxCompleted = true;
                        // Cho phép fetch tiếp tục
                        return originalFetch.apply(this, [url, options]);
                    }).catch((error) => {
                        console.error('Error saving data, but allowing Form.io fetch:', error);
                        ajaxCompleted = true;
                        return originalFetch.apply(this, [url, options]);
                    });
                }
            }
        }
        
        return originalFetch.apply(this, [url, options]);
    };

    // Lắng nghe và lưu dữ liệu từ Form.io khi submit
    function setupFormioListener() {
        const scriptWrapper = document.querySelector('.download-roadmap-script-wrapper');
        if (!scriptWrapper) return;

        // Tìm iframe chứa form
        const iframe = scriptWrapper.querySelector('iframe');
        
        if (!iframe) {
            // Nếu không có iframe, thử tìm form trực tiếp
            setupDirectFormListener(scriptWrapper);
            return;
        }

        // Thử truy cập iframe
        try {
            const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
            if (iframeDoc) {
                setupIframeFormListener(iframe, iframeDoc);
            } else {
                setupCrossOriginListener(iframe);
            }
        } catch (e) {
            // Cross-origin, sử dụng cách khác
            console.log('Cross-origin iframe detected, using alternative method');
            setupCrossOriginListener(iframe);
        }

        // Lắng nghe khi iframe load
        iframe.addEventListener('load', function() {
            setTimeout(() => {
                try {
                    const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                    if (iframeDoc) {
                        setupIframeFormListener(iframe, iframeDoc);
                    }
                } catch (e) {
                    setupCrossOriginListener(iframe);
                }
            }, 1000);
        });
    }

    // Setup listener cho form trực tiếp (không có iframe)
    function setupDirectFormListener(container) {
        const form = container.querySelector('form');
        if (form) {
            setupFormListener(form);
        }

        // Sử dụng MutationObserver để theo dõi
        const observer = new MutationObserver(function(mutations) {
            const form = container.querySelector('form');
            if (form && !form.hasAttribute('data-listener-added')) {
                form.setAttribute('data-listener-added', 'true');
                setupFormListener(form);
            }
        });

        observer.observe(container, {
            childList: true,
            subtree: true
        });

        // Thử lại sau 1 giây
        setTimeout(() => {
            const form = container.querySelector('form');
            if (form && !form.hasAttribute('data-listener-added')) {
                form.setAttribute('data-listener-added', 'true');
                setupFormListener(form);
            }
        }, 1000);
    }

    // Setup listener cho form trong iframe (cùng origin)
    function setupIframeFormListener(iframe, iframeDoc) {
        const form = iframeDoc.querySelector('form');
        if (form && !form.hasAttribute('data-listener-added')) {
            form.setAttribute('data-listener-added', 'true');
            setupFormListener(form);
            
            // Lưu dữ liệu khi user nhập
            setupInputListeners(form, iframeDoc);
        }

        // Sử dụng MutationObserver để theo dõi form được thêm vào
        const observer = new MutationObserver(function(mutations) {
            const form = iframeDoc.querySelector('form');
            if (form && !form.hasAttribute('data-listener-added')) {
                form.setAttribute('data-listener-added', 'true');
                setupFormListener(form);
                setupInputListeners(form, iframeDoc);
            }
        });

        observer.observe(iframeDoc.body || iframeDoc, {
            childList: true,
            subtree: true
        });
    }

    // Setup listener cho cross-origin iframe
    function setupCrossOriginListener(iframe) {
        // Lắng nghe mọi thay đổi trong iframe bằng cách theo dõi network requests
        // Hoặc sử dụng MutationObserver trên parent để detect khi form submit
        
        // Theo dõi khi iframe thay đổi (có thể là form đã submit)
        const checkInterval = setInterval(() => {
            try {
                // Thử truy cập iframe để lấy dữ liệu
                const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                if (iframeDoc) {
                    const form = iframeDoc.querySelector('form');
                    if (form) {
                        clearInterval(checkInterval);
                        setupIframeFormListener(iframe, iframeDoc);
                    }
                }
            } catch (e) {
                // Vẫn cross-origin
            }
        }, 1000);

        // Dừng sau 30 giây
        setTimeout(() => clearInterval(checkInterval), 30000);
    }

    // Lưu dữ liệu khi user nhập vào các input - REAL TIME SAVE
    function setupInputListeners(form, doc) {
        let saveTimeout = null;
        let lastSavedData = null;
        
        const saveDataDebounced = function() {
            // Clear previous timeout
            if (saveTimeout) {
                clearTimeout(saveTimeout);
            }
            
            // Lấy dữ liệu hiện tại
            const formData = new FormData(form);
            const currentData = {};
            for (let [key, value] of formData.entries()) {
                if (!key.includes('_token') && !key.includes('csrf') && !key.includes('__') && value) {
                    currentData[key] = value;
                }
            }
            
            // Lấy từ inputs trực tiếp
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                const name = input.name || input.id || '';
                const value = input.value;
                if (name && value && !name.includes('_token') && !name.includes('csrf')) {
                    currentData[name] = value;
                }
            });
            
            // Kiểm tra xem có thay đổi không
            const dataString = JSON.stringify(currentData);
            if (dataString === lastSavedData) {
                return; // Không thay đổi, không cần lưu
            }
            
            // Có ít nhất name hoặc phone không?
            const hasName = currentData.name || currentData.fullname || currentData.ho_ten;
            const hasPhone = currentData.phone || currentData.sdt || currentData.so_dien_thoai;
            
            if (!hasName && !hasPhone) {
                return; // Chưa có đủ dữ liệu
            }
            
            // Debounce: đợi 2 giây sau khi user ngừng nhập
            saveTimeout = setTimeout(() => {
                console.log('=== AUTO-SAVING DATA (Real-time) ===');
                console.log('Data:', currentData);
                
                saveContactData(currentData).then(() => {
                    console.log('=== DATA AUTO-SAVED SUCCESSFULLY ===');
                    lastSavedData = dataString;
                    formDataCache = { ...currentData };
                }).catch((error) => {
                    console.error('=== AUTO-SAVE FAILED ===', error);
                });
            }, 2000); // 2 giây sau khi user ngừng nhập
        };
        
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            // Lưu vào cache ngay
            input.addEventListener('input', function() {
                const name = this.name || this.id || '';
                const value = this.value;
                if (name && value) {
                    formDataCache[name] = value;
                }
                // Trigger auto-save
                saveDataDebounced();
            });

            input.addEventListener('change', function() {
                const name = this.name || this.id || '';
                const value = this.value;
                if (name && value) {
                    formDataCache[name] = value;
                }
                // Trigger auto-save
                saveDataDebounced();
            });
            
            // Lưu khi blur (rời khỏi field)
            input.addEventListener('blur', function() {
                const name = this.name || this.id || '';
                const value = this.value;
                if (name && value) {
                    formDataCache[name] = value;
                }
                // Save ngay khi blur
                saveDataDebounced();
            });
        });
        
        // Lưu khi form sắp submit (beforeunload hoặc visibility change)
        window.addEventListener('beforeunload', function() {
            const data = extractFormData();
            if (data && Object.keys(data).length > 0) {
                // Gửi synchronous request nếu có thể
                navigator.sendBeacon('{{ route("contact.save.roadmap") }}', 
                    new FormData(Object.keys(data).reduce((fd, key) => {
                        fd.append(key, data[key]);
                        return fd;
                    }, new FormData()))
                );
            }
        });
    }

    // Override form.submit() method để intercept
    function overrideFormSubmit(form) {
        const originalSubmit = form.submit.bind(form);
        
        // Override submit method
        form.submit = function() {
            console.log('=== form.submit() CALLED - BLOCKING ===');
            
            // BLOCK hoàn toàn - không cho submit
            if (formSubmitBlocked) {
                console.log('Form submit already blocked, ignoring...');
                return;
            }
            
            formSubmitBlocked = true;
            
            // Lấy dữ liệu
            let data = extractFormData();
            
            // Nếu không lấy được, thử từ form trực tiếp
            if (!data || Object.keys(data).length === 0) {
                const formData = new FormData(form);
                data = {};
                for (let [key, value] of formData.entries()) {
                    if (!key.includes('_token') && !key.includes('csrf') && !key.includes('__')) {
                        data[key] = value;
                    }
                }
            }
            
            console.log('=== EXTRACTED DATA FOR SAVING ===');
            console.log('Data:', data);
            
            // Gửi dữ liệu về server TRƯỚC - ĐỢI HOÀN THÀNH
            console.log('=== WAITING FOR AJAX TO COMPLETE ===');
            saveContactData(data).then(() => {
                console.log('=== AJAX COMPLETED - ALLOWING FORM SUBMIT ===');
                // Restore và gọi original submit
                form.submit = originalSubmit;
                formSubmitBlocked = false;
                ajaxCompleted = true;
                // Đợi thêm 100ms để đảm bảo
                setTimeout(() => {
                    form.submit();
                }, 100);
            }).catch((error) => {
                console.error('=== AJAX ERROR - BUT ALLOWING SUBMIT ===');
                console.error('Error:', error);
                // Nếu lỗi, vẫn cho submit sau 2 giây
                setTimeout(() => {
                    form.submit = originalSubmit;
                    formSubmitBlocked = false;
                    form.submit();
                }, 2000);
            });
        };
    }

    // Lắng nghe sự kiện submit của form
    function setupFormListener(form) {
        console.log('=== SETTING UP FORM LISTENER ===');
        
        // Override form.submit() method
        overrideFormSubmit(form);
        
        // Lưu dữ liệu hiện tại từ form
        const formData = new FormData(form);
        for (let [key, value] of formData.entries()) {
            if (!key.includes('_token') && !key.includes('csrf') && !key.includes('__')) {
                formDataCache[key] = value;
            }
        }

        // Lắng nghe submit với capture phase (bắt sớm nhất) - MULTIPLE LISTENERS
        const submitHandler1 = function(e) {
            console.log('=== FORM SUBMIT EVENT DETECTED (Handler 1) ===');
            
            if (ajaxCompleted) {
                console.log('AJAX already completed, allowing submit');
                return; // Cho phép submit
            }
            
            if (globalSubmitBlocked) {
                console.log('Submit already blocked globally');
                e.preventDefault();
                e.stopImmediatePropagation();
                e.stopPropagation();
                return false;
            }
            
            console.log('BLOCKING form submit...');
            
            // Prevent default
            e.preventDefault();
            e.stopImmediatePropagation();
            e.stopPropagation();
            
            globalSubmitBlocked = true;
            isSubmitting = true;

            // Lấy dữ liệu
            let data = extractFormData();
            
            // Nếu không lấy được, thử từ form
            if (!data || Object.keys(data).length === 0) {
                data = {};
                const formData = new FormData(form);
                for (let [key, value] of formData.entries()) {
                    if (!key.includes('_token') && !key.includes('csrf') && !key.includes('__')) {
                        data[key] = value;
                    }
                }
            }
            
            // Bổ sung từ cache
            Object.keys(formDataCache).forEach(key => {
                if (!data[key] && formDataCache[key]) {
                    data[key] = formDataCache[key];
                }
            });

            console.log('=== FORM DATA TO SAVE ===');
            console.log('Data:', data);

            // Gửi dữ liệu về server và đợi hoàn thành
            saveContactData(data).then(() => {
                console.log('=== DATA SAVED - ALLOWING FORM SUBMIT ===');
                // Remove listener để tránh loop
                form.removeEventListener('submit', submitHandler1, true);
                form.removeEventListener('submit', submitHandler2, true);
                // Submit form thực sự
                isSubmitting = false;
                globalSubmitBlocked = false;
                ajaxCompleted = true;
                
                // Đợi 100ms rồi submit
                setTimeout(() => {
                    const originalSubmit = HTMLFormElement.prototype.submit;
                    originalSubmit.call(form);
                }, 100);
            }).catch((error) => {
                console.error('=== ERROR SAVING - BUT ALLOWING SUBMIT ===', error);
                // Nếu có lỗi, vẫn cho submit form sau 2 giây
                form.removeEventListener('submit', submitHandler1, true);
                form.removeEventListener('submit', submitHandler2, true);
                isSubmitting = false;
                globalSubmitBlocked = false;
                ajaxCompleted = true;
                
                setTimeout(() => {
                    const originalSubmit = HTMLFormElement.prototype.submit;
                    originalSubmit.call(form);
                }, 2000);
            });
            
            return false;
        };
        
        const submitHandler2 = function(e) {
            console.log('=== FORM SUBMIT EVENT DETECTED (Handler 2 - Backup) ===');
            if (!ajaxCompleted && !globalSubmitBlocked) {
                submitHandler1(e);
            }
        };
        
        // Thêm nhiều listener để chắc chắn bắt được
        form.addEventListener('submit', submitHandler1, true); // Capture
        form.addEventListener('submit', submitHandler2, true); // Backup
        form.addEventListener('submit', submitHandler1, false); // Bubble
        form.addEventListener('submit', submitHandler2, false); // Backup bubble

        // Lắng nghe click vào submit button
        const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"], button:not([type]), [role="button"], button');
        console.log('Found submit buttons:', submitButtons.length);
        
        submitButtons.forEach((btn, index) => {
            console.log(`Setting up listener for button ${index}:`, btn);
            
            const clickHandler = function(e) {
                console.log(`=== SUBMIT BUTTON ${index} CLICKED ===`);
                
                if (ajaxCompleted) {
                    console.log('AJAX completed, allowing click');
                    return; // Cho phép
                }
                
                if (globalSubmitBlocked) {
                    console.log('Submit blocked, preventing click');
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    e.stopPropagation();
                    return false;
                }
                
                console.log('BLOCKING button click...');
                e.preventDefault();
                e.stopImmediatePropagation();
                e.stopPropagation();
                
                globalSubmitBlocked = true;
                
                // Lưu dữ liệu
                const formData = new FormData(form);
                for (let [key, value] of formData.entries()) {
                    if (!key.includes('_token') && !key.includes('csrf') && !key.includes('__')) {
                        formDataCache[key] = value;
                    }
                }
                
                const data = extractFormData();
                
                saveContactData(data).then(() => {
                    console.log('=== DATA SAVED - ALLOWING BUTTON CLICK ===');
                    btn.removeEventListener('click', clickHandler, true);
                    globalSubmitBlocked = false;
                    ajaxCompleted = true;
                    setTimeout(() => {
                        btn.click();
                    }, 100);
                }).catch((error) => {
                    console.error('=== ERROR SAVING - BUT ALLOWING CLICK ===', error);
                    btn.removeEventListener('click', clickHandler, true);
                    globalSubmitBlocked = false;
                    ajaxCompleted = true;
                    setTimeout(() => {
                        btn.click();
                    }, 2000);
                });
                
                return false;
            };
            
            btn.addEventListener('click', clickHandler, true); // Capture
            btn.addEventListener('click', clickHandler, false); // Bubble
        });
    }

    // Gửi dữ liệu về server - trả về Promise
    function saveContactData(data) {
        return new Promise((resolve, reject) => {
            console.log('=== START SAVING CONTACT DATA ===');
            console.log('Raw data received:', data);
            
            const formData = new FormData();
            
            // Map các trường có thể có - lấy tất cả các key có thể
            const nameFields = ['name', 'fullname', 'ho_ten', 'ho_va_ten', 'full_name', 'ten'];
            const phoneFields = ['phone', 'sdt', 'so_dien_thoai', 'phone_number', 'dien_thoai', 'tel'];
            const emailFields = ['email', 'e_mail'];
            const addressFields = ['address', 'dia_chi', 'diachi'];
            const messageFields = ['message', 'mo_ta', 'description', 'ghi_chu', 'note', 'notes'];
            const majorFields = ['major_id', 'nganh_hoc', 'major', 'nganh'];
            
            // Tìm và map name
            for (let field of nameFields) {
                if (data[field]) {
                    formData.append('name', data[field]);
                    console.log('Found name field:', field, '=', data[field]);
                    break;
                }
            }
            
            // Tìm và map phone
            for (let field of phoneFields) {
                if (data[field]) {
                    formData.append('phone', data[field]);
                    console.log('Found phone field:', field, '=', data[field]);
                    break;
                }
            }
            
            // Tìm và map email
            for (let field of emailFields) {
                if (data[field]) {
                    formData.append('email', data[field]);
                    console.log('Found email field:', field, '=', data[field]);
                    break;
                }
            }
            
            // Tìm và map address
            for (let field of addressFields) {
                if (data[field]) {
                    formData.append('address', data[field]);
                    console.log('Found address field:', field, '=', data[field]);
                    break;
                }
            }
            
            // Tìm và map message
            for (let field of messageFields) {
                if (data[field]) {
                    formData.append('message', data[field]);
                    console.log('Found message field:', field, '=', data[field]);
                    break;
                }
            }
            
            // Tìm và map major
            for (let field of majorFields) {
                if (data[field]) {
                    formData.append('major_id', data[field]);
                    console.log('Found major field:', field, '=', data[field]);
                    break;
                }
            }
            
            // Nếu không tìm thấy name/phone từ các field đã biết, thử lấy tất cả
            if (!formData.has('name') && !formData.has('phone')) {
                // Lấy field đầu tiên có vẻ là name
                const keys = Object.keys(data);
                for (let key of keys) {
                    const value = data[key];
                    if (value && typeof value === 'string' && value.length > 0) {
                        // Nếu key chứa 'name' hoặc 'ten'
                        if (key.toLowerCase().includes('name') || key.toLowerCase().includes('ten')) {
                            formData.append('name', value);
                            console.log('Auto-detected name from field:', key, '=', value);
                            break;
                        }
                        // Nếu key chứa 'phone' hoặc 'sdt'
                        if (key.toLowerCase().includes('phone') || key.toLowerCase().includes('sdt') || key.toLowerCase().includes('dien_thoai')) {
                            formData.append('phone', value);
                            console.log('Auto-detected phone from field:', key, '=', value);
                            break;
                        }
                    }
                }
            }

            // Kiểm tra có dữ liệu không
            if (!formData.has('name') && !formData.has('phone')) {
                console.warn('=== NO DATA TO SAVE ===');
                console.warn('Available fields in data:', Object.keys(data));
                resolve({ message: 'no_data' });
                return;
            }

            console.log('=== SENDING AJAX REQUEST ===');
            console.log('URL: {{ route("contact.save.roadmap") }}');
            console.log('FormData entries:');
            for (let [key, value] of formData.entries()) {
                console.log('  ', key, ':', value);
            }

            // Gửi AJAX với timeout
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 seconds timeout
            
            fetch('{{ route("contact.save.roadmap") }}', {
                method: 'POST',
                body: formData,
                signal: controller.signal,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => {
                clearTimeout(timeoutId);
                console.log('=== AJAX RESPONSE RECEIVED ===');
                console.log('Status:', response.status, response.statusText);
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(result => {
                console.log('=== CONTACT DATA SAVED SUCCESSFULLY ===');
                console.log('Result:', result);
                ajaxCompleted = true;
                resolve(result);
            })
            .catch(error => {
                clearTimeout(timeoutId);
                console.error('=== ERROR SAVING CONTACT DATA ===');
                console.error('Error:', error);
                if (error.name === 'AbortError') {
                    console.error('Request timeout after 10 seconds');
                }
                reject(error);
            });
        });
    }

    // Khởi tạo listener khi modal mở
    const modal = document.getElementById('download-roadmap-modal');
    if (modal) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    if (modal.classList.contains('uk-open')) {
                        setTimeout(() => {
                            formDataCache = {}; // Reset cache
                            setupFormioListener();
                        }, 1000);
                    }
                }
            });
        });

        observer.observe(modal, {
            attributes: true,
            attributeFilter: ['class']
        });

        if (modal.classList.contains('uk-open')) {
            setTimeout(setupFormioListener, 1000);
        }
    }

    // UIkit modal event
    if (typeof UIkit !== 'undefined' && UIkit.modal) {
        UIkit.util.on('#download-roadmap-modal', 'show', function() {
            formDataCache = {}; // Reset cache
            ajaxCompleted = false;
            globalSubmitBlocked = false;
            setTimeout(setupFormioListener, 1000);
        });
    }
    
    // TEST BUTTON - XÓA SAU KHI TEST XONG
    const testButton = document.getElementById('test-ajax-button');
    if (testButton) {
        testButton.addEventListener('click', function() {
            console.log('=== TEST BUTTON CLICKED ===');
            const data = extractFormData();
            console.log('Extracted data:', data);
            
            if (data && Object.keys(data).length > 0) {
                console.log('=== TESTING AJAX REQUEST ===');
                saveContactData(data).then((result) => {
                    console.log('=== TEST SUCCESS ===');
                    console.log('Result:', result);
                    alert('✅ TEST THÀNH CÔNG! Dữ liệu đã được lưu vào database. Kiểm tra bảng contacts.');
                }).catch((error) => {
                    console.error('=== TEST FAILED ===');
                    console.error('Error:', error);
                    alert('❌ TEST THẤT BẠI! Kiểm tra console để xem lỗi.');
                });
            } else {
                console.warn('=== NO DATA TO TEST ===');
                alert('⚠️ Không tìm thấy dữ liệu trong form. Hãy điền form trước.');
            }
        });
    }
});
</script>


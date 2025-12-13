@extends('frontend.homepage.layout')
@section('content')
    <!-- Breadcrumb Section -->
    <div class="page-breadcrumb-large">
        <div class="breadcrumb-overlay"></div>
        <div class="uk-container uk-container-center">
            <div class="breadcrumb-content">
                <h1 class="breadcrumb-title">Liên hệ với chúng tôi</h1>
                <div class="breadcrumb-description">
                    <p>Bạn đang có những thắc mắc, nan giải về khóa học hãy chia sẻ vấn đề với chúng tôi</p>
                </div>
                <nav class="breadcrumb-nav">
                    <ul class="breadcrumb-list">
                        <li><a href="{{ config('app.url') }}">Trang chủ</a></li>
                        <li>
                            <span class="breadcrumb-separator">/</span>
                            <span>Liên hệ</span>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Contact Page Content -->
    <div class="panel-contact-page">
        <div class="uk-container uk-container-center">
            <div class="uk-grid uk-grid-medium" data-uk-grid-match>
                <!-- Left Column: Contact Form -->
                <div class="uk-width-large-1-2">
                    <div class="contact-form-wrapper">
                        <h2 class="contact-form-title">Liên hệ ngay</h2>
                        @if(isset($system['text_12']) && $system['text_12'])
                            <p class="contact-form-description">{{ $system['text_12'] }}</p>
                        @endif
                        <form id="contact-form" action="{{ route('contact.save') }}" method="POST" class="contact-form-content">
                            @csrf
                            <div class="form-group">
                                <label for="name">Họ tên <span class="required">*</span></label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Nguyễn Văn A" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="email@example.com">
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Số điện thoại <span class="required">*</span></label>
                                <input type="tel" id="phone" name="phone" class="form-control" placeholder="0901234567" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="content">Tiêu đề</label>
                                <input type="text" id="content" name="content" class="form-control" placeholder="Tôi cần hỗ trợ về...">
                            </div>
                            
                            <div class="form-group">
                                <label for="description">Nội dung cần hỗ trợ</label>
                                <textarea name="description" id="description" class="form-control" rows="6" placeholder="Nhập nội dung cần hỗ trợ..."></textarea>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" class="contact-submit-btn">
                                    <i class="fa fa-paper-plane"></i>
                                    Gửi liên hệ
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right Column: Contact Info & Map -->
                <div class="uk-width-large-1-2">
                    <div class="contact-info-wrapper">
                        <h2 class="contact-info-title">Thông tin liên hệ</h2>
                        
                        <div class="contact-info-list">
                            @if(isset($system['contact_address']) && $system['contact_address'])
                                <div class="contact-info-item">
                                    <i class="fa fa-map-marker contact-icon contact-icon-address"></i>
                                    <div class="contact-info-content">
                                        <span class="contact-label">Địa chỉ:</span>
                                        <span class="contact-text">{{ $system['contact_address'] }}</span>
                                    </div>
                                </div>
                            @endif
                            
                            @if(isset($system['contact_hotline']) && $system['contact_hotline'])
                                <div class="contact-info-item">
                                    <i class="fa fa-phone contact-icon contact-icon-phone"></i>
                                    <div class="contact-info-content">
                                        <span class="contact-label">Hotline:</span>
                                        <a href="tel:{{ $system['contact_hotline'] }}" class="contact-link">{{ $system['contact_hotline'] }}</a>
                                    </div>
                                </div>
                            @endif
                            
                            @if(isset($system['contact_email']) && $system['contact_email'])
                                <div class="contact-info-item">
                                    <i class="fa fa-envelope contact-icon contact-icon-email"></i>
                                    <div class="contact-info-content">
                                        <span class="contact-label">Email:</span>
                                        <a href="mailto:{{ $system['contact_email'] }}" class="contact-link">{{ $system['contact_email'] }}</a>
                                    </div>
                                </div>
                            @endif
                            
                            @if(isset($system['contact_website']) && $system['contact_website'])
                                <div class="contact-info-item">
                                    <i class="fa fa-globe contact-icon contact-icon-website"></i>
                                    <div class="contact-info-content">
                                        <span class="contact-label">Website:</span>
                                        <a href="{{ $system['contact_website'] }}" target="_blank" class="contact-link">{{ $system['contact_website'] }}</a>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="working-hours-section">
                            <h3 class="working-hours-title">Thời gian làm việc</h3>
                            <ul class="working-hours-list">
                                <li>
                                    <strong>Thứ 2 - thứ 6</strong>
                                    <span>8h00 - 17h00</span>
                                </li>
                                <li>
                                    <strong>Thứ 7</strong>
                                    <span>8h00 - 12h00</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    @if(isset($system['contact_map']) && $system['contact_map'])
                        <div class="contact-map-wrapper">
                            <div class="map-container">
                                {!! $system['contact_map'] !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('contact-form');
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(form);
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Đang gửi...';
                
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => Promise.reject(err));
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.message === 'success') {
                        // Redirect sang trang cảm ơn
                        window.location.href = '{{ route("contact.thankyou") }}';
                    } else {
                        alert('Có lỗi xảy ra. Vui lòng thử lại.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (error.errors) {
                        // Hiển thị lỗi validation
                        let errorMessages = [];
                        if (error.errors.name) {
                            errorMessages.push(error.errors.name[0]);
                        }
                        if (error.errors.phone) {
                            errorMessages.push(error.errors.phone[0]);
                        }
                        if (error.errors.email) {
                            errorMessages.push(error.errors.email[0]);
                        }
                        alert(errorMessages.length > 0 ? errorMessages.join('\n') : 'Vui lòng kiểm tra lại thông tin đã nhập.');
                    } else {
                        alert('Có lỗi xảy ra. Vui lòng thử lại.');
                    }
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
            });
        }
    });
    </script>
@endsection


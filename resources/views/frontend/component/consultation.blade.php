<div class="panel-consultation wow fadeInUp" data-wow-delay="0.35s">
    <div class="uk-container uk-container-center">
        <div class="consultation-wrapper">
            <div class="consultation-content">
                <h2 class="consultation-title">Cần tư vấn thêm?</h2>
                <p class="consultation-description">Liên hệ ngay với chúng tôi để được tư vấn miễn phí về chương trình đào tạo từ xa phù hợp</p>
                
                <div class="consultation-buttons">
                    <a href="tel:{{ $system['contact_hotline'] ?? '01234567899' }}" class="consultation-btn consultation-btn-call">
                        <i class="fa fa-phone"></i>
                        <span>Gọi ngay: {{ $system['contact_hotline'] ?? '01234567899' }}</span>
                    </a>
                    <a href="#consultation-modal" class="consultation-btn consultation-btn-register" data-uk-modal>
                        <i class="fa fa-envelope"></i>
                        <span>Đăng ký tư vấn</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


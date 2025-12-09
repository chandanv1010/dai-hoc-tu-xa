<div class="panel-register-banner wow fadeInUp" data-wow-delay="0.25s">
    <div class="uk-container uk-container-center">
        <div class="register-banner-content">
            <h2 class="register-banner-title">Bắt Đầu Hành Trình Thay Đổi Cuộc Đời</h2>
            <p class="register-banner-subtitle">Đừng để thời gian trôi qua, hãy đầu tư cho tương lai của chính bạn ngay hôm nay!</p>
            <a href="#consultation-modal" class="register-banner-btn" data-uk-modal>
                <i class="fa fa-rocket"></i>
                <span>Đăng Ký Ngay</span>
            </a>
        </div>
    </div>
</div>

<!-- Register Modal -->
<div id="register-modal" class="uk-modal">
    <div class="uk-modal-dialog register-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
        <div class="uk-modal-header">
            <h2 class="uk-modal-title">Đăng Ký Tư Vấn</h2>
        </div>
        <form id="register-form" action="{{ route('contact.save') }}" method="POST">
            @csrf
            <div class="uk-modal-body">
                <div class="uk-form-row">
                    <label class="uk-form-label" for="name">Họ và tên <span class="required">*</span></label>
                    <div class="uk-form-controls">
                        <input type="text" name="name" id="name" class="uk-width-1-1" required>
                    </div>
                </div>
                
                <div class="uk-form-row">
                    <label class="uk-form-label" for="phone">Số điện thoại <span class="required">*</span></label>
                    <div class="uk-form-controls">
                        <input type="tel" name="phone" id="phone" class="uk-width-1-1" required>
                    </div>
                </div>
                
                <div class="uk-form-row">
                    <label class="uk-form-label" for="email">Email <span class="required">*</span></label>
                    <div class="uk-form-controls">
                        <input type="email" name="email" id="email" class="uk-width-1-1" required>
                    </div>
                </div>
                
                <div class="uk-form-row">
                    <label class="uk-form-label" for="address">Địa chỉ</label>
                    <div class="uk-form-controls">
                        <input type="text" name="address" id="address" class="uk-width-1-1">
                    </div>
                </div>
                
                <div class="uk-form-row">
                    <label class="uk-form-label" for="message">Lời nhắn</label>
                    <div class="uk-form-controls">
                        <textarea name="message" id="message" class="uk-width-1-1" rows="4"></textarea>
                    </div>
                </div>
            </div>
            <div class="uk-modal-footer uk-text-right">
                <button type="button" class="uk-button uk-button-default uk-modal-close">Hủy</button>
                <button type="submit" class="uk-button uk-button-primary">Gửi</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('register-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Đang gửi...';
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.message === 'success') {
                    UIkit.modal('#register-modal').hide();
                    alert('Đăng ký thành công! Chúng tôi sẽ liên hệ lại với bạn trong thời gian sớm nhất.');
                    form.reset();
                } else {
                    alert('Có lỗi xảy ra. Vui lòng thử lại.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            });
        });
    }
});
</script>


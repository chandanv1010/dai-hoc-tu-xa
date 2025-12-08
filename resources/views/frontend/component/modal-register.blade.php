@php
    // Lấy tên khóa học từ biến được truyền vào
    $courseName = $name ?? 'khóa học';
@endphp

<!-- Register Modal -->
<div id="modal-register" class="uk-modal download-roadmap-modal">
    <div class="uk-modal-dialog download-roadmap-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
        
        <!-- Header với màu cam -->
        <div class="download-roadmap-header">
            <div class="download-roadmap-description">Đăng ký ngay để nhận ưu đãi đặc biệt</div>
            <h2 class="download-roadmap-title">ĐĂNG KÝ {{ strtoupper($courseName) }}</h2>
        </div>
        
        <!-- Wrapper cho form đăng ký -->
        <div class="download-roadmap-form-wrapper">
            <div class="download-roadmap-script-wrapper">
                <form action="{{ route('contact.save.roadmap') }}" method="POST" class="register-form" id="register-course-form">
                    @csrf
                    <input type="hidden" name="course_name" value="{{ $courseName }}">
                    
                    <div class="form-group">
                        <label for="register-name">Họ tên <span class="required">*</span></label>
                        <input type="text" id="register-name" name="name" class="form-control" placeholder="Nhập họ và tên" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="register-phone">Số điện thoại <span class="required">*</span></label>
                        <input type="tel" id="register-phone" name="phone" class="form-control" placeholder="Nhập số điện thoại" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="register-email">Email</label>
                        <input type="email" id="register-email" name="email" class="form-control" placeholder="Nhập email (không bắt buộc)">
                    </div>
                    
                    <div class="form-group">
                        <label for="register-note">Ghi chú</label>
                        <textarea id="register-note" name="description" class="form-control" rows="3" placeholder="Nhập ghi chú (không bắt buộc)"></textarea>
                    </div>
                    
                    <button type="submit" class="btn-submit">Đăng ký ngay</button>
                </form>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="download-roadmap-footer">
            <p>Hotline: <strong>{{ $system['contact_phone'] ?? '0971.746.845' }}</strong></p>
            <p>Email: <strong>{{ $system['contact_email'] ?? '[email protected]' }}</strong></p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Highlight số trong footer (màu cam)
    const footer = document.querySelector('#modal-register .download-roadmap-footer');
    if (footer) {
        const text = footer.innerHTML;
        footer.innerHTML = text.replace(/(\d+)/g, '<span style="color: #FF8C00; font-weight: 700;">$1</span>');
    }
    
    // Xử lý submit form
    const form = document.getElementById('register-course-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const submitBtn = form.querySelector('.btn-submit');
            const originalText = submitBtn.textContent;
            
            // Disable button và hiển thị loading
            submitBtn.disabled = true;
            submitBtn.textContent = 'Đang xử lý...';
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Đăng ký thành công! Chúng tôi sẽ liên hệ với bạn sớm nhất.');
                    UIkit.modal('#modal-register').hide();
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


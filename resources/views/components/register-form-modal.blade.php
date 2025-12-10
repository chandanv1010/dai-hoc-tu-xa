@php
    // Lấy dữ liệu từ System config - form tải lộ trình
    $formTitle = $system['form_tai_lo_trinh_title'] ?? 'NHẬN LỘ TRÌNH ĐÀO TẠO CHI TIẾT';
    $formDescription = $system['form_tai_lo_trinh_description'] ?? '(Bao gồm: Khung chương trình, thời gian đào tạo, số lượng tín chỉ từng môn, học phí ...)';
    $formFooter = $system['form_tai_lo_trinh_footer'] ?? 'Còn 10 chỉ tiêu';
    $formScript = $system['form_tai_lo_trinh_script'] ?? '';
@endphp

{{-- Sử dụng FormModal Component --}}
<x-form-modal
    :title="$formTitle"
    :description="$formDescription"
    :script="$formScript"
    :footer="$formFooter"
    modal-id="register-form-modal"
    modal-class="download-roadmap-modal"
/>

<script>
function openRegisterModal() {
    if (typeof UIkit !== 'undefined') {
        UIkit.modal('#register-form-modal').show();
    } else {
        // Fallback nếu UIkit chưa load
        const modal = document.getElementById('register-form-modal');
        if (modal) {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
    }
}
</script>


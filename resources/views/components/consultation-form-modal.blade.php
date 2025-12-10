@php
    // Lấy dữ liệu từ System config - form tư vấn miễn phí
    $formTitle = $system['form_tu_van_mien_phi_title'] ?? 'ĐĂNG KÝ NHẬN TƯ VẤN MIỄN PHÍ NGAY';
    $formDescription = $system['form_tu_van_mien_phi_description'] ?? 'Cơ hội sở hữu bằng ĐH chỉ từ 2-4 năm';
    $formFooter = $system['form_tu_van_mien_phi_footer'] ?? 'Còn 10 chỉ tiêu tuyển sinh năm 2025';
    $formScript = $system['form_tu_van_mien_phi_script'] ?? '';
@endphp

{{-- Sử dụng FormModal Component --}}
<x-form-modal
    :title="$formTitle"
    :description="$formDescription"
    :script="$formScript"
    :footer="$formFooter"
    modal-id="consultation-form-modal"
    modal-class="download-roadmap-modal"
/>

<script>
function openConsultationModal() {
    if (typeof UIkit !== 'undefined') {
        UIkit.modal('#consultation-form-modal').show();
    } else {
        // Fallback nếu UIkit chưa load
        const modal = document.getElementById('consultation-form-modal');
        if (modal) {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
    }
}
</script>


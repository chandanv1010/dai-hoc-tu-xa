@php
    // Lấy dữ liệu từ System config - form tư vấn miễn phí
    $formTitle = $system['form_tu_van_mien_phi_title'] ?? 'ĐĂNG KÝ NHẬN TƯ VẤN MIỄN PHÍ NGAY';
    $formDescription = $system['form_tu_van_mien_phi_description'] ?? 'Cơ hội sở hữu bằng ĐH chỉ từ 2-4 năm';
    $formFooter = $system['form_tu_van_mien_phi_footer'] ?? 'Còn 10 chỉ tiêu tuyển sinh năm 2025';
    $formScript = $system['form_tu_van_mien_phi_script'] ?? '';
@endphp

<!-- Consultation Modal -->
<div id="consultation-modal" class="uk-modal download-roadmap-modal">
    <div class="uk-modal-dialog download-roadmap-modal-dialog">
        <a class="uk-modal-close uk-close"></a>
        
        <!-- Header với màu cam -->
        <div class="download-roadmap-header">
            <div class="download-roadmap-description">{{ $formDescription }}</div>
            <h2 class="download-roadmap-title">{{ $formTitle }}</h2>
        </div>
        
        <!-- Wrapper cho script nhúng (khung màu đỏ) -->
        <div class="download-roadmap-form-wrapper">
            <div class="download-roadmap-script-wrapper consultation-script-wrapper">
                {!! $formScript !!}
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
    const footer = document.querySelector('#consultation-modal .download-roadmap-footer');
    if (footer) {
        const text = footer.innerHTML;
        footer.innerHTML = text.replace(/(\d+)/g, '<span style="color: #FF8C00; font-weight: 700;">$1</span>');
    }
});
</script>


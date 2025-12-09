@php
    // Lấy dữ liệu từ System config
    $formTitle = $system['form_tai_lo_trinh_title'] ?? 'ĐĂNG KÝ NHẬN TƯ VẤN MIỄN PHÍ NGAY';
    $formDescription = $system['form_tai_lo_trinh_description'] ?? 'Cơ hội sở hữu bằng ĐH chỉ từ 2-4 năm';
    $formFooter = $system['form_tai_lo_trinh_footer'] ?? 'Còn 10 chỉ tiêu tuyển sinh năm 2025';
    $formScript = $system['form_tai_lo_trinh_script'] ?? '';
@endphp

{{-- Sử dụng FormModal Component --}}
<x-form-modal
    :title="$formTitle"
    :description="$formDescription"
    :script="$formScript"
    :footer="$formFooter"
    modal-id="download-roadmap-modal"
    modal-class="download-roadmap-modal"
/>

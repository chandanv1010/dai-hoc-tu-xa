@php
    // Lấy dữ liệu từ System config - form học thử miễn phí
    $formTitle = $system['form_hoc_thu_title'] ?? 'ĐĂNG KÝ HỌC THỬ MIỄN PHÍ';
    $formDescription = $system['form_hoc_thu_description'] ?? 'Trải nghiệm khóa học miễn phí ngay hôm nay';
    $formFooter = $system['form_hoc_thu_footer'] ?? 'Còn 10 suất học thử miễn phí';
    $formScript = $system['form_hoc_thu_script'] ?? '';
@endphp

{{-- Sử dụng FormModal Component --}}
<x-form-modal
    :title="$formTitle"
    :description="$formDescription"
    :script="$formScript"
    :footer="$formFooter"
    modal-id="hoc-thu-modal"
    modal-class="download-roadmap-modal"
/>


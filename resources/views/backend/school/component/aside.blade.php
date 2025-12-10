@include('backend.dashboard.component.publish', ['model' => ($school) ?? null, 'hideImage' => false])

<div class="ibox w">
    <div class="ibox-title">
        <h5>Ký hiệu trường</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="" class="control-label text-left">Ký hiệu (VD: NEU, HOU, TNU, AOF)</label>
                    <input 
                        type="text" 
                        name="short_name" 
                        class="form-control" 
                        value="{{ old('short_name', ($school->short_name ?? '') ?? '') }}" 
                        placeholder="Nhập ký hiệu trường"
                        maxlength="50"
                        autocomplete="off"
                    >
                </div>
            </div>
        </div>
    </div>
</div>

@php
    // Lấy dữ liệu từ old() hoặc từ database - nếu là array thì lấy phần tử đầu tiên, nếu là string thì lấy trực tiếp
    $graduationSystem = old('graduation_system', isset($school) && $school->graduation_system ? $school->graduation_system : '');
    $examLocation = old('exam_location', isset($school) && $school->exam_location ? $school->exam_location : '');
    
    // Nếu là array, lấy phần tử đầu tiên hoặc convert thành string
    if (is_array($graduationSystem)) {
        $graduationSystem = !empty($graduationSystem) ? $graduationSystem[0] : '';
    }
    if (is_array($examLocation)) {
        $examLocation = !empty($examLocation) ? $examLocation[0] : '';
    }
    
    // Đảm bảo là string
    $graduationSystem = is_string($graduationSystem) ? $graduationSystem : '';
    $examLocation = is_string($examLocation) ? $examLocation : '';
@endphp

{{-- Bộ Lọc --}}
<div class="ibox w">
    <div class="ibox-title">
        <h5>Bộ Lọc</h5>
    </div>
    <div class="ibox-content">
        {{-- Hệ Thống Tốt Nghiệp --}}
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="graduation_system" class="control-label text-left">Hệ Thống Tốt Nghiệp</label>
                    <input 
                        type="text" 
                        id="graduation_system" 
                        name="graduation_system" 
                        class="form-control" 
                        value="{{ old('graduation_system', $graduationSystem) }}"
                        placeholder="Nhập hệ thống tốt nghiệp (VD: Hệ thống tốt nghiệp đại học từ xa)"
                        autocomplete="off"
                    >
                </div>
            </div>
        </div>
        
        {{-- Địa Điểm Thi --}}
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="exam_location" class="control-label text-left">Địa Điểm Thi</label>
                    <input 
                        type="text" 
                        id="exam_location" 
                        name="exam_location" 
                        class="form-control" 
                        value="{{ old('exam_location', $examLocation) }}"
                        placeholder="Nhập địa điểm thi (VD: Hà Nội, Đà Nẵng, Hồ Chí Minh)"
                        autocomplete="off"
                    >
                </div>
            </div>
        </div>
    </div>
</div>

@php
    // Xử lý form_tai_lo_trinh_hoc
    $formTaiLoTrinhHocFromOld = old('form_tai_lo_trinh_hoc');
    if ($formTaiLoTrinhHocFromOld === null && isset($school)) {
        $formTaiLoTrinhHocFromOld = $school->form_tai_lo_trinh_hoc;
    }
    $formTaiLoTrinhHoc = is_array($formTaiLoTrinhHocFromOld) ? $formTaiLoTrinhHocFromOld : [];
    
    // Xử lý form_tu_van_mien_phi
    $formTuVanMienPhiFromOld = old('form_tu_van_mien_phi');
    if ($formTuVanMienPhiFromOld === null && isset($school)) {
        $formTuVanMienPhiFromOld = $school->form_tu_van_mien_phi;
    }
    $formTuVanMienPhi = is_array($formTuVanMienPhiFromOld) ? $formTuVanMienPhiFromOld : [];
    
    // Xử lý form_hoc_thu
    $formHocThuFromOld = old('form_hoc_thu');
    if ($formHocThuFromOld === null && isset($school)) {
        $formHocThuFromOld = $school->form_hoc_thu;
    }
    $formHocThu = is_array($formHocThuFromOld) ? $formHocThuFromOld : [];
    
    // Lấy giá trị từ old() hoặc từ array
    $formTaiLoTrinhHocTitle = old('form_tai_lo_trinh_hoc.title', $formTaiLoTrinhHoc['title'] ?? '');
    $formTaiLoTrinhHocDescription = old('form_tai_lo_trinh_hoc.description', $formTaiLoTrinhHoc['description'] ?? '');
    $formTaiLoTrinhHocScript = old('form_tai_lo_trinh_hoc.script', $formTaiLoTrinhHoc['script'] ?? '');
    $formTaiLoTrinhHocFooter = old('form_tai_lo_trinh_hoc.footer', $formTaiLoTrinhHoc['footer'] ?? '');
    
    $formTuVanMienPhiTitle = old('form_tu_van_mien_phi.title', $formTuVanMienPhi['title'] ?? '');
    $formTuVanMienPhiDescription = old('form_tu_van_mien_phi.description', $formTuVanMienPhi['description'] ?? '');
    $formTuVanMienPhiScript = old('form_tu_van_mien_phi.script', $formTuVanMienPhi['script'] ?? '');
    $formTuVanMienPhiFooter = old('form_tu_van_mien_phi.footer', $formTuVanMienPhi['footer'] ?? '');
    
    $formHocThuTitle = old('form_hoc_thu.title', $formHocThu['title'] ?? '');
    $formHocThuDescription = old('form_hoc_thu.description', $formHocThu['description'] ?? '');
    $formHocThuScript = old('form_hoc_thu.script', $formHocThu['script'] ?? '');
    $formHocThuFooter = old('form_hoc_thu.footer', $formHocThu['footer'] ?? '');
@endphp

{{-- Form Tải Lộ Trình Học --}}
<div class="ibox w">
    <div class="ibox-title">
        <h5>Cấu Hình Form Tải Lộ Trình Học</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="form_tai_lo_trinh_hoc_title" class="control-label text-left">Tiêu đề Form</label>
                    <input 
                        type="text" 
                        id="form_tai_lo_trinh_hoc_title" 
                        name="form_tai_lo_trinh_hoc[title]" 
                        class="form-control" 
                        value="{{ $formTaiLoTrinhHocTitle }}"
                        placeholder="Nhập tiêu đề form"
                        autocomplete="off"
                    >
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="form_tai_lo_trinh_hoc_description" class="control-label text-left">Mô tả</label>
                    <textarea 
                        id="form_tai_lo_trinh_hoc_description" 
                        name="form_tai_lo_trinh_hoc[description]" 
                        class="form-control" 
                        rows="3"
                        placeholder="Nhập mô tả"
                    >{{ $formTaiLoTrinhHocDescription }}</textarea>
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="form_tai_lo_trinh_hoc_script" class="control-label text-left">Mã Nhúng</label>
                    <textarea 
                        id="form_tai_lo_trinh_hoc_script" 
                        name="form_tai_lo_trinh_hoc[script]" 
                        class="form-control" 
                        rows="5"
                        placeholder="Nhập mã nhúng form"
                    >{{ $formTaiLoTrinhHocScript }}</textarea>
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="form_tai_lo_trinh_hoc_footer" class="control-label text-left">Footer (có thể dùng HTML)</label>
                    <textarea 
                        id="form_tai_lo_trinh_hoc_footer" 
                        name="form_tai_lo_trinh_hoc[footer]" 
                        class="form-control" 
                        rows="3"
                        placeholder="Nhập footer (VD: Còn <span class='cl'>10</span> chỉ tiêu tuyển sinh năm 2025)"
                    >{{ $formTaiLoTrinhHocFooter }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Form Tư Vấn Miễn Phí --}}
<div class="ibox w">
    <div class="ibox-title">
        <h5>Cấu Hình Form Tư Vấn Miễn Phí</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="form_tu_van_mien_phi_title" class="control-label text-left">Tiêu đề Form</label>
                    <input 
                        type="text" 
                        id="form_tu_van_mien_phi_title" 
                        name="form_tu_van_mien_phi[title]" 
                        class="form-control" 
                        value="{{ $formTuVanMienPhiTitle }}"
                        placeholder="Nhập tiêu đề form"
                        autocomplete="off"
                    >
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="form_tu_van_mien_phi_description" class="control-label text-left">Mô tả</label>
                    <textarea 
                        id="form_tu_van_mien_phi_description" 
                        name="form_tu_van_mien_phi[description]" 
                        class="form-control" 
                        rows="3"
                        placeholder="Nhập mô tả"
                    >{{ $formTuVanMienPhiDescription }}</textarea>
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="form_tu_van_mien_phi_script" class="control-label text-left">Mã Nhúng</label>
                    <textarea 
                        id="form_tu_van_mien_phi_script" 
                        name="form_tu_van_mien_phi[script]" 
                        class="form-control" 
                        rows="5"
                        placeholder="Nhập mã nhúng form"
                    >{{ $formTuVanMienPhiScript }}</textarea>
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="form_tu_van_mien_phi_footer" class="control-label text-left">Footer (có thể dùng HTML)</label>
                    <textarea 
                        id="form_tu_van_mien_phi_footer" 
                        name="form_tu_van_mien_phi[footer]" 
                        class="form-control" 
                        rows="3"
                        placeholder="Nhập footer (VD: Còn <span class='cl'>20</span> chỉ tiêu tuyển sinh năm 2025)"
                    >{{ $formTuVanMienPhiFooter }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Form Học Thử Miễn Phí --}}
<div class="ibox w">
    <div class="ibox-title">
        <h5>Cấu Hình Form Học Thử Miễn Phí</h5>
    </div>
    <div class="ibox-content">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="form_hoc_thu_title" class="control-label text-left">Tiêu đề Form</label>
                    <input 
                        type="text" 
                        id="form_hoc_thu_title" 
                        name="form_hoc_thu[title]" 
                        class="form-control" 
                        value="{{ $formHocThuTitle }}"
                        placeholder="Nhập tiêu đề form"
                        autocomplete="off"
                    >
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="form_hoc_thu_description" class="control-label text-left">Mô tả</label>
                    <textarea 
                        id="form_hoc_thu_description" 
                        name="form_hoc_thu[description]" 
                        class="form-control" 
                        rows="3"
                        placeholder="Nhập mô tả"
                    >{{ $formHocThuDescription }}</textarea>
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="form_hoc_thu_script" class="control-label text-left">Mã Nhúng</label>
                    <textarea 
                        id="form_hoc_thu_script" 
                        name="form_hoc_thu[script]" 
                        class="form-control" 
                        rows="5"
                        placeholder="Nhập mã nhúng form"
                    >{{ $formHocThuScript }}</textarea>
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="form_hoc_thu_footer" class="control-label text-left">Footer (có thể dùng HTML)</label>
                    <textarea 
                        id="form_hoc_thu_footer" 
                        name="form_hoc_thu[footer]" 
                        class="form-control" 
                        rows="3"
                        placeholder="Nhập footer (VD: Còn <span class='cl'>10</span> suất học thử miễn phí)"
                    >{{ $formHocThuFooter }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>

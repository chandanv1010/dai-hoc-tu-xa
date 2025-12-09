@include('backend.dashboard.component.publish', ['model' => ($major) ?? null, 'hideImage' => false])

<div class="ibox w">
    <div class="ibox-title">
        <h5>Danh mục Ngành học</h5>
    </div>
    <div class="ibox-content">
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <select name="major_catalogue_id" class="form-control">
                        <option value="">-- Chọn danh mục --</option>
                        @if(isset($majorCatalogues) && count($majorCatalogues) > 0)
                            @foreach($majorCatalogues as $catalogue)
                                <option 
                                    value="{{ $catalogue->id }}"
                                    {{ old('major_catalogue_id', (isset($major) && isset($major->major_catalogue_id)) ? $major->major_catalogue_id : '') == $catalogue->id ? 'selected' : '' }}
                                >
                                    {{ $catalogue->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    // Lấy dữ liệu từ old() hoặc từ database
    $admissionSubject = old('admission_subject', isset($major) && $major->admission_subject ? $major->admission_subject : '');
    $examLocation = old('exam_location', isset($major) && $major->exam_location ? $major->exam_location : '');
    
    // Đảm bảo là string
    $admissionSubject = is_string($admissionSubject) ? $admissionSubject : '';
    $examLocation = is_string($examLocation) ? $examLocation : '';
@endphp

{{-- Bộ Lọc --}}
<div class="ibox w">
    <div class="ibox-title">
        <h5>Bộ Lọc</h5>
    </div>
    <div class="ibox-content">
        {{-- Đối Tượng Tuyển Sinh --}}
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="admission_subject" class="control-label text-left">Đối Tượng Tuyển Sinh</label>
                    <input 
                        type="text" 
                        id="admission_subject" 
                        name="admission_subject" 
                        class="form-control" 
                        value="{{ old('admission_subject', $admissionSubject) }}"
                        placeholder="Nhập đối tượng tuyển sinh (VD: THPT, Trung Cấp, Cao Đẳng, Đại Học)"
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
                        placeholder="Nhập địa điểm thi (VD: Hà Nội, Đà Nẵng, Hồ Chí Minh, Nhật Bản)"
                        autocomplete="off"
                    >
                </div>
            </div>
        </div>
    </div>
</div>

@php
    // Xử lý form_tai_lo_trinh_json
    $formTaiLoTrinhRaw = old('form_tai_lo_trinh_json');
    if ($formTaiLoTrinhRaw === null) {
        $formTaiLoTrinhRaw = isset($major) ? ($major->form_tai_lo_trinh_json ?? null) : null;
    }
    $formTaiLoTrinh = [];
    if ($formTaiLoTrinhRaw) {
        if (is_array($formTaiLoTrinhRaw)) {
            $formTaiLoTrinh = $formTaiLoTrinhRaw;
        } elseif (is_string($formTaiLoTrinhRaw)) {
            $decoded = json_decode($formTaiLoTrinhRaw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $formTaiLoTrinh = $decoded;
            }
        }
    }
    
    // Xử lý form_tu_van_mien_phi_json
    $formTuVanRaw = old('form_tu_van_mien_phi_json');
    if ($formTuVanRaw === null) {
        $formTuVanRaw = isset($major) ? ($major->form_tu_van_mien_phi_json ?? null) : null;
    }
    $formTuVan = [];
    if ($formTuVanRaw) {
        if (is_array($formTuVanRaw)) {
            $formTuVan = $formTuVanRaw;
        } elseif (is_string($formTuVanRaw)) {
            $decoded = json_decode($formTuVanRaw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $formTuVan = $decoded;
            }
        }
    }
    
    // Xử lý form_hoc_thu_json
    $formHocThuRaw = old('form_hoc_thu_json');
    if ($formHocThuRaw === null) {
        $formHocThuRaw = isset($major) ? ($major->form_hoc_thu_json ?? null) : null;
    }
    $formHocThu = [];
    if ($formHocThuRaw) {
        if (is_array($formHocThuRaw)) {
            $formHocThu = $formHocThuRaw;
        } elseif (is_string($formHocThuRaw)) {
            $decoded = json_decode($formHocThuRaw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $formHocThu = $decoded;
            }
        }
    }
    
    // Lấy giá trị từ old() hoặc từ array đã decode
    $formTaiLoTrinhScript = old('form_tai_lo_trinh_json.script', $formTaiLoTrinh['script'] ?? '');
    $formTaiLoTrinhTitle = old('form_tai_lo_trinh_json.title', $formTaiLoTrinh['title'] ?? '');
    $formTaiLoTrinhDescription = old('form_tai_lo_trinh_json.description', $formTaiLoTrinh['description'] ?? '');
    $formTaiLoTrinhFooter = old('form_tai_lo_trinh_json.footer', $formTaiLoTrinh['footer'] ?? '');
    
    $formTuVanScript = old('form_tu_van_mien_phi_json.script', $formTuVan['script'] ?? '');
    $formTuVanTitle = old('form_tu_van_mien_phi_json.title', $formTuVan['title'] ?? '');
    $formTuVanDescription = old('form_tu_van_mien_phi_json.description', $formTuVan['description'] ?? '');
    $formTuVanFooter = old('form_tu_van_mien_phi_json.footer', $formTuVan['footer'] ?? '');
    
    $formHocThuScript = old('form_hoc_thu_json.script', $formHocThu['script'] ?? '');
    $formHocThuTitle = old('form_hoc_thu_json.title', $formHocThu['title'] ?? '');
    $formHocThuDescription = old('form_hoc_thu_json.description', $formHocThu['description'] ?? '');
    $formHocThuFooter = old('form_hoc_thu_json.footer', $formHocThu['footer'] ?? '');
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
                    <label for="form_tai_lo_trinh_title" class="control-label text-left">Tiêu đề Form</label>
                    <input 
                        type="text" 
                        id="form_tai_lo_trinh_title" 
                        name="form_tai_lo_trinh_json[title]" 
                        class="form-control" 
                        value="{{ $formTaiLoTrinhTitle }}"
                        placeholder="Nhập tiêu đề form"
                        autocomplete="off"
                    >
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="form_tai_lo_trinh_description" class="control-label text-left">Mô tả</label>
                    <input 
                        type="text" 
                        id="form_tai_lo_trinh_description" 
                        name="form_tai_lo_trinh_json[description]" 
                        class="form-control" 
                        value="{{ $formTaiLoTrinhDescription }}"
                        placeholder="Nhập mô tả"
                        autocomplete="off"
                    >
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="form_tai_lo_trinh_script" class="control-label text-left">Mã Nhúng</label>
                    <textarea 
                        id="form_tai_lo_trinh_script" 
                        name="form_tai_lo_trinh_json[script]" 
                        class="form-control" 
                        rows="5"
                        placeholder="Nhập mã nhúng form"
                    >{{ $formTaiLoTrinhScript }}</textarea>
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="form_tai_lo_trinh_footer" class="control-label text-left">Footer (có thể dùng HTML)</label>
                    <textarea 
                        id="form_tai_lo_trinh_footer" 
                        name="form_tai_lo_trinh_json[footer]" 
                        class="form-control" 
                        rows="3"
                        placeholder="Nhập footer (VD: Còn <span class='cl'>10</span> chỉ tiêu tuyển sinh năm 2025)"
                    >{{ $formTaiLoTrinhFooter }}</textarea>
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
                    <label for="form_tu_van_title" class="control-label text-left">Tiêu đề Form</label>
                    <input 
                        type="text" 
                        id="form_tu_van_title" 
                        name="form_tu_van_mien_phi_json[title]" 
                        class="form-control" 
                        value="{{ $formTuVanTitle }}"
                        placeholder="Nhập tiêu đề form"
                        autocomplete="off"
                    >
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="form_tu_van_description" class="control-label text-left">Mô tả</label>
                    <input 
                        type="text" 
                        id="form_tu_van_description" 
                        name="form_tu_van_mien_phi_json[description]" 
                        class="form-control" 
                        value="{{ $formTuVanDescription }}"
                        placeholder="Nhập mô tả"
                        autocomplete="off"
                    >
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="form_tu_van_script" class="control-label text-left">Mã Nhúng</label>
                    <textarea 
                        id="form_tu_van_script" 
                        name="form_tu_van_mien_phi_json[script]" 
                        class="form-control" 
                        rows="5"
                        placeholder="Nhập mã nhúng form"
                    >{{ $formTuVanScript }}</textarea>
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="form_tu_van_footer" class="control-label text-left">Footer (có thể dùng HTML)</label>
                    <textarea 
                        id="form_tu_van_footer" 
                        name="form_tu_van_mien_phi_json[footer]" 
                        class="form-control" 
                        rows="3"
                        placeholder="Nhập footer (VD: Còn <span class='cl'>10</span> chỉ tiêu tuyển sinh năm 2025)"
                    >{{ $formTuVanFooter }}</textarea>
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
                        name="form_hoc_thu_json[title]" 
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
                    <input 
                        type="text" 
                        id="form_hoc_thu_description" 
                        name="form_hoc_thu_json[description]" 
                        class="form-control" 
                        value="{{ $formHocThuDescription }}"
                        placeholder="Nhập mô tả"
                        autocomplete="off"
                    >
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="form_hoc_thu_script" class="control-label text-left">Mã Nhúng</label>
                    <textarea 
                        id="form_hoc_thu_script" 
                        name="form_hoc_thu_json[script]" 
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
                        name="form_hoc_thu_json[footer]" 
                        class="form-control" 
                        rows="3"
                        placeholder="Nhập footer (VD: Còn <span class='cl'>10</span> suất học thử miễn phí)"
                    >{{ $formHocThuFooter }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>

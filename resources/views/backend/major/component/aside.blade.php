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

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
        {{-- Hệ Tốt Nghiệp --}}
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label for="graduation_system" class="control-label text-left">Hệ Tốt Nghiệp</label>
                    <input 
                        type="text" 
                        id="graduation_system" 
                        name="graduation_system" 
                        class="form-control" 
                        value="{{ old('graduation_system', $graduationSystem) }}"
                        placeholder="Nhập hệ tốt nghiệp"
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
                        placeholder="Nhập địa điểm thi"
                        autocomplete="off"
                    >
                </div>
            </div>
        </div>
    </div>
</div>


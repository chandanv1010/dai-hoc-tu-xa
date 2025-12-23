@php
    $majorId = $majorData['major_id'] ?? (is_array($majorData) ? ($majorData['id'] ?? '') : '');
    $majorName = '';
    
    // Tìm tên ngành từ majorsList (có thể là array hoặc collection)
    if (isset($majorsList)) {
        if (is_array($majorsList)) {
            foreach ($majorsList as $m) {
                if (isset($m->id) && $m->id == $majorId) {
                    $majorName = $m->name ?? '';
                    break;
                }
            }
        } else {
            // Nếu là collection
            $major = $majorsList->firstWhere('id', $majorId);
            if ($major) {
                $majorName = $major->name ?? '';
            }
        }
    }
    
    // Nếu vẫn chưa có tên, dùng fallback
    if (empty($majorName)) {
        $majorName = 'Ngành học #' . ($index + 1);
    }
@endphp
<div class="major-item" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px;" data-major-id="{{ $majorId }}">
    <input type="hidden" name="majors[{{ $index }}][major_id]" value="{{ $majorId }}">
    <h5>{{ $majorName }}</h5>
    <div class="row mb15">
        <div class="col-lg-6">
            <div class="form-row">
                <label class="control-label text-left">Hình thức xét tuyển</label>
                <input 
                    type="text" 
                    name="majors[{{ $index }}][admission_method]" 
                    class="form-control" 
                    placeholder="Nhập hình thức xét tuyển"
                    value="{{ old("majors.$index.admission_method", $majorData['admission_method'] ?? '') }}"
                >
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-row">
                <label class="control-label text-left">Thời gian</label>
                <input 
                    type="text" 
                    name="majors[{{ $index }}][duration]" 
                    class="form-control" 
                    placeholder="Nhập thời gian"
                    value="{{ old("majors.$index.duration", $majorData['duration'] ?? '') }}"
                >
            </div>
        </div>
    </div>
    <div class="row mb15">
        <div class="col-lg-6">
            <div class="form-row">
                <label class="control-label text-left">Học phí</label>
                <input 
                    type="text" 
                    name="majors[{{ $index }}][tuition]" 
                    class="form-control" 
                    placeholder="Nhập học phí"
                    value="{{ old("majors.$index.tuition", $majorData['tuition'] ?? '') }}"
                >
            </div>
        </div>
        <div class="col-lg-6">
            <div class="form-row">
                <label class="control-label text-left">Địa điểm</label>
                <input 
                    type="text" 
                    name="majors[{{ $index }}][location]" 
                    class="form-control" 
                    placeholder="Nhập địa điểm"
                    value="{{ old("majors.$index.location", $majorData['location'] ?? '') }}"
                >
            </div>
        </div>
    </div>
    <div class="row mb15">
        <div class="col-lg-4">
            <div class="form-row">
                <label class="control-label text-left">Học phí năm</label>
                <input 
                    type="text" 
                    name="majors[{{ $index }}][annual_tuition]" 
                    class="form-control" 
                    placeholder="Nhập học phí năm"
                    value="{{ old("majors.$index.annual_tuition", $majorData['annual_tuition'] ?? '') }}"
                >
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-row">
                <label class="control-label text-left">Số tín chỉ</label>
                <input 
                    type="text" 
                    name="majors[{{ $index }}][credits]" 
                    class="form-control" 
                    placeholder="Nhập số tín chỉ"
                    value="{{ old("majors.$index.credits", $majorData['credits'] ?? '') }}"
                >
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-row">
                <label class="control-label text-left">Học phí / 1 tín chỉ</label>
                <input 
                    type="text" 
                    name="majors[{{ $index }}][tuition_per_credit]" 
                    class="form-control" 
                    placeholder="Nhập học phí / 1 tín chỉ"
                    value="{{ old("majors.$index.tuition_per_credit", $majorData['tuition_per_credit'] ?? '') }}"
                >
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <button type="button" class="btn btn-danger removeMajorItemBtn"><i class="fa fa-trash"></i> Xóa</button>
        </div>
    </div>
</div>


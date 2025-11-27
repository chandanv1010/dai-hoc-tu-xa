<div class="suitable-item" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px;">
    <div class="row mb15">
        <div class="col-lg-3">
            <div class="form-row">
                <label class="control-label text-left">Ảnh</label>
                <input 
                    type="text" 
                    name="suitable[items][{{ $index }}][image]" 
                    class="form-control upload-image" 
                    placeholder="Chọn ảnh"
                    value="{{ old("suitable.items.$index.image", $item['image'] ?? '') }}"
                    readonly
                >
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-row">
                <label class="control-label text-left">Tên</label>
                <input 
                    type="text" 
                    name="suitable[items][{{ $index }}][name]" 
                    class="form-control" 
                    placeholder="Nhập tên"
                    value="{{ old("suitable.items.$index.name", $item['name'] ?? '') }}"
                >
            </div>
        </div>
        <div class="col-lg-4">
            <div class="form-row">
                <label class="control-label text-left">Mô tả</label>
                <input 
                    type="text" 
                    name="suitable[items][{{ $index }}][description]" 
                    class="form-control" 
                    placeholder="Nhập mô tả"
                    value="{{ old("suitable.items.$index.description", $item['description'] ?? '') }}"
                >
            </div>
        </div>
        <div class="col-lg-1">
            <div class="form-row">
                <label class="control-label text-left">&nbsp;</label>
                <button type="button" class="btn btn-danger btn-sm removeSuitableItemBtn" style="width: 100%;">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>
    </div>
</div>


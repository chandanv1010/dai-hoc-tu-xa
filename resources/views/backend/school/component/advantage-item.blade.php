<div class="advantage-item" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 15px; border-radius: 5px;">
    <div class="row mb15">
        <div class="col-lg-12">
            <div class="form-row">
                <label class="control-label text-left">Tên</label>
                <input 
                    type="text" 
                    name="advantage[items][{{ $index }}][name]" 
                    class="form-control" 
                    placeholder="Nhập tên"
                    value="{{ old("advantage.items.$index.name", $item['name'] ?? '') }}"
                >
            </div>
        </div>
    </div>
    <div class="row mb15">
        <div class="col-lg-12">
            <div class="form-row">
                <label class="control-label text-left">Mô tả</label>
                <input 
                    type="text" 
                    name="advantage[items][{{ $index }}][description]" 
                    class="form-control" 
                    placeholder="Nhập mô tả"
                    value="{{ old("advantage.items.$index.description", $item['description'] ?? '') }}"
                >
            </div>
        </div>
    </div>
    <div class="row mb15">
        <div class="col-lg-12">
            <div class="form-row">
                <label class="control-label text-left">Icon</label>
                <input 
                    type="text" 
                    name="advantage[items][{{ $index }}][icon]" 
                    class="form-control upload-image" 
                    placeholder="Chọn ảnh icon"
                    value="{{ old("advantage.items.$index.icon", $item['icon'] ?? '') }}"
                    readonly
                >
            </div>
        </div>
    </div>
    <div class="row mb15">
        <div class="col-lg-12">
            <div class="form-row">
                <label class="control-label text-left">Ghi chú</label>
                <input 
                    type="text" 
                    name="advantage[items][{{ $index }}][note]" 
                    class="form-control" 
                    placeholder="Nhập ghi chú"
                    value="{{ old("advantage.items.$index.note", $item['note'] ?? '') }}"
                >
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <button type="button" class="btn btn-danger removeAdvantageItemBtn"><i class="fa fa-trash"></i> Xóa</button>
        </div>
    </div>
</div>


<div class="advantage-item mb15" data-index="{{ $index }}">
    <div class="ibox" style="background: #f9f9f9; padding: 15px; border-radius: 5px;">
        <div class="row mb15">
            <div class="col-lg-11">
                <div class="form-row">
                    <label class="control-label text-left">Tên</label>
                    <input 
                        type="text"
                        name="priority[{{ $index }}][name]"
                        value="{{ old("priority.{$index}.name", (is_array($item) ? ($item['name'] ?? '') : ($item->name ?? ''))) }}"
                        class="form-control"
                        placeholder="Nhập tên"
                    >
                </div>
            </div>
            <div class="col-lg-1">
                <div class="form-row">
                    <label class="control-label text-left">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm removeAdvantageItemBtn" style="width: 100%;">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label class="control-label text-left">Ảnh</label>
                    <input 
                        type="text"
                        name="priority[{{ $index }}][image]"
                        value="{{ old("priority.{$index}.image", (is_array($item) ? ($item['image'] ?? '') : ($item->image ?? ''))) }}"
                        class="form-control upload-image"
                        placeholder="Chọn ảnh"
                        readonly
                    >
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <label class="control-label text-left">Mô tả</label>
                    <textarea 
                        name="priority[{{ $index }}][description]"
                        class="form-control"
                        rows="4"
                        placeholder="Nhập mô tả"
                    >{{ old("priority.{$index}.description", (is_array($item) ? ($item['description'] ?? '') : ($item->description ?? ''))) }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>


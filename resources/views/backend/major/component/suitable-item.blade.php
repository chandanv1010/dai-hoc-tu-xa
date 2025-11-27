<div class="suitable-item mb15" data-index="{{ $index }}">
    <div class="ibox" style="background: #f9f9f9; padding: 15px; border-radius: 5px;">
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label class="control-label text-left">Hình ảnh</label>
                    <input 
                        type="text"
                        name="who[{{ $index }}][image]"
                        value="{{ old("who.{$index}.image", (is_array($item) ? ($item['image'] ?? '') : ($item->image ?? ''))) }}"
                        class="form-control upload-image"
                        placeholder="Chọn ảnh"
                        readonly
                    >
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-12">
                <div class="form-row">
                    <label class="control-label text-left">Tên</label>
                    <input 
                        type="text"
                        name="who[{{ $index }}][name]"
                        value="{{ old("who.{$index}.name", (is_array($item) ? ($item['name'] ?? '') : ($item->name ?? ''))) }}"
                        class="form-control"
                        placeholder="Nhập tên"
                    >
                </div>
            </div>
        </div>
        <div class="row mb15">
            <div class="col-lg-11">
                <div class="form-row">
                    <label class="control-label text-left">Mô tả</label>
                    <textarea 
                        name="who[{{ $index }}][description]"
                        class="ck-editor suitable-content-editor"
                        id="ckSuitableContent{{ $index }}"
                        data-height="200"
                    >{{ old("who.{$index}.description", (is_array($item) ? ($item['description'] ?? '') : ($item->description ?? ''))) }}</textarea>
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
        <div class="row">
            <div class="col-lg-12">
                <div class="form-row">
                    <label class="control-label text-left">Người phù hợp</label>
                    <input 
                        type="text"
                        name="who[{{ $index }}][person]"
                        value="{{ old("who.{$index}.person", (is_array($item) ? ($item['person'] ?? '') : ($item->person ?? ''))) }}"
                        class="form-control"
                        placeholder="Nhập người phù hợp"
                    >
                </div>
            </div>
        </div>
    </div>
</div>

